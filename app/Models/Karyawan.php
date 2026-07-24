<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $fillable = [
        'users_id', 'depart_id', 'nama', 'jabatan',
        'no_hp', 'tanggal_mulai', 'tanggal_selesai', 'active',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id');
    }

    public function assignAset()
    {
        return $this->hasMany(AssignAset::class, 'karyawan_id');
    }

    public function clearances()
    {
        return $this->hasMany(Clearance::class, 'karyawan_id');
    }

    public function asetAktif()
    {
        return $this->hasMany(AssignAset::class, 'karyawan_id')->where('status', 'aktif');
    }
}