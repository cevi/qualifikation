<?php

namespace Tests\Feature;

use Tests\Support\CampScenario;
use Tests\TestCase;

class SurveyAccessTest extends TestCase
{
    public function test_leader_is_redirected_from_survey_of_participant_without_leader()
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)
            ->get(route('survey.compare', $s->unassignedSurvey))
            ->assertRedirect('/home');
        $this->actingAs($s->gruppenleiter)
            ->get(route('survey.survey', $s->unassignedSurvey))
            ->assertRedirect('/home');
    }

    public function test_leader_has_access_to_survey_of_own_participant()
    {
        $s = CampScenario::create();

        $this->actingAs($s->gruppenleiter)
            ->get(route('survey.compare', $s->survey))
            ->assertOk();
    }

    public function test_access_does_not_depend_on_loaded_relations_of_the_camp()
    {
        $s = CampScenario::create();
        $this->actingAs($s->gruppenleiter);

        $s->gruppenleiter->camp->load('camp_type');

        $this->assertTrue($s->survey->SurveyIsAllowed());
    }
}
