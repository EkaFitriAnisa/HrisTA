<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Clearance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $departId = $user->karyawan->depart_id;
        $perPage = 5;

        $query = Clearance::query()
            ->where('status', 'approved')
            ->whereHas(
                'approvals.approver.karyawan',
                function ($q) use ($departId) {
                    $q->where('depart_id', $departId);
                }
            )
            ->where('status', 'approved')
            ->with([
                'karyawan.user',
                'karyawan.depart',
                'clearanceAset.aset.kategori',
                'approvals.approver.karyawan',
            ]);

        // Search
        if ($request->search) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('jabatan', 'like', "%{$request->search}%");
            });
        }

        // Filter jenis
        if ($request->jenis) {
            $query->where('jenis', $request->jenis);
        }
        // Filter bulan
        if ($request->bulan) {
            $query->whereMonth('updated_at', $request->bulan);
        }
        // Filter tahun
        if ($request->tahun) {
            $query->whereYear('updated_at', $request->tahun);
        }
        $clearances = $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('mis.pages.riwayat', [
            'clearances' => $clearances,
            'perPage' => $perPage,
            // Card Statistics
            'totalApproved' => Clearance::whereHas(
                'karyawan',
            )
                ->where('status', 'approved')
                ->count(),
            'totalResign' => Clearance::whereHas(
                'karyawan',
                fn ($q) => $q->where('depart_id', $departId)
            )
                ->where('status', 'approved')
                ->where('jenis', 'resign')
                ->count(),
            'totalMutasi' => Clearance::whereHas(
                'karyawan',
            )
                ->where('status', 'approved')
                ->where('jenis', 'mutasi_internal')
                ->count(),
            'approvedThisMonth' => Clearance::whereHas(
                'karyawan',
            )
                ->where('status', 'approved')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),
        ]);
    }
}