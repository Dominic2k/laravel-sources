<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_subject_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('file_url', 255)->nullable();
            $table->enum('semester', ['1', '2', '3', '4', '5', '6']);
            $table->date('achievement_date');
            $table->timestamps();

            $table->foreign('student_id')->references('user_id')->on('students')->onDelete('cascade');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            //
        });
    }
};
