<?php

namespace App\Enums;

/**
 * Status einer Quali, in dieser Reihenfolge. Die Werte sind die IDs der Tabelle survey_statuses,
 * dort stehen auch die Anzeigenamen (App\Models\SurveyStatus).
 *
 * Wird zusätzlich als Kurs-Status (camps.survey_status_id) verwendet, wenn der Quali-Ablauf
 * kontrolliert wird: Teilnehmende dürfen dann nur ausfüllen, solange ihre Quali davor liegt.
 */
enum SurveyStatus: int
{
    case Neu = 5;
    case ErsteRundeOffen = 10;
    case ZweiteRundeOffen = 15;
    case TnAbgeschlossen = 20;
    case Fertig = 50;

    public function isBefore(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function isAtLeast(self $other): bool
    {
        return $this->value >= $other->value;
    }

    /** Neu oder 1. Selbsteinschätzung offen. */
    public function isFirstRound(): bool
    {
        return ! $this->isAtLeast(self::ZweiteRundeOffen);
    }

    /** Ab der 2. Selbsteinschätzung: Antworten gehen in die zweite Runde. */
    public function hasReachedSecondRound(): bool
    {
        return $this->isAtLeast(self::ZweiteRundeOffen);
    }

    /** Die TN-Person hat ihre Selbsteinschätzung abgeschlossen (oder die Quali ist fertig). */
    public function isClosedByParticipant(): bool
    {
        return $this->isAtLeast(self::TnAbgeschlossen);
    }

    public function isFinished(): bool
    {
        return $this === self::Fertig;
    }

    /** Farbe für Badge und Fortschritt (Übersicht, Quali-Liste): offen blau, wartet auf Leitende orange, fertig grün. */
    public function tone(): string
    {
        return match ($this) {
            self::Neu => 'gray',
            self::ErsteRundeOffen, self::ZweiteRundeOffen => 'blue',
            self::TnAbgeschlossen => 'orange',
            self::Fertig => 'green',
        };
    }

    /** Gruppe für den Filter der Quali-Liste: offen, zum Abschliessen (wartet auf Leitende), fertig. */
    public function phase(): string
    {
        return match ($this) {
            self::Neu, self::ErsteRundeOffen, self::ZweiteRundeOffen => 'offen',
            self::TnAbgeschlossen => 'abschliessen',
            self::Fertig => 'fertig',
        };
    }
}
