<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
    public string $name;

    public string $label;

    public string $id;

    public bool $required;

    public $value;

    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $label = '', $id = null, bool $required = false, $value = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->id = $id ?? $name;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.checkbox');
    }
}
