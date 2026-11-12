<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = User::class;

    protected static ?string $password;

    public function definition()
    {
        return [
            'username' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'slug' => (string) Str::uuid(),
            'password' => static::$password ??= Hash::make('password'),
            'role_id' => Role::Teilnehmer,
            'email_verified_at' => now(),
            'camp_id' => null,
            'demo' => false,
        ];
    }

    public function demo()
    {
        return $this->state(['demo' => true]);
    }

    public function unverified()
    {
        return $this->state(['email_verified_at' => null]);
    }

    public function admin()
    {
        return $this->state(['role_id' => Role::Administrator]);
    }

    public function kursleiter()
    {
        return $this->state(['role_id' => Role::Kursleiter]);
    }

    public function gruppenleiter()
    {
        return $this->state(['role_id' => Role::Gruppenleiter]);
    }

    public function stabsleiter()
    {
        return $this->state(['role_id' => Role::Stabsleiter]);
    }

    /**
     * Setzt das aktuelle Camp des Users und legt den passenden CampUser an
     * (gleiche Rolle, optional mit einer oder mehreren zuständigen Leitenden).
     *
     * @param  User|array<User>|null  $leaders
     */
    public function inCamp(Camp $camp, User|array|null $leaders = null)
    {
        return $this->state([
            'camp_id' => $camp->id,
        ])->afterCreating(function (User $user) use ($camp, $leaders) {
            CampUser::create([
                'camp_id' => $camp->id,
                'user_id' => $user->id,
                'role_id' => $user->role_id,
            ])->leaders()->attach(collect(Arr::wrap($leaders))->pluck('id'));
        });
    }
}
