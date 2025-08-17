<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TBL1 = 'prices';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TBL1, function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->comment('FK: products.id');
            $table->foreignId('store_id')->default(0)->comment('Store ID, 0: all stores');
            $table->decimal('amount', 10, 2)->comment('Price amount');
            $table->string('currency', 3)->default('VND')->comment('Currency code, default VND');
            $table->date('start_date')->comment('Start date for price');
            $table->date('end_date')->comment('End date, set 1st date of 50 years later if unsure');
            $table->string('source')->comment('Source: manual, system, API, etc.');
            $table->string('note')->nullable()->comment('Note, e.g. 25Q3 intro price for launch campaign X, nullable');
            $table->timestamps();
            $table->softDeletes();
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
