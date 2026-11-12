<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Answer;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * x-forms-Komponenten verhalten sich wie Form:: (laravelcollective), das sie ersetzen: Wert aus alter Eingabe, sonst
 * value, sonst Modell; Passwörter leer; Labels zeigen auf die ID; Werte immer maskiert.
 */
class FormComponentsTest extends TestCase
{
    public function test_text_takes_old_input_then_value_then_model(): void
    {
        $model = ['name' => 'Aus dem Modell'];

        $this->assertStringContainsString('value="Aus dem Modell"', $this->render('<x-forms.form :model="$model"><x-forms.text name="name"/></x-forms.form>', compact('model')));
        $this->assertStringContainsString('value="Gesetzt"', $this->render('<x-forms.form :model="$model"><x-forms.text name="name" value="Gesetzt"/></x-forms.form>', compact('model')));

        $this->withOldInput(['name' => 'Alte Eingabe']);
        $this->assertStringContainsString('value="Alte Eingabe"', $this->render('<x-forms.form :model="$model"><x-forms.text name="name" value="Gesetzt"/></x-forms.form>', compact('model')));
    }

    public function test_values_are_escaped(): void
    {
        $model = ['name' => '"><script>alert(1)</script>', 'comment' => '</textarea><b>x</b>'];

        $html = $this->render('<x-forms.form :model="$model"><x-forms.text name="name"/><x-forms.text-area name="comment"/></x-forms.form>', compact('model'));

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<b>x</b>', $html);
        $this->assertStringContainsString('&lt;/textarea&gt;', $html);
    }

    public function test_password_is_never_filled(): void
    {
        $model = ['api_token' => 'geheim'];
        $this->withOldInput(['api_token' => 'auch-geheim']);

        $html = $this->render('<x-forms.form :model="$model"><x-forms.text type="password" name="api_token" label="Token:"/></x-forms.form>', compact('model'));

        $this->assertStringNotContainsString('geheim', $html);
        $this->assertMatchesRegularExpression('/<input type="password" name="api_token" id="api_token"\s+class="field-input"\s*>/', $html);
    }

    public function test_select_with_placeholder_model_enum_and_old_input(): void
    {
        $roles = [2 => 'Kursleitung', 4 => 'Teilnehmende'];
        $model = ['role_id' => Role::Teilnehmer];
        $view = '<x-forms.form :model="$model"><x-forms.select name="role_id" id="search_role_id" label="Rolle:" :collection="$roles" placeholder="Wähle Rolle"/></x-forms.form>';

        $html = $this->render($view, compact('roles', 'model'));
        $this->assertStringContainsString('<label for="search_role_id"', $html);
        $this->assertMatchesRegularExpression('/<option value=""\s*>Wähle Rolle<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option value="4"\s+selected[^>]*>Teilnehmende/', $html);

        $this->withOldInput(['role_id' => '2']);
        $this->assertMatchesRegularExpression('/<option value="2"\s+selected[^>]*>Kursleitung/', $this->render($view, compact('roles', 'model')));
    }

    public function test_textarea_has_own_id_and_old_input(): void
    {
        $this->withOldInput(['content' => 'Alter Text']);

        $html = $this->render('<x-forms.text-area name="content" id="content_new" label="Inhalt:" rows="10"/>');

        $this->assertStringContainsString('<label for="content_new"', $html);
        $this->assertMatchesRegularExpression('/<textarea name="content" id="content_new"[^>]*rows="10"[^>]*>Alter Text<\/textarea>/', $html);
    }

    public function test_checkbox_value_and_checked_are_separate(): void
    {
        $this->assertMatchesRegularExpression('/value="1"[^>]*checked/', $this->render('<x-forms.checkbox name="bug" :checked="true"/>'));
        $this->assertDoesNotMatchRegularExpression('/checked/', $this->render('<x-forms.checkbox name="bug"/>'));

        $model = ['status_control' => true];
        $this->assertMatchesRegularExpression('/checked/', $this->render('<x-forms.form :model="$model"><x-forms.checkbox name="status_control"/></x-forms.form>', compact('model')));

        // Abgeschickt ohne Haken: alte Eingabe zählt, nicht das Modell
        $this->withOldInput(['name' => 'x']);
        $this->assertDoesNotMatchRegularExpression('/checked/', $this->render('<x-forms.form :model="$model"><x-forms.checkbox name="status_control"/></x-forms.form>', compact('model')));
    }

    public function test_checkbox_label_is_escaped(): void
    {
        $html = $this->render('<x-forms.checkbox name="delete_file" :label="$label"/>', ['label' => 'Datei <img src=x> löschen']);

        $this->assertStringContainsString('Datei &lt;img src=x&gt; löschen', $html);
    }

    public function test_radio_checked_by_model_prop_or_old_input(): void
    {
        $model = ['answers' => [12 => 3]];
        $view = '<x-forms.form :model="$model"><x-forms.radio name="answers[12]" value="3" id="a3"/><x-forms.radio name="answers[12]" value="4" id="a4"/></x-forms.form>';

        $this->assertMatchesRegularExpression('/id="a3"[^>]*value="3"[^>]*checked/', $this->render($view, compact('model')));
        $this->assertMatchesRegularExpression('/id="a4"[^>]*checked/', $this->render('<x-forms.radio name="answers[12]" value="4" id="a4" :checked="true"/>'));

        $this->withOldInput(['answers' => [12 => '4']]);
        $html = $this->render($view, compact('model'));
        $this->assertMatchesRegularExpression('/id="a4"[^>]*checked/', $html);
        $this->assertDoesNotMatchRegularExpression('/id="a3"[^>]*checked/', $html);
    }

    public function test_form_spoofs_method_and_adds_csrf(): void
    {
        $html = $this->render('<x-forms.form method="PATCH" action="/x" enctype="multipart/form-data"><x-forms.button type="submit">Speichern</x-forms.button></x-forms.form>');

        $this->assertMatchesRegularExpression('/<form method="POST"\s+action="\/x"\s+enctype="multipart\/form-data"\s*>/', $html);
        $this->assertStringContainsString('name="_method" value="PATCH"', $html);
        $this->assertStringContainsString('name="_token"', $html);
    }

    /** Ganzer Ablauf: Person erstellen mit ungültiger E-Mail, zurück auf dem Formular sind die Eingaben noch da. */
    public function test_person_form_keeps_input_after_validation_error(): void
    {
        $s = CampScenario::create();

        $this->actingAs($s->kursleiter)->from(route('admin.users.create'))->post(route('admin.users.store'), [
            'username' => 'Neue Person', 'email' => 'keine-adresse', 'role_id' => Role::Teilnehmer->value,
            'group_id' => $s->group->id, 'password' => 'geheim-123',
        ])->assertRedirect(route('admin.users.create'))->assertSessionHasErrors('email');

        $html = $this->get(route('admin.users.create'))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/name="email" id="email"\s+value="keine-adresse"/', $html);
        $this->assertMatchesRegularExpression('/<option value="'.$s->group->id.'"\s+selected/', $html);
        $this->assertStringNotContainsString('geheim-123', $html);
    }

    /** Quali: Label der Antwort zeigt auf ihren Radio-Knopf (mit Form::label zeigte es ins Leere), ohne Antwort ist «0» gewählt. */
    public function test_survey_answer_labels_point_to_their_radio(): void
    {
        $s = CampScenario::create();
        $question = $s->survey->chapters->first()->questions->first();
        $zero = Answer::where('name', '0')->firstOrFail();

        foreach ([[$s->participant, 1], [$s->gruppenleiter, 3]] as [$user, $round]) {
            $id = $question->question['id'].'.'.$round.'.'.$question->question['number'].$zero->id;
            $html = $this->actingAs($user)->get(route('survey.survey', $s->survey->slug))->assertOk()->getContent();

            $this->assertMatchesRegularExpression('/<input name="answers\['.$question->id.'\]" id="'.preg_quote($id, '/').'" type="radio" value="'.$zero->id.'"[^>]*checked/', $html);
            $this->assertStringContainsString('<label for="'.$id.'"', $html);
            $this->assertStringContainsString('name="comments['.$question->id.']"', $html);
        }
    }

    private function withOldInput(array $input): void
    {
        $session = $this->app['session.store'];
        $session->put('_old_input', $input);
        $this->app['request']->setLaravelSession($session);
    }

    private function render(string $view, array $data = []): string
    {
        return (string) $this->blade($view, $data);
    }
}
