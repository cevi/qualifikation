<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Camp;
use App\Models\Role as RoleModel;
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
        RoleModel::updateOrCreate(['id' => Role::Administrator->value], ['name' => 'Administrator', 'is_admin' => true, 'is_campleader' => false, 'is_leader' => false]);
        RoleModel::updateOrCreate(['id' => Role::Kursleiter->value], ['name' => 'Kursleitende', 'is_admin' => false, 'is_campleader' => true, 'is_leader' => false]);
        RoleModel::updateOrCreate(['id' => Role::Gruppenleiter->value], ['name' => 'Gruppenleitende', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => true]);
        RoleModel::updateOrCreate(['id' => Role::Teilnehmer->value], ['name' => 'Teilnehmende', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => false]);
        RoleModel::updateOrCreate(['id' => Role::Stabsleiter->value], ['name' => 'Stabsleitende', 'is_admin' => false, 'is_campleader' => false, 'is_leader' => true]);

        $user = User::updateOrCreate([
            'id' => 1], [
                'username' => 'Administrator',
                'email' => 'admin@quali',
                'slug' => 'administrator',
                'password' => Hash::make(env('ADMIN_PASSWORD')),
                'role_id' => Role::Administrator,
                'email_verified_at' => now(),
            ]);
        $camp = Camp::updateOrCreate(['name' => 'Global-Camp', 'global_camp' => true]);
        $user->update(['camp_id' => $camp['id']]);
    }
}
