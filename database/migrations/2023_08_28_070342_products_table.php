<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('product_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
