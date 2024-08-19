<?php

namespace App\Http\Controllers;

use App\Models\Help;
use App\Models\User;
use App\Models\CampType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampTypesController extends Controller
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
            $camp_types = CampType::all();
        }
        else{
            $chapters = $user->camp_types;
        }
        $title = 'Kurs-Typen';
        $help = Help::where('title',$title)->first();
        return view('admin.camp_types.index', compact('title', 'camp_types', 'help'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $title = 'Kurs-Typ erstellen';
        $help = Help::where('title',$title)->first();
        return view('home.camp_types.create', compact('title', 'help'));
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
            $user = User::findOrFail(Auth::user()->id);
            $input['user_id'] = $user->id;
            CampType::create($input);
        }
        return redirect()->route('home.camps.create');

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
    public function edit(CampType $camp_type)
    {
        //
        $title = 'Kurs-Typ bearbeiten';
        $help = Help::where('title',$title)->first();
        $help['main_title'] = "Kurs-Typen";
        $help['main_route'] ='/admin/camp_types';
        return view('admin.camp_types.edit', compact('camp_type', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CampType $camp_type)
    {
        //
        $camp_type->update($request->all());

        return redirect('/admin/camp_types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CampType $camp_type)
    {
        //
        $camp_type->delete();

        return redirect('/admin/camp_types');
    }
}
