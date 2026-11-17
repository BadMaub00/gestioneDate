<?php

namespace Database\Factories;

use App\Models\Utente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Utente>
 */
class UtenteFactory extends Factory
{
    protected $model = Utente::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'username'     => fake()->unique()->userName(),
            'password'     => 'password', // l'hash lo applica il cast 'hashed' del modello
            'nome'         => fake()->firstName(),
            'cognome'      => fake()->lastName(),
            'data_nascita' => fake()->date('Y-m-d', '-25 years'),
            'ruolo'        => Utente::RUOLO_DOCENTE,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['ruolo' => Utente::RUOLO_ADMIN]);
    }
}
