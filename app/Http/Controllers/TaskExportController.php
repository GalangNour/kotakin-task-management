<?php

namespace App\Http\Controllers;

use App\Exports\TasksExport;
use App\Http\Requests\StoreTaskExportRequest;
use App\Jobs\ExportTasksJob;
use App\Jobs\ImportTasksJob;
use App\Models\Project;
use App\Models\TaskExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskExportController extends Controller
{
    public function create(Project $project): Response
    {
        return Inertia::render('Tasks/ImportExport', [
            'project' => $project,
            'systemFields' => ImportTasksJob::SYSTEM_FIELDS,
            'availableColumns' => TasksExport::AVAILABLE_COLUMNS,
            'exportHistory' => TaskExport::where('project_id', $project->id)->latest()->limit(10)->get(),
            'activeTab' => 'export',
            'activeImport' => null,
            'activeExport' => null,
        ]);
    }

    public function store(StoreTaskExportRequest $request, Project $project): RedirectResponse
    {
        $taskExport = TaskExport::create([
            'user_id' => $request->user()->id,
            'project_id' => $project->id,
            'columns' => $request->validated('columns'),
            'status' => 'pending',
        ]);

        ExportTasksJob::dispatch($taskExport);

        return redirect()->route('task-exports.show', $taskExport);
    }

    public function show(TaskExport $taskExport): Response
    {
        $project = $taskExport->project;

        return Inertia::render('Tasks/ImportExport', [
            'project' => $project,
            'systemFields' => ImportTasksJob::SYSTEM_FIELDS,
            'availableColumns' => TasksExport::AVAILABLE_COLUMNS,
            'exportHistory' => TaskExport::where('project_id', $project->id)->latest()->limit(10)->get(),
            'activeTab' => 'export',
            'activeImport' => null,
            'activeExport' => $taskExport,
        ]);
    }

    public function status(TaskExport $taskExport): JsonResponse
    {
        return response()->json([
            'status' => $taskExport->status,
            'error' => $taskExport->error,
            'download_url' => $taskExport->status === 'completed'
                ? route('task-exports.download', $taskExport)
                : null,
        ]);
    }

    public function download(TaskExport $taskExport): StreamedResponse
    {
        abort_unless($taskExport->status === 'completed' && $taskExport->file_path, 404);

        return Storage::disk('public')->download($taskExport->file_path, 'tasks-export.xlsx');
    }
}
