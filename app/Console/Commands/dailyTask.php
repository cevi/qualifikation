<?php

namespace App\Console\Commands;

use App\Models\Camp;
use Illuminate\Console\Command;

class dailyTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a task daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->ResetDemoCamp();
        return Command::SUCCESS;
    }

    public function ResetDemoCamp()
    {
        Camp::where('demo', true)->update([
            'secondsurveyopen' => false,
            'status_control' => false,
            'survey_status_id' => null
        ]);
        $camp = Camp::where('demo', true)->first();
        $posts = $camp->posts()->get();
        foreach ($posts as $post){
            $post->update(['comment' => fake()->sentence()]);
        }
        $surveys = $camp->surveys()->get();
        foreach ($surveys as $survey){
            $survey->update([
                'comment' => fake()->sentence(),
                'survey_status_id' => config('status.survey_neu')
            ]);
            $questions = $survey->questions()->get();
            foreach ($questions as $question) {
                $question->update([
                    'comment_second' => fake()->sentence(),
                    'comment_first' => fake()->sentence(),
                    'comment_leader' => fake()->sentence()]);
            }
        }
    }
}
