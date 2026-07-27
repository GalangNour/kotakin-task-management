<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(Request $request, Project $project): Response
    {
        $tasks = $project->tasks()
            ->with('assignee')
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return Inertia::render('Tasks/Index', [
            'project' => $project,
            'tasks' => $tasks,
            'users' => User::query()->where('is_active', true)->get(['id', 'name']),
            'categories' => ProjectCategory::all(['id', 'name']),
            'filters' => $request->only('search'),
        ]);
    }

    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        $project->tasks()->create($request->validated());

        return redirect()->route('projects.tasks.index', $project)->with('success', 'Task berhasil dibuat.');
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()->route('projects.tasks.index', $task->project_id)->with('success', 'Task berhasil diupdate.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->project_id;

        $task->delete();

        return redirect()->route('projects.tasks.index', $projectId)->with('success', 'Task berhasil dihapus.');
    }

    public function modalData(Task $task): JsonResponse
    {
        return response()->json([
            'task' => $task->load(['assignee', 'attachments.uploader', 'comments.author']),
            'audits' => $task->auditHistory()->with('user:id,name')->get(),
        ]);
    }
}
