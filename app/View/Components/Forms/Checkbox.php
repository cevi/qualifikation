<?php

namespace App\View\Components\Forms;

use App\View\Components\Forms\Concerns\ResolvesValue;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Checkbox: value ist der Wert beim Abschicken, checked der Zustand. Ohne checked entscheidet das Modell; nach einem
 * Validierungsfehler die alte Eingabe (nicht abgeschickt = nicht angehakt).
 */
class Checkbox extends Component
{
    use ResolvesValue;

    public string $name;

    public string $label;

    public string $id;

    public bool $required;

    public $value;

    public ?bool $checked;

    public function __construct(string $name, string $label = '', $id = null, bool $required = false, $value = '1', ?bool $checked = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->id = $id ?? $name;
        $this->value = $value;
        $this->checked = $checked;
    }

    public function isChecked($model = null): bool
    {
        if ($this->hasOldInput()) {
            $old = old($this->key());

            return is_array($old) ? in_array((string) $this->value, array_map('strval', $old), true) : $old !== null;
        }

        return $this->checked ?? (bool) ($model ? data_get($model, $this->key()) : false);
    }

    public function render(): View|Closure|string
    {
        return view('components.forms.checkbox');
    }
}
