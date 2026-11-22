<?php

namespace Tests\Feature\Admin;

use App\Enums\SurveyStatus;
use App\Models\Camp;
use App\Models\Classification;
use App\Models\SurveyStatus as SurveyStatusModel;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Zeilen der Admin-Tabellen (Personen, Qualifikationen, Kurse) als JSON für components/DataTable.vue.
 * Gesucht, sortiert und geblättert wird im Browser (tests/js/data-table.test.js).
 */
class AdminTablesTest extends TestCase
{
    public function test_user_rows_show_role_and_classification_of_current_camp()
    {
        $s = CampScenario::create();
        $classification = Classification::firstOrFail();
        $campUser = $s->campUser($s->participant);
        $campUser->update(['classification_id' => $classification->id]);
        $s->participant->forceFill(['last_login_at' => '2026-03-05 18:07:00', 'password_change_at' => now()])->save();

        $row = $this->rows($s->kursleiter, route('users.table'))->firstWhere('id', $s->participant->id);

        $this->assertSame($s->participant->username, $row['name']);
        $this->assertSame(route('admin.users.edit', $s->participant->slug), $row['edit_url']);
        $this->assertSame(route('home.profile', $s->participant->slug), $row['profile_url']);
        $this->assertSame($campUser->role->name, $row['role']);
        $this->assertSame($classification->name, $row['classification']);
        $this->assertSame($s->gruppenleiter->username, $row['leader']);
        $this->assertSame($s->group->shortname, $row['group']);
        $this->assertSame('Ja', $row['password_changed']);
        $this->assertSame(['display' => '05.03.2026 18:07', 'sort' => '2026-03-05 18:07:00'], $row['last_login']);
    }

    public function test_user_rows_contain_only_people_of_current_camp_without_admins()
    {
        $s = CampScenario::create();
        CampScenario::create();

        $ids = $this->rows($s->kursleiter, route('users.table'))->pluck('id')->all();

        $this->assertEqualsCanonicalizing(
            [$s->kursleiter->id, $s->gruppenleiter->id, $s->participant->id, $s->unassignedParticipant->id],
            $ids,
        );
    }

    public function test_survey_rows_show_status_with_steps_without_second_round_when_not_opened()
    {
        $s = CampScenario::create();
        $s->camp->update(['secondsurveyopen' => false]);
        $s->survey->update(['survey_status_id' => SurveyStatus::ErsteRundeOffen]);
        $names = SurveyStatusModel::pluck('name', 'id');

        $row = $this->rows($s->kursleiter, route('surveys.table'))->firstWhere('id', $s->survey->id);

        // Schritte Neu, 1. offen, TN abgeschlossen, Fertig: der zweite ist erreicht
        $this->assertSame([
            'display' => $names[SurveyStatus::ErsteRundeOffen->value],
            'sort' => SurveyStatus::ErsteRundeOffen->value,
            'tone' => 'blue',
            'step' => 2,
            'steps' => 4,
        ], $row['status']);
        $this->assertSame('offen', $row['phase']);
        $this->assertSame($s->participant->username, $row['name']);
        $this->assertSame($s->gruppenleiter->username, $row['leader']);
        $this->assertSame([
            ['label' => 'Zur Qualifikation', 'url' => route('survey.compare', $s->survey->slug)],
            ['label' => 'Bearbeiten', 'url' => route('surveys.edit', $s->survey->slug)],
        ], $row['actions']);
    }

    public function test_survey_rows_include_second_round_step_once_opened()
    {
        $s = CampScenario::create();
        $s->camp->update(['secondsurveyopen' => true]);

        $row = $this->rows($s->kursleiter, route('surveys.table'))->firstWhere('id', $s->survey->id);

        $this->assertSame(count(SurveyStatus::cases()), $row['status']['steps']);
        $this->assertSame(1, $row['status']['step']);
        $this->assertSame('gray', $row['status']['tone']);
    }

    public function test_camp_rows_show_only_own_camp_with_actions_for_course_leader()
    {
        $s = CampScenario::create();
        Camp::factory()->create(['name' => 'Fremder Kurs']);
        $s->camp->update(['end_date' => '2026-03-05']);

        $rows = $this->rows($s->kursleiter, route('camps.table'));

        $this->assertSame([$s->camp->id], $rows->pluck('id')->all());
        $this->assertSame(['display' => '05.03.2026', 'sort' => '2026-03-05'], $rows[0]['end_date']);
        $this->assertSame(['Rückmeldungen exportieren', 'Qualifikationen drucken', 'Kurs abschliessen'], array_column($rows[0]['actions'], 'label'));
    }

    public function test_camp_rows_show_all_camps_with_actions_for_admin()
    {
        $s = CampScenario::create();
        $other = Camp::factory()->finished()->create(['name' => 'Fremder Kurs']);

        $row = $this->rows($s->admin, route('camps.table'))->firstWhere('id', $other->id);

        $this->assertSame('Fremder Kurs', $row['name']);
        $this->assertSame('Ja', $row['finish']);
        // Abgeschlossene Kurse haben keine Rückmeldungen und Qualis mehr
        $this->assertSame([], $row['actions']);

        $open = $this->rows($s->admin, route('camps.table'))->firstWhere('id', $s->camp->id);
        $this->assertSame(['Rückmeldungen exportieren', 'Qualifikationen drucken', 'Kurs abschliessen'], array_column($open['actions'], 'label'));
    }

    public function test_index_pages_mount_the_table_with_its_data_url()
    {
        $s = CampScenario::create();

        foreach (['admin.users.index' => 'users.table', 'surveys.index' => 'surveys.table'] as $page => $data) {
            $this->assertSame(route($data), $this->tableProps($s->kursleiter, route($page))['url'], $page);
        }
        $this->assertSame(route('camps.table'), $this->tableProps($s->admin, route('admin.camps.index'))['url']);
    }

    private function rows($user, string $url)
    {
        return collect($this->actingAs($user)->getJson($url)->assertOk()->json('data'));
    }

    private function tableProps($user, string $url): array
    {
        $html = $this->actingAs($user)->get($url)->assertOk()->getContent();
        $this->assertSame(1, preg_match('/data-vue-component="data-table" data-props="([^"]+)"/', $html, $match), $url);

        return json_decode(html_entity_decode($match[1]), true);
    }
}
