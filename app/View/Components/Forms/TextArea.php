<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextArea extends Component
{
    public string $name;
    public string $label;
    public bool $required;
    public $value;
    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $label, bool $required = false, $value = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.text-area');
    }
}
