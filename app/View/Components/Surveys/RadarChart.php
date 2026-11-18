<?php

namespace App\View\Components\Surveys;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RadarChart extends Component
{
    public $id;

    public $name;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id = 1, $name = '')
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('components.surveys.radar-chart');
    }
}
