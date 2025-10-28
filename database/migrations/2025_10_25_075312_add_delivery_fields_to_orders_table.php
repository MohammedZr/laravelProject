<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_address_line')) {
                $table->string('delivery_address_line', 255)->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'delivery_city')) {
                $table->string('delivery_city', 120)->nullable()->after('delivery_address_line');
            }
            if (!Schema::hasColumn('orders', 'delivery_phone')) {
                $table->string('delivery_phone', 32)->nullable()->after('delivery_city');
            }
            if (!Schema::hasColumn('orders', 'delivery_lat')) {
                $table->decimal('delivery_lat', 10, 7)->nullable()->after('delivery_phone');
            }
            if (!Schema::hasColumn('orders', 'delivery_lng')) {
                $table->decimal('delivery_lng', 10, 7)->nullable()->after('delivery_lat');
            }

            // فهرس للاستعلام السريع حسب الإحداثيات/المدينة
            $table->index(['delivery_city']);
            $table->index(['delivery_lat','delivery_lng']);
        });

        // (اختياري) قيود CHECK في MySQL 8+
        try {
            DB::statement("ALTER TABLE orders ADD CONSTRAINT chk_lat CHECK (delivery_lat IS NULL OR (delivery_lat >= -90 AND delivery_lat <= 90))");
        } catch (\Throwable $e) {}

        try {
            DB::statement("ALTER TABLE orders ADD CONSTRAINT chk_lng CHECK (delivery_lng IS NULL OR (delivery_lng >= -180 AND delivery_lng <= 180))");
        } catch (\Throwable $e) {}
    }

    public function down(): void {
        // احذف القيود قبل الأعمدة
        try { DB::statement("ALTER TABLE orders DROP CONSTRAINT chk_lat"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE orders DROP CONSTRAINT chk_lng"); } catch (\Throwable $e) {}

        Schema::table('orders', function (Blueprint $table) {
            try { $table->dropIndex(['delivery_city']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['delivery_lat','delivery_lng']); } catch (\Throwable $e) {}
            $cols = ['delivery_address_line','delivery_city','delivery_phone','delivery_lat','delivery_lng'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
