<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make("1234")
        ]);

        for($i = 1; $i <= 10; $i++) {
            if ($i <= 5) {
                User::factory()->create([ 
                    'full_name' => 'student_'.$i,
                    'email' => 'student_'.$i.'@pnv.com',
                    'password' => Hash::make("1234"),
                    'role' => 'student'
                ]);
            } else {
                User::factory()->create([ 
                    'full_name' => 'teacher_'.$i,
                    'email' => 'teacher_'.$i.'@pnv.com',
                    'password' => Hash::make("1234"),
                    'role' => 'teacher'
                ]);
            }

        }
    }
}
