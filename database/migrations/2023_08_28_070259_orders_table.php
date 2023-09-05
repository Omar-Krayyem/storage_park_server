<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->decimal('total_price', 15, 2);
            $table->decimal('longitude', 4, 2);
            $table->decimal('latitude', 4, 2);
            $table->date('placed_at');
            $table->date('delivered_at')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('order_type_id');
        });

        DB::statement('ALTER TABLE orders AUTO_INCREMENT = 1000');
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
