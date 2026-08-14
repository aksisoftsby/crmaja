<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description');
            $table->decimal('qty', 12, 2)->default(1);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_items');
    }
};
