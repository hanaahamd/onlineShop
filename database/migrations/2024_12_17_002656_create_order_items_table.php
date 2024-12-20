<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // ربط الطلب بالمنتجات
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // ربط المنتج
            $table->string('name');
            $table->integer('qty'); // الكمية المطلوبة
            $table->double('price', 10, 2); // سعر المنتج
            $table->double('total', 10, 2); // إجمالي سعر هذا المنتج (السعر × الكمية)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};