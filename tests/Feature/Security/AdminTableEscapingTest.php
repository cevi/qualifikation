<?php

namespace Tests\Feature\Security;

use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Die Admin-Tabellen (Vue, components/DataTable.vue) bekommen nur Text und URLs und setzen sie als Text bzw. Attribut
 * ein. Namen stammen aus der öffentlichen Registrierung, dem Spitznamen in der Cevi-DB oder vom Kursleiter
 * (Kursname) und dürfen keinen Code einschleusen.
 */
class AdminTableEscapingTest extends TestCase
{
    // Zielt auf Text zwischen Tags, unquotierte und quotierte Attribute.
    private const PAYLOAD = 'Max onmouseover=alert(1) "><img src=x onerror=alert(1)>';

    public function test_user_list_returns_names_unchanged_as_text()
    {
        $s = CampScenario::create();
        $s->participant->update(['username' => self::PAYLOAD]);
        $s->gruppenleiter->update(['username' => self::PAYLOAD]);

        $row = $this->row($s, route('users.table'), $s->participant->id);

        $this->assertSame(self::PAYLOAD, $row['name']);
        $this->assertSame(self::PAYLOAD, $row['leader']);
    }

    public function test_user_list_returns_avatar_url_unchanged()
    {
        $s = CampScenario::create();
        $s->participant->update(['avatar' => 'https://db.cevi.ch/x.png" onerror="alert(1)']);

        $row = $this->row($s, route('users.table'), $s->participant->id);

        $this->assertSame('https://db.cevi.ch/x.png" onerror="alert(1)', $row['avatar']);
    }

    public function test_survey_list_returns_names_unchanged_as_text()
    {
        $s = CampScenario::create();
        $s->participant->update(['username' => self::PAYLOAD]);
        $s->gruppenleiter->update(['username' => self::PAYLOAD]);

        $row = $this->row($s, route('surveys.table'), $s->survey->id);

        $this->assertSame(self::PAYLOAD, $row['name']);
        $this->assertSame(self::PAYLOAD, $row['leader']);
    }

    public function test_camp_list_returns_names_unchanged_as_text()
    {
        $s = CampScenario::create();
        $s->camp->update(['name' => self::PAYLOAD]);
        $s->kursleiter->update(['username' => self::PAYLOAD]);

        $row = collect($this->actingAs($s->admin->fresh())->getJson(route('camps.table'))->assertOk()->json('data'))
            ->firstWhere('id', $s->camp->id);

        $this->assertSame(self::PAYLOAD, $row['name']);
        $this->assertSame(self::PAYLOAD, $row['username']);
    }

    /**
     * Die Tabelle baut Links, Bilder und Knöpfe mit Vue-Render-Funktionen (Text und Attribute werden escaped).
     * Kein v-html und kein innerHTML, damit Daten nie als HTML gelesen werden.
     */
    public function test_table_components_never_insert_html()
    {
        $files = [
            ...glob(resource_path('js/components/*.{vue,js}'), GLOB_BRACE),
            resource_path('js/libs/table-data.js'),
            resource_path('js/islands.js'),
        ];
        $this->assertContains(resource_path('js/components/DataTable.vue'), $files);

        foreach ($files as $file) {
            $code = file_get_contents($file);
            foreach (['v-html', 'innerHTML', 'outerHTML', 'insertAdjacentHTML'] as $sink) {
                $this->assertStringNotContainsString($sink, $code, basename($file)." enthält {$sink}");
            }
        }
    }

    private function row(CampScenario $s, string $url, int $id): array
    {
        return collect($this->actingAs($s->kursleiter->fresh())->getJson($url)->assertOk()->json('data'))->firstWhere('id', $id);
    }
}
