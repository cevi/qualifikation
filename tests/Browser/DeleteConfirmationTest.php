<?php

namespace Tests\Browser;

use App\Models\Camp;
use App\Models\CampUser;
use App\Models\Group;
use App\Models\Post;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * Löschen-Knöpfe fragen mit SweetAlert2 nach. "Abbrechen" darf nichts löschen, "Ja" löscht genau den gewählten Eintrag.
 * Ebenso das Abschliessen der Selbsteinschätzung.
 */
class DeleteConfirmationTest extends DuskTestCase
{
    public function test_person_is_removed_from_camp_only_after_confirming(): void
    {
        $s = CampScenario::create();
        $user = $s->unassignedParticipant;

        $this->browse(function (Browser $browser) use ($s, $user) {
            $browser->loginAs($s->kursleiter)->visit(route('admin.users.edit', $user->slug));

            $this->cancelDialog($browser, 'form#DeleteForm .confirm');
            $this->assertTrue(CampUser::where('camp_id', $s->camp->id)->where('user_id', $user->id)->exists());

            $this->confirmDialog($browser, 'form#DeleteForm .confirm');
            $browser->waitForLocation('/admin/users');
            $this->assertFalse(CampUser::where('camp_id', $s->camp->id)->where('user_id', $user->id)->exists());
        });
    }

    public function test_camp_is_finished_only_after_confirming(): void
    {
        $s = CampScenario::create();
        // Abschliessen löscht storage/app/{public,files}/<id>_<name>; der Name darf keinen echten Ordner treffen.
        $s->camp->update(['name' => 'Dusk Testkurs']);

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->kursleiter)->visit(route('admin.camps.edit', $s->camp));

            $this->cancelDialog($browser, 'form#DeleteForm .confirm');
            $this->assertFalse((bool) Camp::findOrFail($s->camp->id)->finish);

            $this->confirmDialog($browser, 'form#DeleteForm .confirm');
            $browser->waitForLocation('/home');
            $this->assertTrue((bool) Camp::findOrFail($s->camp->id)->finish);
        });
    }

    public function test_group_is_deleted_only_after_confirming(): void
    {
        $s = CampScenario::create();
        $group = Group::factory()->create();

        $this->browse(function (Browser $browser) use ($s, $group) {
            $browser->loginAs($s->admin)->visit(route('groups.edit', $group));

            $this->cancelDialog($browser, 'form#DeleteForm .confirm');
            $this->assertModelExists($group);

            $this->confirmDialog($browser, 'form#DeleteForm .confirm');
            $browser->waitForLocation('/admin/groups');
            $this->assertModelMissing($group);
        });
    }

    /**
     * Jede Rückmeldung hat ihr eigenes Löschen-Formular; gelöscht wird die, deren Knopf geklickt wurde.
     */
    public function test_deleting_a_post_deletes_exactly_that_post(): void
    {
        $s = CampScenario::create();
        $first = $s->post;
        $second = Post::factory()
            ->for($s->campUser($s->participant), 'campUser')
            ->for($s->gruppenleiter, 'leader')
            ->create(['camp_id' => $s->camp->id]);

        $this->browse(function (Browser $browser) use ($s, $second) {
            $browser->loginAs($s->gruppenleiter)->visit(route('home.profile', $s->participant));
            $button = 'form[action$="/posts/'.$second->id.'"] .confirm';

            $this->cancelDialog($browser, $button);
            $this->assertModelExists($second);

            // Nach dem Löschen lädt die Seite neu und zeigt nur noch eine Rückmeldung.
            $this->confirmDialog($browser, $button);
            $browser->waitUntil('document.querySelectorAll(\'form[action*="/posts/"] .confirm\').length === 1');
        });

        $this->assertModelMissing($second);
        $this->assertModelExists($first);
    }

    /** Auch das Abschliessen der Selbsteinschätzung fragt nach und schickt dann action=close mit. */
    public function test_self_assessment_is_closed_only_after_confirming(): void
    {
        $s = CampScenario::create();
        $button = 'button[name="action"][value="close"]';

        $this->browse(function (Browser $browser) use ($s, $button) {
            $browser->loginAs($s->participant)->visit(route('survey.survey', $s->survey->slug));

            $this->cancelDialog($browser, $button);
            $this->assertFalse($s->survey->fresh()->status()->isClosedByParticipant());

            $this->confirmDialog($browser, $button);
            $browser->waitUsing(10, 100, fn () => $s->survey->fresh()->status()->isClosedByParticipant());
        });
    }

    private function cancelDialog(Browser $browser, string $button): void
    {
        $this->clickCentered($browser, $button);
        $browser->waitFor('.swal2-popup')
            ->click('.swal2-cancel')
            ->waitUntilMissing('.swal2-popup');
    }

    private function confirmDialog(Browser $browser, string $button): void
    {
        $this->clickCentered($browser, $button);
        $browser->waitFor('.swal2-popup')->click('.swal2-confirm');
    }
}
