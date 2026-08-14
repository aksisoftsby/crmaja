<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 20)->default('#6366F1');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_statuses');
    }
};
