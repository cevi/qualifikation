<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Login und Registrieren zeigen Fehler unter dem Feld (bis Oktober 2026 über Bootstraps is-invalid/invalid-feedback,
 * jetzt components/auth/field und field-error).
 */
class AuthPagesTest extends TestCase
{
    public function test_wrong_password_shows_message_and_keeps_email()
    {
        $user = User::factory()->create(['email' => 'anna@example.com']);

        $this->from('/login')
            ->followingRedirects()
            ->post('/login', ['email' => $user->email, 'password' => 'falsch'])
            ->assertSee(__('auth.failed'))
            ->assertSee('value="anna@example.com"', false);
    }

    public function test_hitobito_error_is_shown_on_login_page()
    {
        $errors = (new ViewErrorBag)->put('default', new MessageBag(['hitobito' => 'Anmeldung über die Cevi-DB fehlgeschlagen.']));

        $this->withSession(['errors' => $errors])
            ->get('/login')
            ->assertOk()
            ->assertSee('Anmeldung über die Cevi-DB fehlgeschlagen.');
    }

    public function test_register_shows_field_errors_and_keeps_name_but_not_password()
    {
        $existing = User::factory()->create();

        $this->from('/register')
            ->followingRedirects()
            ->post('/register', [
                'username' => 'Neue Person',
                'email' => $existing->email,
                'password' => 'geheim123',
                'password_confirmation' => 'anders',
            ])
            ->assertSee('value="Neue Person"', false)
            ->assertDontSee('geheim123')
            ->assertSee('field-error', false);
    }

    public function test_password_reset_link_is_sent_only_to_known_addresses()
    {
        Notification::fake();
        $s = CampScenario::create();

        $this->from('/password/reset')->post(route('password.email'), ['email' => $s->participant->email])
            ->assertRedirect('/password/reset')
            ->assertSessionHas('status');
        Notification::assertSentTo($s->participant, ResetPassword::class);

        $this->from('/password/reset')->post(route('password.email'), ['email' => 'niemand@example.com'])
            ->assertSessionHasErrors('email');
        Notification::assertSentTimes(ResetPassword::class, 1);
    }
}
