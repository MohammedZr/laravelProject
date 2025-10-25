<?php

// database/migrations/2025_10_24_234119_create_cart_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();          // carts.id
            $table->foreignId('drug_id')->constrained()->cascadeOnDelete();          // drugs.id
            $table->foreignId('company_id')->constrained('users')->cascadeOnDelete();// users.id (شركة)
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['cart_id','drug_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('cart_items');
    }
};

