<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Clearance;

class HrdDashboardController extends Controller
{
    public function index()
    {
        $departId = auth()->user()->karyawan->depart_id;

        $base = fn() => Clearance::whereHas('karyawan', fn($q) => $q->where('depart_id', $departId));

        return view('hrd.pages.dashboard', [
            'pendingClearance'  => $base()->whereIn('status', ['pending', 'process', 'revision'])->count(),
            'approvedClearance' => $base()->where('status', 'approved')->count(),
            'rejectedClearance' => $base()->where('status', 'rejected')->count(),
            'totalAssets'       => Aset::where('depart_id', $departId)->count(),
            'availableAssets'   => Aset::where('depart_id', $departId)->where('status', 'available')->count(),
            'assignedAssets'    => Aset::where('depart_id', $departId)->where('status', 'assigned')->count(),
        ]);
    }
}