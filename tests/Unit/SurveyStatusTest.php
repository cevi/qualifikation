<?php

namespace Tests\Unit;

use App\Enums\SurveyStatus;
use App\Models\SurveyStatus as SurveyStatusModel;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SurveyStatusTest extends TestCase
{
    public function test_enum_matches_seeded_status_table()
    {
        $this->assertSame(
            array_map(fn (SurveyStatus $status) => $status->value, SurveyStatus::cases()),
            SurveyStatusModel::orderBy('id')->pluck('id')->all()
        );
    }

    /**
     * [Status, erste Runde, zweite Runde erreicht, von TN abgeschlossen, fertig]
     */
    public static function rules(): array
    {
        return [
            'Neu' => [SurveyStatus::Neu, true, false, false, false],
            '1. offen' => [SurveyStatus::ErsteRundeOffen, true, false, false, false],
            '2. offen' => [SurveyStatus::ZweiteRundeOffen, false, true, false, false],
            'TN abgeschlossen' => [SurveyStatus::TnAbgeschlossen, false, true, true, false],
            'Fertig' => [SurveyStatus::Fertig, false, true, true, true],
        ];
    }

    #[DataProvider('rules')]
    public function test_named_rules(SurveyStatus $status, bool $firstRound, bool $secondRound, bool $closed, bool $finished)
    {
        $this->assertSame(
            [$firstRound, $secondRound, $closed, $finished],
            [$status->isFirstRound(), $status->hasReachedSecondRound(), $status->isClosedByParticipant(), $status->isFinished()]
        );
    }

    public function test_order()
    {
        $this->assertTrue(SurveyStatus::Neu->isBefore(SurveyStatus::ErsteRundeOffen));
        $this->assertFalse(SurveyStatus::Fertig->isBefore(SurveyStatus::TnAbgeschlossen));
        $this->assertTrue(SurveyStatus::TnAbgeschlossen->isAtLeast(SurveyStatus::TnAbgeschlossen));
        $this->assertFalse(SurveyStatus::ZweiteRundeOffen->isAtLeast(SurveyStatus::TnAbgeschlossen));
    }

    /** Farbe (Badge, Balken) und Filtergruppe der Quali-Liste: wartet auf Leitende = orange, "Zum Abschliessen". */
    public function test_tone_and_phase()
    {
        $this->assertSame(
            ['gray', 'blue', 'blue', 'orange', 'green'],
            array_map(fn (SurveyStatus $status) => $status->tone(), SurveyStatus::cases())
        );
        $this->assertSame(
            ['offen', 'offen', 'offen', 'abschliessen', 'fertig'],
            array_map(fn (SurveyStatus $status) => $status->phase(), SurveyStatus::cases())
        );
    }
}
