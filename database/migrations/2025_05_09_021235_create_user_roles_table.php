<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function getKeyType()
    {
        return 'string'; // Thay vì 'int', bạn trả về 'string' nếu id là chuỗi
    }
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
        $table->string("id")->primary();
        $table->enum('role', ['admin', 'teacher', 'student']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
