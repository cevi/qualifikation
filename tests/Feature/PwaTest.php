<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Installierbare App (PWA): Manifest, Service Worker und Offline-Seite. Registrierung im Browser:
 * Browser/MobileNavigationTest.
 */
class PwaTest extends TestCase
{
    public function test_manifest_starts_on_overview_with_icons_and_shortcut(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('/home', $manifest['start_url']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/posts?neu=1', $manifest['shortcuts'][0]['url']);
        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
        }
    }

    public function test_service_worker_never_caches_pages(): void
    {
        $worker = file_get_contents(public_path('sw.js'));

        // Seiten (Personendaten) nur aus dem Netz; gespeichert wird allein über cacheFirst für /build/ und Bilder
        $this->assertMatchesRegularExpression("/request\.mode === 'navigate'.*?respondWith\(fetch\(request\)\.catch\(\(\) => caches\.match\(OFFLINE_URL\)\)\)/s", $worker);
        $this->assertSame(1, substr_count($worker, 'cache.put('));
        foreach (['/offline.html', '/img/logo.svg', '/android-chrome-192x192.png'] as $file) {
            $this->assertStringContainsString("'$file'", $worker);
            $this->assertFileExists(public_path(ltrim($file, '/')));
        }
    }

    public function test_layout_links_manifest(): void
    {
        $this->get('/login')->assertOk()->assertSee('manifest.webmanifest', false);
    }
}
