<?php

namespace Tests\Feature\Security;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Texte von Rückmeldungen und Bemerkungen zur Quali schreiben Leitende (auch über den Demo-Login /loginLeiter),
 * gelesen werden sie von der Kursleitung (im Vergleich nur Rückmeldungen "Auf Quali").
 * Sie erscheinen als Text mit Zeilenumbrüchen, nie als HTML.
 */
class CommentEscapingTest extends TestCase
{
    private const COMMENT = "Gut gemacht\n<img src=x onerror=alert(1)>";

    private const RENDERED = "Gut gemacht<br />\n&lt;img src=x onerror=alert(1)&gt;";

    public static function pages(): array
    {
        return [
            'Rückmeldung auf dem Profil' => ['post', static fn (CampScenario $s) => route('home.profile', $s->participant)],
            'Rückmeldung im Vergleich' => ['post', static fn (CampScenario $s) => route('survey.compare', $s->survey)],
            'Bemerkung im Vergleich' => ['survey', static fn (CampScenario $s) => route('survey.compare', $s->survey)],
            'Bemerkung in der Druckansicht' => ['survey', static fn (CampScenario $s) => route('survey.downloadPDF', $s->survey)],
            'Bemerkung in der Druckansicht aller Qualis' => ['survey', static fn () => route('surveys.downloadPDF')],
        ];
    }

    #[DataProvider('pages')]
    public function test_comment_is_shown_as_text_with_line_breaks(string $model, Closure $url)
    {
        $s = CampScenario::create();
        $model === 'post'
            ? $s->post->update(['comment' => self::COMMENT, 'show_on_survey' => true])
            : $s->survey->update(['comment' => self::COMMENT]);

        $this->actingAs($s->kursleiter)
            ->get($url($s))
            ->assertOk()
            ->assertDontSee('<img src=x', false)
            ->assertSee(self::RENDERED, false);
    }
}
