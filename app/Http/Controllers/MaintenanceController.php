<?php

namespace App\Http\Controllers;

use App\Models\Help;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

/**
 * Wartung für Admins ohne SSH-Zugang: offene Migrationen ausführen und Caches leeren. Aktionen nur per POST, damit
 * ein Link oder Bild auf einer fremden Seite nichts auslöst. Destruktive Befehle (migrate:fresh, db:seed) gibt es
 * hier bewusst nicht.
 */
class MaintenanceController extends Controller
{
    public function index(): View
    {
        $title = 'Wartung';
        $help = Help::where('title', $title)->first();
        $pending = $this->pendingMigrations();

        return view('admin.maintenance', compact('title', 'help', 'pending'));
    }

    public function migrate(): RedirectResponse
    {
        Artisan::call('migrate', ['--force' => true]);

        return $this->backWithOutput();
    }

    public function clearCache(): RedirectResponse
    {
        Artisan::call('optimize:clear');

        return $this->backWithOutput();
    }

    /** Dateinamen der Migrationen, die noch nicht gelaufen sind. */
    private function pendingMigrations(): array
    {
        $migrator = app('migrator');
        $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));
        if (! $migrator->repositoryExists()) {
            return $files;
        }

        return array_values(array_diff($files, $migrator->getRepository()->getRan()));
    }

    private function backWithOutput(): RedirectResponse
    {
        return redirect()->route('admin.maintenance')->with('maintenance_output', trim(Artisan::output()));
    }
}
