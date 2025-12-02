<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password123'), // default password
            'role' => 'anggota', // default role
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * State untuk admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
    }

    /**
     * State untuk bendahara.
     */
    public function bendahara(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'bendahara',
            'name' => 'Bendahara',
            'email' => 'bendahara@example.com',
        ]);
    }
}
