<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable()->index();
            $table->date('deadline')->nullable()->index();
            $table->string('status')->default('not_started')->index();
            $table->string('billing_type')->default('not_billed')->index();
            $table->decimal('budget', 15, 2)->default(0);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('project_members', function (Blueprint $table): void {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['project_id', 'user_id']);
        });

        Schema::create('milestones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->date('due_date')->nullable()->index();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium')->index();
            $table->string('status')->default('todo')->index();
            $table->date('start_date')->nullable()->index();
            $table->date('due_date')->nullable()->index();
            $table->foreignId('milestone_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('related');
            $table->boolean('is_recurring')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('task_assignees', function (Blueprint $table): void {
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['task_id', 'user_id']);
        });

        Schema::create('task_checklist_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->boolean('is_finished')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('task_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('comment');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('task_time_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('start_time')->index();
            $table->dateTime('end_time')->nullable()->index();
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_time_logs');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('task_checklist_items');
        Schema::dropIfExists('task_assignees');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('projects');
    }
};
