<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class File extends Component
{
    public string $name;
    public string $label;
    public string $id;
    public bool $required;
    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $label = '', string $id = null, bool $required = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->id = $id ?? $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.file');
    }
}
