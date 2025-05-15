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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->enum('entity_type', ['goal', 'self_study_plan', 'in_class_plan', 'journal']);
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('teacher_id');
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();
            
            // Thêm ràng buộc khóa ngoại
            $table->foreign('teacher_id')->references('user_id')->on('teachers')->onDelete('cascade');
            
            // Thêm index cho cặp entity_type và entity_id
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};