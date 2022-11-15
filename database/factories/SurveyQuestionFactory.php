<?php

namespace Database\Factories;

use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = SurveyQuestion::class;

    public function definition()
    {
        return [
            //
            'comment_first' => $this->faker->text(300),
            'comment_second' => $this->faker->text(300),
            'comment_leader' => $this->faker->text(300),
            'answer_first_id' => $this->faker->randomElement([1, 2, 3, 4]),
            'answer_second_id' => $this->faker->randomElement([1, 2, 3, 4]),
            'answer_leader_id' => $this->faker->randomElement([1, 2, 3, 4]),
        ];
    }
}
