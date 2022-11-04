<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackCreated;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Ixudra\Curl\Facades\Curl;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $feedbacks = Feedback::all();
        return view('admin.feedback.index', compact('feedbacks'));
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if(!Auth::user()->demo) {
            $input = $request->all();
            $input['user_id'] = Auth::user()->id;
            $feedback = Feedback::create($input);
            Mail::to(config('mail.camp.address'))->send(new FeedbackCreated($feedback));
        }
        return redirect('admin/changes')->with('success', 'Vielen Dank für die Rückmeldung.');
    }

    public function send(Request $request)
    {
        //
        $input = $request->all();
        $input['bug'] = $request->has('bug');
        $body = array(
            'title' => $input['title'],
            'body' => $input['description'],
            'assignees' => [config('auth.github.user')],
            'labels' => $input['bug'] ? ['enhancement','bug'] : ['enhancement'],
        );
        $issue = Curl::to(config('auth.github.api_url'). '/issues')
            ->withHeader('Accept: application/vnd.github+json')
            ->withHeader('User-Agent: '.config('auth.github.user'))
            ->withBearer(config('auth.github.token'))
            ->withData($body)
            ->asJson(true)
            ->post();
        //
        return redirect('admin/feedback')->with('success', 'Issue wurde erstellt.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Feedback $feedback)
    {
        //
        return view('admin.feedback.edit', compact('feedback'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feedback $feedback)
    {
        //
        $feedback->update($request->all());

        return redirect('/admin/feedback');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feedback $feedback)
    {
        //
        $feedback->delete();
        return redirect('/admin/feedback');
    }
}
