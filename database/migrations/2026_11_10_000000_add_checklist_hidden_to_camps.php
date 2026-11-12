<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * «Erste Schritte» auf der Übersicht: Die Kursleitung kann die Karte für den ganzen Kurs ausblenden.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('camps', function (Blueprint $table) {
            $table->boolean('checklist_hidden')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('camps', function (Blueprint $table) {
            $table->dropColumn('checklist_hidden');
        });
    }
};
