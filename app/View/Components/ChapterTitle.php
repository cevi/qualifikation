<?php

namespace App\View\Components;

use App\Models\SurveyChapter;
use Illuminate\View\Component;

class ChapterTitle extends Component
{
    public $chapter;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(SurveyChapter $chapter)
    {
        //
        $this->chapter = $chapter;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.chapter-title');
    }
}
