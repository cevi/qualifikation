<?php

namespace Tests\Browser;

use App\Models\Post;
use Closure;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Feature\Smoke\PagesRenderTest;
use Tests\Support\CampScenario;

/**
 * Alle Seiten aus PagesRenderTest im echten Browser, pro Rolle. Die PHP-Tests sehen nur das HTML;
 * hier zählt, was die Skripte daraus machen.
 */
class PagesInBrowserTest extends DuskTestCase
{
    private const GUEST_PAGES = ['/', '/login', '/register', '/password/reset'];

    public function test_pages_have_no_javascript_errors(): void
    {
        $errors = $this->crawl(fn (Browser $browser) => $this->consoleErrors($browser));

        $this->assertSame([], $errors, 'Fehler in der Browser-Konsole: '.json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Jede id nur einmal pro Seite: Sonst zeigt <label for> auf das falsche Feld und
     * getElementById() trifft das erste von mehreren Elementen (z. B. das falsche Löschen-Formular).
     */
    public function test_ids_are_unique_on_every_page(): void
    {
        $script = <<<'JS'
            const seen = {};
            document.querySelectorAll('[id]').forEach((element) => { seen[element.id] = (seen[element.id] ?? 0) + 1; });
            return Object.entries(seen).filter(([, count]) => count > 1).map(([id, count]) => `${id} (${count}×)`);
        JS;

        $duplicates = $this->crawl(fn (Browser $browser) => $browser->script($script)[0]);

        $this->assertSame([], $duplicates, 'Doppelte ids: '.json_encode($duplicates, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Besucht alle Seiten und sammelt pro Seite, was $check findet (leere Ergebnisse fallen weg).
     *
     * @param  Closure(Browser): array  $check
     * @return array<string, array>
     */
    private function crawl(Closure $check): array
    {
        $s = CampScenario::create();
        // Zwei Rückmeldungen derselben Leitung, damit Schleifen in den Ansichten mehr als ein Element erzeugen.
        Post::factory()
            ->for($s->campUser($s->participant), 'campUser')
            ->for($s->gruppenleiter, 'leader')
            ->create(['camp_id' => $s->camp->id]);

        $pages = collect(self::GUEST_PAGES)->map(fn ($url) => ['gast', $url, "gast: $url"]);
        foreach (PagesRenderTest::pages() as $label => [$role, $url]) {
            $pages->push([$role, $url($s), $label]);
        }

        $found = [];
        $this->browse(function (Browser $browser) use ($s, $pages, $check, &$found) {
            $currentRole = null;
            foreach ($pages as [$role, $url, $label]) {
                if ($role !== $currentRole) {
                    $role === 'gast' ? $browser->logout() : $browser->loginAs($s->users()[$role]);
                    $currentRole = $role;
                    $this->consoleErrors($browser);
                }

                $browser->visit($url)
                    ->waitUntil('document.readyState === "complete"')
                    ->waitUntil('! window.jQuery || window.jQuery.active === 0')
                    ->pause(200);

                if ($result = $check($browser)) {
                    $found[$label] = $result;
                }
            }
        });

        return $found;
    }
}
