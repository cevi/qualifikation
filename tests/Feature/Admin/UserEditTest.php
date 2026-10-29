<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Person bearbeiten im Admin-Bereich.
 */
class UserEditTest extends TestCase
{
    public function test_form_preselects_current_role()
    {
        // Sonst würde Speichern ohne Änderung still eine andere Rolle setzen.
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)
            ->get(route('admin.users.edit', $s->gruppenleiter))
            ->assertOk()
            ->assertSee('<option value="'.Role::Gruppenleiter->value.'" selected="selected">', false);
    }
}
