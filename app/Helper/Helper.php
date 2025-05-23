<?php

namespace App\Helper;

use Str;
use App\Models\Camp;
use App\Models\Post;
use App\Models\User;
use App\Models\Answer;
use App\Models\Survey;
use App\Models\CampUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

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

    public static function storePost(Request $request, User $user = null){
        $aktUser = Auth::user();
        $camp = $aktUser->camp;
        $input = $request->all();

        $input['leader_id'] = $aktUser->id;
        $input['camp_id'] = $camp->id;
        $input['show_on_survey'] = $request->has('show_on_survey');
        if(isset($user)){
            $input['user_id'] = $user->id;
        }
        
        $post = Post::find($input['post_id']);
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
            if ($input['post_id']) {
                if($request->has('delete_file')){
                    $input['file'] = null;
                }
            }
            else{
                $input['file'] = null;
            }
        }

        if (!$input['post_id']) {
            Post::create($input);
        } else {
            $post->update($input);
        }
    }

}
