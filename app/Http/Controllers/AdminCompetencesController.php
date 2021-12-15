<?php

namespace App\Http\Controllers;

use App\CampType;
use App\Question;
use App\Competence;
use Illuminate\Http\Request;

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
        $competences = Competence::all();
        $questions = Question::pluck('competence','id')->all();
        $camp_types = CampType::pluck('name','id')->all();
        return view('admin.competences.index', compact('competences', 'questions', 'camp_types'));
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
        $questions = Question::pluck('competence','id')->all();
        $camp_types = CampType::pluck('name','id')->all();
        return view('admin.competences.edit', compact('competence', 'questions', 'camp_types'));
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
