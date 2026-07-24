<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Clearance;
use App\Models\ClearanceApproval;
use App\Models\ClearanceAset;
use App\Models\Depart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClearanceController extends Controller
{
    public function index()
    {
        $karyawan = Auth::user()
            ->karyawan
            ->load('depart');

        $clearances = Clearance::with([
            'clearanceAset.aset.kategori',
            'approvals',
            'departTujuan',
        ])
            ->where('karyawan_id', $karyawan->id)
            ->latest()
            ->get();

        $asetAktif = $karyawan->asetAktif()
            ->with(['aset.kategori'])
            ->get();

        $departemens = Depart::where(
            'id',
            '!=',
            $karyawan->depart_id
        )->get();

        $hasActiveClearance = $clearances
            ->whereNotIn('status', [
                'approved',
                'rejected',
            ])
            ->isNotEmpty();

        return view('karyawan.pages.clearance', compact(
            'karyawan',
            'clearances',
            'asetAktif',
            'departemens',
            'hasActiveClearance',
        ));
    }

    public function store(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        // Ambil aset aktif
        $asetAktif = $karyawan->asetAktif()
            ->with(['aset.kategori'])
            ->get();

        $rules = [
            'jenis' => 'required|in:resign,mutasi_internal,mutasi_eksternal',
            'tanggal_efektif' => 'required|date|after:today',
            'depart_tujuan_id' => 'nullable|required_if:jenis,mutasi_internal,mutasi_eksternal|exists:depart,id',
            'alasan' => 'required|string|max:1000',
        ];

        $messages = [
            'jenis.required' => 'Jenis clearance wajib dipilih.',
            'tanggal_efektif.required' => 'Tanggal efektif wajib diisi.',
            'tanggal_efektif.after' => 'Tanggal efektif harus setelah hari ini.',
            'depart_tujuan_id.required_if' => 'Departemen tujuan wajib dipilih.',
            'alasan.required' => 'Alasan wajib diisi.',
        ];


        foreach ($asetAktif as $assign) {
            $rules["bukti.{$assign->aset_id}"] = 'required|image|mimes:jpg,jpeg,png|max:2048';
            $messages["bukti.{$assign->aset_id}.required"] = "Bukti {$assign->aset->nama} wajib diupload.";
            $messages["bukti.{$assign->aset_id}.image"] = "Bukti {$assign->aset->nama} harus berupa gambar.";
            $messages["bukti.{$assign->aset_id}.mimes"] = "Format {$assign->aset->nama} harus JPG/PNG.";
            $messages["bukti.{$assign->aset_id}.max"] = "Ukuran {$assign->aset->nama} maksimal 2MB.";
        }

        $validated = $request->validate(
            $rules,
            $messages
        );

        $alreadyActive = Clearance::where(
            'karyawan_id',
            $karyawan->id
        )
            ->whereNotIn('status', [
                'approved',
                'rejected',
            ])
            ->exists();

        if ($alreadyActive) {

            return back()->with('error', 'Kamu masih memiliki clearance berjalan.');
        }

        DB::transaction(function () use (
            $validated,
            $request,
            $karyawan,
            $asetAktif
        ) {

            $clearance = Clearance::create([
                'karyawan_id' => $karyawan->id,
                'depart_id' => $karyawan->depart_id,
                'jenis' => $validated['jenis'],
                'tanggal_efektif' => $validated['tanggal_efektif'],
                'depart_tujuan_id' => $validated['depart_tujuan_id'] ?? null,
                'alasan' => $validated['alasan'],
                'status' => 'pending',
            ]);

            foreach ($asetAktif as $assign) {
                $buktiPath = null;
                if ($request->hasFile(
                    "bukti.{$assign->aset_id}"
                )) {
                    $buktiPath = $request
                        ->file("bukti.{$assign->aset_id}")
                        ->store(
                            'uploads/clearance',
                            'public'
                        );
                }

                ClearanceAset::create([
                    'clearance_id' => $clearance->id,
                    'aset_id' => $assign->aset_id,
                    'status_pengembalian' => 'pending',
                    'bukti_file' => $buktiPath,
                ]);
            }

            $flow = ['HOD', 'MIS', 'HRD'];

            $usedRoles = $asetAktif
                ->pluck('aset.kategori.managed_role')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $approvalSteps = collect($flow)
                ->filter(fn ($role) =>
                    in_array($role, $usedRoles)
                )
                ->values()
                ->all();

            foreach ($approvalSteps as $index => $role) {
                $approver = User::where(
                    'role',
                    $role
                )
                    ->whereHas(
                        'karyawan',
                        fn ($q) =>
                        $q->where(
                            'depart_id',
                            $karyawan->depart_id
                        )
                    )
                    ->first();

                ClearanceApproval::create([
                    'clearance_id' => $clearance->id,
                    'step_order' => $index + 1,
                    'approved_by' => $approver?->id,
                    'status' => 'pending',
                ]);
            }
        });

        return redirect()->route('karyawan.clearance.index')->with('success', 'Clearance berhasil diajukan.');
    }

    public function show($id)
    {
        $karyawan = Auth::user()->karyawan;
        $clearance = Clearance::with([
            'clearanceAset.aset.kategori',
            'approvals.approver.karyawan',
            'departTujuan',
        ])
            ->where('karyawan_id', $karyawan->id)
            ->findOrFail($id);

        return response()->json($clearance);
    }


    public function revisi(Request $request, $id)
    {
        $request->validate([
            'bukti' => 'required|array|min:1',
            'bukti.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'bukti.required' => 'Minimal satu file wajib diupload.',
            'bukti.*.mimes' => 'File harus JPG, PNG, atau PDF.',
            'bukti.*.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $karyawan = Auth::user()->karyawan;
        $clearance = Clearance::where(
            'karyawan_id',
            $karyawan->id
        )
            ->where('status', 'revision')
            ->findOrFail($id);

        DB::transaction(function () use (
            $request,
            $clearance
        ) {
            foreach (
                $request->file('bukti')
                as $asetId => $file
            ) {

                $clearanceAset = ClearanceAset::where(
                    'clearance_id',
                    $clearance->id
                )
                    ->where('aset_id', $asetId)
                    ->first();

                if (! $clearanceAset) {
                    continue;
                }

                if ($clearanceAset->bukti_file) {
                    Storage::disk('public')
                        ->delete(
                            $clearanceAset->bukti_file
                        );
                }

                $path = $file->store(
                    'uploads/clearance',
                    'public'
                );

                $clearanceAset->update([
                    'bukti_file' => $path,
                    'status_pengembalian' => 'pending',
                    'catatan' => null,
                ]);
            }

            ClearanceApproval::where(
                'clearance_id',
                $clearance->id
            )
                ->where('status', 'revision')
                ->update([
                    'status' => 'pending',
                    'notes' => null,
                    'approved_at' => null,
                ]);

                $clearance->update([
                'status' => 'process',
            ]);
        });

        return redirect()->route('karyawan.clearance.index')->with('success', 'Revisi berhasil diupload.');
    }
}