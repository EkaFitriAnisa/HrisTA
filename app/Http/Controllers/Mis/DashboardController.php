<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Clearance;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $departId = auth()->user()->karyawan->depart_id;
        $baseQuery = Clearance::query()->where('depart_id', $departId);
        
        $totalClearance = (clone $baseQuery)->count();
        $pendingClearance = (clone $baseQuery)
            ->where('status', 'pending')
            ->count();
        $processClearance = (clone $baseQuery)
            ->where('status', 'process')
            ->count();
        $revisionClearance = (clone $baseQuery)
            ->where('status', 'revision')
            ->count();
        $activities = Clearance::query()
            ->where('depart_id', $departId)
            ->with(['karyawan',])
            ->latest()
            ->take(5)
            ->get();

        return view('mis.pages.dashboard', [
            'totalClearance' => $totalClearance,
            'pendingClearance' => $pendingClearance,
            'processClearance' => $processClearance,
            'revisionClearance' => $revisionClearance,
            'activities' => $activities,
        ]);
    }
}