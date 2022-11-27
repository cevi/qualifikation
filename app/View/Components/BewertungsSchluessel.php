<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BewertungsSchluessel extends Component
{
    public $answers;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($answers)
    {
        //
        $this->answers = $answers;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.bewertungs-schluessel');
    }
}
