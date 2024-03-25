<?php

namespace App\Http\Controllers;

use Str;
use File;
use App\Models\Help;
use App\Models\Role;
use App\Models\User;
use App\Helper\Helper;
use App\Models\CampUser;
use App\Events\UserCreated;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AdminUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $camp = Auth::user()->camp;
        $group = $camp->group;
        if ($group) {
            $has_api_token = (bool) $group->api_token;
        } else {
            $has_api_token = false;
        }

        $title = 'Personen';
        $help = Help::where('title',$title)->first();
        
        return view('admin.users.index', compact('has_api_token', 'title', 'help'));
    }

    public function createDataTables()
    {
        //
        if (! Auth::user()->isAdmin()) {
            $camp = Auth::user()->camp;
            $users = $camp->allusers;
        } else {
            $users = User::get();
        }

        return DataTables::of($users)
            ->addColumn('picture', function ($user) {
                return '<a href='.\URL::route('home.profile', $user->slug).' title="Zum Profil"><img class="img-user" alt="" src="' . $user->getAvatar() . '"></a>';
            })
            ->addColumn('user', function ($user) {
                return '<a name='.$user['username'].' title="Person bearbeiten" href='.\URL::route('users.edit', $user['slug']).'>'.$user['username'].'</a>';
            })
            ->addColumn('role', function (User $user) {
                $camp = null;
                if (!Auth::user()->isAdmin()) {
                    $camp = Auth::user()->camp;
                }
                if ($camp) {
                    $camp_user = CampUser::where('camp_id','=',$camp['id'])->where('user_id','=',$user['id'])->first();
                    return [
                        'display' => $camp_user->role ? $camp_user->role['name'] : '',
                        'sort' => $camp_user->role ? $camp_user->role['id'] : '',
                    ];
                }
                else {
                    return [
                        'display' => $user->role ? $user->role['name'] : '',
                        'sort' => $user->role ? $user->role['id'] : '',
                    ];
                }
            })
            ->addColumn('leader', function (User $user) {
                return $user->leader ? $user->leader['username'] : '';
            })
            ->addColumn('classification', function (User $user) {
                $camp = Auth::user()->camp;
                $camp_user = CampUser::where('user_id', '=', $user['id'])->where('camp_id', '=', $camp['id'])->first();

                return $camp_user && $camp_user->classification ? $camp_user->classification['name'] : '';
            })
            ->addColumn('camp', function (User $user) {
                return $user->camp ? $user->camp['name'] : '';
            })
            ->addColumn('password_changed', function (User $user) {
                if (isset($user->password_change_at)) {
                    return 'Ja';
                } else {
                    return 'Nein';
                }
            })
            ->rawColumns(['picture', 'user'])
            ->make(true);
    }

    public function searchResponseUser(Request $request)
    {
        $camp = Auth::user()->camp;
        $allusers = $camp->allusers->pluck('id')->toArray();

        $users = User::where('role_id', '<>', config('status.role_Administrator'))->whereNotIn('id', $allusers)->search($request->get('term'))->get();
        return $users;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        $aktUser = Auth::user();
        if ($aktUser->isAdmin()) {
            $roles = Role::pluck('name', 'id')->all();
        } else {
            $roles = Role::where('id', '>', config('status.role_Administrator'))->pluck('name', 'id')->all();
        }
        $leader_campUsers_Id = CampUser::where('camp_id', $aktUser->camp->id)
            ->where(function ($query) {
                $query->where('role_id', config('status.role_Gruppenleiter'))
                    ->Orwhere('role_id', config('status.role_Kursleiter'))
                    ->Orwhere('role_id', config('status.role_Stabsleiter'));
            })
            ->pluck('user_id')->all();
        $leaders = User::whereIn('id', $leader_campUsers_Id)->pluck('username', 'id')->all();
        $title = "Person erstellen / suchen";
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Personen';
        $help['main_route'] = '/admin/users';

        return view('admin.users.create', compact('roles', 'leaders', 'title', 'help'));
    }

    public function import()
    {

        if (!Auth::user()->demo) {
            $aktUser = Auth::user();
            $camp = $aktUser->camp;
            if ($aktUser->foreign_id && $camp->foreign_id && $camp->group && $camp->group['api_token']) {
                $url = 'https://db.cevi.ch/groups/' . $camp->group['foreign_id'] . '/events/' . $camp['foreign_id'] . '/participations.json';
                $response = Curl::to($url)
                    ->withData(['token' => Crypt::decryptString($camp->group['api_token'])])
                    ->get();
                $response = json_decode($response);
                $participants = $response->event_participations;
                for ($i = 1; $i < $response->total_pages; $i++) {
                    $url = $response->next_page_link;
                    $response = Curl::to($url)
                        ->withData([
                            'page' => $i + 1,
                            'token' => Crypt::decryptString($camp->group['api_token'])
                        ])
                        ->get();
                    $response = json_decode($response);
                    $participants = array_merge($participants, $response->event_participations);
                }
                $isLeader = false;
                foreach ($participants as $participant) {
                    if ((intval($participant->links->person) === $aktUser['foreign_id']) && ($participant->roles[0]->type === 'Event::Role::Leader')) {
                        $isLeader = true;
                    }
                }
                if ($isLeader) {
                    foreach ($participants as $participant) {
                        if ($participant->roles[0]->type === 'Event::Course::Role::Participant' ||
                            $participant->roles[0]->type === 'Event::Role::AssistantLeader' ||
                            $participant->roles[0]->type === 'Event::Role::Leader') {
                            if ($participant->links->person != $aktUser['foreign_id']) {
                                $username = $participant->nickname ? $participant->nickname : $participant->first_name;
                                switch ($participant->roles[0]->type) {
                                    case  'Event::Course::Role::Participant':
                                        $role_id = config('status.role_Teilnehmer');
                                        break;
                                    case  'Event::Role::AssistantLeader':
                                        $role_id = config('status.role_Gruppenleiter');
                                        break;
                                    case  'Event::Role::Leader':
                                        $role_id = config('status.role_Kursleiter');
                                        break;
                                }
                                $insertData = [
                                    'username' => $username,
                                    'email' => $participant->email,
                                    'email_verified_at' => now(),
                                    'classification_id' => config('status.classification_yellow'),];
                                $user = User::whereraw('LOWER(`email`) LIKE "' . mb_strtolower($participant->email) . '"')->Orwhere('foreign_id', $participant->links->person)->first();
                                if (!$user) {
                                    $user = User::create($insertData);
                                    UserCreated::dispatch($user);
                                }
                                $user->update([
                                    'role_id' => $role_id,
                                    'camp_id' => $camp['id'],
                                    'classification_id' => config('status.classification_yellow'),
                                ]);
                            } else {
                                $user = Auth::user();
                            }
//                        if(!$user->avatar){
//                            $user->update(['avatar' => 'https://db.cevi.ch'. $participant->picture->url]);
//                        }
                            if (!$user->email) {
                                $user->update(['email' => $participant->email]);
                            }
                            if (!$user->foreign_id) {
                                $user->update(['foreign_id' => $participant->links->person]);
                            }
                            Helper::updateCamp($user, $camp);
                        }
                    }

                    return true;
                } else {
                    $errorText = 'Der DB-Import steht nur den Kursleitern zur Verfügung';
                    abort(412, $errorText);
                }
            } else {
                $errorText = '';
                if (!$camp->foreign_id) {
                    $errorText = 'Keine Cevi-DB-ID auf dem Kurs hinterlegt.';
                }
                if (!$aktUser->foreign_id) {
                    $errorText = 'Dein Cevi-DB-Benutzer ist noch nicht mit deinem Benutzer verknüpft.';
                } elseif (!$camp->group) {
                    $errorText = $errorText + ' Keine Gruppe auf dem Kurs hinterlegt.';
                } elseif (!$camp->group['api_token']) {
                    $errorText = $errorText + ' Deine Region hat den DB-Import nicht freigeschalten.';
                }
                abort(412, $errorText);
            }
        }
    }

    public function uploadFile(Request $request)
    {

        if (!Auth::user()->demo) {
            if ($request->hasFile('csv_file')) {
                $array = (new UsersImport)->toArray(request()->file('csv_file'));
                $importData_arr = $array[0];

                // Insert to MySQL database
                $user = Auth::user();
                $camp = $user->camp;
                foreach ($importData_arr as $importData) {
                    $username = mb_strtolower($importData['ceviname']);

                    if ($importData['rollen'] === 'K') {
                        $insertData = [

                            'username' => $username,
                            'email' => $importData['e_mail'],
                            'password' => bcrypt($importData['passwort']),
                            'role_id' => config('status.role_Kursleiter'),
                            'camp_id' => $user['camp_id'],
                            'email_verified_at' => now(),
                            'classification_id' => config('status.classification_yellow'),];

                        $user = User::firstOrCreate(['email' => $insertData['email']], $insertData);
                        UserCreated::dispatch($user);
                        Helper::updateCamp($user, $camp);
                    } elseif ($importData['rollen'] === 'G') {
                        $insertData = [

                            'username' => $username,
                            'email' => $importData['e_mail'],
                            'password' => bcrypt($importData['passwort']),
                            'role_id' => config('status.role_Gruppenleiter'),
                            'camp_id' => $user['camp_id'],
                            'email_verified_at' => now(),
                            'classification_id' => config('status.classification_yellow'),];

                        $user = User::firstOrCreate(['email' => $insertData['email']], $insertData);
                        UserCreated::dispatch($user);
                        Helper::updateCamp($user, $camp);
                    }
                }
                foreach ($importData_arr as $importData) {
                    $username = mb_strtolower($importData['ceviname']);

                    if ($importData['rollen'] === 'T') {
                        $user = Auth::user();
                        $leader = User::where('email', $importData['leiter'])->first();

                        $insertData = [

                            'username' => $username,
                            'email' => $importData['e_mail'],
                            'password' => bcrypt($importData['passwort']),
                            'role_id' => config('status.role_Teilnehmer'),
                            'camp_id' => $user['camp_id'],
                            'email_verified_at' => now(),
                            'leader_id' => $leader['id'],
                            'classification_id' => config('status.classification_yellow'),];

                        $user = User::firstOrCreate(['email' => $insertData['email']], $insertData);
                        UserCreated::dispatch($user);
                        Helper::updateCamp($user, $camp);
                    }
                }
            }
        }

        return redirect()->action('AdminUsersController@index');
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'username' => 'required|max:255',
            'email' => 'required|unique:users|email',
            'role_id' => 'required',
            'password' => 'required',
        ], [
            'role_id.required' => 'Die Rolle muss ausgefüllt sein.', ]);

        $aktUser = Auth::user();

        if (!Auth::user()->demo) {
            $camp = $aktUser->camp;
            if (trim($request->password) == '') {
                $input = $request->except('password');
            } else {
                $input = $request->all();
                $input['password'] = bcrypt($request->password);
            }

            if (!$aktUser->isAdmin()) {
                $input['camp_id'] = $camp['id'];
            }
            // $input['slug'] = Str::slug($input['username']);
            $input['email_verified_at'] = now();

            $user = User::create($input);
            UserCreated::dispatch($user);
            Helper::updateCamp($user, $camp);
            Helper::updateAvatar($request, $user);
        }

        return redirect('/admin/users/create');
    }

    public function add(Request $request)
    {
        $input = $request->all();

        if (!Auth::user()->demo) {
            if ($input['user_id']) {
                $aktUser = Auth::user();
                $camp = $aktUser->camp;
                $user = User::findOrFail($input['user_id']);
                UserCreated::dispatch($user);
                $user->update(['leader_id' => NULL]);
                Helper::updateCamp($user, $camp);
            }
        }

        return redirect('/admin/users/create');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
        // $user = User::findOrFail($id);
        $aktUser = Auth::user();
        $roles = Role::where('id', '>', config('status.role_Administrator'))->pluck('name', 'id')->all();
        $leader_campUsers_Id = CampUser::where('camp_id', $aktUser->camp->id)
            ->where(function ($query) {
                $query->where('role_id', config('status.role_Gruppenleiter'))
                    ->Orwhere('role_id', config('status.role_Kursleiter'))
                    ->Orwhere('role_id', config('status.role_Stabsleiter'));
            })
            ->pluck('user_id')->all();
        $leaders = User::whereIn('id', $leader_campUsers_Id)->pluck('username', 'id')->all();
        $title = "Person bearbeiten";
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Personen';
        $help['main_route'] = '/admin/users';

        return view('admin.users.edit', compact('user', 'roles', 'leaders', 'title', 'help'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'username' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);
        $user = User::findOrFail($id);
        $aktuser = Auth::user();
        $camp = $aktuser->camp;

        if (! $aktuser->demo) {
            if (trim($request->password) == '') {
                $input = $request->except('password');
            } else {
                $input = $request->all();
                $input['password'] = bcrypt($request->password);
            }
            // $input['slug'] = Str::slug($input['username']);

            $user->update($input);
            // UserCreated::dispatch($user);
            $camp_user = CampUser::firstOrCreate(['camp_id' => $aktuser->camp->id, 'user_id' => $user->id]);
            $camp_user->update([
                'role_id' => $user['role_id'],
                'leader_id' => $user['leader_id'],
            ]);

            Helper::updateCamp($user, $aktuser->camp);
            Helper::updateAvatar($request, $user);
        }

        return redirect('/admin/users');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //

        if (!Auth::user()->demo) {
            User::findOrFail($id)->delete();
        }

        return redirect('/admin/users');
    }

}
