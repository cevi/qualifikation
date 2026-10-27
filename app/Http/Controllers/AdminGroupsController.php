<?php

namespace App\Http\Controllers;

use App\Models\Camp;
use App\Models\Group;
use App\Models\Help;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

class AdminGroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $groups = Group::all();
        $title = 'Gruppen';
        $help = Help::where('title', $title)->first();

        return view('admin.groups.index', compact('groups', 'title', 'help'));
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
        $input = $request->all();
        $input['api_token'] = Crypt::encryptString($request->api_token);
        $input['campgroup'] = true;
        Group::create($input);

        return redirect('admin/groups');
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
    public function edit($id)
    {
        //
        $group = Group::findOrFail($id);
        $title = 'Gruppe bearbeiten';
        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Gruppen';
        $help['main_route'] = '/admin/groups';

        return view('admin.groups.edit', compact('group', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $input = $request->all();
        $input['api_token'] = Crypt::encryptString($request->api_token);
        $group->update($input);

        return redirect('/admin/groups');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Group $group)
    {
        // users.group_id und camps.group_id verweisen ohne cascade auf die Gruppe; die Datenbank würde das Löschen
        // mit einem Fehler ablehnen.
        $users = User::where('group_id', $group->id)->count();
        $camps = Camp::where('group_id', $group->id)->count();
        if ($users > 0 || $camps > 0) {
            return back()->withErrors([
                'group' => "Die Gruppe wird noch von {$users} Person(en) und {$camps} Kurs(en) verwendet und kann nicht gelöscht werden.",
            ]);
        }

        $group->delete();

        return redirect('/admin/groups');
    }
}
