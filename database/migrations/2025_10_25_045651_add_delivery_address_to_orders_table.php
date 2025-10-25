<?php

// database/migrations/2025_10_25_000003_create_deliveries_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('deliveries', function (Blueprint $table) {
      $table->id();
      $table->foreignId('order_id')->constrained()->cascadeOnDelete()->unique();
      $table->foreignId('courier_id')->constrained('users')->cascadeOnDelete(); // المندوب
      $table->enum('status', ['assigned','picked_up','delivering','delivered','failed'])->default('assigned');
      $table->timestamp('assigned_at')->nullable();
      $table->timestamp('picked_up_at')->nullable();
      $table->timestamp('delivered_at')->nullable();
      $table->string('failed_reason')->nullable();
      $table->timestamps();
    });
  }
  public function down(): void {
    Schema::dropIfExists('deliveries');
  }
};
