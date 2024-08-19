<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageTitle extends Component
{
    public $help;
    public $title;
    public $subtitle;
    public $header;
    /**
     * Create a new component instance.
     */
    public function __construct($title, $help = null, $header=true, $subtitle = '')
    {
        //
        $this->title = $title;
        $this->help = $help;
        $this->header = $header;
        $this->subtitle = $subtitle;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.page-title');
    }
}
