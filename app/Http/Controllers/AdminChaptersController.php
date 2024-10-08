<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Help;
use App\Models\Chapter;
use App\Models\CampType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        if($user->isAdmin()) {
            $chapters = Chapter::all();
        }
        else{
            $chapters = $user->camp->camp_type->chapters;
        }
        $title = "Kapitel";
        $help = Help::where('title',$title)->first();
        return view('admin.chapters.index', compact('chapters', 'title', 'help'));
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

        $user = Auth::user();
        if(!$user->demo) {

            $input = $request->all();
            if (!$user->isAdmin()) {
                $camp_type = $user->camp->camp_type;
                $input['camp_type_id'] = $camp_type['id'];
            }
            Chapter::create($input);
        }

        return redirect('admin/chapters');
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
        $title = "Kapitel bearbeiten";
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Kapitel';
        $help['main_route'] = '/admin/chapters';

        return view('admin.chapters.edit', compact('chapter', 'help', 'title'));
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
