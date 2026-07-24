<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignAset extends Model
{
    protected $table = 'assign_aset';

    protected $fillable = ['karyawan_id', 'aset_id', 'tanggal_assign', 'keterangan', 'status'];

    protected $casts = [
        'tanggal_assign' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }
}