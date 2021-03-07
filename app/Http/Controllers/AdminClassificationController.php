<?php

namespace App\Http\Controllers;

use App\classification;
use Illuminate\Http\Request;

class AdminClassificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $classifications = Classification::all();
        return view('admin.classifications.index', compact('classifications'));
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\classification  $classification
     * @return \Illuminate\Http\Response
     */
    public function show(classification $classification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\classification  $classification
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $classification = Classification::findOrFail($id);
        return view('admin.classifications.edit', compact('classification'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\classification  $classification
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(classification $classification)
    {
        //
    }
}
