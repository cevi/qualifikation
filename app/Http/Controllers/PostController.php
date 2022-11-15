<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $posts_no_user = $aktUser->posts->whereNull('user_id');
        $posts_user = $aktUser->posts->whereNotNull('user_id');
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
        if ($file = $request->file('file')) {
            $save_path = 'images/'.Str::slug($camp['name']).'/files';
            if (! file_exists($save_path)) {
                mkdir($save_path, 0755, true);
            }
            $name = time().'_'.str_replace(' ', '', $file->getClientOriginalName());

            $file->move($save_path, $name);
            $input['file'] = $save_path.'/'.$name;
        }

        if (! $input['post_id']) {
            Post::create($input);
        } else {
            $post = Post::findOrFail($input['post_id']);
            $post->update($input);
        }

        return redirect()->back();
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
        $post->delete();

        return redirect()->back();
    }
}
