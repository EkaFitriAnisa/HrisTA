<?php

namespace App\Http\Controllers;

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

            ->where('depart_id', $departId)

            ->where('status', 'approved')

            ->with([
                'depart',
                'karyawan.user',
                'karyawan.depart',
                'clearanceAset.aset.kategori',
                'approvals.approver.karyawan',
            ]);

        if ($request->search) {

            $query->whereHas('karyawan', function ($q) use ($request) {

                $q->where(
                    'nama',
                    'like',
                    "%{$request->search}%"
                )

                ->orWhere(
                    'jabatan',
                    'like',
                    "%{$request->search}%"
                );
            });
        }

        if ($request->jenis) {

            $query->where(
                'jenis',
                $request->jenis
            );
        }

        if ($request->bulan) {

            $query->whereMonth(
                'updated_at',
                $request->bulan
            );
        }

        if ($request->tahun) {

            $query->whereYear(
                'updated_at',
                $request->tahun
            );
        }

        $clearances = $query

            ->latest()

            ->paginate($perPage)

            ->withQueryString();

        $baseStat = Clearance::query()

            ->where('depart_id', $departId)

            ->where('status', 'approved');

        return view(
            strtolower($user->role) . '.pages.riwayat',
            [

                'clearances' => $clearances,

                'perPage' => $perPage,

                'totalApproved' => (clone $baseStat)->count(),

                'totalResign' => (clone $baseStat)
                    ->where('jenis', 'resign')
                    ->count(),

                'totalMutasi' => (clone $baseStat)
                    ->where('jenis', 'mutasi_internal')
                    ->count(),

                'approvedThisMonth' => (clone $baseStat)

                    ->whereMonth(
                        'updated_at',
                        now()->month
                    )

                    ->whereYear(
                        'updated_at',
                        now()->year
                    )

                    ->count(),
            ]
        );
    }
}