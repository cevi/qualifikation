<?php

namespace App\View\Components\Forms\Concerns;

use BackedEnum;

/**
 * Wert eines Formularfelds wie bei Form::text (laravelcollective): nach einem Validierungsfehler die alte Eingabe,
 * sonst der übergebene Wert, sonst das Modell aus <x-forms.form :model="…">.
 */
trait ResolvesValue
{
    /** Schlüssel für old() und das Modell: "leader_ids[]" → "leader_ids", "answers[12]" → "answers.12". */
    public function key(): string
    {
        return preg_replace(['/\[\]$/', '/\[([^\]]*)\]/'], ['', '.$1'], $this->name);
    }

    public function current($model = null): mixed
    {
        $value = $this->value ?? ($model ? data_get($model, $this->key()) : null);
        $value = old($this->key(), $value);

        return $value instanceof BackedEnum ? $value->value : $value;
    }

    /** Wurde das Formular schon einmal abgeschickt und kam mit Fehlern zurück? (wie Form::oldInputIsEmpty) */
    protected function hasOldInput(): bool
    {
        return request()->hasSession() && request()->session()->hasOldInput();
    }
}
