<?php

namespace Tests\Unit;

use App\Enums\Classification;
use App\Enums\Role;
use App\Models\Classification as ClassificationModel;
use App\Models\Role as RoleModel;
use Tests\TestCase;

class RoleAndClassificationTest extends TestCase
{
    public function test_role_enum_matches_seeded_roles()
    {
        $this->assertSame(
            array_map(fn (Role $role) => $role->value, Role::cases()),
            RoleModel::orderBy('id')->pluck('id')->all()
        );
    }

    public function test_role_rights_match_seeded_flags()
    {
        $flags = RoleModel::all()->keyBy('id');

        $this->assertTrue((bool) $flags[Role::Administrator->value]->is_admin);
        $this->assertTrue((bool) $flags[Role::Kursleiter->value]->is_campleader);
        $this->assertTrue((bool) $flags[Role::Gruppenleiter->value]->is_leader);
        $this->assertTrue((bool) $flags[Role::Stabsleiter->value]->is_leader);
        $this->assertFalse((bool) $flags[Role::Teilnehmer->value]->is_leader);
    }

    public function test_classification_enum_matches_seeded_classifications()
    {
        $this->assertSame(
            array_map(fn (Classification $classification) => $classification->value, Classification::cases()),
            ClassificationModel::orderBy('id')->pluck('id')->all()
        );
    }

    public function test_classification_css_classes()
    {
        $this->assertSame(['red', 'yellow', 'green'], array_map(fn (Classification $c) => $c->cssClass(), Classification::cases()));
    }
}
