<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Models\Clearance;
use App\Models\ClearanceApproval;
use App\Models\ClearanceAset;
use App\Services\Clearance\ClearanceFinalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClearanceController extends Controller
{
    private array $approvalFlow = ['HOD', 'MIS', 'HRD'];

    public function __construct(private ClearanceFinalizer $finalizer) {}

    public function index(Request $request): View
    {
        $user     = auth()->user();
        $departId = $user->karyawan->depart_id;
        $perPage  = max(5, (int) $request->input('per_page', 5));

        $clearances = Clearance::query()
            ->whereHas('karyawan', fn (Builder $q) =>
                $q->where('depart_id', $departId)
            )
            ->whereNotIn('status', [
                'approved',
                'rejected',
            ])
            ->with([
                'karyawan.depart',
                'departTujuan',
                'clearanceAset.aset.kategori',
                'approvals.approver.karyawan',
            ])
            ->when($request->search, fn (Builder $q) =>
                $q->whereHas('karyawan', fn (Builder $sub) =>
                    $sub->where('nama', 'like', "%{$request->search}%")
                        ->orWhere('jabatan', 'like', "%{$request->search}%")
                )
            )
            ->when($request->status, fn (Builder $q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('hod.pages.clearance', [
            'clearances'        => $clearances,
            'perPage'           => $perPage,
            'currentRole'       => $user->role,
            'totalClearance'    => $this->countByStatus($departId),
            'pendingClearance'  => $this->countByStatus($departId, 'pending'),
            'processClearance'  => $this->countByStatus($departId, 'process'),
            'approvedClearance' => $this->countByStatus($departId, 'approved'),
            'revisiClearance' => $this->countByStatus($departId, 'revision'),
        ]);
    }

    public function approveAset(ClearanceAset $clearanceAset): RedirectResponse
    {
        $user = auth()->user();

        if (! $this->canAct($clearanceAset, $user)) {
            return back()->with('error', 'Approval sebelumnya belum selesai.');
        }

        DB::transaction(function () use ($clearanceAset, $user) {
            $clearanceAset->update(['status_pengembalian' => 'returned', 'catatan' => null]);

            $clearance = $this->loadClearance($clearanceAset);

            $this->syncApproval($clearance, $user);
            $this->syncClearanceStatus($clearance);
            $this->finalizer->finalize($clearance);
        });

        return back()->with('success', 'Aset berhasil di-approve.');
    }

    public function rejectAset(Request $request, ClearanceAset $clearanceAset): RedirectResponse
    {
        $user  = auth()->user();
        $notes = $request->validate(['notes' => 'required|string|max:1000'])['notes'];

        if (! $this->canAct($clearanceAset, $user)) {
            return back()->with('error', 'Approval sebelumnya belum selesai.');
        }

        DB::transaction(function () use ($clearanceAset, $user, $notes) {
            $clearanceAset->update(['status_pengembalian' => 'missing', 'catatan' => $notes]);

            $clearance = $this->loadClearance($clearanceAset);

            $this->syncApproval($clearance, $user, $notes);
            $this->syncClearanceStatus($clearance);
        });

        return back()->with('success', 'Aset berhasil direvisi.');
    }

    private function loadClearance(ClearanceAset $clearanceAset): Clearance
    {
        return $clearanceAset->clearance()
            ->with('clearanceAset.aset.kategori', 'approvals')
            ->first();
    }

    private function canAct(ClearanceAset $clearanceAset, $user): bool
    {
        $clearance = $clearanceAset->clearance()
            ->with('karyawan', 'approvals', 'clearanceAset.aset.kategori')
            ->first();

        return $clearanceAset->aset?->kategori?->managed_role === $user->role
            && $clearance?->karyawan?->depart_id === $user->karyawan->depart_id
            && $this->canProcessCurrentStep($clearance, $user->role);
    }

    private function requiredRoles(Clearance $clearance): array
    {
        $usedRoles = $clearance->clearanceAset
            ->pluck('aset.kategori.managed_role')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return collect($this->approvalFlow)
            ->filter(fn ($role) => in_array($role, $usedRoles, true))
            ->values()
            ->all();
    }

    private function canProcessCurrentStep(Clearance $clearance, string $role): bool
    {
        $roles        = $this->requiredRoles($clearance);
        $currentIndex = array_search($role, $roles, true);

        if ($currentIndex === false) {
            return false;
        }

        for ($i = 0; $i < $currentIndex; $i++) {
            $prev = $clearance->approvals->firstWhere('step_order', $i + 1);

            if (! $prev || $prev->status !== 'approved') {
                return false;
            }
        }

        return true;
    }

    private function syncApproval(Clearance $clearance, $user, ?string $notes = null): void
    {
        $roles     = $this->requiredRoles($clearance);
        $stepIndex = array_search($user->role, $roles, true);

        if ($stepIndex === false) {
            return;
        }

        $roleAssets = $clearance->clearanceAset->filter(
            fn ($ca) => $ca->aset?->kategori?->managed_role === $user->role
        );

        if ($roleAssets->isEmpty()) {
            return;
        }

        $status = match (true) {
            $roleAssets->contains('status_pengembalian', 'missing') => 'revision',
            $roleAssets->every(fn ($ca) => $ca->status_pengembalian === 'returned') => 'approved',
            default => 'pending',
        };

        ClearanceApproval::updateOrCreate(
            ['clearance_id' => $clearance->id, 'step_order' => $stepIndex + 1],
            ['approved_by' => $user->id, 'status' => $status, 'notes' => $notes, 'approved_at' => now()]
        );
    }

    private function syncClearanceStatus(Clearance $clearance): void
    {
        $assets = $clearance->clearanceAset;

        $clearance->update([
            'status' => match (true) {
                $assets->contains('status_pengembalian', 'missing') => 'revision',
                $assets->every(fn ($ca) => $ca->status_pengembalian === 'returned') => 'approved',
                default => 'process',
            },
        ]);
    }

    private function countByStatus(int $departId, string|array|null $status = null): int
    {
        return Clearance::query()
            ->whereHas('karyawan', fn (Builder $q) => $q->where('depart_id', $departId))
            ->when(is_array($status),  fn ($q) => $q->whereIn('status', $status))
            ->when(is_string($status), fn ($q) => $q->where('status', $status))
            ->count();
    }
}