<?php

namespace App\Http\Controllers;

use App\Answer;
use App\User;
use App\Survey;
use App\Chapter;
use App\Question;
use App\SurveyChapter;
use App\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('admin.surveys.index', compact('surveys'));
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
            $input['name'] = 'Teilnehmerumfrage';
            $input['user_id'] = $user->id;
            $input['responsible_id'] =  $user['leader_id'];
            $input['survey_status_id'] = config('status.survey_aktiv');
            $survey = Survey::create($input);
            foreach($chapters as $chapter){
                $input['chapter_id'] = $chapter->id;
                $input['survey_id'] = $survey->id;
                $survey_chapter = SurveyChapter::create($input);
                $questions = Question::where('chapter_id', $chapter->id)->get();
                foreach($questions as $question){
                    $input['survey_chapter_id'] = $survey_chapter->id;
                    $input['question_id'] = $question->id;
                    $input['answer_id'] = $answer->id;
                    $input['answer_leader_id'] = $answer->id;
                    SurveyQuestion::create($input);
                }
            }
        }
        return redirect('admin/surveys');

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
    public function edit($id)
    {
        //
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
