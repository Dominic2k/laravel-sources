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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name', 100);
            $table->enum('semester', ['1', '2', '3', '4', '5', '6']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planning', 'ongoing', 'completed']);
            $table->timestamp('created_at')->useCurrent();  
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
