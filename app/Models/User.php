<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'badge_id',
        'kata_sandi',
        'is_active',
        'role',
    ];

    protected $hidden = ['kata_sandi'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getAuthPassword(): string
    {
        return $this->kata_sandi;
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'users_id');
    }

    public function clearanceApprovals()
    {
        return $this->hasMany(ClearanceApproval::class, 'action_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function isApprover(): bool
    {
        return in_array($this->role, ['HRD', 'MIS', 'HOD']);
    }
}