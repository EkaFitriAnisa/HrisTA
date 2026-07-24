<?php

namespace App\Http\Controllers;

use App\Models\Clearance;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $departId = $user->karyawan->depart_id;

        $baseQuery = Clearance::query()

            ->whereHas(
                'karyawan',
                fn ($q) =>
                    $q->where('depart_id', $departId)
            );

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

            ->whereHas(
                'karyawan',
                fn ($q) =>
                    $q->where('depart_id', $departId)
            )

            ->with([
                'karyawan.depart',
            ])

            ->latest()

            ->take(5)

            ->get();

        return view(
            strtolower($user->role) . '.pages.dashboard',
            [
                'totalClearance' => $totalClearance,
                'pendingClearance' => $pendingClearance,
                'processClearance' => $processClearance,
                'revisionClearance' => $revisionClearance,
                'activities' => $activities,
            ]
        );
    }
}