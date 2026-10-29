<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Help;
use App\Models\StandardText;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        if ($user->isAdmin()) {
            $standard_texts = StandardText::all();
        } else {
            $camp = $user->camp;
            $standard_texts = Helper::getStandardTextsForCamp($camp->id);
        }
        $title = 'Standard-Text';
        $help = Help::where('title', $title)->first();

        return view('admin.standard_texts.index', compact('title', 'standard_texts', 'help'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
        if (! Auth::user()->demo) {
            $input = $request->only(['title', 'content']);
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
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(StandardText $standard_text)
    {
        $this->authorize('manage', $standard_text);
        //
        $title = 'Standard-Text bearbeiten';
        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Standard-Text';
        $help['main_route'] = '/admin/standard_texts';

        return view('admin.standard_texts.edit', compact('standard_text', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, StandardText $standard_text)
    {
        $this->authorize('manage', $standard_text);
        //
        $standard_text->update($request->only(['title', 'content']));

        return redirect('/admin/standard_texts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(StandardText $standard_text)
    {
        $this->authorize('manage', $standard_text);
        //
        $standard_text->delete();

        return redirect('/admin/standard_texts');
    }
}
