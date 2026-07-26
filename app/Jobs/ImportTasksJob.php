<?php

namespace App\Jobs;

use App\Models\TaskImport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ImportTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const SYSTEM_FIELDS = [
        'title' => 'Judul (wajib)',
        'description' => 'Deskripsi',
        'status' => 'Status (todo/in_progress/done)',
        'priority' => 'Prioritas (low/medium/high)',
        'due_date' => 'Due Date (YYYY-MM-DD)',
        'assignee_email' => 'Email Assignee',
    ];

    public function __construct(
        public readonly TaskImport $taskImport,
    ) {
    }

    public function handle(): void
    {
        $this->taskImport->update(['status' => 'processing']);

        try {
            $fullPath = Storage::disk('local')->path($this->taskImport->file_path);
            $spreadsheet = IOFactory::load($fullPath);
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

            $headerRow = array_map('strval', array_shift($rows) ?? []);
            $mapping = $this->taskImport->column_mapping;

            $columnIndex = fn (string $header) => array_search($header, $headerRow, true);

            $imported = 0;
            $failed = 0;
            $errors = [];

            foreach ($rows as $rowNumber => $row) {
                if (empty(array_filter($row, fn ($v) => $v !== null && $v !== ''))) {
                    continue;
                }

                $value = function (string $field) use ($mapping, $row, $columnIndex) {
                    $header = $mapping[$field] ?? null;
                    if (! $header) {
                        return null;
                    }
                    $index = $columnIndex($header);

                    return $index === false ? null : ($row[$index] ?? null);
                };

                $title = trim((string) $value('title'));

                if ($title === '') {
                    $failed++;
                    $errors[] = "Baris ".($rowNumber + 2).": judul kosong, dilewati.";

                    continue;
                }

                $status = strtolower(trim((string) $value('status')));
                $status = in_array($status, ['todo', 'in_progress', 'done'], true) ? $status : 'todo';

                $priority = strtolower(trim((string) $value('priority')));
                $priority = in_array($priority, ['low', 'medium', 'high'], true) ? $priority : 'medium';

                $assigneeEmail = trim((string) $value('assignee_email'));
                $assignee = $assigneeEmail !== '' ? User::where('email', $assigneeEmail)->first() : null;

                $dueDateRaw = $value('due_date');
                $dueDate = null;
                if ($dueDateRaw) {
                    try {
                        $dueDate = \Illuminate\Support\Carbon::parse((string) $dueDateRaw);
                    } catch (Throwable) {
                        $dueDate = null;
                    }
                }

                $this->taskImport->project->tasks()->create([
                    'title' => $title,
                    'description' => $value('description'),
                    'status' => $status,
                    'priority' => $priority,
                    'due_date' => $dueDate,
                    'assigned_to' => $assignee?->id,
                ]);

                $imported++;
            }

            $this->taskImport->update([
                'status' => 'completed',
                'total_rows' => count($rows),
                'imported_rows' => $imported,
                'failed_rows' => $failed,
                'errors' => $errors ?: null,
            ]);
        } catch (Throwable $e) {
            $this->taskImport->update([
                'status' => 'failed',
                'errors' => [$e->getMessage()],
            ]);
        } finally {
            Storage::disk('local')->delete($this->taskImport->file_path);
        }
    }
}
