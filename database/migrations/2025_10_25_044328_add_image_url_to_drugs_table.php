<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('drugs', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('barcode'); // عدّل مكانه كما تحب
        });
    }

    public function down(): void
    {
        Schema::table('drugs', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};

