<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskImport extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'project_id',
        'original_file_name',
        'file_path',
        'column_mapping',
        'status',
        'total_rows',
        'imported_rows',
        'failed_rows',
        'errors',
    ];

    protected function casts(): array
    {
        return [
            'column_mapping' => 'array',
            'errors' => 'array',
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
