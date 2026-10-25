<?php

namespace Tests\Browser;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\Smoke\PagesRenderTest;
use Tests\Support\CampScenario;

/**
 * Ganzseitige Screenshots aller Seiten für einen Vorher/Nachher-Vergleich, z. B. beim Umbau von CSS.
 * Läuft nur mit SNAPSHOT_SET, sonst übersprungen. Ablauf siehe tests/README.md (Abschnitt Browser-Tests).
 */
class VisualSnapshotTest extends DuskTestCase
{
    private const GUEST_PAGES = ['/', '/login', '/register', '/password/reset'];

    // 390: Handy (unter sm = 480), 1000: zwischen lg (976) und dem Tailwind-Standard 1024, 1440: Desktop
    private const WIDTHS = [390, 1000, 1440];

    private ?bool $dark = null;

    public function test_snapshots(): void
    {
        $set = getenv('SNAPSHOT_SET');
        if (! $set) {
            $this->markTestSkipped('Nur für den CSS-Vergleich (SNAPSHOT_SET setzen).');
        }

        // Gleiche Testdaten (und Zeitstempel) in beiden Läufen
        fake()->seed(20261023);
        $this->travelTo(now()->setDate(2026, 10, 1)->setTime(12, 0));
        $s = CampScenario::create();
        Post::factory()
            ->for($s->campUser($s->participant), 'campUser')
            ->for($s->gruppenleiter, 'leader')
            ->create(['camp_id' => $s->camp->id]);

        $this->freezeData($s);

        $pages = collect(self::GUEST_PAGES)->map(fn ($url) => ['gast', $url, "gast $url"]);
        foreach (PagesRenderTest::pages() as $label => [$role, $url]) {
            $pages->push([$role, $url($s), $label]);
        }

        $this->browse(function (Browser $browser) use ($s, $set, $pages) {
            $currentRole = null;
            foreach ($pages as [$role, $url, $label]) {
                if ($role !== $currentRole) {
                    $role === 'gast' ? $browser->logout() : $browser->loginAs($s->users()[$role]);
                    $currentRole = $role;
                    // LogSuccessfulLogin setzt die echte Uhrzeit des Servers (Spalte "Letztes Login")
                    User::query()->whereNotNull('last_login_at')->update(['last_login_at' => '2026-10-01 12:00:00']);
                }
                foreach (self::WIDTHS as $width) {
                    $this->snapshot($browser, $set, $url, $width, $label, dark: false);
                }
                $this->snapshot($browser, $set, $url, 1440, $label, dark: true);
            }
        });
    }

    /**
     * Was der Server sonst je Lauf anders zeigt: "vor X Minuten" (echte Uhrzeit des Servers, Seeder läuft vor travelTo)
     * und Tabellen, die nach dem Link-HTML sortieren, in dem der zufällige Slug steckt.
     */
    private function freezeData(CampScenario $s): void
    {
        foreach (array_column(Schema::getTables(), 'name') as $table) {
            if (Schema::hasColumns($table, ['created_at', 'updated_at'])) {
                DB::table($table)->update(['created_at' => '2026-01-01 12:00:00', 'updated_at' => '2026-01-01 12:00:00']);
            }
        }
        foreach (DB::table('users')->pluck('id') as $id) {
            DB::table('users')->where('id', $id)->update(['slug' => sprintf('00000000-0000-4000-8000-%012d', $id)]);
        }
        // Die URLs der Seiten entstehen aus den Modellen des Szenarios
        foreach (get_object_vars($s) as $value) {
            if ($value instanceof Model) {
                $value->refresh();
            }
        }
    }

    private function snapshot(Browser $browser, string $set, string $url, int $width, string $label, bool $dark): void
    {
        // Farbschema wie der Umschalter im Menü (header.blade.php und custom.js lesen localStorage)
        if ($dark !== $this->dark) {
            $browser->visit($url)->script('localStorage.setItem("color-theme", "'.($dark ? 'dark' : 'light').'")');
            $this->dark = $dark;
        }
        $browser->resize($width, 900)->visit($url)
            ->waitUntil('document.readyState === "complete"')
            ->waitUntil('! window.jQuery || window.jQuery.active === 0')
            ->pause(1200);

        // Ganze Seite: Fensterhöhe an den Inhalt anpassen, bis sie sich nicht mehr ändert (min-h-screen wächst mit)
        $height = 0;
        for ($i = 0; $i < 4; $i++) {
            // Hier scrollt <html>, nicht <body>
            $measured = min((int) $browser->script('return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight)')[0], 12000);
            if ($measured === $height) {
                break;
            }
            $height = $measured;
            $browser->resize($width, max($height, 900))->pause(500);
        }

        // Webfonts (u. a. Font Awesome vom CDN) fertig laden, sonst fehlen Symbole je nach Netz
        $browser->waitUsing(10, 100, fn () => $browser->script('return document.fonts.status')[0] === 'loaded');

        $name = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($label)), '-');
        $browser->screenshot("{$set}/{$width}".($dark ? '-dark' : '')."-{$name}");
    }
}
