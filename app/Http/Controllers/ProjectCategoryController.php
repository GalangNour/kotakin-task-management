<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectCategoryRequest;
use App\Http\Requests\UpdateProjectCategoryRequest;
use App\Models\ProjectCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = ProjectCategory::query()
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ProjectCategories/Index', [
            'categories' => $categories,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ProjectCategories/Create');
    }

    public function store(StoreProjectCategoryRequest $request): RedirectResponse
    {
        ProjectCategory::create($request->validated());

        return redirect()->route('project-categories.index')->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(ProjectCategory $projectCategory): Response
    {
        return Inertia::render('ProjectCategories/Edit', [
            'category' => $projectCategory,
        ]);
    }

    public function update(UpdateProjectCategoryRequest $request, ProjectCategory $projectCategory): RedirectResponse
    {
        $projectCategory->update($request->validated());

        return redirect()->route('project-categories.index')->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy(ProjectCategory $projectCategory): RedirectResponse
    {
        $projectCategory->delete();

        return redirect()->route('project-categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
