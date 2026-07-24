<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clearance extends Model
{
    protected $table = 'clearance';

    protected $fillable = [
        'karyawan_id', 'depart_id', 'jenis', 'tanggal_efektif',
        'depart_tujuan_id', 'alasan', 'status',
    ];

    protected $casts = [
        'tanggal_efektif' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id');
    }

    public function departTujuan()
    {
        return $this->belongsTo(Depart::class, 'depart_tujuan_id');
    }

    public function clearanceAset()
    {
        return $this->hasMany(ClearanceAset::class, 'clearance_id');
    }

    public function approvals()
    {
        return $this->hasMany(ClearanceApproval::class, 'clearance_id')->orderBy('step_order');
    }
}