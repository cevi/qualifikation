<?php

namespace App\Http\Controllers;

use App\Chapter;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;

class AdminChaptersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $chapters = Chapter::all();
        return view('admin.chapters.index', compact('chapters'));
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
        $attributes = [
            'number' => 'Nummer',
            'name' => 'Name',
        ];

        $validator = Validator::make($request->all(), [
            'number' => 'required',
            'name' => 'required',
        ], [], $attributes);

        if ($validator->fails()) {
            return redirect('admin/chapters')
                        ->withErrors($validator)
                        ->withInput();
        }

        Chapter::create($request->all());

        return redirect('admin/chapters');
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
        $chapter = Chapter::findOrFail($id);
        return view('admin.chapters.edit', compact('chapter'));
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
        Chapter::findOrFail($id)->update($request->all());

        return redirect('/admin/chapters');
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
        Chapter::findOrFail($id)->delete();
        return redirect('/admin/chapters');
    }


}
