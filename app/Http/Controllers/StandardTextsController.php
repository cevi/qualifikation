<?php

namespace App\Http\Controllers;

use App\Models\Help;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\StandardText;
use Illuminate\Support\Facades\Auth;

class StandardTextsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = Auth::user();
        if($user->isAdmin()) {
            $standard_texts = StandardText::all();
        }
        else{
            $camp = $user->camp;
            $standard_texts = StandardText::where('camp_id', $camp->id)
                ->orWhere('global', true)
                ->get();
        }
        $title = 'Standard-Text';
        $help = Help::where('title',$title)->first();
        return view('admin.standard_texts.index', compact('title', 'standard_texts', 'help'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $title = 'Standard-Texterstellen';
        $help = Help::where('title',$title)->first();
        return view('admin.standard_texts.create', compact('title', 'help'));
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
        if (!Auth::user()->demo) {
            $input = $request->all();
            $camp = Auth::user()->camp;
            $input['camp_id'] = $camp->id;
            StandardText::create($input);
        }
        return redirect('/admin/standard_texts');

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
    public function edit(StandardText $standard_text)
    {
        //
        $title = 'Standart-Text bearbeiten';
        $help = Help::where('title',$title)->first();
        $help['main_title'] = "Standart-Texte";
        $help['main_route'] ='/admin/standard_texts';
        return view('admin.standard_texts.edit', compact('standard_text', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StandardText $standard_text)
    {
        //
        $standard_text->update($request->all());

        return redirect('/admin/standard_texts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(StandardText $standard_text)
    {
        //
        $standard_text->delete();

        return redirect('/admin/standard_texts');
    }
}
