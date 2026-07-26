<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskImportHeadersRequest;
use App\Http\Requests\StoreTaskImportRequest;
use App\Jobs\ImportTasksJob;
use App\Models\Project;
use App\Models\TaskImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TaskImportController extends Controller
{
    public function create(Project $project): Response
    {
        return Inertia::render('Tasks/Import', [
            'project' => $project,
            'systemFields' => ImportTasksJob::SYSTEM_FIELDS,
        ]);
    }

    public function headers(StoreTaskImportHeadersRequest $request): JsonResponse
    {
        $path = $request->file('file')->store('imports', 'local');
        $fullPath = Storage::disk('local')->path($path);

        $spreadsheet = IOFactory::load($fullPath);
        $firstRow = $spreadsheet->getActiveSheet()->rangeToArray('A1:'.$spreadsheet->getActiveSheet()->getHighestColumn().'1')[0];

        return response()->json([
            'file_path' => $path,
            'original_file_name' => $request->file('file')->getClientOriginalName(),
            'headers' => array_values(array_filter(array_map('strval', $firstRow), fn ($h) => $h !== '')),
        ]);
    }

    public function store(StoreTaskImportRequest $request, Project $project): RedirectResponse
    {
        $filePath = $request->validated('file_path');

        abort_unless(
            str_starts_with($filePath, 'imports/') && Storage::disk('local')->exists($filePath),
            422,
            'File tidak valid atau sudah kedaluwarsa.',
        );

        $taskImport = TaskImport::create([
            'user_id' => $request->user()->id,
            'project_id' => $project->id,
            'original_file_name' => $request->validated('original_file_name'),
            'file_path' => $filePath,
            'column_mapping' => $request->validated('mapping'),
            'status' => 'pending',
        ]);

        ImportTasksJob::dispatch($taskImport);

        return redirect()->route('task-imports.show', $taskImport);
    }

    public function show(TaskImport $taskImport): Response
    {
        return Inertia::render('Tasks/ImportStatus', [
            'import' => $taskImport,
        ]);
    }

    public function status(TaskImport $taskImport): JsonResponse
    {
        return response()->json([
            'status' => $taskImport->status,
            'total_rows' => $taskImport->total_rows,
            'imported_rows' => $taskImport->imported_rows,
            'failed_rows' => $taskImport->failed_rows,
            'errors' => $taskImport->errors,
            'tasks_url' => route('projects.tasks.index', $taskImport->project_id),
        ]);
    }
}
