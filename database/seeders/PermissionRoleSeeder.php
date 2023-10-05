<?php

namespace Database\Seeders;

use App\Models\Camp;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        // Insert some stuff
        Role::updateOrCreate(['id' => config('status.role_Administrator')], ['name' => 'Administrator', 'is_admin' => true, 'is_campleader' => false, 'is_leader' => false]);
        Role::updateOrCreate(['id' => config('status.role_Kursleiter')], [ 'name' => 'Kursleitende', 'is_admin' => false, 'is_campleader' => true, 'is_leader' => false]);
        Role::updateOrCreate(['id' => config('status.role_Gruppenleiter')], [ 'name' => 'Gruppenleitende', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => true]);
        Role::updateOrCreate(['id' => config('status.role_Teilnehmer')], [ 'name' => 'Teilnehmende', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => false]);
        Role::updateOrCreate(['id' => config('status.role_Stabsleiter')], [ 'name' => 'Stabsleitende', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => true]);

        $user = User::updateOrCreate([
            'id' => 1], [
            'username' => 'Administrator',
            'email' => 'Administrator',
            'slug' => 'administrator',
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'role_id' => config('status.role_Administrator'),
            'email_verified_at' => now(),
        ]);
        $camp = Camp::updateOrCreate(['name' => 'Global-Camp', 'global_camp' => true]);
        $user->update(['camp_id' => $camp['id']]);
    }
}
