<?php

namespace App\Http\Controllers;

use App\Camp;
use App\User;
use App\CampStatus;
use App\CampType;
use App\Group;
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
        $aktUser = Auth::user();
        if(!$aktUser->isAdmin()){
            if(isset($aktUser->camp)){
                $camps = [$aktUser->camp];
            }
            else
            {
                $camps = null;
            }
        }
        else{
            $camps = Camp::all();
        }
        $camptypes = CampType::pluck('name','id')->all();
        $groups = Group::where('campgroup',true)->pluck('name','id')->all();
        return view('admin.camps.index', compact('camps', 'camptypes', 'groups'));
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
        $camptypes = CampType::pluck('name','id')->all();
        $groups = Group::where('campgroup',true)->pluck('name','id')->all();
        $users = User::where('role_id', config('status.role_Kursleiter'))->pluck('username','id')->all();
        return view('admin.camps.edit', compact('camp', 'users', 'camptypes', 'groups'));
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
        
        $user = Auth::user();
        $camp = Camp::where('user_id',$user['id'])->first();
        $users = User::where('camp_id',$camp['id'])->where(function($query){
                $query->where('role_id', config('status.role_Teilnehmer'))->orWhere('role_id', config('status.role_Gruppenleiter'));
            });
        if(count($users->get())>0){
            $users->delete();
        }
        Camp::findOrFail($id)->delete();
        return redirect('/admin/camps');
    }

    public function opensurvey()
    {
        $camp = Auth::user()->camp;
        $camp->update(['secondsurveyopen' => true]);
        return redirect('/admin/surveys');
    }
}
