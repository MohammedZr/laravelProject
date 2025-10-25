<?php

// database/migrations/2025_10_24_215055_create_drugs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('drug_group_id')->constrained()->cascadeOnDelete();

            // ↓↓↓ قلّل الأطوال لتفادي 3072 بايت
            $table->string('name', 150);
            $table->string('generic_name', 150)->nullable();
            $table->string('dosage_form', 60)->nullable();
            $table->string('strength', 60)->nullable();

            $table->unsignedInteger('pack_size')->default(1);
            $table->string('unit', 30)->nullable();

            $table->string('sku', 190)->nullable()->index();
            $table->string('barcode', 32)->nullable()->unique();
            $table->decimal('price', 10, 2)->default(0)->index();
            $table->unsignedInteger('stock')->default(0)->index();

            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();

            // ↓↓↓ الفهرس المركّب بعد تقليل الأطوال
            $table->index(['name','generic_name','dosage_form','strength'], 'drugs_search_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drugs');
    }
};
