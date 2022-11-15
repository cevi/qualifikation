<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Post extends Component
{
    public $posts;

    public $showLeader;

    public $title;

    public $editable;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($posts, $showLeader, $title, $editable = true)
    {
        //
        $this->posts = $posts;
        $this->showLeader = $showLeader;
        $this->title = $title;
        $this->editable = $editable;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.post');
    }
}
