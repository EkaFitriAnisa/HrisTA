<?php

namespace App\Services\Clearance;

use App\Models\AssignAset;
use App\Models\Clearance;

class ClearanceFinalizer
{
    public function finalize(Clearance $clearance): void
    {
        if ($clearance->status !== 'approved') {
            return;
        }

        $clearance->loadMissing([
            'karyawan.user',
            'clearanceAset',
        ]);

        AssignAset::where('karyawan_id', $clearance->karyawan_id)
            ->where('status', 'aktif')
            ->update([
                'status' => 'dikembalikan',
            ]);

        $clearance->clearanceAset()->update([
            'status_pengembalian' => 'returned',
        ]);

        match ($clearance->jenis) {
            'mutasi_internal' => $this->handleInternalTransfer($clearance),
            'resign' => $this->handleResignation($clearance),
            default => null,
        };
    }

    private function handleInternalTransfer(Clearance $clearance): void
    {
        $clearance->karyawan->update([
            'depart_id' => $clearance->depart_tujuan_id,
        ]);
    }

    private function handleResignation(Clearance $clearance): void
    {
        $clearance->karyawan->update([
            'active' => false,
            'tanggal_selesai' => $clearance->tanggal_efektif,
        ]);

        $clearance->karyawan->user?->update([
            'is_active' => false,
        ]);
    }
}