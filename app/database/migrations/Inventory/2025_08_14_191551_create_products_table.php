<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TBL1 = 'products';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TBL1, function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Product name');
            $table->string('sku')->unique()->comment('Stock Keeping Unit, unique');
            $table->text('description')->nullable()->comment('Product description, nullable');
            $table->string('source')->default('default')->comment('Source: manual, system, API, etc.');
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
