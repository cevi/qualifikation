<?php

namespace App\Http\Controllers;

use Str;
use App\Models\Camp;
use App\Models\Help;
use App\Models\User;
use App\Models\Group;
use App\Helper\Helper;
use App\Models\CampType;
use App\Events\CampCreated;
use App\Exports\PostsExport;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AdminCampsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $aktUser = Auth::user();
        if (! $aktUser) {
            return redirect('/home');
        }
      
        $camptypes = CampType::pluck('name', 'id')->all();
        $groups = Group::where('campgroup', true)->pluck('name', 'id')->all();
        $title = 'Kursübersicht';
        $help = Help::where('title',$title)->first();

        return view('admin.camps.index', compact('camptypes', 'groups', 'title', 'help'));
    }

    public function createDataTables()
    {
        //
        $aktUser = Auth::user();
        if (! $aktUser->isAdmin()) {
            if (isset($aktUser->camp)) {
                $camps = [$aktUser->camp];
            } else {
                $camps = null;
            }
        } else {
            $camps = Camp::all();
        }
        return DataTables::of($camps)
            ->addColumn('camp', function($camp){
                return '<a href="'.\URL::route('admin.camps.edit', $camp).'" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">'.$camp['name'].'</a>';
            })
            ->addColumn('username', function($camp){
                return $camp->user ? $camp->user['username'] : '';
            })
            ->addColumn('email', function($camp){
                return $camp->user ? $camp->user['email'] : '';
            })
            ->addColumn('camp_type', function($camp){
                return $camp->camp_type ? $camp->camp_type['name'] : '';
            })
            ->addColumn('group', function($camp){
                return $camp->group ? $camp->group['name'] : '';
            })
            ->addColumn('end_date', function($camp){
                return $camp->end_date > 1 ? date('d.m.Y', strtotime($camp->end_date)) : '';
            })
            ->addColumn('finish', function($camp){
                return $camp->finish ? 'Ja' : 'Nein';
            })
            ->addColumn('counter', function($camp){
                return $camp->counter ?: count($camp->surveys);
            })
            ->addColumn('actions', function($camp){
                if(!$camp->finish && (Auth::user()->camp['id'] === $camp['id']) || Auth::user()->isAdmin())
                    return '<a href="'.\URL::route('admin.camps.export').'" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Export der Rückmeldungen</a>
                    <a href="'.\URL::route('surveys.downloadPDF').'" target="_blank" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900" role="button">Druckversion aller Qualifikationen</a>';
            })
            ->rawColumns(['camp','actions'])
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
        $input = $request->all();

        if (!Auth::user()->demo) {
            $user = User::findOrFail(Auth::user()->id);

            if (!$user->isAdmin()) {
                $input['user_id'] = $user->id;
            }
            $input['status_control'] = $request->has('status_control');
            if ($input['status_control']){
                $input['survey_status_id'] = config('status.survey_neu');
            }

            $camp = Camp::create($input);
            CampCreated::dispatch($camp);
            if (!$user->isAdmin()) {
                $user->update(['camp_id' => $camp->id]);
            }
        }

        return redirect('admin/camps');
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
        $camp = Camp::findOrFail($id);
        $camptypes = CampType::pluck('name', 'id')->all();
        $groups = Group::where('campgroup', true)->pluck('name', 'id')->all();
        $users = User::where('role_id', config('status.role_Kursleiter'))->pluck('username', 'id')->all();
        $title = "Lager bearbeiten";
        $help = Help::where('title',$title)->first();
        $help['main_title'] = 'Lager';
        $help['main_route'] = '/admin/camps';
        
        return view('admin.camps.edit', compact('camp', 'users', 'camptypes', 'groups', 'title', 'help'));
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
        if (!Auth::user()->demo) {
            $camp = Camp::findOrFail($id);
            $input = $request->all();
            $input['status_control'] = $request->has('status_control');
            if ($input['status_control'] && $camp['survey_status_id'] == null){
                $input['survey_status_id'] = config('status.survey_neu');
            }
            $camp->update($input);
        }

        return redirect('/admin/camps');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Camp $camp)
    {
        if (!Auth::user()->demo){
            $users = Auth::user()->camp->allUsers;
            $camp_global = Camp::where('global_camp', true)->first();

            foreach ($users as $user) {
                Helper::updateCamp($user, $camp_global);
            }
            $counter = $camp->surveys()->count();
            foreach ($camp->camp_users_all()->get() as $camp_user) {
                $camp_user->delete();
            }
            foreach ($camp->posts()->get() as $post) {
                $post->delete();
            }
            File::deleteDirectory(storage_path('app/public/' . $camp['id'] . '_'. Str::slug($camp['name'])));
            File::deleteDirectory(storage_path('app/files/' . $camp['id'] . '_'. Str::slug($camp['name'])));
            $camp->update(['finish' => true, 'counter' => $counter]);
        }

        return redirect('/home');
    }

    public function opensurvey()
    {
        $camp = Auth::user()->camp;
        if($camp['status_control'] && $camp['survey_status_id'] < config('status.survey_1offen')){
            $camp->update(['survey_status_id' => config('status.survey_2offen')]);
        }
        else {
            $surveys = $camp->surveys;
            foreach ($surveys as $survey) {
                if ($survey['survey_status_id'] >= config('status.survey_tnAbgeschlossen')) {
                    $survey->update(['survey_status_id' => config('status.survey_2offen')]);
                }
            }
            $camp->update(['secondsurveyopen' => true]);
            if($camp['status_control'] && $camp['survey_status_id'] == config('status.survey_2offen')){
                $camp->update(['survey_status_id' => config('status.survey_tnAbgeschlossen')]);
            }
        }

        return redirect('/admin/surveys');
    }


    public function export()
    {
        $camp = Auth::user()->camp;
//        $save_path = 'files/' . $camp['id'] . '_' . Str::slug($camp['name']);
//        $directory = storage_path($save_path);
//        if (!File::isDirectory($directory)) {
//            File::makeDirectory($directory, 0775, true);
//        }
//        $posts_name = $save_path . '/exports/posts.xlsx';
        return Excel::download(new PostsExport, 'Rückmeldungen_'. Str::slug($camp['name']) . '.xlsx');
//        $users_name = $save_path .'/exports/users.xlsx';
//        Excel::store(new UsersExport, $users_name);

//        $zip = new ZipArchive;
//        $zip_file = Str::slug($camp['name']) . '.zip';
//        if($zip->open(public_path($zip_file), ZipArchive::CREATE) === TRUE)
//        {
//            Log::info('Status Zip: ' . $zip->status);

    //        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
//           $files = Storage::allFiles($save_path);
//            foreach ($files as $key => $value) {
//                Log::info('Name file: ' . $value);
//                $relativeNameInZipFile = basename($value);
//                Log::info('FilePath: ' . $relativeNameInZipFile);
//                $zip->addFile($value, $relativeNameInZipFile);
//            }
//            Log::info("Anzahl files: " . $zip->numFiles);
//            Log::info("Status Zip: " . $zip->status);
//            $zip->close();
//        }
//        return response()->download(public_path($zip_file));

//        if ($zip->open(public_path($zip_file), ZipArchive::CREATE) === TRUE)
//        {
//            $files = \File::files(storage_path($save_path));
//            foreach ($files as $key => $value) {
//                Log::info('Name file: '.$value);
//                $relativeNameInZipFile = basename($value);
//                $zip->addFile($value, $relativeNameInZipFile);
//            }
//            Log::info("Anzahl files: ".$zip->numFiles);
//            Log::info("Status Zip: ".$zip->status);
//            $zip->close();
//        }
//        return response()->download(public_path($zip_file));

//        return  redirect()->route('surveys.downloadPDF');
    }
}
