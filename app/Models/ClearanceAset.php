<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClearanceAset extends Model
{
    protected $table = 'clearance_aset';

    protected $fillable = [
        'clearance_id', 'aset_id', 'status_pengembalian', 'catatan', 'bukti_file',
    ];

    public function clearance()
    {
        return $this->belongsTo(Clearance::class, 'clearance_id');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }
}