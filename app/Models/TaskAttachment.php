<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAttachment extends Model
{
    use HasAudit, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'task_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_size',
        'uploaded_at',
        'is_verified',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'is_verified' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
