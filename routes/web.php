<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskAttachmentController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskExportController;
use App\Http\Controllers\TaskImportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('roles', RoleController::class)->except(['show', 'create', 'edit']);
    Route::resource('project-categories', ProjectCategoryController::class)->except('show');
    Route::get('projects/trashed', [ProjectController::class, 'trashed'])->name('projects.trashed');
    Route::get('projects/trashed/{project}', [ProjectController::class, 'trashedShow'])->name('projects.trashed.show');
    Route::resource('projects', ProjectController::class)->except(['show', 'create']);
    Route::resource('projects.tasks', TaskController::class)->shallow()->except(['show', 'create', 'edit']);
    Route::get('tasks/{task}/modal-data', [TaskController::class, 'modalData'])->name('tasks.modal-data');
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
    Route::post('tasks/{task}/attachments', [TaskAttachmentController::class, 'store'])->name('tasks.attachments.store');
    Route::patch('attachments/{attachment}', [TaskAttachmentController::class, 'update'])->name('attachments.update');
    Route::delete('attachments/{attachment}', [TaskAttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::get('projects/{project}/tasks/export', [TaskExportController::class, 'create'])->name('projects.tasks.export.create');
    Route::post('projects/{project}/tasks/export', [TaskExportController::class, 'store'])->name('projects.tasks.export.store');
    Route::get('task-exports/{taskExport}', [TaskExportController::class, 'show'])->name('task-exports.show');
    Route::get('task-exports/{taskExport}/status', [TaskExportController::class, 'status'])->name('task-exports.status');
    Route::get('task-exports/{taskExport}/download', [TaskExportController::class, 'download'])->name('task-exports.download');

    Route::get('projects/{project}/tasks/import', [TaskImportController::class, 'create'])->name('projects.tasks.import.create');
    Route::post('tasks/import/headers', [TaskImportController::class, 'headers'])->name('tasks.import.headers');
    Route::post('projects/{project}/tasks/import', [TaskImportController::class, 'store'])->name('projects.tasks.import.store');
    Route::get('task-imports/{taskImport}', [TaskImportController::class, 'show'])->name('task-imports.show');
    Route::get('task-imports/{taskImport}/status', [TaskImportController::class, 'status'])->name('task-imports.status');
});

Route::middleware(['auth', 'role:Administrator'])->group(function () {
    Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
});

require __DIR__.'/auth.php';
