<?php

namespace App\Http\Controllers;

use App\Post;
use App\Role;
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
        $aktUser = Auth::user();
        if($aktUser->isCampleader()){
            $users = User::where('camp_id',$aktUser['camp_id'])->pluck('id')->all();
        }
        else
        {
            $users = User::where('leader_id',$aktUser['id'])->pluck('id')->all();
        }
        $surveys = Survey::where('user_id', $aktUser['id'])->orWhereIn('user_id', $users)->get()->sortBy('user.username');
        if($aktUser->id == $id)
        {
            return view('home.user', compact('aktUser','surveys'));
        }
        else
        {
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        //
        $aktUser = Auth::user();
        if($aktUser->isCampleader()){
            $users = User::where('camp_id',$aktUser['camp_id'])->pluck('id')->all();
        }
        else
        {
            $users = User::where('leader_id',$aktUser['id'])->pluck('id')->all();
        }
        $user = User::findOrFail($id);
        $posts = Post::where('user_id',$id)->get();
        $roles = Role::pluck('name','id')->all();
        $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->pluck('username','id')->all();
        $survey = Survey::with(['chapters.questions.answer','chapters.questions.answer_leader', 'user', 'responsible', 'chapters.questions.question'])
            ->where('user_id', $id)->get()->values();
        $surveys = Survey::where('user_id', $aktUser['id'])->orWhereIn('user_id', $users)->get()->sortBy('user.username');
        return view('home.profile', compact('user','roles', 'leaders', 'survey', 'surveys', 'posts'));
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

    public function changeClassifications($id, $color)
    {
        $aktUser = Auth::user();
        $user = User::findOrFail($id);
        if($aktUser->isCampleader() || $aktUser['id'] == $user['leader_id']){
            $user->update(['classification_id' => $color]);    
        }
        return true;
    }

}
