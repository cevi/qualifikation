<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackCreated;
use App\Models\Feedback;
use App\Models\Help;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Ixudra\Curl\Facades\Curl;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $feedbacks = Feedback::all();
        $title = 'Feedbacks';
        $help = Help::where('title', $title)->first();

        return view('admin.feedback.index', compact('feedbacks', 'title', 'help'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
        if (! Auth::user()->demo) {
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
        $body = [
            'title' => $input['title'],
            'body' => $input['description'],
            'assignees' => [config('auth.github.user')],
            'labels' => $input['bug'] ? ['enhancement', 'bug'] : ['enhancement'],
        ];
        $issue = Curl::to(config('auth.github.api_url').'/issues')
            ->withHeader('Accept: application/vnd.github+json')
            ->withHeader('User-Agent: '.config('auth.github.user'))
            ->withBearer(config('auth.github.token'))
            ->withData($body)
            ->asJson(true)
            ->returnResponseObject()
            ->post();

        // GitHub antwortet mit 201 und dem neuen Issue; sonst (abgelehnt, nicht erreichbar) Fehler zeigen
        if ($issue->status !== 201) {
            return redirect('admin/feedback')->withErrors(['github' => 'Das Issue konnte nicht erstellt werden (GitHub: '.($issue->content['message'] ?? $issue->error ?? 'keine Antwort').').']);
        }

        return redirect('admin/feedback')->with('success', 'Issue wurde erstellt.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Feedback $feedback)
    {
        //
        $title = 'Feedback bearbeiten';

        $help = Help::where('title', $title)->first();
        $help['main_route'] = '/admin/feedback';
        $help['main_title'] = 'Feedbacks';

        return view('admin.feedback.edit', compact('feedback', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, Feedback $feedback)
    {
        //

        if (! Auth::user()->demo) {
            $feedback->update($request->all());
        }

        return redirect('/admin/feedback');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Feedback $feedback)
    {
        //

        if (! Auth::user()->demo) {
            $feedback->delete();
        }

        return redirect('/admin/feedback');
    }
}
