<?php

namespace App\Enums;

/**
 * Ampel-Einschätzung einer TN-Person im Kurs (camp_users.classification_id), auf der Profilseite
 * als drei Kreise. Die Werte sind die IDs der Tabelle classifications, dort stehen die Anzeigenamen.
 */
enum Classification: int
{
    case Rot = 10;
    case Gelb = 20;
    case Gruen = 30;

    /** CSS-Klasse des Kreises auf der Profilseite. */
    public function cssClass(): string
    {
        return match ($this) {
            self::Rot => 'red',
            self::Gelb => 'yellow',
            self::Gruen => 'green',
        };
    }
}
