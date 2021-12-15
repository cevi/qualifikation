<?php

namespace Database\Seeders;

use App\Camp;
use App\Role;

use App\User;
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
        Role::create(['id' => config('status.role_Administrator'), 'name' => 'Administrator', 'is_admin' => true, 'is_campleader' => false, 'is_leader' => false]);
        Role::create(['id' => config('status.role_Kursleiter'), 'name' => 'Kursleiter', 'is_admin' => false, 'is_campleader' => true, 'is_leader' => false]);
        Role::create(['id' => config('status.role_Gruppenleiter'), 'name' => 'Gruppenleiter', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => true]);
        Role::create(['id' => config('status.role_Teilnehmer'), 'name' => 'Teilnehmer', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => false]);
        
        $user = User::create( [
            'id' => 1,
            'username' => 'Administrator', 
            'slug' => 'administrator',
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'role_id' => config('status.role_Administrator'),
            'is_active' => true]);
        $camp = Camp::create(['name' => 'Global-Camp', 'global_camp' => true]);
        $user->update(['camp_id' => $camp['id']]);
    }
}
