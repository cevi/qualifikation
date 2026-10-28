<?php

namespace Tests\Browser;

use App\Enums\Role;
use App\Models\CampUser;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * Person hinzufügen: Vorschläge über <datalist> aus der Personensuche, die Wahl setzt das versteckte Feld user_id.
 * Die Logik prüft tests/js/user-search.test.js.
 */
class UserSearchTest extends DuskTestCase
{
    public function test_existing_person_is_added_to_camp_via_suggestion(): void
    {
        $s = CampScenario::create();
        $outsider = User::factory()->create(['username' => 'Zora Aussen', 'email' => 'zora@example.com']);
        $suggestion = 'Zora Aussen - zora@example.com';
        $input = 'input[list="user-suggestions"]';

        $this->browse(function (Browser $browser) use ($s, $outsider, $suggestion, $input) {
            $browser->loginAs($s->kursleiter)->visit(route('admin.users.create'))
                ->type($input, 'Zora')
                ->waitUntil("document.querySelector('#user-suggestions option[value=\"{$suggestion}\"]') !== null");

            // Wie die Wahl aus der Liste: der Browser ersetzt den Text auf einmal. (type() tippt Zeichen für Zeichen und
            // kommt dabei an "Z", "Zo" vorbei, das leert die Vorschläge.) Die Liste selbst lässt sich headless nicht öffnen.
            $browser->script("const input = document.querySelector('{$input}');
                input.value = '{$suggestion}';
                input.dispatchEvent(new InputEvent('input', { inputType: 'insertReplacementText' }));");
            $browser->assertInputValue($input, 'Zora Aussen')
                ->assertScript("document.querySelector('{$input}').form.elements.user_id.value", (string) $outsider->id);

            $browser->select('role_id', (string) Role::Teilnehmer->value)
                ->press('Person hinzufügen')
                ->waitForLocation('/admin/users/create');
        });

        $this->assertTrue(CampUser::where('camp_id', $s->camp->id)->where('user_id', $outsider->id)->exists());
    }
}
