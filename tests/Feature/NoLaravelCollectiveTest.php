<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Formulare laufen über die x-forms-Komponenten (FormComponentsTest), nicht mehr über Form:: und Html:: aus
 * laravelcollective (rdx/laravelcollective-html, November 2026 entfernt). Hält fest, dass keine Aufrufe zurückkommen.
 */
class NoLaravelCollectiveTest extends TestCase
{
    public function test_no_form_or_html_facade_calls_in_views_or_code(): void
    {
        $found = [];
        foreach ([resource_path('views'), app_path(), base_path('routes'), config_path()] as $dir) {
            foreach (File::allFiles($dir) as $file) {
                if (! str_ends_with($file->getFilename(), '.php')) {
                    continue;
                }
                $lines = preg_split('/\R/', $file->getContents());
                foreach ($lines as $number => $line) {
                    // Aufruf (Form::text(…), {!! Form::close() !!}) oder Import; Erwähnungen in Kommentaren zählen nicht
                    if (preg_match('/\b(Form|Html|HTML)::\w+\(|Collective\\\\Html/', $line)) {
                        $found[] = str_replace(base_path().'/', '', $file->getPathname()).':'.($number + 1).'  '.trim($line);
                    }
                }
            }
        }

        $this->assertSame([], $found, "Form:: durch x-forms-Komponenten ersetzen:\n".implode("\n", $found));
    }
}
