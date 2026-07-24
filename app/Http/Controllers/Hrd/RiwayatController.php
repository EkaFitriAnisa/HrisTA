<?php

namespace App\Http\Controllers\Hrd;

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

            // Riwayat berdasarkan approver depart
            ->whereHas(
                'approvals.approver.karyawan',
                function ($q) use ($departId) {

                    $q->where('depart_id', $departId);
                }
            )

            ->with([
                'karyawan.user',
                'karyawan.depart',
                'clearanceAset.aset.kategori',
                'approvals.approver.karyawan',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->search) {

            $query->whereHas('karyawan', function ($q) use ($request) {

                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('jabatan', 'like', "%{$request->search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Jenis
        |--------------------------------------------------------------------------
        */

        if ($request->jenis) {

            $query->where('jenis', $request->jenis);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Bulan
        |--------------------------------------------------------------------------
        */

        if ($request->bulan) {

            $query->whereMonth(
                'updated_at',
                $request->bulan
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Tahun
        |--------------------------------------------------------------------------
        */

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

        return view('hrd.pages.riwayat', [

            'clearances' => $clearances,

            'perPage' => $perPage,

            /*
            |--------------------------------------------------------------------------
            | Card Statistics
            |--------------------------------------------------------------------------
            */

            'totalApproved' => Clearance::query()

                ->where('status', 'approved')

                ->whereHas(
                    'approvals.approver.karyawan',
                    fn ($q) =>
                    $q->where('depart_id', $departId)
                )

                ->count(),

            'totalResign' => Clearance::query()

                ->where('status', 'approved')

                ->where('jenis', 'resign')

                ->whereHas(
                    'approvals.approver.karyawan',
                    fn ($q) =>
                    $q->where('depart_id', $departId)
                )

                ->count(),

            'totalMutasi' => Clearance::query()

                ->where('status', 'approved')

                ->where('jenis', 'mutasi_internal')

                ->whereHas(
                    'approvals.approver.karyawan',
                    fn ($q) =>
                    $q->where('depart_id', $departId)
                )

                ->count(),

            'approvedThisMonth' => Clearance::query()

                ->where('status', 'approved')

                ->whereMonth(
                    'updated_at',
                    now()->month
                )

                ->whereYear(
                    'updated_at',
                    now()->year
                )

                ->whereHas(
                    'approvals.approver.karyawan',
                    fn ($q) =>
                    $q->where('depart_id', $departId)
                )

                ->count(),
        ]);
    }
}