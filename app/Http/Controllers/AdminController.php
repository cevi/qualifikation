<?php

namespace App\Http\Controllers;

use App\User;
use App\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $user = Auth::user();
        $users = User::where('camp_id',$user['camp_id'])->where('is_active',true)->pluck('id')->all();
        $surveys = Survey::with(['chapters.questions.answer','chapters.questions.answer_leader', 'user', 'responsible', 'chapters.questions.question'])
            ->whereIn('user_id', $users)->get()->sortBy('user.username')->values();
        $surveys_all = Survey::whereIn('user_id', $users)->count();
        $surveys_abgeschlossen = Survey::whereIn('user_id', $users)->where('survey_status_id', config('status.survey_abgeschlossen'))->count();
        $surveys_fertig = Survey::whereIn('user_id', $users)->where('survey_status_id', config('status.survey_fertig'))->count();
        return view('admin/index', compact('user','surveys', 'surveys_all', 'surveys_abgeschlossen','surveys_fertig'));
    }
}
