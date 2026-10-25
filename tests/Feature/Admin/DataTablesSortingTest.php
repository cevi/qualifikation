<?php

namespace Tests\Feature\Admin;

use App\Models\Camp;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Admin-Tabellen (DataTables, serverseitig) sortieren nach dem angezeigten Namen. Yajra sortiert die fertigen Zeilen;
 * Spalten mit Link-HTML würden sonst nach der URL sortieren (Slug bzw. ID). Die Ansicht verweist mit `name:` auf eine
 * Spalte mit dem reinen Namen.
 */
class DataTablesSortingTest extends TestCase
{
    public function test_survey_list_sorts_by_person_name_not_by_profile_link()
    {
        $s = CampScenario::create();
        // Slugs in umgekehrter Reihenfolge der Namen
        $s->participant->update(['username' => 'Zora Muster', 'slug' => '00000000-0000-4000-8000-000000000001']);
        $s->unassignedParticipant->update(['username' => 'Anna Beispiel', 'slug' => 'ffffffff-0000-4000-8000-000000000002']);

        $this->actingAs($s->kursleiter)
            ->get(route('surveys.index'))
            ->assertSee("data: 'user', name: 'user_name'", false);

        $rows = $this->actingAs($s->kursleiter)
            ->getJson(route('surveys.CreateDataTables', $this->orderBy('user', 'user_name')))
            ->assertOk()
            ->json('data');

        $this->assertSame(['Anna Beispiel', 'Zora Muster'], array_map(fn ($row) => strip_tags($row['user']), $rows));
    }

    public function test_camp_list_sorts_by_camp_name_not_by_edit_link()
    {
        $s = CampScenario::create();
        // Zuerst angelegt, also kleinere ID
        Camp::factory()->create(['name' => 'Zeltlager Herbst']);
        Camp::factory()->create(['name' => 'Abendkurs Frühling']);

        $this->actingAs($s->admin)
            ->get(route('admin.camps.index'))
            ->assertSee("data: 'camp', name: 'camp_name'", false);

        $rows = $this->actingAs($s->admin)
            ->getJson(route('camps.CreateDataTables', $this->orderBy('camp', 'camp_name')))
            ->assertOk()
            ->json('data');
        $names = array_map(fn ($row) => strip_tags($row['camp']), $rows);

        $this->assertLessThan(array_search('Zeltlager Herbst', $names), array_search('Abendkurs Frühling', $names));
        $this->assertSame($names, collect($names)->sort(fn ($a, $b) => strnatcasecmp($a, $b))->values()->all());
    }

    /**
     * Anfrage wie DataTables sie schickt: Spalte 0 mit data/name aus der Ansicht, aufsteigend sortiert.
     */
    private function orderBy(string $data, string $name): array
    {
        return [
            'draw' => 1,
            'start' => 0,
            'length' => 25,
            'columns' => [['data' => $data, 'name' => $name, 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '']]],
            'order' => [['column' => 0, 'dir' => 'asc']],
            'search' => ['value' => ''],
        ];
    }
}
