<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DrawerContent extends Component
{
    public $help;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($help = null)
    {
        $this->help = $help;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.drawer-content');
    }
}
