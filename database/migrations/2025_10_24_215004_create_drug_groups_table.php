<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_drug_groups_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('drug_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // الشركة المالكة للمجموعة
            $table->string('title')->nullable(); // اسم اختياري للمجموعة (مثل: شحنة مايو)
            $table->enum('status', ['draft','submitted','published','archived'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['user_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_groups');
    }
};
