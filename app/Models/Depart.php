<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depart extends Model
{
    protected $table = 'depart';

    protected $fillable = ['code_depart', 'nama'];

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'depart_id');
    }
}