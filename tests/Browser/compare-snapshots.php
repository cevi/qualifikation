<?php

// Vergleicht zwei Screenshot-Ordner aus VisualSnapshotTest Pixel für Pixel und markiert Unterschiede rot:
// php tests/Browser/compare-snapshots.php tests/Browser/screenshots/before tests/Browser/screenshots/after [<diff-ordner>]
[$script, $dirA, $dirB] = $argv;
$diffDir = $argv[3] ?? null;
if ($diffDir && ! is_dir($diffDir)) {
    mkdir($diffDir, 0777, true);
}

$changed = 0;
foreach (glob("$dirA/*.png") as $fileA) {
    $name = basename($fileA);
    $fileB = "$dirB/$name";
    if (! file_exists($fileB)) {
        echo "FEHLT    $name\n";
        $changed++;

        continue;
    }
    $a = imagecreatefrompng($fileA);
    $b = imagecreatefrompng($fileB);
    [$wa, $ha] = [imagesx($a), imagesy($a)];
    [$wb, $hb] = [imagesx($b), imagesy($b)];
    if ($wa !== $wb || $ha !== $hb) {
        echo "GRÖSSE   $name: {$wa}x{$ha} → {$wb}x{$hb}\n";
        $changed++;

        continue;
    }
    $diff = 0;
    $minY = $ha;
    $maxY = 0;
    $marked = $diffDir ? imagecreatefrompng($fileB) : null;
    $red = $marked ? imagecolorallocate($marked, 255, 0, 0) : null;
    for ($y = 0; $y < $ha; $y++) {
        for ($x = 0; $x < $wa; $x++) {
            if (imagecolorat($a, $x, $y) !== imagecolorat($b, $x, $y)) {
                $diff++;
                $minY = min($minY, $y);
                $maxY = max($maxY, $y);
                if ($marked) {
                    imagesetpixel($marked, $x, $y, $red);
                }
            }
        }
    }
    if ($diff) {
        printf("ANDERS   %s: %d Pixel (%.3f %%), Zeilen %d–%d\n", $name, $diff, 100 * $diff / ($wa * $ha), $minY, $maxY);
        $changed++;
        if ($marked) {
            imagepng($marked, "$diffDir/$name");
        }
    }
}
echo $changed ? "$changed von ".count(glob("$dirA/*.png"))." Seiten unterschiedlich\n" : 'Alle '.count(glob("$dirA/*.png"))." Seiten identisch\n";
