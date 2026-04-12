<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Hidden extends Component
{
    public string $name;
    public string $id;
    public string $label;
    public $value;
    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $label = '', $id = null, $value = null)
    {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->label = $label;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.hidden');
    }
}
