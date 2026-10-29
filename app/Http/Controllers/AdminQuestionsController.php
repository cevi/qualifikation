<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Help;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AdminQuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $user = Auth::user();
        if ($user->isAdmin()) {
            $questions = Question::all();
            $chapters = Chapter::where('default_chapter', true)->pluck('name', 'id')->all();
        } else {
            $camp_Type = $user->camp->camp_type;
            $questions = $camp_Type->questions;
            $chapters = $camp_Type->chapters->pluck('name', 'id')->all();
        }
        $title = 'Kompetenzen';
        $help = Help::where('title', $title)->first();

        return view('admin.questions.index', compact('questions', 'chapters', 'title', 'help'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //

        $user = Auth::user();
        if (! $user->demo) {

            $input = $request->all();
            if (! $user->isAdmin()) {
                $camp_type = $user->camp->camp_type;
                $input['camp_type_id'] = $camp_type['id'];
            }
            $question = new Question($input);
            $this->authorize('manage', $question);
            $question->save();
        }

        return redirect('admin/questions');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
        $user = Auth::user();
        $question = Question::findOrFail($id);
        $this->authorize('manage', $question);
        if ($user->isAdmin()) {
            $chapters = Chapter::where('default_chapter', true)->pluck('name', 'id')->all();
        } else {
            $camp_Type = $user->camp->camp_type;
            $chapters = $camp_Type->chapters->pluck('name', 'id')->all();
        }
        $title = 'Kompetenz bearbeiten';
        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Kompetenzen';
        $help['main_route'] = '/admin/questions';

        return view('admin.questions.edit', compact('question', 'chapters', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
        // return $request;
        $question = Question::findOrFail($id);
        $this->authorize('manage', $question);
        // Auch das Zielkapitel muss zu einem eigenen Kurstyp gehören.
        $question->fill($request->only(['name', 'number', 'competence', 'chapter_id', 'sort-index']));
        $this->authorize('manage', $question->setRelations([]));
        $question->save();

        return redirect('/admin/questions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
        $question = Question::findOrFail($id);
        $this->authorize('manage', $question);
        $question->delete();

        return redirect('/admin/questions');
    }
}
