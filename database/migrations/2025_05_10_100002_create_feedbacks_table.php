<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->enum('entity_type', ['goal', 'self_study_plan', 'in_class_plan', 'journal']);
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('teacher_id');
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade'); // giả sử giáo viên là user trong bảng users
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
