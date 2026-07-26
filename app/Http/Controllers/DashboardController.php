<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAttachment;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('is_active', true)->count();
        $totalTasks = Task::count();
        $doneTasks = Task::where('status', 'done')->count();
        $overdueCount = Task::where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();

        $stats = [
            [
                'label' => 'Total Project',
                'value' => (string) $totalProjects,
                'sub' => "{$activeProjects} aktif",
                'subTone' => 'success',
            ],
            [
                'label' => 'Total Task',
                'value' => (string) $totalTasks,
                'sub' => ($totalTasks - $doneTasks).' belum selesai',
                'subTone' => 'neutral',
            ],
            [
                'label' => 'Task Selesai',
                'value' => (string) $doneTasks,
                'sub' => $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100).'% dari total' : '0% dari total',
                'subTone' => 'success',
            ],
            [
                'label' => 'Task Overdue',
                'value' => (string) $overdueCount,
                'sub' => 'perlu perhatian',
                'subTone' => 'danger',
            ],
        ];

        $progressPerProject = Project::query()
            ->whereHas('tasks')
            ->withCount([
                'tasks',
                'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done'),
            ])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($project) => [
                'name' => $project->name,
                'doneCount' => $project->done_tasks_count,
                'totalCount' => $project->tasks_count,
                'percentage' => round(($project->done_tasks_count / $project->tasks_count) * 100),
            ]);

        $attentionTasks = Task::query()
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays(3))
            ->with(['project', 'assignee'])
            ->orderBy('due_date')
            ->limit(8)
            ->get()
            ->map(function (Task $task) {
                $overdue = $task->due_date->isPast();

                return [
                    'title' => $task->title,
                    'projectName' => $task->project?->name,
                    'assignee' => $task->assignee?->name ?? 'Belum ditugaskan',
                    'dueLabel' => $overdue
                        ? 'Terlambat '.(int) floor($task->due_date->diffInDays(now())).' hari'
                        : $task->due_date->diffForHumans(),
                    'overdue' => $overdue,
                ];
            });

        $recentActivity = Audit::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(fn (Audit $audit) => [
                'text' => $this->describeAudit($audit),
                'time' => $audit->created_at->diffForHumans(),
                'event' => $audit->event,
            ]);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'progressPerProject' => $progressPerProject,
            'attentionTasks' => $attentionTasks,
            'recentActivity' => $recentActivity,
        ]);
    }

    private function describeAudit(Audit $audit): string
    {
        $userName = $audit->user?->name ?? 'System';
        $modelLabel = match ($audit->auditable_type) {
            Task::class => 'task',
            Project::class => 'project',
            TaskAttachment::class => 'file',
            default => 'data',
        };

        $subject = $audit->auditable?->title
            ?? $audit->auditable?->name
            ?? $audit->auditable?->file_name
            ?? $audit->new_values['title'] ?? $audit->new_values['name'] ?? $audit->new_values['file_name']
            ?? $audit->old_values['title'] ?? $audit->old_values['name'] ?? $audit->old_values['file_name']
            ?? $modelLabel;

        return match (true) {
            $audit->event === 'created' && $modelLabel === 'task' => "{$userName} menambahkan task \"{$subject}\"",
            $audit->event === 'created' && $modelLabel === 'project' => "{$userName} membuat project baru \"{$subject}\"",
            $audit->event === 'created' => "{$userName} menambahkan {$modelLabel} \"{$subject}\"",
            $audit->event === 'deleted' => "{$userName} menghapus {$modelLabel} \"{$subject}\"",
            $audit->event === 'restored' => "{$userName} memulihkan {$modelLabel} \"{$subject}\"",
            $audit->event === 'updated' && $modelLabel === 'task' && ($audit->new_values['status'] ?? null) === 'done'
                => "{$userName} menyelesaikan task \"{$subject}\"",
            $audit->event === 'updated' && $modelLabel === 'project' && array_key_exists('is_active', $audit->new_values ?? [])
                => "{$userName} mengubah status project \"{$subject}\" menjadi ".(($audit->new_values['is_active'] ?? null) ? 'aktif' : 'nonaktif'),
            default => "{$userName} memperbarui {$modelLabel} \"{$subject}\"",
        };
    }
}
