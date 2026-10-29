<?php

namespace App\Http\Controllers;

use App\Models\Help;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminHelpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $helps = Help::all();
        $title = 'Hilfe';

        $help = Help::where('title', $title)->first();

        return view('admin.helps.index', compact('helps', 'title', 'help'));
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
        Help::create($request->all());

        return redirect('admin/helps');
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Help $help)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Help $help)
    {
        //
        $title = 'Hilfe bearbeiten';

        $help_article = Help::where('title', $title)->first();
        $help_article['main_route'] = '/admin/helps';
        $help_article['main_title'] = 'Hilfe';

        return view('admin.helps.edit', compact('help', 'help_article', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Help $help)
    {
        //
        $help->update($request->all());

        return redirect('admin/helps');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Help $help)
    {
        //
        $help->delete();

        return redirect('admin/helps');
    }
}
