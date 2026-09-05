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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo');
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->dateTime('due_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
        // TODO
        // Schema::create('task_user', function (Blueprint $table) {
        //     $table->foreignId('task_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        //     $table->primary(['task_id', 'user_id']); 
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
