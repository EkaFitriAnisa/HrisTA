<?php

namespace App\Services\Clearance;

use App\Models\AssignAset;
use App\Models\Clearance;

class ClearanceFinalizer
{
    public function finalize(Clearance $clearance): void
    {
        $clearance->loadMissing('karyawan', 'clearanceAset', 'approvals');

        // Hanya jalan kalau status final sudah approved
        if ($clearance->status !== 'approved') {
            return;
        }

        // Kembalikan semua aset aktif
        AssignAset::where('karyawan_id', $clearance->karyawan_id)
            ->where('status', 'aktif')
            ->update([
                'status' => 'dikembalikan',
            ]);

        // Selaraskan clearance aset
        $clearance->clearanceAset()->update([
            'status_pengembalian' => 'returned',
        ]);

        // Finalisasi data karyawan
        if ($clearance->jenis === 'mutasi_internal') {
            $clearance->karyawan->update([
                'depart_id' => $clearance->depart_tujuan_id,
            ]);
        }

        if ($clearance->jenis === 'resign') {
            $clearance->karyawan->update([
                'active' => 0,
                'tanggal_selesai' => $clearance->tanggal_efektif,
            ]);
        }
    }
}