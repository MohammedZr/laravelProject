<?php

// database/migrations/2025_10_25_000001_add_company_id_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('users', function (Blueprint $table) {
      $table->foreignId('company_id')->nullable()->constrained('users')->nullOnDelete();
      // الفكرة: مندوب (role=delivery) يتبع شركة (user role=company)
    });
  }
  public function down(): void {
    Schema::table('users', function (Blueprint $table) {
      $table->dropConstrainedForeignId('company_id');
    });
  }
};
