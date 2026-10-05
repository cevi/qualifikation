<?php

namespace Database\Factories;

use App\Enums\SurveyStatus;
use App\Models\Camp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Camp::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'user_id' => User::factory()->kursleiter(),
            'camp_type_id' => config('status.camptype_JS1'),
            'global_camp' => false,
            'demo' => false,
            'finish' => false,
            'end_date' => now()->addMonth()->toDateString(),
        ];
    }

    public function finished()
    {
        return $this->state(['finish' => true]);
    }

    public function withStatusControl()
    {
        return $this->state([
            'status_control' => true,
            'survey_status_id' => SurveyStatus::Neu,
        ]);
    }
}
