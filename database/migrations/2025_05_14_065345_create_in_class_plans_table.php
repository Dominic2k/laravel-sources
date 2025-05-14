<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInClassPlansTable extends Migration
{
    public function up()
    {
        Schema::create('in_class_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goal_id')->nullable();
            // Loại bỏ student_id và class_subject_id vì có thể truy vấn từ goal_id
            $table->date('date')->nullable();
            $table->string('skills_module', 255)->nullable();
            $table->text('lesson_summary')->nullable();
            $table->enum('self_assessment', [1, 2, 3])->nullable();
            $table->text('difficulties_faced')->nullable();
            $table->text('improvement_plan')->nullable();
            $table->boolean('problem_solved')->default(true);
            $table->text('additional_notes')->nullable();
            $table->timestamps();
            
            // Thêm foreign key cho goal_id
            $table->foreign('goal_id')->references('id')->on('goals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('in_class_plans');
    }
}

