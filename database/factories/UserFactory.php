<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = User::class;

    public function definition()
    {
        return [
            //
            'username' => $this->faker->name,
            'email' => $this->faker->email,
            'role_id' => config('status.role_Teilnehmer'),
            'email_verified_at' => now(),
            'camp_id' => 1,
            'demo' => true,
        ];
    }
}
