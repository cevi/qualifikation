<?php

namespace App\Http\Controllers;

use App\User;
use App\Answer;
use App\Survey;
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
        $user = Auth::user();
        if($user->isCampleader()){
            $users = User::where('camp_id',$user['camp_id'])->pluck('id')->all();
        }
        else
        {
            $users = User::where('leader_id',$user['id'])->pluck('id')->all();
        }
        $surveys = Survey::where('user_id', $user['id'])->orWhereIn('user_id', $users)->get()->sortBy('user.username');
        $answers = Answer::all();
        return view('home.surveys', compact('user', 'surveys', 'answers'));
    }
}
