<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->decimal('rate', 15, 2)->default(0);
            $table->string('unit', 50)->default('unit');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
