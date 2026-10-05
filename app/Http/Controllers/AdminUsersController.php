<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Events\UserCreated;
use App\Helper\Helper;
use App\Models\Camp;
use App\Models\CampUser;
use App\Models\Group;
use App\Models\Help;
use App\Models\Role as RoleModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Str;
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
        $help = Help::where('title', $title)->first();

        // Server-side parsing of the sorting order query parameter
        $defaultOrder = [[3, 'asc'], [4, 'asc'], [0, 'asc']];
        $initialOrder = $defaultOrder;
        if ($orderParam = request('order')) {
            if (is_string($orderParam) && strlen($orderParam) <= 100) {
                $parsed = [];
                $items = explode(',', $orderParam);
                $items = array_slice($items, 0, 10);
                foreach ($items as $item) {
                    $parts = explode(':', $item);
                    if (count($parts) === 2 && is_numeric($parts[0]) && in_array($parts[1], ['asc', 'desc'])) {
                        $colIndex = (int) $parts[0];
                        if ($colIndex >= 0 && $colIndex <= 9) {
                            $parsed[] = [$colIndex, $parts[1]];
                        }
                    }
                }
                if (count($parsed) > 0) {
                    $initialOrder = $parsed;
                }
            }
        }

        return view('admin.users.index', compact('has_api_token', 'title', 'help', 'initialOrder', 'defaultOrder'));
    }

    public function createDataTables()
    {
        //
        $camp = Auth::user()->camp;
        $users = $camp->allusers;

        return DataTables::of($users)
            ->addColumn('picture', function ($user) {
                return '<a href='.\URL::route('home.profile', $user->slug).' title="Zum Profil"><img class="img-user rounded-full" alt="" src="'.$user->getAvatar().'"></a>';
            })
            ->addColumn('user', function ($user) {
                return '<a name='.$user['username'].' title="Person bearbeiten" href='.\URL::route('admin.users.edit', $user['slug']).'>'.$user['username'].'</a>';
            })
            ->addColumn('role', function (User $user) {
                $camp = Auth::user()->camp;
                if ($camp) {
                    $camp_user = CampUser::where('camp_id', '=', $camp['id'])->where('user_id', '=', $user['id'])->first();

                    return $camp_user && $camp_user->role ? $camp_user->role['name'] : '';
                }

                return $user->role ? $user->role['name'] : '';
            })
            ->addColumn('leader', function (User $user) {
                return $user->leader ? $user->leader['username'] : '';
            })
            ->addColumn('group', function (User $user) {
                return $user->group ? $user->group['shortname'] : '';
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

        $users = User::where('role_id', '<>', Role::Administrator)->whereNotIn('id', $allusers)->search($request->get('term'))->get();

        // Nur was die Autovervollständigung beim Hinzufügen braucht.
        return $users->map->only(['id', 'username', 'email'])->values();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        $aktUser = Auth::user();
        if ($aktUser->isAdmin()) {
            $roles = RoleModel::pluck('name', 'id')->all();
        } else {
            $roles = RoleModel::where('id', '>', Role::Administrator->value)->pluck('name', 'id')->all();
        }
        $groups = Group::orderByRaw("CASE WHEN shortname IS NULL OR TRIM(shortname) = '' THEN 1 ELSE 0 END")
            ->orderBy('campgroup', 'DESC')
            ->orderBy('shortname')
            ->get()
            ->mapWithKeys(function ($group) {
                $shortname = trim((string) $group->shortname);
                $label = $shortname !== '' ? $shortname.' - '.$group->name : $group->name;

                return [$group->id => $label];
            })
            ->all();
        $leader_campUsers_Id = CampUser::where('camp_id', $aktUser->camp->id)
            ->where(function ($query) {
                $query->where('role_id', Role::Gruppenleiter)
                    ->Orwhere('role_id', Role::Kursleiter)
                    ->Orwhere('role_id', Role::Stabsleiter);
            })
            ->pluck('user_id')->all();
        $leaders = User::whereIn('id', $leader_campUsers_Id)->pluck('username', 'id')->all();
        $title = 'Person erstellen / suchen';
        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Personen';
        $help['main_route'] = '/admin/users';

        return view('admin.users.create', compact('roles', 'leaders', 'title', 'help', 'groups'));
    }

    public function import()
    {
        if (Auth::user()->demo) {
            return;
        }

        $aktUser = Auth::user();
        $camp = $aktUser->camp;

        // if (!$aktUser->foreign_id) {
        //     return response()->json(['error' => 'Dein Cevi-DB-Benutzer ist noch nicht mit deinem Benutzer verknüpft.', 'ok' => false], 404);
        // }
        if (! $camp->foreign_id) {
            return response()->json(['error' => 'Keine Cevi-DB-ID auf dem Kurs hinterlegt.', 'ok' => false], 404);
        }
        if (! $camp->group) {
            return response()->json(['error' => 'Keine Gruppe auf dem Kurs hinterlegt.', 'ok' => false], 404);
        }
        if (! $camp->group['api_token']) {
            return response()->json(['error' => 'Deine Region hat den DB-Import nicht freigeschalten.', 'ok' => false], 404);
        }

        $token = Crypt::decryptString($camp->group['api_token']);
        $baseUrl = rtrim($camp->group['url'], '/');

        // Fetch all participations with sideloaded person and role data, following pagination links
        $participations = [];
        $included = [];
        // Without an explicit sort the API orders participations by created_at, which is not
        // unique for bulk-created rows. Paginating a non-unique order can return a row twice
        // and skip another, so we sort by id.
        $url = '/api/event_participations?'.http_build_query([
            'filter[event_id][eq]' => $camp['foreign_id'],
            'include' => 'participant,roles',
            'sort' => 'id',
        ]);

        while ($url) {
            $url = $baseUrl.$url;
            $body = Http::withHeaders(['X-TOKEN' => $token])
                ->accept('application/vnd.api+json')
                ->get($url)
                ->json();
            $participations = array_merge($participations, $body['data'] ?? []);
            $included = array_merge($included, $body['included'] ?? []);
            $url = $body['links']['next'] ?? null;
        }

        // The API answers 200 with an empty `included` when the service token lacks the
        // `people` scope or the `layer_and_below_read` permission, instead of returning an
        // error. Without this check the missing roles would later surface as the unrelated
        // "nur den Kursleitern" message.
        if ($participations && ! $included) {
            return response()->json([
                'error' => 'Die Cevi-DB hat keine Personen- und Rollendaten geliefert. '
                    .'Der API-Key deiner Region braucht die Rechte "Personen" und "Anlässe" '
                    .'sowie die Berechtigung "layer_and_below_read".',
                'ok' => false,
            ], 404);
        }

        return Helper::importParticipations($aktUser, $camp, $participations, $included);
    }

    /**
     * Teilnehmende aus einem JSON-Export der Cevi-DB importieren (gleiche Logik wie der API-Import).
     */
    public function uploadFile(Request $request)
    {
        if (! Auth::user()->demo && $request->hasFile('csv_file')
            && $request->file('csv_file')->getClientOriginalExtension() === 'json') {
            $aktUser = Auth::user();
            $camp = $aktUser->camp;
            $body = json_decode(file_get_contents($request->file('csv_file')), true);
            Helper::importParticipations($aktUser, $camp, $body['data'], $body['included']);
        }

        return redirect()->action('AdminUsersController@index');
    }

    public function store(Request $request)
    {
        //
        $aktUser = Auth::user();
        // Wie im Formular (create()): Administrator vergeben nur Admins.
        $roleRules = ['required'];
        if (! $aktUser->isAdmin()) {
            $roleRules[] = Rule::notIn([Role::Administrator->value]);
        }
        $this->validate($request, [
            'username' => 'required|max:255',
            'email' => 'required|unique:users|email',
            'role_id' => $roleRules,
            'password' => 'required',
        ], [
            'role_id.required' => 'Die Rolle muss ausgefüllt sein.', ]);

        if (! Auth::user()->demo) {
            $camp = $aktUser->camp;
            // Nur die Felder des Formulars, das Startpasswort eingeschlossen.
            $input = $request->only(['username', 'email', 'role_id', 'leader_id', 'group_id']);
            $input['password'] = bcrypt($request->password);

            if (! $aktUser->isAdmin()) {
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

        if (! Auth::user()->demo) {
            if ($input['user_id']) {
                $aktUser = Auth::user();
                $camp = $aktUser->camp;
                $user = User::findOrFail($input['user_id']);
                UserCreated::dispatch($user);
                $user->update(['leader_id' => null]);
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
        $this->authorize('manage', $user);
        $aktUser = Auth::user();
        $roles = RoleModel::where('id', '>', Role::Administrator->value)->pluck('name', 'id')->all();
        $groups = Group::orderByRaw("CASE WHEN shortname IS NULL OR TRIM(shortname) = '' THEN 1 ELSE 0 END")
            ->orderBy('campgroup', 'DESC')
            ->orderBy('shortname')
            ->get()
            ->mapWithKeys(function ($group) {
                $shortname = trim((string) $group->shortname);
                $label = $shortname !== '' ? $shortname.' - '.$group->name : $group->name;

                return [$group->id => $label];
            })
            ->all();
        $leader_campUsers_Id = CampUser::where('camp_id', $aktUser->camp->id)
            ->where(function ($query) {
                $query->where('role_id', Role::Gruppenleiter)
                    ->Orwhere('role_id', Role::Kursleiter)
                    ->Orwhere('role_id', Role::Stabsleiter);
            })
            ->pluck('user_id')->all();
        $leaders = User::whereIn('id', $leader_campUsers_Id)->pluck('username', 'id')->all();
        $title = 'Person bearbeiten';
        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Personen';
        $help['main_route'] = '/admin/users';

        return view('admin.users.edit', compact('user', 'roles', 'leaders', 'title', 'help', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('manage', $user);
        $this->validate($request, [
            'username' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            // Die Rollenauswahl im Formular bietet Administrator nicht an.
            'role_id' => ['nullable', Rule::notIn([Role::Administrator->value])],
        ]);
        $aktuser = Auth::user();
        $camp = $aktuser->camp;

        if (! $aktuser->demo) {
            // Nur die Felder des Formulars. Passwörter ändern Personen selbst auf ihrer Kontoseite.
            $input = $request->only(['username', 'email', 'role_id', 'leader_id', 'group_id']);

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
    public function destroy(User $user)
    {
        $this->authorize('manage', $user);

        if (! Auth::user()->demo) {
            $camp = Auth::user()->camp;
            $camp_global = Camp::where('global_camp', true)->first();
            Helper::updateCamp($user, $camp_global);
            CampUser::where('camp_id', $camp->id)->where('user_id', $user->id)->delete();
        }

        return redirect('/admin/users');
    }
}
