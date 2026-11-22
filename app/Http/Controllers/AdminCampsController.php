<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\SurveyStatus;
use App\Events\CampCreated;
use App\Exports\PostsExport;
use App\Helper\Helper;
use App\Models\Camp;
use App\Models\CampType;
use App\Models\CampUser;
use App\Models\Group;
use App\Models\Help;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Str;

class AdminCampsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $aktUser = Auth::user();
        if (! $aktUser) {
            return redirect('/home');
        }

        return $this->indexView();
    }

    /** Kursübersicht; mit $editCamp ist der Dialog «Kurs bearbeiten» beim Laden offen. */
    private function indexView(?array $editCamp = null)
    {
        $camptypes = CampType::pluck('name', 'id')->all();
        $groups = Group::where('campgroup', true)->pluck('name', 'id')->all();
        $title = 'Kursübersicht';
        $help = Help::where('title', $title)->first();

        return view('admin.camps.index', compact('camptypes', 'groups', 'title', 'help', 'editCamp'));
    }

    /** Abschliessen nur bei offenen Kursen; das Global-Camp nimmt beim Abschliessen alle Personen auf und bleibt. */
    private function canFinish(Camp $camp): bool
    {
        return ! $camp->finish && ! $camp->global_camp;
    }

    /** Werte für den Dialog «Kurs bearbeiten» (resources/js/pages/camps-index.js). */
    private function editData(Camp $camp): array
    {
        return [
            'id' => $camp->id,
            'name' => $camp->name,
            'user_id' => $camp->user_id,
            'camp_type_id' => $camp->camp_type_id,
            'group_id' => $camp->group_id,
            'status_control' => (bool) $camp->status_control,
            'end_date' => $camp->end_date ? date('Y-m-d', strtotime($camp->end_date)) : '',
            'foreign_id' => $camp->foreign_id,
            'can_finish' => $this->canFinish($camp),
            // Liste statt Objekt, sonst sortiert JavaScript nach ID statt nach Name
            'leaders' => collect($camp->campLeaderOptions())->map(fn ($name, $id) => ['id' => $id, 'name' => $name])->values()->all(),
            'update_url' => route('admin.camps.update', $camp),
            'destroy_url' => route('admin.camps.destroy', $camp),
        ];
    }

    /**
     * Zeilen der Kursliste (admin/camps/index, Vue-Tabelle): nur Text und URLs, Links und Knöpfe baut die Tabelle.
     */
    public function tableData()
    {
        $aktUser = Auth::user();
        if (! $aktUser->isAdmin()) {
            $camps = collect($aktUser->camp ? [$aktUser->camp] : []);
        } else {
            $camps = Camp::all();
        }

        return response()->json(['data' => $camps->map(function (Camp $camp) use ($aktUser) {
            $actions = [];
            $manage = $aktUser->can('manage', $camp);
            // Export und Druck gelten für den Kurs der Zeile; abgeschlossene Kurse haben keine Daten mehr, das
            // Global-Camp ist nur Sammelstelle ohne eigene Qualis
            if (! $camp->finish && ! $camp->global_camp && $manage) {
                $actions = [
                    ['label' => 'Rückmeldungen exportieren', 'url' => route('admin.camps.export', $camp), 'button' => true],
                    ['label' => 'Qualifikationen drucken', 'url' => route('surveys.downloadPDF', $camp), 'button' => true, 'newTab' => true],
                ];
            }
            // Wie im Dialog «Kurs bearbeiten»: nur offene Kurse, die man verwalten darf, nie das Global-Camp
            if ($this->canFinish($camp) && $manage) {
                $actions[] = [
                    'label' => 'Kurs abschliessen',
                    'url' => route('admin.camps.destroy', $camp),
                    'method' => 'DELETE',
                    'danger' => true,
                    'confirm' => 'Kurs «'.$camp->name.'» abschliessen / löschen?',
                    'confirmText' => 'Beim Kurs abschliessen/löschen werden alle Qualifikationen und hochgeladenen Dokumente gelöscht.',
                ];
            }

            return [
                'id' => $camp->id,
                'name' => $camp->name,
                'edit_url' => route('admin.camps.edit', $camp),
                'username' => $camp->user?->username ?? '',
                'email' => $camp->user?->email ?? '',
                'camp_type' => $camp->camp_type?->name ?? '',
                'group' => $camp->group?->name ?? '',
                'end_date' => [
                    'display' => $camp->end_date > 1 ? date('d.m.Y', strtotime($camp->end_date)) : '',
                    'sort' => $camp->end_date > 1 ? date('Y-m-d', strtotime($camp->end_date)) : '',
                ],
                'finish' => $camp->finish ? 'Ja' : 'Nein',
                'counter' => $camp->counter ?: count($camp->surveys),
                'actions' => $actions,
            ];
        })->values()]);
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
        // Nur die Felder des Formulars; global_camp, finish, counter usw. setzt nie die Anfrage
        $input = $request->only(['name', 'camp_type_id', 'group_id', 'end_date', 'foreign_id', 'user_id']);

        if (! Auth::user()->demo) {
            $user = User::findOrFail(Auth::user()->id);

            if (! $user->isAdmin()) {
                $input['user_id'] = $user->id;
            }
            $input['status_control'] = $request->has('status_control');
            if ($input['status_control']) {
                $input['survey_status_id'] = SurveyStatus::Neu;
            }

            $camp = Camp::create($input);
            CampCreated::dispatch($camp);
            if (! $user->isAdmin()) {
                // Wie "Kurs erstellen" (CampsController@store): die Kursleitung ist Mitglied ihres Kurses
                $user->update(['camp_id' => $camp->id]);
                CampUser::firstOrCreate(['camp_id' => $camp->id, 'user_id' => $user->id], ['role_id' => Role::Kursleiter]);

                // Der neue Kurs ist jetzt der aktuelle; auf der Übersicht wartet «Erste Schritte»
                return redirect('/home');
            }
        }

        return redirect('admin/camps');
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
     * Dialog «Kurs bearbeiten»: als JSON für die Kursübersicht, sonst die Übersicht mit offenem Dialog
     * (Links aus «Erste Schritte», der Kursauswahl und der Mail «Kurs abschliessen»).
     */
    public function edit(Request $request, Camp $camp)
    {
        $this->authorize('manage', $camp);
        $data = $this->editData($camp);

        return $request->wantsJson() ? response()->json($data) : $this->indexView($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Camp $camp)
    {
        $this->authorize('manage', $camp);
        // Kursleitung nur aus den Personen mit Rolle Kursleitung in diesem Kurs
        $validator = Validator::make($request->only('user_id'), [
            'user_id' => ['nullable', Rule::in(array_keys($camp->campLeaderOptions()))],
        ], [
            'user_id.in' => 'Als Kursleitung kann nur eine Person mit der Rolle Kursleitung in diesem Kurs gewählt werden.',
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.camps.edit', $camp)->withErrors($validator);
        }
        if (! Auth::user()->demo) {
            $input = $request->only(['name', 'user_id', 'camp_type_id', 'end_date', 'group_id', 'foreign_id']);
            $input['status_control'] = $request->has('status_control');
            if ($input['status_control'] && $camp->survey_status_id === null) {
                $input['survey_status_id'] = SurveyStatus::Neu;
            }
            $camp->update($input);
        }

        return redirect('/admin/camps');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Camp $camp)
    {
        $this->authorize('manage', $camp);
        abort_if($camp->global_camp, 403, 'Das Global-Camp kann nicht abgeschlossen werden.');
        if (! Auth::user()->demo) {
            $users = $camp->allUsers;
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
            File::deleteDirectory(storage_path('app/public/'.$camp['id'].'_'.Str::slug($camp['name'])));
            File::deleteDirectory(storage_path('app/files/'.$camp['id'].'_'.Str::slug($camp['name'])));
            $camp->update(['finish' => true, 'counter' => $counter]);
        }

        return redirect('/home');
    }

    public function opensurvey()
    {
        $camp = Auth::user()->camp;
        // Erste Runde freigeben: Teilnehmende dürfen ausfüllen, solange ihre Quali vor "2. offen" liegt.
        if ($camp['status_control'] && ! $camp->survey_status_id?->isAtLeast(SurveyStatus::ErsteRundeOffen)) {
            $camp->update(['survey_status_id' => SurveyStatus::ZweiteRundeOffen]);
        } else {
            $surveys = $camp->surveys;
            foreach ($surveys as $survey) {
                if ($survey->status()->isClosedByParticipant()) {
                    $survey->update(['survey_status_id' => SurveyStatus::ZweiteRundeOffen]);
                }
            }
            $camp->update(['secondsurveyopen' => true]);
            if ($camp['status_control'] && $camp->survey_status_id === SurveyStatus::ZweiteRundeOffen) {
                $camp->update(['survey_status_id' => SurveyStatus::TnAbgeschlossen]);
            }
        }

        // Aus «Erste Schritte» zurück auf die Übersicht; nur genau dieser Wert, keine freie Adresse
        return redirect(request()->input('from') === 'dashboard' ? '/home' : '/admin/surveys');
    }

    /**
     * «Erste Schritte» auf der Übersicht aus- oder einblenden, für den ganzen aktuellen Kurs. Demo-Benutzer teilen
     * sich den Demo-Kurs, für sie gilt es nur in der Sitzung.
     */
    public function checklist(Request $request)
    {
        $camp = Auth::user()->camp;
        abort_unless($camp, 404);
        $this->authorize('manage', $camp);
        $hidden = $request->boolean('hidden');

        if (Auth::user()->demo) {
            $request->session()->put("checklist_hidden.{$camp->id}", $hidden);
        } else {
            $camp->update(['checklist_hidden' => $hidden]);
        }

        return redirect('/home');
    }

    /** Rückmeldungen eines Kurses als Excel (Kursübersicht: Kurs der Zeile), ohne {camp} des aktuellen Kurses. */
    public function export(?Camp $camp = null)
    {
        $camp ??= Auth::user()->camp;
        $this->authorize('manage', $camp);

        //        $save_path = 'files/' . $camp['id'] . '_' . Str::slug($camp['name']);
        //        $directory = storage_path($save_path);
        //        if (!File::isDirectory($directory)) {
        //            File::makeDirectory($directory, 0775, true);
        //        }
        //        $posts_name = $save_path . '/exports/posts.xlsx';
        return Excel::download(new PostsExport($camp), 'Rückmeldungen_'.Str::slug($camp['name']).'.xlsx');
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
