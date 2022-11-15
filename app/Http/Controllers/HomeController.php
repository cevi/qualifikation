<?php

namespace App\Http\Controllers;

use App\Models\CampUser;
use App\Models\Survey;
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
        $users = [];
        $surveys = [];
        if ($aktUser->camp) {
            $users = $aktUser->camp->participants;
            if ($aktUser->isCampleader()) {
                $camp_users_id = CampUser::where('camp_id', $aktUser->camp['id'])->pluck('id')->all();
            } else {
                $camp_users_id = CampUser::where('leader_id', $aktUser['id'])->where('camp_id', $aktUser->camp['id'])->pluck('id')->all();
            }
            $camp_user = CampUser::where('user_id', $aktUser['id'])->where('camp_id', $aktUser->camp['id'])->first();
            $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'chapters.questions.question'])
                ->where('camp_user_id', $camp_user['id'])
                ->orWhereIn('camp_user_id', $camp_users_id)->get()->sortBy('campuser.user.username')->values();
        }
        $users_id = [];
        if ($users) {
            $users_id = $users->pluck('id')->all();
        }
        $title = 'Übersicht';

        return view('home.surveys', compact('aktUser', 'users', 'surveys', 'title'));
    }
}
