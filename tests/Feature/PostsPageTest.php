<?php

namespace Tests\Feature;

use App\Models\Post;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Seite «Rückmeldungen» (/posts): Knopf «Neue Rückmeldung» rechts im Titel, nicht zugeordnete und zugeordnete
 * Rückmeldungen je als Karte, leere Listen gar nicht, ganz ohne Rückmeldungen ein Hinweis.
 */
class PostsPageTest extends TestCase
{
    private const CARD_TITLE = 'md:text-lg dark:text-white">';

    public function test_both_lists_are_cards_and_button_sits_in_the_title(): void
    {
        $s = CampScenario::create();
        Post::factory()->for($s->gruppenleiter, 'leader')->create(['camp_id' => $s->camp->id, 'camp_user_id' => null, 'comment' => 'Ohne Person']);

        $html = $this->actingAs($s->gruppenleiter)->get('/posts')->assertOk()
            ->assertSee(self::CARD_TITLE.'Nicht zugeordnete Rückmeldungen</h2>', false)
            ->assertSee(self::CARD_TITLE.'Zugeordnete Rückmeldungen</h2>', false)
            ->assertSee('Ohne Person')
            ->assertDontSee('Noch keine Rückmeldungen')
            ->getContent();

        // Knopf im Kopf neben dem Titel, vor den Karten
        $header = substr($html, strpos($html, '<header'), strpos($html, '</header>') - strpos($html, '<header'));
        $this->assertStringContainsString('id="new-feedback-btn"', $header);
    }

    public function test_empty_list_is_hidden_and_without_any_feedback_a_hint_is_shown(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)->get('/posts')->assertOk()
            ->assertDontSee(self::CARD_TITLE.'Nicht zugeordnete Rückmeldungen</h2>', false)
            ->assertSee(self::CARD_TITLE.'Zugeordnete Rückmeldungen</h2>', false);

        $s->post->delete();

        // fresh(): sonst hängen die Rückmeldungen des ersten Aufrufs noch am angemeldeten Benutzer
        $this->actingAs($s->gruppenleiter->fresh())->get('/posts')->assertOk()
            ->assertDontSee(self::CARD_TITLE.'Zugeordnete Rückmeldungen</h2>', false)
            ->assertSee('Noch keine Rückmeldungen');
    }
}
