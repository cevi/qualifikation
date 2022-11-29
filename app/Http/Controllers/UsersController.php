<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\CampUser;
use App\Models\Post;
use App\Models\Role;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(User $user)
    {
        $aktUser = Auth::user();
        if (! $aktUser) {
            return redirect('/home');
        }
        $users = $aktUser->camp->participants;
        $users_id = [];
        if ($users) {
            $users_id = $users->pluck('id')->all();
        }
        if ($aktUser->id == $user->id) {
            return view('home.user', compact('aktUser', 'users'));
        } else {
            return redirect()->back();
        }
    }

    public function edit(User $user)
    {
        //
        $aktUser = Auth::user();
        if (! $aktUser->isTeilnehmer()) {
            $users = $aktUser->camp->participants;
            $users_id = [];
            if ($users) {
                $users_id = $users->pluck('id')->all();
            }
            $camp_user = CampUser::where('user_id', $user['id'])->where('camp_id', $aktUser->camp['id'])->first();

            $posts = Post::where('user_id', $user->id)->get()->sortByDesc('created_at');
            $roles = Role::pluck('name', 'id')->all();
            $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->pluck('username', 'id')->all();
            $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'campuser.user', 'chapters.questions.question'])
                ->where('camp_user_id', $camp_user->id)->get()->values();

            $title = $user['username'];

            $labels = Helper::GetSurveyLabels($surveys);
            $datasets = Helper::GetSurveysDataset($surveys);

            return view('home.profile', compact('user', 'roles', 'leaders', 'surveys', 'posts', 'users', 'camp_user', 'title', 'labels', 'datasets'));
        } else {
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if (trim($request->password) == '') {
            $input = $request->except('password');
            $user->update($input);
        } else {
            $request->validate([
                'password' => ['required', 'confirmed'],
            ]);
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
            $input['password_change_at'] = now();
            $user->update($input);
            Session::flash('message', 'Passwort erfolgreich verändert!');
        }

        return redirect('/home');
    }

    public function changeClassifications($id, $color)
    {
        $aktUser = Auth::user();
        $camp = $aktUser->camp;
        $camp_user = CampUser::where('user_id', '=', $id)->where('camp_id', '=', $camp['id'])->first();
        $user = User::findOrFail($id);
        if ($aktUser->isCampleader() || $aktUser['id'] == $camp_user['leader_id']) {
            $camp_user->update(['classification_id' => $color]);
        }

        return true;
    }
}
