<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Task::query()->with(['related', 'milestone', 'assignees'])->latest();
        if (! $request->user()->can('tasks.view_all')) {
            $tasks->where(function ($query) use ($request): void {
                $query->where('created_by', $request->user()->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->whereKey($request->user()->id));
            });
        }

        return view('tasks.index', ['tasks' => $tasks->paginate(20)]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Task::class);

        return view('tasks.create', $this->formOptions($request));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = $this->persist(new Task, $request->validated(), $request->user()->id);

        return to_route('tasks.show', $task)->with('status', 'Task berhasil dibuat.');
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);
        $task->load(['related', 'milestone.project', 'creator', 'assignees', 'checklistItems', 'comments.user', 'timeLogs.user']);

        return view('tasks.show', compact('task'));
    }

    public function edit(Request $request, Task $task): View
    {
        $this->authorize('update', $task);
        $task->load('assignees');

        return view('tasks.edit', array_merge(['task' => $task], $this->formOptions($request)));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task = $this->persist($task, $request->validated(), $task->created_by);

        return to_route('tasks.show', $task)->with('status', 'Task berhasil diperbarui.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);
        $project = $task->related instanceof Project ? $task->related : null;
        $task->delete();
        if ($project) {
            $this->syncProjectProgress($project);
        }

        return to_route('tasks.index')->with('status', 'Task telah diarsipkan.');
    }

    private function persist(Task $task, array $data, int $creatorId): Task
    {
        $assigneeIds = $data['assignee_ids'] ?? [];
        $projectId = $data['project_id'] ?? null;
        $relationType = $data['related_type'] ?? null;
        $relationId = $data['related_id'] ?? null;
        $previousProject = $task->exists && $task->related instanceof Project ? $task->related : null;
        unset($data['assignee_ids'], $data['project_id'], $data['related_type'], $data['related_id']);

        if (! $projectId && ! empty($data['milestone_id'])) {
            $projectId = Milestone::query()->find($data['milestone_id'])?->project_id;
        }

        if ($projectId) {
            $data['related_type'] = Project::class;
            $data['related_id'] = $projectId;
        } elseif ($relationType && $relationId) {
            $relatedClasses = ['project' => Project::class, 'lead' => Lead::class, 'client' => Client::class];
            $relatedClass = $relatedClasses[$relationType];
            abort_unless($relatedClass::query()->whereKey($relationId)->exists(), 422);
            $data['related_type'] = $relatedClass;
            $data['related_id'] = $relationId;
        } else {
            $data['related_type'] = null;
            $data['related_id'] = null;
        }

        if (! $task->exists) {
            $data['created_by'] = $creatorId;
        }

        $task->fill($data);
        $task->save();
        $task->assignees()->sync($assigneeIds);
        if ($previousProject) {
            $this->syncProjectProgress($previousProject);
        }
        if ($task->related instanceof Project) {
            $this->syncProjectProgress($task->related);
        }

        return $task;
    }

    private function syncProjectProgress(Project $project): void
    {
        $total = $project->tasks()->count();
        $completed = $project->tasks()->where('status', 'completed')->count();
        $project->update(['progress' => $total === 0 ? 0 : (int) round(($completed / $total) * 100)]);
    }

    private function formOptions(Request $request): array
    {
        $projects = Project::query()->orderBy('name');
        if (! $request->user()->can('projects.view_all')) {
            $projects->where(function ($query) use ($request): void {
                $query->where('created_by', $request->user()->id)
                    ->orWhereHas('members', fn ($members) => $members->whereKey($request->user()->id));
            });
        }

        return [
            'projects' => $projects->get(),
            'clients' => Client::query()->active()->orderBy('company_name')->get(),
            'leads' => Lead::query()->orderBy('name')->get(),
            'staff' => User::query()->orderBy('name')->get(),
            'milestones' => Milestone::query()->with('project')->orderBy('due_date')->get(),
        ];
    }
}
