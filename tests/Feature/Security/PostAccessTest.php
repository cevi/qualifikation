<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Rückmeldungen enthalten persönliche Einschätzungen. Bearbeiten und löschen darf sie nur, wer sie geschrieben hat.
 */
class PostAccessTest extends TestCase
{
    public function test_author_can_edit_and_delete_own_post()
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)->get(route('posts.edit', $s->post))->assertOk();
        $this->actingAs($s->gruppenleiter)->delete(route('posts.destroy', $s->post))->assertRedirect();

        $this->assertModelMissing($s->post);
    }

    public function test_other_leader_cannot_open_or_delete_post()
    {
        $s = CampScenario::create();
        $otherLeader = User::factory()->gruppenleiter()->inCamp($s->camp)->create();

        $this->actingAs($otherLeader)->get(route('posts.edit', $s->post))->assertForbidden();
        $this->actingAs($otherLeader)->delete(route('posts.destroy', $s->post))->assertForbidden();

        $this->assertModelExists($s->post);
    }

    public function test_participant_cannot_delete_post_about_them()
    {
        $s = CampScenario::create();

        $this->actingAs($s->participant)->delete(route('posts.destroy', $s->post))->assertForbidden();

        $this->assertModelExists($s->post);
    }

    public function test_other_leader_cannot_overwrite_post_via_profile_form()
    {
        $s = CampScenario::create();
        $otherLeader = User::factory()->gruppenleiter()->inCamp($s->camp)->create();
        $comment = $s->post->comment;

        $this->actingAs($otherLeader)
            ->post(route('profile.post.store', $s->participant), ['post_id' => $s->post->id, 'comment' => 'Überschrieben'])
            ->assertForbidden();

        $post = $s->post->fresh();
        $this->assertSame($comment, $post->comment);
        $this->assertSame($s->gruppenleiter->id, $post->leader_id);
    }

    public function test_author_can_update_post_via_profile_form()
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)
            ->post(route('profile.post.store', $s->participant), ['post_id' => $s->post->id, 'comment' => 'Angepasst'])
            ->assertRedirect(route('home.profile', $s->participant));

        $this->assertSame('Angepasst', $s->post->fresh()->comment);
    }
}
