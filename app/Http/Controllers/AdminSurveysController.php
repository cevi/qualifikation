<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\User;
use App\Models\Answer;
use App\Models\Survey;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\SurveyStatus;
use App\Models\SurveyChapter;
use App\Models\SurveyQuestion;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AdminSurveysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $camp = Auth::user()->camp;
        return view('admin.surveys.index',compact('camp'));
    }

    public function createDataTables()
    {
        //

        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $surveys = $camp->surveys;
        }
        else{
            $surveys = Survey::all();
        }

        return DataTables::of($surveys)
            ->addColumn('user', function($survey) {
                $username = $survey->campuser ? $survey->campuser->user['username'] : '';
                return '<a href='.\URL::route('home.profile', $survey->campuser->user['slug']).' title="Zum Profil">'.$username.'</a>';
            })
            ->addColumn('responsible', function (Survey $survey) {
                return $survey->campuser->leader ? $survey->campuser->leader['username'] : '';})
            ->addColumn('camp', function (Survey $survey) {
                return $survey->campuser->camp['name'];})
            ->addIndexColumn()
            ->addColumn('status', function($survey) {
                $survey_statuses_id = [config('status.survey_neu'),
                config('status.survey_1offen'),
                config('status.survey_2offen'),
                config('status.survey_tnAbgeschlossen'),
                config('status.survey_fertig')];
                $result = '<div class="card card-progress">
                <ul id="progressbar" class="text-center">';
                foreach($survey_statuses_id as $status_id){
                    $survey_status = SurveyStatus::findOrFail($status_id);
                    $step = $survey->survey_status_id >= $survey_status['id'] ? 'active' : '';
                    $result = $result.'<li class="'.$step.' step0" title="'.$survey_status['name'].'"></li>';
                }
                return $result;
            })
            ->addColumn('Actions', function($survey) {
                return '<a href='. \URL::route('survey.compare', $survey['slug']).'>Zur Qualifikationen</a><br><br>
                <a href='. \URL::route('surveys.edit', $survey->slug).'>Bearbeiten</a>';
            })
            ->rawColumns(['user', 'Actions', 'status'])
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $camp = Auth::user()->camp;
        $camp_users = $camp->camp_users()->doesntHave('surveys')->get();
        $chapters = Chapter::all();
        $answer = Answer::where('name','0')->first();

        foreach($camp_users as $camp_user){
            $input['name'] = 'Qualifikationsprozess';
            $input['camp_user_id'] = $camp_user->id;
            $user = User::find($camp_user['user_id']);
            $input['slug'] = Str::slug($user['username'].'@'.$camp['name']);
            $input['survey_status_id'] = config('status.survey_neu');
            $survey = Survey::create($input);
            foreach($chapters as $chapter){
                $input['chapter_id'] = $chapter->id;
                $input['survey_id'] = $survey->id;
                $survey_chapter = SurveyChapter::create($input);
                $questions = Question::where('chapter_id', $chapter->id)->get();
                foreach($questions as $question){
                    $input['survey_chapter_id'] = $survey_chapter->id;
                    $input['question_id'] = $question->id;
                    $input['answer_first_id'] = $answer->id;
                    $input['answer_second_id'] = $answer->id;
                    $input['answer_leader_id'] = $answer->id;
                    SurveyQuestion::create($input);
                }
            }
        }
        return true;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Survey $survey)
    {
        //
        // $survey = Survey::findOrFail($id);
        $users = Auth::user()->camp->participants->pluck('username','id')->all();
        $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->pluck('username','id')->all();
        $survey_statuses_id = SurveyStatus::pluck('name','id')->all();
        $user_id = $survey->campUser->user();
        return view('admin.surveys.edit', compact('survey','users', 'leaders', 'survey_statuses_id', 'user_id'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        if(!Auth::user()->demo){
            $input = $request->all();
            $survey = Survey::findOrFail($id);
            $camp = Auth::user()->camp;
            if(isset($input['update'])){
                if($input['survey_status_id'] == config('status.survey_neu')){
                    Helper::clearsurvey($survey, 'first');
                }
                if($input['survey_status_id'] <= config('status.survey_1offen')){
                    Helper::clearsurvey($survey, 'second');
                }
            }
            $survey->update($input);
        }

        return redirect('/admin/surveys');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



}
