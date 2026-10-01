<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Tests\TestCase;

class GroupsTest extends TestCase
{
    public function test_admin_can_create_group_without_cevi_db_id()
    {
        $this->actingAs(User::findOrFail(1))
            ->post(route('groups.store'), ['name' => 'Cevi Testdorf', 'shortname' => 'TD'])
            ->assertRedirect('admin/groups');

        $this->assertDatabaseHas('groups', ['name' => 'Cevi Testdorf', 'foreign_id' => null]);
    }
}
