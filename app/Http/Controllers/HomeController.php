<?php

namespace App\Http\Controllers;

use App\Models\Help;
use App\Helper\Helper;
use App\Models\Survey;
use App\Models\CampUser;
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
        $camp = $aktUser->camp()->first();
        $surveys = [];
        if ($aktUser->camp) {
            if ($aktUser->isCampleader() || $aktUser->role_id == config('status.role_Stabsleiter')) {
                $camp_users_id = CampUser::where('camp_id', $aktUser->camp['id'])->pluck('id')->all();
            } else {
                $camp_users_id = CampUser::where('leader_id', $aktUser['id'])->where('camp_id', $aktUser->camp['id'])->pluck('id')->all();
            }
            $camp_user = CampUser::where('user_id', $aktUser['id'])->where('camp_id', $aktUser->camp['id'])->first();
            $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'chapters.questions.question'])
                ->where('camp_user_id', $camp_user['id'])
                ->orWhereIn('camp_user_id', $camp_users_id)->get()->sortBy('campuser.user.username')->values();
        }
        $title = 'Übersicht';
        $help = Help::where('title',$title)->first();


        $labels = Helper::GetSurveysLabels($surveys);
        $datasets = Helper::GetSurveysDataset($surveys);

        return view('home.surveys', compact('aktUser', 'camp', 'surveys', 'title', 'labels', 'datasets', 'help'));
    }
}
