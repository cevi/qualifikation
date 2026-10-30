<?php

namespace App\Services;

/**
 * Migrationen, die noch nicht gelaufen sind. Für die Wartungsseite und RedirectAdminToMaintenance.
 */
class PendingMigrations
{
    /** @return list<string> Dateinamen ohne .php */
    public function all(): array
    {
        $migrator = app('migrator');
        $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));
        if (! $migrator->repositoryExists()) {
            return $files;
        }

        return array_values(array_diff($files, $migrator->getRepository()->getRan()));
    }

    public function any(): bool
    {
        return $this->all() !== [];
    }
}
