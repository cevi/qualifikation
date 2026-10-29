<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder
{
    /**
     * Stammdaten, die jede Instanz braucht (Rollen, Status, Kapitel, Admin-User und Global-Camp),
     * ohne Demo-Camp. Wird von Tests\TestCase verwendet.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            BasisdatenSeeder::class,
            PermissionRoleSeeder::class,
        ]);
    }
}
