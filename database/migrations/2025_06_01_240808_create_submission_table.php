<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('submission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('deadline_id');
            $table->enum('status', ['missed', 'late', 'process', 'done']); // sửa "lated" thành "late"s
            $table->date('submitted_day')->nullable(); // thêm ->nullable() nếu có thể không nộp
            $table->timestamps();

            // Khóa ngoại (tuỳ chọn nếu có bảng student và deadline)
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deadline_id')->references('id')->on('deadlines')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('submission');
    }
};
