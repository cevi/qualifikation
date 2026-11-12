<?php

namespace App\Http\Controllers;

use App\Models\Help;
use App\Models\Post;
use App\Services\CampChecklist;
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
     * Übersicht nach dem Login: «Erste Schritte» (CampChecklist), Aufgaben, Zähler und eine Karte pro Quali (CampChecks).
     *
     * @return Renderable
     */
    public function index(CampChecks $checks, CampChecklist $checklist)
    {
        $aktUser = Auth::user();
        $camp = $aktUser->camp;
        $surveys = $checks->surveysFor($aktUser);
        // «Erste Schritte» nur für die Kursleitung, solange der Kurs offen ist
        $steps = $camp && $aktUser->isCampleader() && $checklist->shows($camp) ? $checklist->for($camp) : null;
        $stepsHidden = $camp && ($aktUser->demo ? session("checklist_hidden.{$camp->id}", false) : $camp->checklist_hidden);
        $tasks = $checks->dashboardTasks($aktUser, $surveys);
        $postCounts = Post::whereIn('camp_user_id', $surveys->pluck('camp_user_id'))
            ->selectRaw('camp_user_id, count(*) as total')->groupBy('camp_user_id')->pluck('total', 'camp_user_id');
        $title = 'Übersicht';
        $help = Help::where('title', $title)->first();

        return view('home.surveys', compact('aktUser', 'camp', 'surveys', 'tasks', 'postCounts', 'steps', 'stepsHidden', 'title', 'help'));
    }
}
