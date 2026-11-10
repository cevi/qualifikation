<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\StandardText;
use App\Models\Survey;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Karte «Erste Schritte» auf der Übersicht der Kursleitung. Abgehakt wird aus den Daten (gleiche Prüfungen wie die
 * Aufgabenliste in CampChecks); Knöpfe hat nur der nächste offene Pflichtschritt, optionale Schritte zählen nicht
 * zum Fortschritt. Abgeschlossene Kurse und der Global-Kurs haben keine Karte.
 *
 * Schritt: array{key: string, group: string, title: string, detail: string, done: bool, optional: bool, next: bool,
 *     actions: list<array{label: string, url: string, method: 'get'|'post', variant: 'primary'|'secondary', confirm?: string, confirm_text?: string}>}
 */
class CampChecklist
{
    public const GROUPS = ['Vorbereitung', 'Im Kurs', 'Nach dem Kurs'];

    public function __construct(private CampChecks $checks) {}

    public function shows(Camp $camp): bool
    {
        return ! $camp->finish && ! $camp->global_camp;
    }

    /** @return array{steps: Collection, done: int, total: int} */
    public function for(Camp $camp): array
    {
        $steps = $this->steps($camp);
        $required = $steps->reject(fn (array $step) => $step['optional']);
        $nextKey = $required->first(fn (array $step) => ! $step['done'])['key'] ?? null;

        $steps = $steps->map(function (array $step) use ($nextKey) {
            $step['next'] = $step['key'] === $nextKey;
            // Ruhig halten: Knöpfe nur beim nächsten Schritt und bei offenen optionalen
            if (! $step['next'] && ! ($step['optional'] && ! $step['done'])) {
                $step['actions'] = [];
            }

            return $step;
        });

        return ['steps' => $steps, 'done' => $required->where('done', true)->count(), 'total' => $required->count()];
    }

    private function steps(Camp $camp): Collection
    {
        $roles = CampUser::where('camp_id', $camp->id)->pluck('role_id');
        $participants = $roles->filter(fn (Role $role) => $role === Role::Teilnehmer)->count();
        $leaders = $roles->filter(fn (Role $role) => in_array($role, [Role::Gruppenleiter, Role::Stabsleiter], true))->count();
        $surveys = $camp->surveys()->get();
        $endDate = $this->endDate($camp);
        $texts = StandardText::where('camp_id', $camp->id)->count();

        $steps = collect([
            [
                'key' => 'camp', 'group' => 'Vorbereitung', 'title' => 'Kurs erstellen',
                'done' => $endDate !== null,
                'detail' => $endDate
                    ? ($camp->camp_type['name'] ?? 'Kurs').' · Schlussdatum '.$endDate->format('d.m.Y')
                    : 'Schlussdatum fehlt, ohne kommt keine Erinnerung zum Abschliessen',
                'actions' => [$this->link('Kurs bearbeiten', route('admin.camps.edit', $camp))],
            ],
            [
                'key' => 'people', 'group' => 'Vorbereitung', 'title' => 'Leitende und Teilnehmende importieren',
                'done' => $participants > 0,
                'detail' => $participants > 0
                    ? ($leaders === 1 ? '1 leitende Person' : "$leaders Leitende").' · '.($participants === 1 ? '1 teilnehmende Person' : "$participants Teilnehmende")
                    : ($this->canImportFromDb($camp) ? 'Aus der Cevi-DB oder als Teilnehmendenliste hochladen' : 'Teilnehmendenliste hochladen oder einzeln erfassen'),
                'actions' => [$this->link('Personen importieren', route('admin.users.index').'#import')],
            ],
            [
                'key' => 'leaders', 'group' => 'Vorbereitung', 'title' => 'Teilnehmende den Leitenden zuteilen',
                'done' => $participants > 0 && $this->checks->participantsWithoutLeader($camp)->isEmpty(),
                'detail' => $participants > 0
                    ? ($participants - $this->checks->participantsWithoutLeader($camp)->count())." von $participants zugeteilt"
                    : 'Eine Person kann mehrere Leitende haben',
                'actions' => [$this->link('Zuteilen', route('admin.users.index'))],
            ],
            [
                'key' => 'surveys', 'group' => 'Vorbereitung', 'title' => 'Qualifikationen erstellen',
                'done' => $participants > 0 && $this->checks->participantsWithoutSurvey($camp)->isEmpty(),
                'detail' => $participants > 0
                    ? ($participants - $this->checks->participantsWithoutSurvey($camp)->count())." von $participants erstellt"
                    : 'Für alle Teilnehmenden auf einmal',
                'actions' => [$this->post('Qualifikationen erstellen', route('surveys.store'), 'Qualifikationen erstellen?',
                    'Dies erstellt den Qualifikationsprozess für alle Teilnehmenden deines Kurses, welche ihn noch nicht haben.')],
            ],
            [
                'key' => 'texts', 'group' => 'Vorbereitung', 'title' => 'Standardtexte für Rückmeldungen', 'optional' => true,
                'done' => $texts > 0,
                'detail' => $texts > 0 ? ($texts === 1 ? '1 Text' : "$texts Texte") : 'Vorlagen, die Leitende in Rückmeldungen einfügen',
                'actions' => [$this->link('Standardtexte', route('admin.standard_texts.index'), 'secondary')],
            ],
        ]);

        // Ohne «Quali-Ablauf kontrollieren» ist die 1. Runde sofort offen, der Schritt fällt weg.
        if ($camp->status_control) {
            $steps->push([
                'key' => 'first-round', 'group' => 'Im Kurs', 'title' => '1. Selbsteinschätzung freigeben',
                'done' => $this->checks->firstRoundReleased($camp),
                'detail' => $this->checks->firstRoundReleased($camp) ? 'Freigegeben' : 'Danach können die Teilnehmenden ausfüllen',
                'actions' => [$this->post('Freigeben', route('admin.camps.opensurvey'))],
            ]);
        }

        $withoutPost = $this->checks->participantsWithoutPost($camp)->count();
        $closedFirst = $surveys->filter(fn (Survey $survey) => $survey->status()->isClosedByParticipant())->count();
        $finished = $surveys->filter(fn (Survey $survey) => $survey->status()->isFinished())->count();

        return $steps->push(
            [
                'key' => 'posts', 'group' => 'Im Kurs', 'title' => 'Zu allen Teilnehmenden eine Rückmeldung',
                'done' => $participants > 0 && $withoutPost === 0,
                'detail' => $participants > 0
                    ? ($participants - $withoutPost)." von $participants haben mindestens eine"
                    : 'Erfassen die Leitenden während des Kurses',
                'actions' => [$this->link('Teilnehmende anzeigen', '#teilnehmende', 'secondary')],
            ],
            [
                'key' => 'second-round', 'group' => 'Im Kurs', 'title' => '2. Selbsteinschätzung freigeben',
                'done' => (bool) $camp->secondsurveyopen,
                'detail' => $camp->secondsurveyopen
                    ? 'Freigegeben'
                    : ($surveys->isNotEmpty() ? "$closedFirst von {$surveys->count()} haben die 1. abgeschlossen" : 'Wenn alle die 1. abgeschlossen haben'),
                'actions' => [$this->post('Freigeben', route('admin.camps.opensurvey'))],
            ],
            [
                'key' => 'finish-surveys', 'group' => 'Im Kurs', 'title' => 'Qualifikationen abschliessen',
                'done' => $surveys->isNotEmpty() && $finished === $surveys->count(),
                'detail' => $surveys->isNotEmpty()
                    ? "$finished von {$surveys->count()} abgeschlossen · im Gespräch mit den Leitenden"
                    : 'Im Gespräch mit den Leitenden',
                'actions' => [$this->link('Zur Liste', route('surveys.index'), 'secondary')],
            ],
            [
                'key' => 'finish-camp', 'group' => 'Nach dem Kurs', 'title' => 'Kurs abschliessen',
                'done' => (bool) $camp->finish,
                'detail' => ($endDate ? 'Spätestens '.CampChecks::FINISH_AFTER_MONTHS.' Monate nach dem Schlussdatum. ' : '')
                    .'Löscht Qualifikationen und Dokumente.',
                // Löschen ist endgültig: auf die Kursseite mit der bestehenden Rückfrage, nicht direkt von hier
                'actions' => [$this->link('Kurs abschliessen', route('admin.camps.edit', $camp))],
            ],
        )->map(fn (array $step) => $step + ['optional' => false]);
    }

    /** Wie die Prüfungen in AdminUsersController@import, die hier vorher sichtbar sein sollen. */
    private function canImportFromDb(Camp $camp): bool
    {
        return config('app.import_db') && $camp->foreign_id && $camp->group?->api_token;
    }

    /** Alte Kurse haben teils "0000-00-00" als Schlussdatum, das gilt wie keines (siehe CampChecks). */
    private function endDate(Camp $camp): ?Carbon
    {
        return $camp->end_date && ! str_starts_with($camp->end_date, '0000') ? Carbon::parse($camp->end_date) : null;
    }

    private function link(string $label, string $url, string $variant = 'primary'): array
    {
        return ['label' => $label, 'url' => $url, 'method' => 'get', 'variant' => $variant];
    }

    /** POST an eine bestehende Route mit from=dashboard (zurück auf die Übersicht), optional mit Rückfrage. */
    private function post(string $label, string $url, ?string $confirm = null, ?string $confirmText = null): array
    {
        return array_filter([
            'label' => $label, 'url' => $url, 'method' => 'post', 'variant' => 'primary',
            'confirm' => $confirm, 'confirm_text' => $confirmText,
        ]);
    }
}
