<?php

namespace App\Http\Controllers;

use App\Camp;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCampsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(!Auth::user()->isAdmin()){
            if(isset(Auth::user()->camp)){
                $camps = Auth::user()->camp->get();
            }
            else
            {
                $camps = null;
            }
        }
        else{
            $camps = Camp::all();
        }
        return view('admin.camps.index', compact('camps'));
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
        $input = $request->all();
        $user = User::findOrFail(Auth::user()->id);;

        if(!$user->isAdmin()){
            $input['user_id'] = $user->id;
        }

        $input['camp_status_id'] = 5;
        $camp = Camp::create($input);
        if(!$user->isAdmin()){
            $user->update(['camp_id' => $camp->id]);
        }

        return redirect('admin/camps');
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
        $camp = Camp::findOrFail($id);
        $users = User::where('role_id',2)->pluck('username','id')->all();
        return view('admin.camps.edit', compact('camp', 'users'));
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
        Camp::findOrFail($id)->update($request->all());

        return redirect('/admin/camps');
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
        Camp::findOrFail($id)->delete();
        return redirect('/admin/camps');
    }
}
