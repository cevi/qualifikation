<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * admin/run-migrations und admin/run-deployment rufen Artisan auf. Das Deployment migriert im Container selbst.
 */
class MaintenanceRoutesTest extends TestCase
{
    public static function routes(): array
    {
        return [
            'Migrationen' => ['/admin/run-migrations'],
            'Deployment' => ['/admin/run-deployment'],
        ];
    }

    #[DataProvider('routes')]
    public function test_guest_cannot_trigger_artisan(string $uri)
    {
        Artisan::spy();

        $this->get($uri)->assertRedirect();

        Artisan::shouldNotHaveReceived('call');
    }

    #[DataProvider('routes')]
    public function test_kursleiter_cannot_trigger_artisan(string $uri)
    {
        $s = CampScenario::create();
        Artisan::spy();

        $this->actingAs($s->kursleiter)->get($uri)->assertRedirect('/home');

        Artisan::shouldNotHaveReceived('call');
    }
}
