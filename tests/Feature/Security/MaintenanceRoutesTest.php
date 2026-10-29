<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Wartungsseite (MaintenanceController): nur Admins, Aktionen nur per POST. Artisan ist in jedem Test gefakt, bevor
 * eine Anfrage läuft, damit ein noch roter Test nie wirklich migriert.
 */
class MaintenanceRoutesTest extends TestCase
{
    public static function actions(): array
    {
        return [
            'Migrationen' => ['admin.maintenance.migrate'],
            'Cache leeren' => ['admin.maintenance.clear-cache'],
        ];
    }

    #[DataProvider('actions')]
    public function test_guest_cannot_trigger_artisan(string $route)
    {
        Artisan::shouldReceive('call')->never();

        $this->get(route('admin.maintenance'))->assertRedirect();
        $this->post(route($route))->assertRedirect();
    }

    #[DataProvider('actions')]
    public function test_kursleiter_cannot_trigger_artisan(string $route)
    {
        $s = CampScenario::create();
        Artisan::shouldReceive('call')->never();

        $this->actingAs($s->kursleiter)->get(route('admin.maintenance'))->assertRedirect('/home');
        $this->actingAs($s->kursleiter)->post(route($route))->assertRedirect('/home');
    }

    #[DataProvider('actions')]
    public function test_actions_do_not_run_via_get(string $route)
    {
        $s = CampScenario::create();
        Artisan::shouldReceive('call')->never();

        $this->actingAs($s->admin)->get(route($route))->assertMethodNotAllowed();
    }

    public function test_old_get_urls_are_gone()
    {
        $s = CampScenario::create();
        Artisan::shouldReceive('call')->never();

        $this->actingAs($s->admin)->get('/admin/run-migrations')->assertNotFound();
        $this->actingAs($s->admin)->get('/admin/run-deployment')->assertNotFound();
    }

    public function test_page_lists_pending_migrations()
    {
        $s = CampScenario::create();

        $this->actingAs($s->admin)->get(route('admin.maintenance'))
            ->assertOk()
            ->assertSee('Alle Migrationen sind ausgeführt.');

        DB::table('migrations')->where('migration', '2026_10_28_000000_rename_help_titles')->delete();

        $this->actingAs($s->admin)->get(route('admin.maintenance'))
            ->assertOk()
            ->assertSee('1 offene Migration:')
            ->assertSee('2026_10_28_000000_rename_help_titles');
    }

    public function test_admin_runs_migrations_and_sees_output()
    {
        $s = CampScenario::create();
        Artisan::shouldReceive('call')->once()->with('migrate', ['--force' => true]);
        Artisan::shouldReceive('output')->once()->andReturn("INFO  Running migrations.\n");

        $this->actingAs($s->admin)->post(route('admin.maintenance.migrate'))
            ->assertRedirect(route('admin.maintenance'))
            ->assertSessionHas('maintenance_output', 'INFO  Running migrations.');
    }

    public function test_admin_clears_cache_and_sees_output()
    {
        $s = CampScenario::create();
        Artisan::shouldReceive('call')->once()->with('optimize:clear');
        Artisan::shouldReceive('output')->once()->andReturn('INFO  Clearing cached bootstrap files.');

        $this->actingAs($s->admin)->post(route('admin.maintenance.clear-cache'))
            ->assertRedirect(route('admin.maintenance'))
            ->assertSessionHas('maintenance_output', 'INFO  Clearing cached bootstrap files.');

        $this->actingAs($s->admin)->get(route('admin.maintenance'))
            ->assertSee('INFO  Clearing cached bootstrap files.');
    }
}
