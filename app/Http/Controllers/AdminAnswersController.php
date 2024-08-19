<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Help;
use Illuminate\Http\Request;

class AdminAnswersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $answers = Answer::all();
        $title= 'Antworten';
        $help = Help::where('title',$title)->first();

        return view('admin.answers.index', compact('answers', 'title', 'help'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        Answer::create($request->all());

        return redirect('admin/answers');
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
        $answer = Answer::findOrFail($id);
        $title= 'Antworten bearbeiten';
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Antworten';
        $help['main_route'] = '/admin/answers';

        return view('admin.answers.edit', compact('answer', 'title', 'help'));
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
        Answer::findOrFail($id)->update($request->all());

        return redirect('/admin/answers');
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
        Answer::findOrFail($id)->delete();

        return redirect('/admin/answers');
    }
}
