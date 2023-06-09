<?php

namespace App\Http\Controllers;

use App\Events\CampCreated;
use App\Helper\Helper;
use App\Models\Camp;
use App\Models\CampType;
use App\Models\CampUser;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CampsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $users = [];
        $camptypes = CampType::where('default_type',true)->orWhere('user_id','=', Auth::user()->id)->pluck('name', 'id')->all();
        $groups = Group::where('campgroup', true)->pluck('name', 'id')->all();
        $title = 'Kurs erstellen';
        return view('home.camps.create', compact('users', 'camptypes', 'groups', 'title'));
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
        $validator = Validator::make($request->all(), [
            'name' => 'unique:camps',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous())
                        ->withErrors($validator, 'camps')
                        ->withInput();
        }
        if (!Auth::user()->demo) {

            $input = $request->all();

            $user = User::findOrFail(Auth::user()->id);
            $input['user_id'] = $user->id;
            $input['global_camp'] = false;
            $input['status_control'] = $request->has('status_control');
            if ($input['status_control']){
                $input['survey_status_id'] = config('status.survey_neu');
            }
            $camp = Camp::create($input);
            CampCreated::dispatch($camp);
            $user->update(['camp_id' => $camp->id, 'role_id' => config('status.role_Kursleiter')]);
            CampUser::create([
                'user_id' => $user->id,
                'camp_id' => $camp->id,
                'role_id' => config('status.role_Kursleiter'),]);
        }

        return redirect('home');
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Camp $camp)
    {
        //

        if (!Auth::user()->demo) {
            Helper::updateCamp(Auth::user(), $camp);
        }

        return redirect('/home');
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
    }
}
