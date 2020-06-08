<?php

namespace App\Http\Controllers;

use App\Camp;
use App\Role;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $users = User::where('camp_id', $camp['id'])->where('is_active', true)->get();
        }
        else{
            $users = User::where('is_active', true)->get();
        }
        return view('admin.users.index', compact('users'));
    }

    public function usersList()
    {
        if(!Auth::user()->isAdmin()){
            $camp = Auth::user()->camp;
            $users = User::where('camp_id', $camp['id'])->get();
        }
        else{
            $users = User::all();
        }
        return Datatables::of($users)->make(true);
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
        $leaders = User::where('role_id',config('status.role_Gruppenleiter'))->pluck('username','id')->all();
        return view('admin.users.create', compact('roles', 'leaders'));
    }

    public function uploadFile(Request $request){
        if($request->hasFile('csv_file')){
            $file = $request->file('csv_file');
      
            // File Details 
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
      
            // Valid File Extensions
            $valid_extension = array("csv");
      
            // 2MB in Bytes
            $maxFileSize = 2097152; 
      
            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
      
              // Check file size
              if($fileSize <= $maxFileSize){
      
                // File upload location
                $location = 'uploads';
      
                // Upload file
                $file->move($location,$filename);
      
                // Import CSV to Database
                $filepath = public_path($location."/".$filename);
      
                // Reading file
                $file = fopen($filepath,"r");
      
                $importData_arr = array();
                $i = 0;
      
                while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
                   $num = count($filedata );
                   
                   // Skip first row (Remove below comment if you want to skip the first row)
                   if($i == 0){
                      $i++;
                      continue; 
                   }
                   for ($c=0; $c < $num; $c++) {
                      $importData_arr[$i][] = $filedata [$c];
                   }
                   $i++;
                }
                fclose($file);

      
                // Insert to MySQL database
                foreach($importData_arr as $importData){
                    if($importData[1]==='K'){
                        $user = Auth::user();

                        $insertData = array(
                        
                            "username"=>$importData[3],
                            "password"=>bcrypt($importData[4]),
                            "role_id"=>config('status.role_Lagerleiter'),
                            "is_active"=>true,
                            "camp_id"=>$user['camp_id']);
                        User::create($insertData);
                    }
                    elseif($importData[1]==='G'){
                        
                        $user = Auth::user();

                        $insertData = array(
                        
                            "username"=>$importData[3],
                            "password"=>bcrypt($importData[4]),
                            "role_id"=>config('status.role_Gruppenleiter'),
                            "is_active"=>true,
                            "camp_id"=>$user['camp_id']);
                        User::create($insertData);
                    }

      
                }
                foreach($importData_arr as $importData){
                    if($importData[1]==='T'){
                        
                        $user = Auth::user();
                        $leader = User::where('username', $importData[5])->first();

                        $insertData = array(
                        
                            "username"=>$importData[3],
                            "password"=>bcrypt($importData[4]),
                            "role_id"=>config('status.role_Teilnehmer'),
                            "is_active"=>true,
                            "camp_id"=>$user['camp_id'],
                            "leader_id"=>$leader['id']);
                        User::create($insertData);
                    }
      
                }
              }
      
            }
        }

        
        return redirect()->action('AdminUsersController@index');

        
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
    public function edit($id)
    {
        //
        $user = User::findOrFail($id);
        $roles = Role::pluck('name','id')->all();
        $leaders = User::where('role_id', config('status.role_Gruppenleiter'))->pluck('username','id')->all();
        return view('admin.users.edit', compact('user','roles', 'leaders'));
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

        if(trim($request->password) == ''){
            $input = $request->except('password');
        }
        else{
            $input = $request->all();
            $input['password'] = bcrypt($request->password);
        }

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
