<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->index();
            $table->string('company_name')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->foreignId('status_id')->constrained('lead_statuses')->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('lead_value', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_converted')->default(false)->index();
            $table->foreignId('converted_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status_id', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
