<?php

namespace Tests\Feature\Smoke;

use App\Models\User;
use Database\Seeders\DemoCampSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Die Demo-Logins auf der Startseite sind öffentlich und loggen ohne Passwort ein.
 */
class DemoLoginTest extends TestCase
{
    public static function demoLogins(): array
    {
        return [
            'Teilnehmer' => ['/loginTN', 'tn11@demo'],
            'Leiter' => ['/loginLeiter', 'leiter1@demo'],
            'Kursleiter' => ['/loginKursleiter', 'kursleiter@demo'],
        ];
    }

    #[DataProvider('demoLogins')]
    public function test_demo_login_leads_to_home(string $uri, string $username)
    {
        $this->seed(DemoCampSeeder::class);

        $this->get($uri)->assertRedirect('home');
        $this->assertAuthenticatedAs(User::where('username', $username)->first());

        $this->get('/home')->assertOk();
    }

    #[DataProvider('demoLogins')]
    public function test_demo_login_only_logs_in_demo_users(string $uri, string $username)
    {
        $this->seed(DemoCampSeeder::class);

        $this->get($uri);

        $this->assertTrue(auth()->user()->demo, "$username ist nicht als Demo-User markiert und hat damit volle Schreibrechte.");
    }
}
