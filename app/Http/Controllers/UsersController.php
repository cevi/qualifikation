<?php

namespace App\Http\Controllers;

use App\Post;
use App\Role;
use App\User;
use App\Survey;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    //
    public function index($id)
    {   
        $aktUser = Auth::user();
        $users = Helper::getUsers($aktUser);
        $users_id = [];
        if($users){
            $users_id = $users->pluck('id')->all();
        }
        if($aktUser->id == $id)
        {
            return view('home.user', compact('aktUser', 'users'));
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
        if(!$aktUser->isTeilnehmer()){
            $users = Helper::getUsers($aktUser);
            $users_id = [];
            if($users){
                $users_id = $users->pluck('id')->all();
            }

            $user = User::findOrFail($id);
            $posts = Post::where('user_id',$id)->get()->sortByDesc('created_at');
            $roles = Role::pluck('name','id')->all();
            $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->pluck('username','id')->all();
            $surveys = Survey::with(['chapters.questions.answer_first','chapters.questions.answer_second','chapters.questions.answer_leader', 'user', 'chapters.questions.question'])
                ->where('user_id', $id)->get()->values();
            return view('home.profile', compact('user','roles', 'leaders', 'surveys', 'posts', 'users'));
        }
        else {
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
