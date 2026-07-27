<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const AVAILABLE_PERMISSIONS = [
        'project.manage' => 'Kelola Project',
        'task.manage' => 'Kelola Task',
        'user.manage' => 'Kelola User',
        'role.manage' => 'Kelola Role',
        'audit.view' => 'Lihat Audit Trail',
    ];

    protected $fillable = [
        'name',
        'is_active',
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'permissions' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
