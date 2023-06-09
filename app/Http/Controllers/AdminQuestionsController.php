<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminQuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Auth::user();
        if($user->isAdmin()) {
            $questions = Question::all();
            $chapters = Chapter::where('default_chapter',true)->pluck('name', 'id')->all();
        }
        else{
            $camp_Type = $user->camp->camp_type;
            $questions = $camp_Type->questions;
            $chapters = $camp_Type->chapters->pluck('name', 'id')->all();
        }

        return view('admin.questions.index', compact('questions', 'chapters'));
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

        $user = Auth::user();
        if(!$user->demo) {

            $input = $request->all();
            if (!$user->isAdmin()) {
                $camp_type = $user->camp->camp_type;
                $input['camp_type_id'] = $camp_type['id'];
            }
            Question::create($input);
        }

        return redirect('admin/questions');
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
        $user = Auth::user();
        $question = Question::findOrFail($id);
        if($user->isAdmin()) {
            $chapters = Chapter::where('default_chapter',true)->pluck('name', 'id')->all();
        }
        else{
            $camp_Type = $user->camp->camp_type;
            $chapters = $camp_Type->chapters->pluck('name', 'id')->all();
        }

        return view('admin.questions.edit', compact('question', 'chapters'));
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
        // return $request;
        if ($request['competence_js1'] === null) {
            $request['competence_js1'] = 0;
        }
        if ($request['competence_js2'] === null) {
            $request['competence_js2'] = 0;
        }
        // return $request;
        Question::findOrFail($id)->update($request->all());

        return redirect('/admin/questions');
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
        Question::findOrFail($id)->delete();

        return redirect('/admin/questions');
    }

    public function uploadFile(Request $request)
    {
        if ($request->input('submit') != null) {
            $file = $request->file('file');

            // File Details
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = ['csv'];

            // 2MB in Bytes
            $maxFileSize = 2097152;

            // Check file extension
            if (in_array(strtolower($extension), $valid_extension)) {
                // Check file size
                if ($fileSize <= $maxFileSize) {
                    // File upload location
                    $location = 'uploads';

                    // Upload file
                    $file->move($location, $filename);

                    // Import CSV to Database
                    $filepath = public_path($location.'/'.$filename);

                    // Reading file
                    $file = fopen($filepath, 'r');

                    $importData_arr = [];
                    $i = 0;

                    while (($filedata = fgetcsv($file, 1000, ';')) !== false) {
                        $num = count($filedata);

                        // Skip first row (Remove below comment if you want to skip the first row)
                        /*if($i == 0){
                           $i++;
                           continue;
                        }*/
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);

                    // Insert to MySQL database
                    foreach ($importData_arr as $importData) {
                        $chapter = Chapter::where('number', $importData[0])->first();

                        $insertData = [

                            'chapter_id' => $chapter['id'],
                            'number' => $importData[1],
                            'competence' => $importData[2],
                            'name' => $importData[3], ];
                        Question::create($insertData);
                    }

                    Session::flash('message', 'Import Successful.');
                } else {
                    Session::flash('message', 'File too large. File must be less than 2MB.');
                }
            } else {
                Session::flash('message', 'Invalid File Extension.');
            }
        }

        // Redirect to index
        return redirect()->action('AdminQuestionsController@index');
    }
}
