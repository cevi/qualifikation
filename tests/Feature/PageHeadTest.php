<?php

namespace Tests\Feature;

use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Kopf aller Seiten (includes/header.blade.php) und Bilder der öffentlichen Seiten: Zeichensatz zuerst, Beschreibung
 * und Vorschau beim Teilen (Open Graph), Sprunglink zum Inhalt in beiden Layouts, keine Bilder über 400 KB.
 */
class PageHeadTest extends TestCase
{
    public function test_charset_comes_first_and_old_ie_header_is_gone(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<head>\s*<meta charset="utf-8">/', $html);
        $this->assertStringNotContainsString('X-UA-Compatible', $html);
    }

    public function test_description_and_open_graph_for_sharing(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringContainsString('<meta name="description" content="Online-Qualifikationstool für J+S-Kurse:', $html);
        $this->assertStringContainsString('<meta property="og:title" content="'.config('app.name').'">', $html);
        $this->assertStringContainsString('<meta property="og:image" content="'.url('/img/og-image.jpg').'">', $html);
        $this->assertStringContainsString('<meta property="og:url" content="'.url('/').'">', $html);
        $this->assertStringContainsString('<meta property="og:locale" content="de_CH">', $html);
        $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image">', $html);
    }

    public function test_open_graph_image_has_the_size_messengers_expect(): void
    {
        [$width, $height] = getimagesize(public_path('img/og-image.jpg'));

        $this->assertSame([1200, 630], [$width, $height]);
        $this->assertLessThan(300 * 1024, filesize(public_path('img/og-image.jpg')));
    }

    public function test_both_layouts_have_a_skip_link_to_the_content(): void
    {
        $s = CampScenario::create();

        // layouts.app (Gäste, Übersicht) und layouts.admin (Kursadmin)
        foreach ([$this->get('/login'), $this->actingAs($s->kursleiter)->get('/home'), $this->get(route('admin.users.index'))] as $response) {
            $html = $response->assertOk()->getContent();
            $this->assertMatchesRegularExpression('/<body[^>]*>\s*<a href="#inhalt"[^>]*>Zum Inhalt springen<\/a>/', $html);
            $this->assertStringContainsString('<main id="inhalt" tabindex="-1"', $html);
        }
    }

    public function test_images_of_public_pages_are_small(): void
    {
        foreach (['login.jpg', 'fixed-background-2.jpg', 'photogrid.jpg'] as $image) {
            $this->assertLessThan(400 * 1024, filesize(public_path("img/$image")), $image);
        }
        // Banner und Fotostreifen füllen per bg-cover (welcome.css, !important) das Band: gleiches Seitenverhältnis wie
        // das Original, gross genug für breite Bildschirme
        $this->assertSame([1920, 1440], array_slice(getimagesize(public_path('img/fixed-background-2.jpg')), 0, 2));
        $this->assertSame([2094, 796], array_slice(getimagesize(public_path('img/photogrid.jpg')), 0, 2));
    }
}
