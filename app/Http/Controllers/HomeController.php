<?php

namespace App\Http\Controllers;

use App\User;
use App\Answer;
use App\Survey;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $aktUser = Auth::user();
        $users = Helper::getUsers($aktUser);
        if($aktUser->isCampleader()){
            $users_id = $users->pluck('id')->all();
        }
        else {
            $users_id = User::where('leader_id',$aktUser['id'])->pluck('id')->all();
        }
        $surveys = Survey::with(['chapters.questions.answer_first','chapters.questions.answer_second','chapters.questions.answer_leader','chapters.questions.question'])
            ->where('user_id', $aktUser ['id'])
            ->orWhereIn('user_id', $users_id)->get()->sortBy('user.username')->values();

        $users_id = [];
        if($users){
            $users_id = $users->pluck('id')->all();
        }
        return view('home.surveys', compact('aktUser', 'users', 'surveys'));
    }
}
