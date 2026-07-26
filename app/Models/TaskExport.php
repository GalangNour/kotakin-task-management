<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskExport extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'project_id',
        'columns',
        'status',
        'file_path',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'columns' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
