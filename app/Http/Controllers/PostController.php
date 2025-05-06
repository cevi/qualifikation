<?php

namespace App\Http\Controllers;

use Str;
use App\Models\Help;
use App\Models\Post;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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
        $camp = $aktUser->camp()->first();
        $posts_no_user = $aktUser->posts->whereNull('user_id')->where('camp_id', $camp->id);
        $posts_user = $aktUser->posts->whereNotNull('user_id')->where('camp_id', $camp->id);
        $users_select = $aktUser->camp->participants->pluck('username', 'id')->all();
        $title = 'Rückmeldungen';
        $help = Help::where('title',$title)->first();
        $post_new = new Post();    

        return view('home.posts.index', compact('aktUser', 'camp', 'posts_user', 'posts_no_user', 'users_select', 'title','help', 'post_new'));
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
       Helper::storePost($request);

        return redirect('/posts');
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
        $aktUser = Auth::user();
        $camp = $aktUser->camp()->first();
        $posts_no_user = $aktUser->posts->whereNull('user_id')->where('camp_id', $camp->id);
        $posts_user = $aktUser->posts->whereNotNull('user_id')->where('camp_id', $camp->id);
        $users_select = $aktUser->camp->participants->pluck('username', 'id')->all();
        $title = 'Rückmeldungen';
        $help = Help::where('title',$title)->first();
        $post_new = $post;    

        return view('home.posts.index', compact('aktUser', 'camp', 'posts_user', 'posts_no_user', 'users_select', 'title','help', 'post_new'));
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
