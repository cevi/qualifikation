<?php

namespace Database\Seeders;

use App\Models\Camp;
use App\Models\CampUser;
use App\Models\Survey;
use App\Models\SurveyChapter;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoCampSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $user = User::updateOrCreate([
            'username' => 'kursleiter@demo'], [
            'email' => 'kursleiter@demo',
            'slug' => 'kursleiter@demo',
            'password' => Hash::make('kursleiter@demo'),
            'email_verified_at' => now(),
            'role_id' => config('status.role_Kursleiter'),
            'camp_id' => 1,
            'demo' => true, ]);
        $camp = Camp::updateOrCreate([
            'name' => 'Demo-Kurs'], [
            'demo' => true,
            'secondsurveyopen' => true,
            'camp_type_id' => config('status.camptype_JS2'),
            'user_id' => $user['id'], ]);
        $user->update(['camp_id' => $camp['id']]);
        CampUser::updateOrCreate([
            'camp_id' => $camp['id'],
            'user_id' => $user['id']], [
            'role_id' => config('status.role_Kursleiter'),
        ]);
        CampUser::updateOrCreate([
            'camp_id' => 1,
            'user_id' => $user['id']], [
            'role_id' => config('status.role_Teilnehmer'),
        ]);
        if($camp->participants->count = 0){
            for ($i = 0; $i < 4; $i++) {
                $name = 'leiter' . ($i + 1) . '@demo';
                $leader = User::factory()
                    ->create([
                        'username' => $name], [
                        'email' => $name,
                        'slug' => $name,
                        'password' => Hash::make($name),
                        'role_id' => config('status.role_Gruppenleiter'),
                        'camp_id' => $camp['id'],
                    ]);
                CampUser::updateOrCreate([
                    'camp_id' => $camp['id'],
                    'user_id' => $leader['id']], [
                    'role_id' => config('status.role_Gruppenleiter'),
                ]);
                CampUser::updateOrCreate([
                    'camp_id' => 1,
                    'user_id' => $leader['id']], [
                    'role_id' => config('status.role_Teilnehmer'),
                ]);
                for ($j = 0; $j < 4; $j++) {
                    $name = 'tn' . ($i + 1) . ($j + 1) . '@demo';
                    $user = User::factory()
                        ->for($leader, 'leader')
                        ->create([
                            'username' => $name], [
                            'email' => $name,
                            'slug' => $name,
                            'password' => Hash::make($name),
                            'camp_id' => $camp['id'],
                        ]);
                    $camp_user = CampUser::updateOrCreate([
                        'camp_id' => $camp['id'],
                        'user_id' => $user['id']], [
                        'leader_id' => $leader['id'],
                        'role_id' => config('status.role_Teilnehmer'),
                    ]);
                    CampUser::updateOrCreate([
                        'camp_id' => 1,
                        'user_id' => $user['id']], [
                        'role_id' => config('status.role_Teilnehmer'),
                    ]);
                    $survey = Survey::factory()
                        ->for($camp_user)
                        ->create([
                            'slug' => $name . '@' . $camp['name'],
                        ]);
                    for ($k = 0; $k < 4; $k++) {
                        $survey_chapter = SurveyChapter::updateOrCreate([
                            'survey_id' => $survey['id'],
                            'chapter_id' => $k + 1,
                        ]);
                        for ($l = 0; $l < 3; $l++) {
                            SurveyQuestion::factory()
                                ->for($survey_chapter, 'survey_chapter')
                                ->create([
                                    'question_id' => ($k * 3) + ($l + 1),
                                ]);
                        }
                    }
                }
            }
        }
    }
}
