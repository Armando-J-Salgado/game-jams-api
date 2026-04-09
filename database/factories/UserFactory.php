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
            'name' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'username' => fake()->unique()->userName(),
            'dui' => fake()->unique()->numerify('########-#'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
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

    /**
     * Define el rol del usuario como "Administrador".
     * Relaciona este registro con el rol especificado usando Spatie tras crearlo.
     */
    public function asAdministrador(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('administrador');
        });
    }

    /**
     * Define el rol del usuario como "Organizador".
     * Relaciona este registro con el rol especificado usando Spatie tras crearlo.
     */
    public function asOrganizador(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('organizador');
        });
    }

    /**
     * Define el rol del usuario como "Líder".
     * Relaciona este registro con el rol especificado usando Spatie tras crearlo.
     */
    public function asLider(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('lider');
        });
    }

    /**
     * Define el rol del usuario como "Participante".
     * Relaciona este registro con el rol especificado usando Spatie tras crearlo.
     */
    public function asParticipante(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('participante');
        });
    }
}
