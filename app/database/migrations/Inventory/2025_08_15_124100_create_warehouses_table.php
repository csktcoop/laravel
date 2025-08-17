<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TBL1 = 'warehouses';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TBL1, function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Warehouse name');
            $table->string('location')->nullable()->comment('Warehouse location, nullable');
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
