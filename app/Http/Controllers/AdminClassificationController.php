<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Help;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminClassificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $classifications = Classification::all();
        $title = 'Klassifizierungen';
        $help = Help::where('title', $title)->first();

        return view('admin.classifications.index', compact('classifications', 'title', 'help'));
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\classification  $classification
     * @return Response
     */
    public function show(Classification $classification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\classification  $classification
     * @return Response
     */
    public function edit($id)
    {
        //
        $classification = Classification::findOrFail($id);
        $title = 'Klassifizierung bearbeiten';
        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Klassifizierungen';
        $help['main_route'] = '/admin/classifications';

        return view('admin.classifications.edit', compact('classification', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\classification  $classification
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
        Classification::findOrFail($id)->update($request->all());

        return redirect('/admin/classifications');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\classification  $classification
     * @return Response
     */
    public function destroy(Classification $classification)
    {
        //
    }
}
