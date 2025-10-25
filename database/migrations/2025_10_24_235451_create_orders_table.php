<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();         // الصيدلية
            $table->foreignId('company_id')->constrained('users')->cascadeOnDelete(); // الشركة
            $table->enum('status', ['pending','confirmed','shipped','delivered','cancelled'])->default('pending')->index();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
