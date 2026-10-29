<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Group::class;

    public function definition()
    {
        return [
            'name' => 'Cevi '.$this->faker->unique()->city,
            'shortname' => $this->faker->lexify('???'),
            'foreign_id' => $this->faker->unique()->numberBetween(10000, 99999),
            'campgroup' => false,
        ];
    }
}
