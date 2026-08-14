<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::query()->with(['client', 'members'])->latest();
        if (! $request->user()->can('projects.view_all')) {
            $projects->where(function ($query) use ($request): void {
                $query->where('created_by', $request->user()->id)
                    ->orWhereHas('members', fn ($members) => $members->whereKey($request->user()->id));
            });
        }

        return view('projects.index', ['projects' => $projects->paginate(15)]);
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('projects.create', $this->formOptions());
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $memberIds = $data['member_ids'] ?? [];
        unset($data['member_ids']);
        $data['created_by'] = $request->user()->id;
        $project = Project::create($data);
        $project->members()->sync(array_unique([...$memberIds, $request->user()->id]));

        return to_route('projects.show', $project)->with('status', 'Project berhasil dibuat.');
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);
        $project->load(['client', 'members', 'milestones.tasks.assignees', 'tasks.assignees', 'tasks.checklistItems', 'tasks.timeLogs']);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);
        $project->load('members');

        return view('projects.edit', array_merge(['project' => $project], $this->formOptions()));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();
        $memberIds = $data['member_ids'] ?? [];
        unset($data['member_ids']);
        $project->update($data);
        $project->members()->sync(array_unique([...$memberIds, $project->created_by]));

        return to_route('projects.show', $project)->with('status', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);
        $project->delete();

        return to_route('projects.index')->with('status', 'Project telah diarsipkan.');
    }

    private function formOptions(): array
    {
        return [
            'clients' => Client::query()->active()->orderBy('company_name')->get(),
            'staff' => User::query()->orderBy('name')->get(),
        ];
    }
}
