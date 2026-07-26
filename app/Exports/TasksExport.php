<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TasksExport implements FromCollection, WithHeadings, WithMapping
{
    public const AVAILABLE_COLUMNS = [
        'title' => 'Judul',
        'description' => 'Deskripsi',
        'status' => 'Status',
        'priority' => 'Prioritas',
        'due_date' => 'Due Date',
        'is_completed' => 'Selesai',
        'project_name' => 'Project',
        'assignee_name' => 'Assignee',
        'assignee_email' => 'Email Assignee',
        'created_at' => 'Dibuat Pada',
    ];

    public function __construct(
        private readonly array $columns,
        private readonly string $projectId,
    ) {
    }

    public function collection(): Collection
    {
        return Task::query()
            ->with(['project', 'assignee'])
            ->where('project_id', $this->projectId)
            ->get();
    }

    public function headings(): array
    {
        return array_map(
            fn (string $key) => self::AVAILABLE_COLUMNS[$key] ?? $key,
            $this->columns,
        );
    }

    public function map($task): array
    {
        return array_map(function (string $key) use ($task) {
            return match ($key) {
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date?->format('Y-m-d'),
                'is_completed' => $task->is_completed ? 'Ya' : 'Tidak',
                'project_name' => $task->project?->name,
                'assignee_name' => $task->assignee?->name,
                'assignee_email' => $task->assignee?->email,
                'created_at' => $task->created_at?->format('Y-m-d H:i'),
                default => '',
            };
        }, $this->columns);
    }
}
