<?php

namespace Tests\Feature\Smoke;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Jede Seite, die eine Rolle über die Navigation erreicht, muss für diese Rolle mit 200 laden.
 */
class PagesRenderTest extends TestCase
{
    public static function pages(): iterable
    {
        $teilnehmer = [
            'Startseite' => static fn () => '/home',
            'eigenes Konto' => static fn (CampScenario $s) => route('home.user', $s->participant),
            'Kurs erstellen' => static fn () => route('home.camps.create'),
            'eigene Quali' => static fn (CampScenario $s) => route('survey.survey', $s->survey),
        ];

        $gruppenleiter = [
            'Startseite' => static fn () => '/home',
            'eigenes Konto' => static fn (CampScenario $s) => route('home.user', $s->gruppenleiter),
            'Kurs erstellen' => static fn () => route('home.camps.create'),
            'Rückmeldungen' => static fn () => '/posts',
            'Rückmeldung bearbeiten' => static fn (CampScenario $s) => route('posts.edit', $s->post),
            'Profil eigener TN' => static fn (CampScenario $s) => route('home.profile', $s->participant),
            'Profil TN ohne Leiter' => static fn (CampScenario $s) => route('home.profile', $s->unassignedParticipant),
            'Quali eigener TN' => static fn (CampScenario $s) => route('survey.survey', $s->survey),
            'Vergleich eigener TN' => static fn (CampScenario $s) => route('survey.compare', $s->survey),
        ];

        $kursleiter = [
            'Startseite' => static fn () => '/home',
            'eigenes Konto' => static fn (CampScenario $s) => route('home.user', $s->kursleiter),
            'Rückmeldungen' => static fn () => '/posts',
            'Profil TN' => static fn (CampScenario $s) => route('home.profile', $s->participant),
            'Vergleich TN' => static fn (CampScenario $s) => route('survey.compare', $s->survey),
            'Vergleich TN ohne Leiter' => static fn (CampScenario $s) => route('survey.compare', $s->unassignedSurvey),
            'Dashboard' => static fn () => '/admin',
            'Änderungen' => static fn () => '/admin/changes',
            'Personen' => static fn () => route('admin.users.index'),
            'Qualis' => static fn () => route('surveys.index'),
            'Kurse' => static fn () => route('admin.camps.index'),
            'Kurs bearbeiten' => static fn (CampScenario $s) => route('admin.camps.edit', $s->camp),
            'Kapitel' => static fn () => route('chapters.index'),
            'Fragen' => static fn () => route('questions.index'),
            'Kompetenzen' => static fn () => route('competences.index'),
            'Standardtexte' => static fn () => route('admin.standard_texts.index'),
            'Standardtext bearbeiten' => static fn (CampScenario $s) => route('admin.standard_texts.edit', $s->standardText),
        ];

        $admin = [
            'Startseite' => static fn () => '/home',
            'Dashboard' => static fn () => '/admin',
            'Personen' => static fn () => route('admin.users.index'),
            'Kurse' => static fn () => route('admin.camps.index'),
            'Kurstypen' => static fn () => route('admin.camp_types.index'),
            'Gruppen' => static fn () => route('groups.index'),
            'Gruppe bearbeiten' => static fn (CampScenario $s) => route('groups.edit', $s->group),
            'Antworten' => static fn () => route('answers.index'),
            'Klassifizierungen' => static fn () => route('classifications.index'),
            'Hilfe' => static fn () => route('helps.index'),
            'Hilfe bearbeiten' => static fn (CampScenario $s) => route('helps.edit', $s->help),
            'Feedback' => static fn () => route('feedback.index'),
        ];

        foreach (compact('teilnehmer', 'gruppenleiter', 'kursleiter', 'admin') as $role => $rolePages) {
            foreach ($rolePages as $label => $url) {
                yield "$role: $label" => [$role, $url];
            }
        }
    }

    #[DataProvider('pages')]
    public function test_page_renders_for_role(string $role, Closure $url)
    {
        $scenario = CampScenario::create();

        $this->actingAs($scenario->users()[$role])
            ->get($url($scenario))
            ->assertOk();
    }
}
