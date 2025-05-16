<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo một admin user
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'email' => 'admin@example.com',
            'full_name' => 'Admin User',
            'birthday' => '1990-01-01',
        ]);

        // Tạo 10 user cho giáo viên
        User::factory()->count(10)->create([
            'username' => function () {
                return 'teacher_' . fake()->unique()->userName();
            },
        ]);

        // Tạo 50 user cho học sinh
        User::factory()->count(50)->create([
            'username' => function () {
                return 'student_' . fake()->unique()->userName();
            },
        ]);
    }
}