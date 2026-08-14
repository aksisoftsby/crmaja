<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilestoneRequest;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function store(StoreMilestoneRequest $request, Project $project): RedirectResponse
    {
        $project->milestones()->create(array_merge($request->validated(), [
            'sort_order' => ((int) $project->milestones()->max('sort_order')) + 1,
        ]));

        return to_route('projects.show', $project)->with('status', 'Milestone berhasil ditambahkan.');
    }

    public function destroy(Request $request, Project $project, Milestone $milestone): RedirectResponse
    {
        $this->authorize('update', $project);
        abort_unless($milestone->project_id === $project->id, 404);
        $milestone->delete();

        return to_route('projects.show', $project)->with('status', 'Milestone telah diarsipkan.');
    }
}
