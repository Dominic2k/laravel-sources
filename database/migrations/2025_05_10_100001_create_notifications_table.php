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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->enum('notification_type', [
                'goal_feedback',
                'plan_feedback',
                'journal_feedback',
                'deadline_reminder',
                'deadline_missed',
                'support_request',
                'support_response',
                'system_announcement',
                'teacher_tagged',
                'student_reply',
                'tag_resolved'
            ]);
            $table->string('related_entity_type', 50);
            $table->unsignedBigInteger('related_entity_id');
            $table->string('title', 255);
            $table->text('message');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->timestamp('created_at')->useCurrent();
            $table->boolean('is_read')->default(false);
            
            // Thêm ràng buộc khóa ngoại
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            
            // Thêm index cho cặp related_entity_type và related_entity_id
            $table->index(['related_entity_type', 'related_entity_id']);
            
            // Giữ nguyên index hiện có
            $table->index(['receiver_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};