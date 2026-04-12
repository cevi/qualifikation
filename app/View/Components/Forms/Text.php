<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Text extends Component
{
    
    public string $name;
    public string $label;
    public string $id;
    public string $type;
    public bool $required;
    public $value;
    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $label = '', $id = null, bool $required = false, string $type = 'text', $value = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->required = $required;
        $this->id = $id ?? $name;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.text');
    }
}
