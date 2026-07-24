<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriAset extends Model
{
    protected $table = 'kategori_aset';

    protected $fillable = ['nama', 'managed_role'];

    public function asets()
    {
        return $this->hasMany(Aset::class, 'kategori_id');
    }
}