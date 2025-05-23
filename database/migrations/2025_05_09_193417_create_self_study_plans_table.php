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
        Schema::create('self_study_plans', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('subject_id');
            $table->string('lesson');
            $table->string('time');
            $table->text('resources')->nullable();
            $table->text('activities')->nullable();
            $table->string('concentration');
            $table->string('plan_follow');
            $table->text('evaluation')->nullable();
            $table->text('reinforcing')->nullable();
            $table->timestamps();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('self_study_plans');
    }
};
