<?php

namespace App\Jobs;

use App\Exports\TasksExport;
use App\Models\TaskExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ExportTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly TaskExport $taskExport,
    ) {
    }

    public function handle(): void
    {
        $this->taskExport->update(['status' => 'processing']);

        try {
            $fileName = "exports/tasks-{$this->taskExport->id}.xlsx";

            Excel::store(
                new TasksExport($this->taskExport->columns, $this->taskExport->project_id),
                $fileName,
                'public',
            );

            $this->taskExport->update([
                'status' => 'completed',
                'file_path' => $fileName,
            ]);
        } catch (Throwable $e) {
            $this->taskExport->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
