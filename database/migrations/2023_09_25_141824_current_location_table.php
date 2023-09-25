<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('current_locations', function (Blueprint $table) {
            $table->id();
            $table->decimal('longitude', 4, 4);
            $table->decimal('latitude', 4, 4);
            $table->unsignedBigInteger('worker_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
