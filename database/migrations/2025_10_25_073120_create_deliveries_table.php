<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            // الطلب الذي سيتم توصيله
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // الشركة المالكة للطلب (user_id للشركة)
            $table->foreignId('company_id')->constrained('users')->cascadeOnDelete();
            // المندوب المسؤول
            $table->foreignId('delivery_user_id')->constrained('users')->cascadeOnDelete();

            // حالات المنذوب: assigned -> accepted -> picked_up -> delivered / cancelled
            $table->string('status', 32)->default('assigned');
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->index(['delivery_user_id', 'status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('deliveries');
    }
};
