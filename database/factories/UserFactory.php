<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'nisn' => fake()->numerify('##########'),
            'class' => fake()->randomElement(['X', 'XI', 'XII']) . ' ' . fake()->randomElement(['RPL', 'TKJ', 'DKV']),
            'role' => 'student',
            'status' => 'pending',
            'password' => bcrypt('password'),
        ];
    }
}
