<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Models\Role;
use App\Models\User;
use App\Models\Group;
use App\Models\CampUser;
use App\Models\Classification;
use App\Helper\Helper;
use Illuminate\Support\Str;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Auth;
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $camp = Auth::user()->camp;
        $group = $camp->group;
        return view('admin.users.index', compact('group'));
    }

    public function createDataTables()
    {
        //
        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $users = $camp->allusers;
        }
        else{
            $users = User::get();
        }

        return DataTables::of($users)
            ->addColumn('picture', function($user) {
                $path = $user->avatar ? $user->avatar : '/img/default_avatar.svg';
                return '<a href='.\URL::route('home.profile', $user['slug']).' title="Zum Profil"><img height="50" src="'.$path .'" alt=""></a>';
            })
            ->addColumn('user', function($user) {
                return '<a name='.$user['username'].' title="Person bearbeiten" href='.\URL::route('users.edit', $user['slug']).'>'.$user['username'].'</a>';
            })
            ->addColumn('role', function (User $user) {
                return $user->role ? $user->role['name'] : '';})
            ->addColumn('leader', function (User $user) {
                return $user->leader ? $user->leader['username'] : '';})
            ->addColumn('classification', function (User $user) {
                return $user->classification ? $user->classification['name'] : '';})
            ->addColumn('camp', function (User $user) {
                return $user->camp ? $user->camp['name'] : '';})
            ->addColumn('password_changed', function (User $user) {
                if(isset($user->password_change_at)){
                    return 'Ja';
                }
                else{
                    return 'Nein';
                }})
            ->rawColumns(['picture','user'])
            ->make(true);
    }

    public function searchResponseUser(Request $request)
    {
        $camp = Auth::user()->camp;
        $allusers = $camp->allusers;
        $users = User::where('role_id','<>',config('status.role_Administrator'))->whereNotIn('id', $allusers)->search($request->get('term'))->get();
        return $users;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $aktUser = Auth::user();
        if( $aktUser->isAdmin()){
            $roles = Role::pluck('name','id')->all();
            $leaders = User::where('role_id',config('status.role_Gruppenleiter'))->pluck('username','id')->all();
        }
        else{
            $roles = Role::where('id','>',config('status.role_Administrator'))->pluck('name','id')->all();
            $leaders = User::where('role_id',config('status.role_Gruppenleiter'))->where('camp_id',$aktUser->camp->id)->pluck('username','id')->all();
        }
        $classifications = Classification::pluck('name','id')->all();
        return view('admin.users.create', compact('roles', 'leaders', 'classifications'));
    }

    public function import(){
        $aktUser = Auth::user();
        $camp = $aktUser->camp;
        if($aktUser->foreign_id && $camp->foreign_id && $camp->group && $camp->group['api_token']){
            $response = Curl::to('https://db.cevi.ch/groups/' .$camp->group['foreign_id']. '/events/' .$camp['foreign_id']. '/participations.json')
                ->withData(array('token' => $camp->group['api_token']))
                ->get();
            $response = json_decode($response);
            $participants = $response->event_participations;
            $isLeader = false;
            foreach($participants as $participant){
                if ((intval($participant->links->person) === $aktUser['foreign_id']) && ($participant->roles[0]->type === "Event::Role::Leader")){
                    $isLeader = true;
                }
            }
            if($isLeader){
                foreach($participants as $participant){
                    if ($participant->roles[0]->type === "Event::Course::Role::Participant" ||
                            $participant->roles[0]->type === "Event::Role::AssistantLeader" ||
                            $participant->roles[0]->type === "Event::Role::Leader"){

                        if ($participant->links->person != $aktUser['foreign_id']){
                            $username = $participant->nickname;
                            switch($participant->roles[0]->type){
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
                            $insertData = array(
                                "username" =>  $username,
                                "email_verified_at" => now(),
                                'classification_id' => config('status.classification_green'));
                            $user = User::whereraw('LOWER(`username`) LIKE "' . mb_strtolower($username). '"')->Orwhere('foreign_id', $participant->links->person)->first();
                            if(!$user){
                                $user = User::create($insertData);
                                UserCreated::dispatch($user);
                            }
                            $user->update([
                                "role_id" => $role_id,
                                "camp_id" => $camp['id'],
                                'classification_id' => config('status.classification_green')
                            ]);

                        }
                        else{
                            $user = Auth::user();
                        }
                        if(!$user->avatar){
                            $user->update(['avatar' => 'https://db.cevi.ch'. $participant->picture->url]);
                        }
                        if(!$user->email){
                            $user->update(['email' => $participant->email]);
                        }
                        if(!$user->foreign_id)  {
                            $user->update(['foreign_id' => $participant->links->person]);
                        }
                        Helper::updateCamp($user, $camp, true);
                    }
                }
                return true;
            }
            else{
                $errorText = 'Der DB-Import steht nur den Kursleitern zur Verfügung';
                abort(412, $errorText);
            }

        }
        else{
            $errorText = '';
            if(!$camp->foreign_id){
                $errorText = 'Keine Cevi-DB-ID auf dem Kurs hinterlegt.';
            }
            if(!$aktUser->foreign_id){
                $errorText = 'Dein Cevi-DB ist noch nicht mit deinem Benutzer verknüpft.';
            }
            elseif(!$camp->group){
                $errorText = $errorText + ' Keine Gruppe auf dem Kurs hinterlegt.';
            }
            elseif(!$camp->group['api_token']){
                $errorText = $errorText + ' Deine Region hat den DB-Import nicht freigeschalten.';
            }
            abort(412, $errorText);
        }
    }

    public function uploadFile(Request $request){
        if($request->hasFile('csv_file')){

            $array = (new UsersImport)->toArray(request()->file('csv_file'));
            $importData_arr = $array[0];


            // Insert to MySQL database
            $user = Auth::user();
            $camp = $user->camp;
            foreach($importData_arr as $importData){

                $username = mb_strtolower($importData['username']);

                if($importData['rollen']==='K'){

                    $insertData = array(

                        "username"=> $username,
                        "password"=>bcrypt($importData['password']),
                        "role_id"=>config('status.role_Kursleiter'),
                        "camp_id"=>$user['camp_id'],
                        "email_verified_at" => now(),
                        'classification_id' => config('status.classification_green'));

                    $user = User::firstOrCreate(['username' => $username], $insertData);
                    Helper::updateCamp($user, $camp, true);

                }
                elseif($importData['rollen']==='G'){


                    $insertData = array(

                        "username"=> $username,
                        "password"=>bcrypt($importData['password']),
                        "role_id"=>config('status.role_Gruppenleiter'),
                        "camp_id"=>$user['camp_id'],
                        "email_verified_at" => now(),
                        'classification_id' => config('status.classification_green'));

                    $user = User::firstOrCreate(['username' => $username], $insertData);
                    Helper::updateCamp($user, $camp, true);
                }


            }
            foreach($importData_arr as $importData){

                $username = mb_strtolower($importData['username']);

                if($importData['rollen']==='T'){

                    $user = Auth::user();
                    $leader = User::where('username', $importData['leiter'])->first();

                    $insertData = array(

                        "username"=> $username,
                        "password"=>bcrypt($importData['password']),
                        "role_id"=>config('status.role_Teilnehmer'),
                        "camp_id"=>$user['camp_id'],
                        "email_verified_at" => now(),
                        "leader_id"=>$leader['id'],
                        'classification_id' => config('status.classification_green'));

                    $user = User::firstOrCreate(['username' => $username], $insertData);
                    Helper::updateCamp($user, $camp, true);
                }

            }
        }

        return redirect()->action('AdminUsersController@index');


    }

    public function download(){
        return Storage::download('file.jpg', 'Teilnehmerliste.xlsx');
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
        $this->validate($request, [
            'username' => 'required|max:255',
            'email' => 'required|unique:users|email',
            'role_id' => 'required',
            'password' => 'required',
        ], [
            'role_id.required' => 'Die Rolle muss ausgefüllt sein.']);

        $aktUser = Auth::user();
        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

        if(!$aktUser->isAdmin()){
            $camp = $aktUser->camp;
            $input['camp_id'] = $camp['id'];
            if($file = $request->file('avatar')){
                if($input['cropped_photo_id']){
                    $save_path = 'images/'.$camp['name'];
                    if (!file_exists($save_path)) {
                        mkdir($save_path, 0755, true);
                    }
                    $name = time() . str_replace(' ', '', $file->getClientOriginalName());
                    Image::make($input['cropped_photo_id'])->save($save_path.'/'.$name, 80);
                    $input['avatar'] = '/'.$save_path.'/'.$name;
                }
            }
        }
        // $input['slug'] = Str::slug($input['username']);
        $input['email_verified_at'] = now();


        $user = User::create($input);
        UserCreated::dispatch($user);
        Helper::updateCamp($user, $camp, true);

        return redirect('/admin/users/create');
    }

    public function add(Request $request)
    {
        $input = $request->all();
        if($input['user_id']){
            $aktUser = Auth::user();
            $camp = $aktUser->camp;
            $user = User::findOrFail($input['user_id']);
            CampUser::create([
                'user_id' => $user->id,
                'camp_id' => $camp->id,
                'role_id' => $input['role_id']]
            );

        }
       return redirect('/admin/users/create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
        // $user = User::findOrFail($id);
        $aktUser = Auth::user();
        $roles = Role::pluck('name','id')->all();
        $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->where('camp_id',$aktUser->camp->id)->pluck('username','id')->all();
        $classifications = Classification::pluck('name','id')->all();
        return view('admin.users.edit', compact('user','roles', 'leaders', 'classifications'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'username' => 'required|max:255|unique:users,username,' . $id,
        ]);
        $user = User::findOrFail($id);
        $aktuser = Auth::user();

        if(!$aktuser->demo){
            if(trim($request->password) == ''){
                $input = $request->except('password');
            }
            else{
                $input = $request->all();
                $input['password'] = bcrypt($request->password);
            }
            if($file = $request->file('avatar')){
                if($input['cropped_photo_id']){
                    $save_path = 'images/'.$aktuser->camp['name'];
                    if (!file_exists($save_path)) {
                        mkdir($save_path);
                    }
                    $name = time() . str_replace(' ', '', $file->getClientOriginalName());
                    Image::make($input['cropped_photo_id'])->save($save_path.'/'.$name, 80);
                    $input['avatar'] = '/'.$save_path.'/'.$name;
                }
            }
            // $input['slug'] = Str::slug($input['username']);

            $user->update($input);
           // UserCreated::dispatch($user);
            $camp_user = CampUser::firstOrCreate(['camp_id' => $aktuser->camp->id, 'user_id' =>$user->id]);
            $camp_user->update([
                'role_id' => $user['role_id'],
                'leader_id' => $user['leader_id'],
            ]);

            Helper::updateCamp($user, $aktuser->camp);
        }

        return redirect('/admin/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        User::findOrFail($id)->delete();
        return redirect('/admin/users');
    }
}
