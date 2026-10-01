<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Beispiel: Post::factory()->for($campUser, 'campUser')->for($leader, 'leader')->create(['camp_id' => $camp->id])
     *
     * @return array
     */
    protected $model = Post::class;

    public function definition()
    {
        return [
            'comment' => $this->faker->paragraph,
            'show_on_survey' => false,
        ];
    }

    public function shownOnSurvey()
    {
        return $this->state(['show_on_survey' => true]);
    }
}
