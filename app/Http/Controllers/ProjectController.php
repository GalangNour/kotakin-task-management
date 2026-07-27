<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $projects = Project::query()
            ->with(['category', 'creator'])
            ->withCount([
                'tasks',
                'tasks as done_tasks_count' => fn ($query) => $query->where('status', 'done'),
            ])
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'categories' => ProjectCategory::all(['id', 'name']),
            'filters' => $request->only('search'),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project berhasil dibuat.');
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('Projects/Edit', [
            'project' => $project->load(['category', 'creator'])->loadCount('tasks'),
            'categories' => ProjectCategory::all(['id', 'name']),
            'audits' => $this->projectActivity($project),
        ]);
    }

    private function projectActivity(Project $project): array
    {
        $projectAudits = $project->auditHistory()->with('user:id,name')->get()
            ->map(fn (Audit $audit) => [
                ...$audit->toArray(),
                'subject_type' => 'project',
                'subject_label' => null,
            ]);

        $taskIds = $project->tasks()->withTrashed()->pluck('id');

        $taskAudits = Audit::where('auditable_type', Task::class)
            ->whereIn('auditable_id', $taskIds)
            ->with(['user:id,name', 'auditable' => fn ($q) => $q->withTrashed()])
            ->get()
            ->map(fn (Audit $audit) => [
                ...$audit->toArray(),
                'subject_type' => 'task',
                'subject_label' => $audit->new_values['title'] ?? $audit->old_values['title']
                    ?? $audit->auditable?->title ?? null,
            ]);

        return $projectAudits->concat($taskAudits)
            ->sortByDesc('created_at')
            ->take(50)
            ->values()
            ->all();
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('projects.index')->with('success', 'Project berhasil diupdate.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project berhasil dihapus.');
    }

    public function trashed(Request $request): Response
    {
        $projects = Project::onlyTrashed()
            ->with(['category', 'creator'])
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest('deleted_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Projects/Trashed', [
            'projects' => $projects,
            'filters' => $request->only('search'),
        ]);
    }

    public function trashedShow(string $project): Response
    {
        $project = Project::onlyTrashed()->with(['category', 'creator'])->findOrFail($project);

        return Inertia::render('Projects/TrashedShow', [
            'project' => $project,
            'audits' => $this->projectActivity($project),
        ]);
    }
}
