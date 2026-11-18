<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\CampScenario;

/**
 * Profilbild zuschneiden (x-users.photo-cropper, resources/js/pages/photo-cropper.js): Datei wählen öffnet den Dialog,
 * "Zuschneiden" legt das Bild als data-URL ins versteckte Feld, "Abbrechen" lässt es leer.
 */
class PhotoCropperTest extends DuskTestCase
{
    public function test_chosen_photo_is_cropped_into_hidden_field(): void
    {
        $s = CampScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->kursleiter)->visit(route('admin.users.edit', $s->participant->slug));

            $browser->attach('input.photo', public_path('apple-touch-icon.png'))
                ->waitUntil("document.getElementById('photo-cropper').open")
                ->waitFor('#photo-cropper .cropper-container')
                ->click('#photo-cropper [data-action="close"]')
                ->assertScript("document.getElementById('photo-cropper').open", false)
                ->assertInputValue('#cropped_photo_id', '');

            $browser->attach('input.photo', public_path('favicon-32x32.png'))
                ->waitFor('#photo-cropper .cropper-container')
                ->click('#photo-cropper [data-action="crop"]')
                ->assertScript("document.getElementById('photo-cropper').open", false)
                ->assertScript("document.getElementById('cropped_photo_id').value.startsWith('data:image/png;base64,')");
        });
    }
}
