<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RadarChart extends Component
{
    public $save;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($save = false )
    {
        //
        $this->save = $save;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.radar-chart');
    }
}
