<?php

namespace App\View\Components\Forms;

use App\View\Components\Forms\Concerns\ResolvesValue;
use BackedEnum;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Radio-Knopf (ersetzt Form::radio): angewählt nach alter Eingabe, sonst checked, sonst wenn das Modell den Wert hat.
 * Ohne eigene Klassen, wie bisher in der Quali (Form::radio ignorierte die übergebenen Klassen).
 */
class Radio extends Component
{
    use ResolvesValue;

    public string $name;

    public string $id;

    public string $label;

    public $value;

    public ?bool $checked;

    public function __construct(string $name, $value, ?string $id = null, string $label = '', ?bool $checked = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->id = $id ?? $name;
        $this->label = $label;
        $this->checked = $checked;
    }

    public function isChecked($model = null): bool
    {
        if ($this->hasOldInput()) {
            return (string) old($this->key()) === (string) $this->value;
        }
        if ($this->checked !== null) {
            return $this->checked;
        }
        $current = $model ? data_get($model, $this->key()) : null;
        $current = $current instanceof BackedEnum ? $current->value : $current;

        return $current !== null && (string) $current === (string) $this->value;
    }

    public function render(): View|Closure|string
    {
        return view('components.forms.radio');
    }
}
