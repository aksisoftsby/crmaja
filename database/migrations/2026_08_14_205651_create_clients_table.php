<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name')->index();
            $table->string('vat_number')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable()->index();
            $table->string('state')->nullable();
            $table->string('zip', 32)->nullable();
            $table->string('country')->nullable()->index();
            $table->string('currency', 3)->default('IDR');
            $table->foreignId('customer_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
