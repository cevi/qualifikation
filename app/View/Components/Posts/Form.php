<?php

namespace App\View\Components\Posts;

use App\Models\Post;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public bool $chooseFromUser;

    public string $route;

    public Post $post;

    public array $campusers;

    public array $questions;

    /**
     * Create a new component instance.
     */
    public function __construct(Post $post, string $route, array $campusers = [], array $questions = [], bool $chooseFromUser = false)
    {
        $this->chooseFromUser = $chooseFromUser;
        $this->post = $post;
        $this->campusers = $campusers;
        $this->route = $route;
        $this->questions = $questions;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.posts.form');
    }
}
