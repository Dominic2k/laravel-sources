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
        Schema::create('deadlines', function (Blueprint $table) {
            $table->id();
            $table->enum('target_type', ['goal', 'self_study_plan', 'in_class_plan', 'journal_entry', 'achievement']);
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('set_by');
            $table->string('title', 255);
            $table->text('description');
            $table->timestamp('due_date');
            $table->json('reminder_settings')->nullable();
            $table->enum('status', ['pending', 'completed', 'missed', 'extended']);
            $table->timestamps();
            
            // Thêm ràng buộc khóa ngoại
            $table->foreign('set_by')->references('id')->on('users')->onDelete('cascade');
            
            // Thêm index cho cặp target_type và target_id
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deadlines');
    }
};