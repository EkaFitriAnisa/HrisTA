<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Depart;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $karyawan = Auth::user()->karyawan->load('depart');

        $asetAktif = $karyawan->asetAktif()
            ->with(['aset.kategori'])
            ->get();

        $clearanceAktif = $karyawan->clearances()
            ->with(['approvals'])
            ->whereNotIn('status', ['approved', 'rejected'])
            ->latest()
            ->first();

        // Departemen untuk pilihan tujuan mutasi (kecuali departemen sendiri)
        $departemens = Depart::where('id', '!=', $karyawan->depart_id)->get();

        return view('karyawan.pages.dashboard', compact(
            'karyawan',
            'asetAktif',
            'clearanceAktif',
            'departemens',
        ));
    }
}