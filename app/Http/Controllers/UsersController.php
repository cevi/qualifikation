<?php

namespace App\Http\Controllers;

use Str;
use App\Models\Help;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use App\Helper\Helper;
use App\Models\Survey;
use App\Models\CampUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

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
        $camp = $aktUser->camp()->first();
        if ($aktUser->id == $user->id) {
            
            $title = 'Profil';
            $help = Help::where('title',$title)->first();
            $post_new = new Post();
            return view('home.user', compact('aktUser', 'camp', 'title', 'help', 'post_new'));
        } else {
            return redirect()->back();
        }
    }

    public function edit(User $user)
    {
        //
        $aktUser = Auth::user();
        if (! $aktUser->isTeilnehmer()) {
            $camp = $aktUser->camp()->first();
            $camp_user = CampUser::where('user_id', $user['id'])->where('camp_id', $aktUser->camp['id'])->first();
            if(! $camp_user) {
                return redirect()->back();
            }
            $posts = Post::where('user_id', $user->id)->get()->sortByDesc('created_at');
            $roles = Role::pluck('name', 'id')->all();
            $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->pluck('username', 'id')->all();
            $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'campuser.user', 'chapters.questions.question'])
                ->where('camp_user_id', $camp_user->id)->get()->values();

            $title = "Profil";
            $group = $user->group['shortname'] ?? '';
            $subtitle = "von " . $user['username'] . " " . $group;
            $help = Help::where('title',$title)->first();

            $labels = Helper::GetSurveysLabels($surveys);
            $datasets = Helper::GetSurveysDataset($surveys);

            $post_new = new Post();
            return view('home.profile', compact('user', 'roles', 'leaders', 'surveys', 'posts', 'camp', 'camp_user', 'title', 'labels', 'datasets', 'subtitle', 'help', 'post_new'));
        } else {
            return redirect()->back();
        }
    }

    public function editPost(User $user, Post $post)
    {
        //
        $aktUser = Auth::user();
        if (! $aktUser->isTeilnehmer()) {
            $camp = $aktUser->camp()->first();
            $camp_user = CampUser::where('user_id', $user['id'])->where('camp_id', $aktUser->camp['id'])->first();

            $posts = Post::where('user_id', $user->id)->get()->sortByDesc('created_at');
            $roles = Role::pluck('name', 'id')->all();
            $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->pluck('username', 'id')->all();
            $surveys = Survey::with(['chapters.questions.answer_first', 'chapters.questions.answer_second', 'chapters.questions.answer_leader', 'campuser.user', 'chapters.questions.question'])
                ->where('camp_user_id', $camp_user->id)->get()->values();

            $title = "Profil";
            $group = $user->group['shortname'] ?? '';
            $subtitle = "von " . $user['username'] . " " . $group;
            $help = Help::where('title',$title)->first();

            $labels = Helper::GetSurveysLabels($surveys);
            $datasets = Helper::GetSurveysDataset($surveys);

            $post_new = $post;    

            return view('home.profile', compact('user', 'roles', 'leaders', 'surveys', 'posts', 'camp', 'camp_user', 'title', 'labels', 'datasets', 'subtitle', 'help', 'post_new'));
        } else {
            return redirect()->back();
        }
    }

    
    public function storePost(Request $request, User $user)
    {
        
       Helper::storePost($request, $user);
        return redirect()->route('home.profile', ['user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user->demo) {
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
