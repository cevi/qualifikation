<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Post extends Component
{
    public $posts;

    public $showLeader;

    public $title;

    public $editable;
    public $user;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($posts, $showLeader, $title, $editable = true,  $user = null)
    {
        //
        $this->posts = $posts;
        $this->showLeader = $showLeader;
        $this->title = $title;
        $this->editable = $editable;
        $this->user = $user;
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
