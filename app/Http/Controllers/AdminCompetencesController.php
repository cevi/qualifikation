<?php

namespace App\Http\Controllers;

use App\Models\Help;
use App\Models\CampType;
use App\Models\Question;
use App\Models\Competence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCompetencesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Auth::user();
        if($user->isAdmin()) {
            $competences = Competence::all();
            $camp_types = CampType::pluck('name', 'id')->all();
            $questions = Question::pluck('competence', 'id')->all();
        }
        else{
            $camp_type = $user->camp->camp_type;
            $questions = $camp_type->questions()->pluck('competence', 'id')->toArray();
            $competences = $camp_type->competences;
            $camp_types =  $user->camp->camp_type()->pluck('name', 'id')->toArray();
        }
        $title = "Kernkompetenzen";
        $help = Help::where('title',$title)->first();

        return view('admin.competences.index', compact('competences', 'questions', 'camp_types', 'help', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        Competence::create($request->all());

        return redirect('admin/competences');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Competence  $competence
     * @return \Illuminate\Http\Response
     */
    public function edit(Competence $competence)
    {
        //
        $questions = Question::pluck('competence', 'id')->all();
        $camp_types = CampType::pluck('name', 'id')->all();
        $title = "Kernkompetenz bearbeiten";
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Kernkompetenzen';
        $help['main_route'] = '/admin/competences';

        return view('admin.competences.edit', compact('competence', 'questions', 'camp_types', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Competence  $competence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Competence $competence)
    {
        //
        $competence->update($request->all());

        return redirect('/admin/competences');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Competence  $competence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Competence $competence)
    {
        //
        $competence->delete();

        return redirect('/admin/competences');
    }
}
