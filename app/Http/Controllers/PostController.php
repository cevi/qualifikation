<?php

namespace App\Http\Controllers;

use App\Models\HealthForm;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Str;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $aktUser = Auth::user();
        $camp = $aktUser->camp;
        $posts_no_user = $aktUser->posts->whereNull('user_id')->where('camp_id', $camp->id);
        $posts_user = $aktUser->posts->whereNotNull('user_id')->where('camp_id', $camp->id);
        $users = $aktUser->camp->participants;
        $users_select = [];
        if ($users) {
            $users_select = $users->pluck('username', 'id')->all();
        }
        $title = 'Rückmeldungen';

        return view('home.posts.index', compact('aktUser', 'users', 'posts_user', 'posts_no_user', 'users_select', 'title'));
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
        $aktUser = Auth::user();
        $camp = $aktUser->camp;

        $input = $request->all();
        $input['leader_id'] = $aktUser->id;
        $input['camp_id'] = $camp->id;
        $input['show_on_survey'] = $request->has('show_on_survey');
        if (!$aktUser->demo && $file = $request->file('file')) {
            $save_path = 'app/files/' . Str::slug($camp['name']);
            $directory = storage_path($save_path);
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0775, true);
            }
            $input['uuid'] = Str::uuid();
            $name = str_replace(' ', '', $file->getClientOriginalName());
            $file->move($directory, $name);
            $input['file'] = $save_path . '/' . $name;
        }
        else{
            $input['file'] = null;
        }

        if (!$input['post_id']) {
            Post::create($input);
        } else {
            $post = Post::findOrFail($input['post_id']);
            $post->update($input);
        }

        return redirect()->back();
    }

    public function downloadFile($id)
    {
        //
        $post = Post::where('uuid', $id)->firstOrFail();
        $camp_post = $post->camp;
        $aktUser = Auth::user();
        $camp_user = $aktUser->camp;
        if (($camp_post->id == $camp_user->id) && !$aktUser->isTeilnehmer()){
            return response()->download(storage_path($post['file']));
        } else {
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
        if($post['file']){
            unlink(storage_path($post['file']));
        }
        $post->delete();

        return redirect()->back();
    }
}
