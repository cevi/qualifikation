<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $users = User::where('camp_id', $camp['id'])->where('is_active', true)->get();
        }
        else{
            $users = User::where('is_active', true)->get();
        }
        return view('admin.users.index', compact('users'));
    }

    public function usersList()
    {
        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $users = User::where('camp_id', $camp['id'])->get();
        }
        else{
            $users = User::all();
        }
        return Datatables::of($users)->make(true);
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $roles = Role::pluck('name','id')->all();
        $leaders = User::where('role_id',3)->pluck('username','id')->all();
        return view('admin.users.create', compact('roles', 'leaders'));
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
        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $input['camp_id'] = $camp['id'];
        }

        User::create($input);

        return redirect('/admin/users/create');
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
        $user = User::findOrFail($id);
        $roles = Role::pluck('name','id')->all();
        $leaders = User::where('role_id',3)->pluck('username','id')->all();
        return view('admin.users.edit', compact('user','roles', 'leaders'));
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
        $user = User::findOrFail($id);

        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

        $user->update($input);

        return redirect('/admin/users');
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
        User::findOrFail($id)->delete();
        return redirect('/admin/users');
    }
}
