<?php

use Database\Seeders\BasisdatenSeeder;
use Database\Seeders\DemoCampSeeder;
use Database\Seeders\PermissionRoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            BasisdatenSeeder::class,
            PermissionRoleSeeder::class,
            DemoCampSeeder::class,
        ]);
    }
}
