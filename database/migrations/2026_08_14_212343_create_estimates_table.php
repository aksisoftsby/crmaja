<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('number')->unique();
            $table->date('date');
            $table->date('expiry_date')->nullable();
            $table->string('status')->default('draft')->index();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
