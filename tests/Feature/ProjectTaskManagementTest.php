<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTaskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_create_project_and_task_and_project_progress_updates(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Project Test', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);

        $this->actingAs($admin)->post('/projects', [
            'name' => 'Implementasi CRM',
            'client_id' => $client->id,
            'status' => 'in_progress',
            'billing_type' => 'fixed',
            'budget' => 1500000,
            'progress' => 0,
            'member_ids' => [$admin->id],
        ])->assertRedirect();

        $project = Project::query()->where('name', 'Implementasi CRM')->firstOrFail();
        $this->assertDatabaseHas('project_members', ['project_id' => $project->id, 'user_id' => $admin->id]);

        $this->actingAs($admin)->post("/projects/{$project->id}/milestones", [
            'title' => 'Tahap awal',
            'due_date' => now()->addWeek()->toDateString(),
            'description' => 'Konfigurasi fondasi project.',
        ])->assertRedirect();
        $this->assertDatabaseHas('milestones', ['project_id' => $project->id, 'title' => 'Tahap awal']);

        $this->actingAs($admin)->post('/tasks', [
            'name' => 'Konfigurasi dasar',
            'project_id' => $project->id,
            'priority' => 'high',
            'status' => 'todo',
            'assignee_ids' => [$admin->id],
        ])->assertRedirect();

        $task = Task::query()->where('name', 'Konfigurasi dasar')->firstOrFail();
        $this->assertSame(Project::class, $task->related_type);
        $this->assertSame($project->id, $task->related_id);

        $this->actingAs($admin)->put("/tasks/{$task->id}", [
            'name' => 'Konfigurasi dasar',
            'project_id' => $project->id,
            'priority' => 'high',
            'status' => 'completed',
            'assignee_ids' => [$admin->id],
        ])->assertRedirect();

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'progress' => 100]);
    }

    public function test_super_admin_can_collaborate_on_a_task(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $task = Task::create(['name' => 'Task kolaborasi', 'priority' => 'medium', 'status' => 'todo', 'created_by' => $admin->id]);
        $task->assignees()->attach($admin->id);

        $this->actingAs($admin)->post("/tasks/{$task->id}/checklist-items", ['description' => 'Cek requirement'])->assertRedirect();
        $checklistItem = $task->checklistItems()->firstOrFail();
        $this->actingAs($admin)->patch("/tasks/{$task->id}/checklist-items/{$checklistItem->id}/toggle")->assertRedirect();
        $this->assertDatabaseHas('task_checklist_items', ['id' => $checklistItem->id, 'is_finished' => 1]);

        $this->actingAs($admin)->post("/tasks/{$task->id}/comments", ['comment' => 'Diskusi dimulai.'])->assertRedirect();
        $this->assertDatabaseHas('task_comments', ['task_id' => $task->id, 'user_id' => $admin->id, 'comment' => 'Diskusi dimulai.']);

        $this->actingAs($admin)->post("/tasks/{$task->id}/time-logs", ['start_time' => now()->subHour()->format('Y-m-d H:i:s'), 'end_time' => now()->format('Y-m-d H:i:s'), 'note' => 'Pengerjaan awal'])->assertRedirect();
        $this->assertDatabaseHas('task_time_logs', ['task_id' => $task->id, 'user_id' => $admin->id, 'note' => 'Pengerjaan awal']);
    }

    public function test_super_admin_can_attach_a_task_to_a_lead(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $lead = Lead::create([
            'name' => 'Lead untuk Task',
            'source_id' => LeadSource::query()->firstOrFail()->id,
            'status_id' => LeadStatus::query()->firstOrFail()->id,
            'assigned_to' => $admin->id,
        ]);

        $this->actingAs($admin)->post('/tasks', [
            'name' => 'Follow-up lead',
            'related_type' => 'lead',
            'related_id' => $lead->id,
            'priority' => 'medium',
            'status' => 'todo',
            'assignee_ids' => [$admin->id],
        ])->assertRedirect();

        $this->assertDatabaseHas('tasks', ['name' => 'Follow-up lead', 'related_type' => Lead::class, 'related_id' => $lead->id]);
    }
}
