<?php

namespace App\Http\Controllers;

use App\User;
use App\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    //
    public function index($id)
    {   
        $user = Auth::user();
        if($user->isCampleader()){
            $users = User::where('camp_id',$user['camp_id'])->pluck('id')->all();
        }
        else
        {
            $users = User::where('leader_id',$user['id'])->pluck('id')->all();
        }
        $surveys = Survey::where('user_id', $user['id'])->orWhereIn('user_id', $users)->get()->sortBy('user.username');
        if($user->id == $id)
        {
            return view('home.user', compact('user','surveys'));
        }
        else
        {
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }
        $input['password_change_at'] = now();
        $user->update($input);

        Session::flash('message', 'Passwort erfolgreich verändert!'); 

        return redirect('/home');


    }

}
