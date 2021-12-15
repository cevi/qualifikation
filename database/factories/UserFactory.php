<?php

namespace Database\Factories;

use App\User;
use DateTime;
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
            'role_id' => config('status.role_Teilnehmer'),
            'is_active' => true,
            'email_verified_at' => now(),
            'camp_id' => 1,
            'demo' => true            
        ];
    }
}
