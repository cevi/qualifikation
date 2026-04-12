<?php

namespace App\View\Components\Posts;

use App\Models\Post;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public bool $chooseFromUser;
    public string $route;   
    public Post $post;
    public Array $campusers;
    /**
     * Create a new component instance.
     */
    public function __construct(Post $post, String $route, Array $campusers = [], bool $chooseFromUser = false)
    {
        $this->chooseFromUser = $chooseFromUser;
        $this->post = $post;
        $this->campusers = $campusers;
        $this->route = $route;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.posts.form');
    }
}
