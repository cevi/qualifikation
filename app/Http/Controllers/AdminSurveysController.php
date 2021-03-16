<?php

namespace App\Http\Controllers;

use App\User;
use App\Answer;
use App\Survey;
use App\Chapter;
use App\Question;
use App\SurveyStatus;
use App\SurveyChapter;
use App\SurveyQuestion;
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
        return view('admin.surveys.index');
    }

    public function createDataTables()
    {
        //

        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $surveys = Survey::whereHas('user', function($query) use($camp){
                $query->where('camp_id', $camp['id'])->where('role_id', config('status.role_Teilnehmer'))->where('is_active', true);
            })->get();
        }
        else{
            $surveys = Survey::whereHas('user', function($query){
                    $query->where('role_id', config('status.role_Teilnehmer'))->where('is_active', true);})->get();
        }
        
        return DataTables::of($surveys)
            ->addColumn('user', function($survey) {
                $username = $survey->user ? $survey->user['username'] : '';
                return '<a href='.\URL::route('home.profile', $survey->user['slug']).' title="Zum Profil">'.$username.'</a>';
            })
            ->addColumn('responsible', function (Survey $survey) {
                return $survey->user->leader ? $survey->user->leader['username'] : '';})
            ->addColumn('camp', function (Survey $survey) {
                return $survey->user ? $survey->user->camp['name'] : '';})
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
                return '<a href='. \URL::route('survey.compare', $survey->user['slug']).'>Zur Qualifikationen</a><br><br>
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
        $users = User::where('camp_id', $camp['id'])->where('role_id', config('status.role_Teilnehmer'))->where('is_active', true)->doesntHave('own_surveys')->get();
        $chapters = Chapter::all();
        $answer = Answer::where('name','0')->first();

        foreach($users as $user){
            $input['name'] = 'Qualifikationsprozess';
            $input['user_id'] = $user->id;
            $input['slug'] = Str::slug($user['username']);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $users = User::where('role_id', config('status.role_Teilnehmer'))->pluck('username','id')->all();
        $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->pluck('username','id')->all();
        $survey_statuses_id = SurveyStatus::pluck('name','id')->all();
        return view('admin.surveys.edit', compact('survey','users', 'leaders', 'survey_statuses_id'));

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
        // return $request;
        $input = $request->all();
        $user = User::findorFail($input['user_id']);
        $input['slug'] = Str::slug($user['username']);
        Survey::findOrFail($id)->update($input);
  
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
