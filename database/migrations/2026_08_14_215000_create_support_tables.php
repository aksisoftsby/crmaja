<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_departments', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('email_piping_address')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('number')->unique();
            $table->string('subject');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('ticket_departments')->nullOnDelete();
            $table->string('priority')->default('medium')->index();
            $table->string('status')->default('open')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source')->default('manual')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('ticket_replies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->string('user_type');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('message');
            $table->boolean('is_internal_note')->default(false);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['user_type', 'user_id']);
        });

        Schema::create('kb_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('kb_categories')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('kb_articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('kb_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_client_only')->default(false)->index();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kb_articles');
        Schema::dropIfExists('kb_categories');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_departments');
    }
};
