<?php

namespace App\Http\Controllers;

use App\Models\Help;
use App\Models\Post;
use App\Services\CampChecks;
use Illuminate\Contracts\Support\Renderable;
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
     * Übersicht nach dem Login: Aufgaben, Zähler und eine Karte pro Quali (siehe CampChecks).
     *
     * @return Renderable
     */
    public function index(CampChecks $checks)
    {
        $aktUser = Auth::user();
        $camp = $aktUser->camp;
        $surveys = $checks->surveysFor($aktUser);
        $tasks = $checks->dashboardTasks($aktUser, $surveys);
        $postCounts = Post::whereIn('camp_user_id', $surveys->pluck('camp_user_id'))
            ->selectRaw('camp_user_id, count(*) as total')->groupBy('camp_user_id')->pluck('total', 'camp_user_id');
        $title = 'Übersicht';
        $help = Help::where('title', $title)->first();

        return view('home.surveys', compact('aktUser', 'camp', 'surveys', 'tasks', 'postCounts', 'title', 'help'));
    }
}
