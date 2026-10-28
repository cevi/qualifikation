<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hilfetexte werden über den Seitentitel gefunden (Help::where('title', $title)). Die Titel heissen neu
 * "Kurs bearbeiten" statt "Lager bearbeiten" und "Standard-Text bearbeiten" statt "Standart-Text bearbeiten".
 */
return new class extends Migration
{
    private const TITLES = [
        'Lager bearbeiten' => 'Kurs bearbeiten',
        'Standart-Text bearbeiten' => 'Standard-Text bearbeiten',
    ];

    public function up(): void
    {
        foreach (self::TITLES as $old => $new) {
            DB::table('helps')->where('title', $old)->update(['title' => $new]);
        }
    }

    public function down(): void
    {
        foreach (self::TITLES as $old => $new) {
            DB::table('helps')->where('title', $new)->update(['title' => $old]);
        }
    }
};
