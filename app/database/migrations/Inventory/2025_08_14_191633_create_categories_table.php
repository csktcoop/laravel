<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TBL1 = 'categories';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TBL1, function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Category name');
            $table->string('slug')->unique()->comment('Unique slug for category');
            $table->text('description')->nullable()->comment('Category description, nullable');
            $table->foreignId('parent_id')->default(0)->comment('Parent category ID, 0 for root');
            $table->string('path')->nullable()->comment('Path, null on creation');
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
