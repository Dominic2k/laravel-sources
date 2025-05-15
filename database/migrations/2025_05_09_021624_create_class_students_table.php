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
        Schema::create('class_students', function (Blueprint $table) {
        $table->unsignedBigInteger('class_id');
        $table->unsignedBigInteger('student_id');
        $table->timestamp('created_at')->useCurrent();
        $table->primary(['class_id', 'student_id']);
        $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        $table->foreign('student_id')->references('user_id')->on('students')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_students');
    }
};
