<?php

namespace App\Helper;

use App\Models\Answer;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\Group;
use App\Models\Post;
use App\Models\StandardText;
use App\Models\Survey;
use App\Models\User;
use App\Events\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Str;

class Helper
{
    public static function clearSurvey($survey, $which)
    {
        $questions = $survey->questions;
        $answer = Answer::where('name', '0')->first();
        foreach ($questions as $question) {
            if ($which == 'first') {
                $question->update(['answer_first_id' => $answer['id'], 'comment_first' => '']);
            }
            $question->update(['answer_second_id' => $answer['id'], 'comment_second' => '']);
        }
    }

    public static function updateCamp(User $user, Camp $camp)
    {
        $camp_user = CampUser::firstOrCreate(['camp_id' => $camp->id, 'user_id' => $user->id]);
        $leader_id = $camp['global_camp'] ? null : $user['leader_id'];
        $role_id = $camp_user['role_id'];
        if ($role_id === null) {
            $role_id = $camp['global_camp'] ? config('status.role_Teilnehmer') : $user['role_id'];
        }
        $classification_id = $camp_user['classification_id'] ?: config('status.classification_yellow');
        $camp_user->update([
            'role_id' => $role_id,
            'leader_id' => $leader_id,
            'classification_id' => $classification_id,
        ]);
        if ($camp_user->leader) {
            $leader_id = $camp_user->leader->id;
        } else {
            $leader_id = null;
        }
        $user->update([
            'camp_id' => $camp->id,
            'leader_id' => $leader_id,
            'role_id' => $camp_user->role->id,
        ]);
    }

    public static function updateAvatar($request, $user){
        if ($file = $request->file('avatar')) {
            $input = $request->all();
            $aktUser = Auth::user();
            $camp = $aktUser->camp;
            if ($input['cropped_photo_id']) {
                $save_path = $camp['id'] . '_'. Str::slug($camp['name']).'/profiles';
                $directory = storage_path('app/public/'.$save_path);
                if (!File::isDirectory($directory)) {
                    File::makeDirectory($directory, 0775, true);
                }
                $name =  Str::uuid() . '_' . str_replace(' ', '', $file->getClientOriginalName());
                Image::make($input['cropped_photo_id'])->save($directory.'/'.$name, 80);
                $input['avatar'] = $save_path.'/'.$name;
                $camp_user = CampUser::where('user_id', $user->id)->where('camp_id', $camp->id)->first();
                $camp_user->update(['avatar' => $input['avatar']]);
            }
        }
    }

    public static function getAvatarPath($avatar)
    {
        $path = null;
        if($avatar){
            if(str_starts_with($avatar, 'https')){
                $path = $avatar;
            }
            else{
                $path = asset("storage/".$avatar);
            }
        }
        return $path;
    }

    public static function getStandardTextsForCamp($campId)
    {
        return StandardText::where('camp_id', $campId)->orWhere('global', true)->get();
    }


    public static function GetSurveysLabels($surveys){

        $labels = [];
        if($surveys->count()>0) {
            $survey = $surveys[0];
            $labels = Self::GetSurveyLabels($survey);
        }

        return $labels;

    }

    public static function GetSurveyLabels($survey){

        $labels = [];
        $questions =  $survey->questions()->with('question')->get()->sortBy('question.sort-index')->values();
        foreach ($questions as $survey_question) {
            $question = $survey_question->question;
            $text = $survey_question->competence_text() ? '*' : '';
            $labels[] = $text . $question['number'] . ' ' . $question['competence'];
        }

        return $labels;

    }

    public static function GetSurveysDataset($surveys){

        $dataset = [];

        foreach ($surveys as $survey) {
            $dataset[] = Self::GetSurveyDataset($survey);
        }

        return $dataset;

    }


    public static function GetSurveyDataset($survey)
    {
        $dataset_add = [];
        $camp = $survey->campUser->camp;
        $first_answers = [];
        $second_answers = [];
        $leader_answers = [];
        $questions =  $survey->questions()->with('question')->get()->sortBy('question.sort-index')->values();
        foreach ($questions as $i => $question) {
            $first_answers[] = $question->answer_first['count'];
            $second_answers[] = $question->answer_second['count'];
            $leader_answers[] = $question->answer_leader['count'];
        }
        $dataset_first = Self::GetDataset('1. Selbsteinschätzung', 'rgba(179,181,198,0.2)', '#fff', 2, $first_answers);
        $dataset_second = Self::GetDataset('2. Selbsteinschätzung', 'rgba(50,181,198,0.2)', '#fff', 2, $second_answers);
        $dataset_leader = Self::GetDataset('Leiter Qualifikation', 'rgba(51, 179, 90, 0.2)', '#fff', 2, $leader_answers);
        if (Auth::user()->role_id != config('status.role_Teilnehmer')) {
            if (($survey['survey_status_id'] >= config('status.survey_2offen')) &&
                ($camp['secondsurveyopen'])) {
                $dataset_add = [
                    $dataset_first,
                    $dataset_second,
                    $dataset_leader,
                ];
            } else {
                $dataset_add = [
                    $dataset_first,
                    $dataset_leader,
                ];
            }
        } else {
            if (($survey['survey_status_id'] >= config('status.survey_2offen')) &&
                ($camp['secondsurveyopen'])) {
                $dataset_add = [
                    $dataset_first,
                    $dataset_second,
                ];
            } else {
                $dataset_add = [
                    $dataset_first,
                ];
            }
        }
        return $dataset_add;
    }

    public static function GetDataset($title, $color, $point_color, $borderwith, $dataset){
        return [
            'label' => $title,
            'backgroundColor' => $color,
            'borderWidth' => $borderwith,
            'borderColor' => $color,
            'pointBackgroundColor' => $color,
            'pointBorderColor' => $point_color,
            'pointHoverBackgroundColor' => $point_color,
            'pointHoverBorderColor' => $color,
            'data' => $dataset
        ];
    }

    public static function storePost(Request $request, ?User $user){
        $aktUser = Auth::user();
        $camp = $aktUser->camp;
        $input = $request->all();
        $input['camp_id'] = $camp->id;

        $input['leader_id'] = $aktUser->id;
        $input['show_on_survey'] = $request->has('show_on_survey');
        if(isset($user)){
            $input['user_id'] = $user->id;
        }
        
        if (isset($input['user_id']) && $input['user_id'] === 'null') {
            $input['user_id'] = null;
        }

        $camp_user = CampUser::where('user_id', $input['user_id'] ?? null)->where('camp_id', $camp->id)->first();
        if($camp_user){
            $input['camp_user_id'] = $camp_user->id;
        }
        
        $post_id = $input['post_id'] ?? null;
        $post = $post_id ? Post::find($post_id) : null;
        if (!$aktUser->demo && $file = $request->file('file')) {
            $save_path = 'app/files/' . $camp['id'] . '_'. Str::slug($camp['name']);
            $directory = storage_path($save_path);
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0775, true);
            }
            $input['uuid'] = Str::uuid();
            $name = $input['uuid'] . '_' . str_replace(' ', '', $file->getClientOriginalName());
            $file->move($directory, $name);
            $input['file'] = $save_path . '/' . $name;
        }
        else{
            if ($post_id) {
                if($request->has('delete_file')){
                    $input['file'] = null;
                }
            }
            else{
                $input['file'] = null;
            }
        }

        if (!$post_id) {
            Post::create($input);
        } elseif ($post) {
            $post->update($input);
        }
    }

    public static function importParticipations(User $aktUser, Camp $camp, array $participations, array $included)
    {
        // Build lookup maps from sideloaded resources
        $peopleById = [];
        $rolesById = [];
        foreach ($included as $resource) {
            if ($resource['type'] === 'people') {
                $peopleById[$resource['id']] = $resource;
            } elseif ($resource['type'] === 'event_roles') {
                $rolesById[$resource['id']] = $resource;
            }
        }
        logger()->info('Importing ' . count($participations) . ' participations with ' . count($peopleById) . ' people and ' . count($rolesById) . ' roles');

        // Verify the requesting user is a course leader
        $isLeader = false;
        foreach ($participations as $participation) {
            $personId = $participation['relationships']['participant']['data']['id'] ?? null;
            if (intval($personId) !== $aktUser['foreign_id']) {
                continue;
            }
            foreach ($participation['relationships']['roles']['data'] ?? [] as $roleRef) {
                if (($rolesById[$roleRef['id']]['attributes']['type'] ?? null) === 'Event::Role::Leader') {
                    $isLeader = true;
                    break 2;
                }
            }
        }
        if (!$isLeader) {
            return response()->json(['error' => 'Der DB-Import steht nur den Kursleitern zur Verfügung', 'ok' => false], 404);
        }

        $relevantRoles = ['Event::Course::Role::Participant', 'Event::Role::AssistantLeader', 'Event::Role::Leader'];

        foreach ($participations as $participation) {
            $personId = $participation['relationships']['participant']['data']['id'] ?? null;
            if (!$personId) {
                continue;
            }

            // Resolve the first matching role for this participation
            $roleType = null;
            foreach ($participation['relationships']['roles']['data'] ?? [] as $roleRef) {
                $type = $rolesById[$roleRef['id']]['attributes']['type'] ?? null;
                if ($type) {
                    $roleType = $type;
                    break;
                }
            }

            if (!in_array($roleType, $relevantRoles)) {
                continue;
            }

            $isSelf = intval($personId) === $aktUser['foreign_id'];
            $person = $peopleById[$personId] ?? null;
            $attrs = $person['attributes'] ?? [];

            if ($isSelf) {
                $user = Auth::user();
            } else {
                if (!$person) {
                    continue;
                }
                $email = $attrs['email'];
                $username = $attrs['nickname'] ?? $attrs['first_name'];
                $role_id = match ($roleType) {
                    'Event::Course::Role::Participant' => config('status.role_Teilnehmer'),
                    'Event::Role::AssistantLeader'     => config('status.role_Gruppenleiter'),
                    default                            => config('status.role_Kursleiter'),
                };

                $user = User::whereRaw('LOWER(`email`) LIKE ?', [mb_strtolower($email)])
                    ->orWhere('foreign_id', $personId)
                    ->first();

                if (!$user) {
                    $user = User::create([
                        'username' => $username,
                        'email' => $email,
                        'email_verified_at' => now(),
                        'classification_id' => config('status.classification_yellow'),
                    ]);
                    UserCreated::dispatch($user);
                }

                $user->update([
                    'role_id' => $role_id,
                    'camp_id' => $camp['id'],
                    'classification_id' => config('status.classification_yellow'),
                ]);
            }

            if (!$user->email && isset($attrs['email'])) {
                $user->update(['email' => $attrs['email']]);
            }
            if (!$user->foreign_id) {
                $user->update(['foreign_id' => intval($personId)]);
            }
            if (!$user->group_id && isset($attrs['primary_group_id'])) {
                $group = Group::where('foreign_id', $attrs['primary_group_id'])->first();
                if ($group) {
                    $user->update(['group_id' => $group->id]);
                }
            }

            Helper::updateCamp($user, $camp);
        }

        return true;
    }

}
