<?php

namespace App\View\Components\Forms;

use App\View\Components\Forms\Concerns\ResolvesValue;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextArea extends Component
{
    use ResolvesValue;

    public string $name;

    public string $label;

    public string $id;

    public bool $required;

    public $value;

    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $label = '', bool $required = false, $value = null, ?string $id = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->value = $value;
        $this->id = $id ?? $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.text-area');
    }
}
