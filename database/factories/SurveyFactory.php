<?php

namespace Database\Factories;

use App\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Survey::class;
    public function definition()
    {
        return [
            //
            'demo' => true,
            'survey_status_id' => $this->faker->randomElement([5, 10, 15, 20, 50]),           
        ];
    }
}
