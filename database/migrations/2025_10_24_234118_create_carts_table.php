<?php

// database/migrations/2025_10_24_234118_create_carts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED (يتوافق مع users.id)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // يشير إلى users.id
            $table->enum('status', ['open','checked_out'])->default('open')->index();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('carts');
    }
};
