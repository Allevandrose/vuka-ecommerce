<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'user_type' => fake()->randomElement(['customer', 'admin', 'delivery', 'pickup']),
            'google_id' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_type' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a customer.
     */
    public function customer(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_type' => 'customer',
        ]);
    }

    /**
     * Indicate that the user is delivery staff.
     */
    public function delivery(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_type' => 'delivery',
        ]);
    }

    /**
     * Indicate that the user is pickup staff.
     */
    public function pickup(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_type' => 'pickup',
        ]);
    }
}
