<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'password'   => Hash::make('password'), // hoặc Hash::make('password')
            'email'      => $this->faker->unique()->safeEmail(),
            'full_name'  => $this->faker->name(),
            'birthday'   => $this->faker->date('Y-m-d'),
            'role'       => $this->faker->randomElement(['teacher', 'student']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

