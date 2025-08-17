<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TBL1 = 'product_warehouse';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TBL1, function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->comment('FK: products.id');
            $table->foreignId('warehouse_id')->constrained()->comment('FK: warehouses.id');
            $table->unsignedInteger('quantity')->nullable()->comment('NULL: non-stock-managed for drop-ship, made-to-order, digital products');
            $table->timestamps();

            $table->primary(['product_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TBL1);
    }
};
