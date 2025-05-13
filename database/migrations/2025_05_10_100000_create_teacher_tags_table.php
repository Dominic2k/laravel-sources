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
        Schema::create('teacher_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('tagged_by');
            $table->enum('entity_type', ['journal', 'self_study_plan', 'in_class_plan', 'goal']);
            $table->unsignedBigInteger('entity_id');
            $table->text('message');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            
            // Thêm ràng buộc khóa ngoại
            $table->foreign('teacher_id')->references('user_id')->on('teachers')->onDelete('cascade');
            $table->foreign('tagged_by')->references('user_id')->on('students')->onDelete('cascade');
            
            // Thêm index cho cặp entity_type và entity_id
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_tags');
    }
};