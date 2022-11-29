<?php

namespace App\Helper;

use App\Models\Answer;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
            'role_id' => $camp_user->role->id, ]);
    }

    public static function GetSurveyLabels(Survey $survey){

        $labels = [];
        $questions = $survey->questions;
        foreach ($questions as $survey_question){
            $question = $survey_question->question;
            $text = $survey_question->competence_text() ? '*' : '';
            $labels[] = $text . $question['competence'];
        }

        return $labels;

    }

    public static function GetSurveysDataset($surveys){

        $dataset = [];

        foreach ($surveys as $survey) {
            $first_answers = [];
            $second_answers = [];
            $leader_answers = [];
            $questions = $survey->questions()->get();
            foreach ($questions as $question) {
                $first_answers[] = $question->answer_first['count'];
                $second_answers[] = $question->answer_second['count'];
                $leader_answers[] = $question->answer_leader['count'];
            }
            $dataset_first = Self::GetDataset('1. Selbsteinschätzung', 'rgba(179,181,198,0.2)', '#fff', 2, $first_answers);
            $dataset_second = Self::GetDataset('2. Selbsteinschätzung', 'rgba(50,181,198,0.2)', '#fff', 2, $second_answers);
            $dataset_leader = Self::GetDataset('Leiter Qualifikation', 'rgba(51, 179, 90, 0.2)', '#fff', 2, $leader_answers);
            if(($survey['survey_status_id'] >= config('status.survey_2offen')) &&
                (Auth::user()->role_id != config('status.role_Teilnehmer') )) {
                $dataset_add = [
                    $dataset_first,
                    $dataset_second,
                    $dataset_leader,
                ];
            }
            elseif($survey['survey_status_id'] >= config('status.survey_2offen')){
                $dataset_add = [
                    $dataset_first,
                    $dataset_second,
                ];
            }
            elseif (Auth::user()->role_id != config('status.role_Teilnehmer')){
                $dataset_add = [
                    $dataset_first,
                    $dataset_leader,
                ];
            }
            else{
                $dataset_add = [
                    $dataset_first,
                ];
            }
            $dataset[] = $dataset_add;
        }

        return $dataset;

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

}
