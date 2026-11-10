<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public bool $delete = false;

    /** primary (blau, Hauptaktion), danger (rot, Löschen), purple (Import/Export), secondary (weiss mit Rand), icon (nur Symbol) */
    public string $variant;

    public function __construct(bool $delete = false, string $variant = 'primary')
    {
        $this->delete = $delete;
        $this->variant = $delete ? 'danger' : $variant;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.button');
    }
}
