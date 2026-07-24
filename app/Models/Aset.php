<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    protected $table = 'aset';

    protected $fillable = [
        'kategori_id', 'depart_id', 'nama', 'status',
        'asset_no', 'jumlah', 'kondisi', 'username',
        'platform', 'alamat', 'no_plat', 'cc_last4', 'catatan',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriAset::class, 'kategori_id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id');
    }
}