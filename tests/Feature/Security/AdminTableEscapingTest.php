<?php

namespace Tests\Feature\Security;

use Dom\HTMLDocument;
use Dom\HTMLElement;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Die Admin-Tabellen (DataTables) setzen Zellen als HTML ein. Namen stammen aus der öffentlichen Registrierung,
 * dem Spitznamen in der Cevi-DB oder vom Kursleiter (Kursname) und dürfen dort keinen Code einschleusen.
 */
class AdminTableEscapingTest extends TestCase
{
    // Zielt auf Text zwischen Tags, unquotierte und quotierte Attribute.
    private const PAYLOAD = 'Max onmouseover=alert(1) "><img src=x onerror=alert(1)>';

    public function test_user_list_shows_names_as_text()
    {
        $s = CampScenario::create();
        $s->participant->update(['username' => self::PAYLOAD]);
        $s->gruppenleiter->update(['username' => self::PAYLOAD]);

        $rows = $this->actingAs($s->kursleiter->fresh())
            ->getJson(route('users.CreateDataTables'))
            ->assertOk()
            ->json('data');
        $row = collect($rows)->firstWhere('email', $s->participant->email);

        $this->assertRendersAsText(self::PAYLOAD, $row['user']);
        $this->assertRendersAsText(self::PAYLOAD, $row['leader']);
    }

    public function test_user_list_keeps_avatar_url_inside_src()
    {
        $s = CampScenario::create();
        $s->participant->update(['avatar' => 'https://db.cevi.ch/x.png" onerror="alert(1)']);

        $rows = $this->actingAs($s->kursleiter->fresh())
            ->getJson(route('users.CreateDataTables'))
            ->json('data');
        $row = collect($rows)->firstWhere('email', $s->participant->email);

        $img = $this->parse($row['picture'])->querySelector('img');
        $this->assertSame(['class', 'alt', 'src'], array_map(fn ($a) => $a->name, iterator_to_array($img->attributes, false)));
        $this->assertSame('https://db.cevi.ch/x.png" onerror="alert(1)', $img->getAttribute('src'));
    }

    public function test_survey_list_shows_names_as_text()
    {
        $s = CampScenario::create();
        $s->participant->update(['username' => self::PAYLOAD]);
        $s->gruppenleiter->update(['username' => self::PAYLOAD]);

        $rows = collect($this->actingAs($s->kursleiter->fresh())
            ->getJson(route('surveys.CreateDataTables'))
            ->assertOk()
            ->json('data'));
        $row = $rows->first(fn ($r) => str_contains($r['Actions'], $s->survey->slug));

        $this->assertRendersAsText(self::PAYLOAD, $row['user']);
        $this->assertRendersAsText(self::PAYLOAD, $row['responsible']);
    }

    public function test_camp_list_shows_camp_name_as_text()
    {
        $s = CampScenario::create();
        $s->camp->update(['name' => self::PAYLOAD]);
        $s->kursleiter->update(['username' => self::PAYLOAD]);

        $rows = collect($this->actingAs($s->admin->fresh())
            ->getJson(route('camps.CreateDataTables'))
            ->assertOk()
            ->json('data'));
        $row = $rows->firstWhere('id', $s->camp->id);

        $this->assertRendersAsText(self::PAYLOAD, $row['camp']);
        $this->assertRendersAsText(self::PAYLOAD, $row['username']);
    }

    /**
     * Die Zelle darf nur aus Text und einem Link bestehen, ohne Event-Attribute.
     */
    private function assertRendersAsText(string $expected, string $html): void
    {
        $body = $this->parse($html);

        foreach ($body->querySelectorAll('*') as $element) {
            $this->assertSame('A', $element->tagName, 'Unerwartetes Element in: '.$html);
            foreach ($element->attributes as $attribute) {
                $this->assertContains($attribute->name, ['name', 'title', 'href', 'class'], 'Unerwartetes Attribut in: '.$html);
            }
        }
        $this->assertSame($expected, trim($body->textContent));
    }

    private function parse(string $html): HTMLElement
    {
        return HTMLDocument::createFromString('<!DOCTYPE html><body>'.$html.'</body>', LIBXML_NOERROR)->body;
    }
}
