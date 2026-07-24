<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClearanceApproval extends Model
{
    protected $table = 'clearance_approval';

    protected $fillable = [
        'clearance_id',
        'step_order',
        'approved_by',
        'status',
        'notes',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function clearance()
    {
        return $this->belongsTo(Clearance::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}