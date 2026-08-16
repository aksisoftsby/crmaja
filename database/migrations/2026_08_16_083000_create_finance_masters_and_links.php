<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->string('symbol', 10);
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->decimal('exchange_rate', 18, 8)->default(1);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('taxes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 8, 4)->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('payment_methods', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $currencyId = DB::table('currencies')->insertGetId([
            'code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'decimal_places' => 0,
            'exchange_rate' => 1, 'is_default' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('taxes')->insert(['name' => 'PPN', 'rate' => 11, 'is_default' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('payment_methods')->insert([
            ['name' => 'Bank Transfer', 'code' => 'bank-transfer', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cash', 'code' => 'cash', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('clients', function (Blueprint $table): void {
            $table->foreignId('currency_id')->nullable()->after('currency')->constrained('currencies')->nullOnDelete();
        });
        Schema::table('invoices', function (Blueprint $table): void {
            $table->foreignId('currency_id')->nullable()->after('client_id')->constrained('currencies')->nullOnDelete();
            $table->foreignId('tax_id')->nullable()->after('discount')->constrained('taxes')->nullOnDelete();
            $table->decimal('tax_rate', 8, 4)->default(0)->after('tax_id');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
        });
        Schema::table('payments', function (Blueprint $table): void {
            $table->foreignId('payment_method_id')->nullable()->after('invoice_id')->constrained('payment_methods')->nullOnDelete();
        });
        DB::table('clients')->whereNull('currency_id')->update(['currency_id' => $currencyId]);
        DB::table('invoices')->whereNull('currency_id')->update(['currency_id' => $currencyId]);
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('payment_method_id');
        });
        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('currency_id');
            $table->dropConstrainedForeignId('tax_id');
            $table->dropColumn(['tax_rate', 'tax_amount']);
        });
        Schema::table('clients', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('currency_id');
        });
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('currencies');
    }
};
