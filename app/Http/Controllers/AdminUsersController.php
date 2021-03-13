<?php

namespace App\Http\Controllers;

use App\Camp;
use App\Classification;
use App\Role;
use App\User;
use Illuminate\Support\Str;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AdminUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.users.index');
    }

    public function createDataTables()
    {
        //
        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $users = User::where('camp_id', $camp['id'])->where('is_active', true)->get();
        }
        else{
            $users = User::where('is_active', true)->get();
        }

        return DataTables::of($users)
            ->addColumn('picture', function($user) {
                $path = $user->avatar ? $user->avatar : 'http://placehold.it/50x50';
                return '<a href='.\URL::route('home.profile', $user['slug']).'><img height="50" src="'.$path .'" alt=""></a>';
            })
            ->addColumn('user', function($user) {
                return '<a name='.$user['username'].' href='.\URL::route('users.edit', $user['slug']).'>'.$user['username'].'</a>';
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

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        
        if(Auth::user()->isAdmin()){
            $roles = Role::pluck('name','id')->all();
        }
        else{
            $roles = Role::where('id','>',config('status.role_Administrator'))->pluck('name','id')->all(); 
        }
        $classifications = Classification::pluck('name','id')->all();
        $leaders = User::where('role_id',config('status.role_Gruppenleiter'))->pluck('username','id')->all();
        return view('admin.users.create', compact('roles', 'leaders', 'classifications'));
    }

    public function uploadFile(Request $request){
        if($request->hasFile('csv_file')){
          
            $array = (new UsersImport)->toArray(request()->file('csv_file'));
            $importData_arr = $array[0];
            
      
            // Insert to MySQL database
            $user = Auth::user();
            foreach($importData_arr as $importData){

                $username = mb_strtolower($importData['username']);

                if($importData['rollen']==='K'){
                    
                    $insertData = array(
                    
                        "username"=> $username,
                        "password"=>bcrypt($importData['password']),
                        "role_id"=>config('status.role_Lagerleiter'),
                        "is_active"=>true,
                        "camp_id"=>$user['camp_id'],
                        'classification_id' => config('status.classification_green'));

                    User::firstOrCreate(['username' => $username], $insertData);

                }
                elseif($importData['rollen']==='G'){
                    

                    $insertData = array(
                    
                        "username"=> $username,
                        "password"=>bcrypt($importData['password']),
                        "role_id"=>config('status.role_Gruppenleiter'),
                        "is_active"=>true,
                        "camp_id"=>$user['camp_id'],
                        'classification_id' => config('status.classification_green'));

                    User::firstOrCreate(['username' => $username], $insertData);
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
                        "is_active"=>true,
                        "camp_id"=>$user['camp_id'],
                        "leader_id"=>$leader['id'],
                        'classification_id' => config('status.classification_green'));

                    User::firstOrCreate(['username' => $username], $insertData);
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
        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $input['camp_id'] = $camp['id'];
        }
        $input['api_token'] = Str::random(60);
        $input['classification_id'] = config('status.classification_green');
        $input['slug'] = Str::slug($input['username']);

        User::create($input);

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
        $roles = Role::pluck('name','id')->all();
        $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->pluck('username','id')->all();
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
        $user = User::findOrFail($id);
        $aktuser = Auth::user();

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
                    mkdir($save_path, 666, true);
                }
                $name = time() . str_replace(' ', '', $file->getClientOriginalName());
                Image::make($input['cropped_photo_id'])->save($save_path.'/'.$name, 80);  
                $input['avatar'] = '/'.$save_path.'/'.$name;
            }
        }
        $input['slug'] = Str::slug($input['username']);

        $user->update($input);

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
