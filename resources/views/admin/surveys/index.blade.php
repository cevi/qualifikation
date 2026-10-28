@extends('layouts.admin')

@section('content')
    <div>
        <x-layouts.page-title :title="$title" :help="$help"/>
        <!-- Page Header-->
        <div class="grid grid-cols-12 sm:gap-x-8">
            <div class="min-w-0 col-span-12 sm:col-span-4" style="margin-bottom: 10px;">
                <a href="javascript:;" class="focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900 create" role="button">Qualifikationen erstellen</a>
            </div>
            <x-surveys.survey-button text="Druckversion aller Qualifikationen" :has-surveys="$hasSurveys" url="{{ route('surveys.downloadPDF') }}" target="_blank" btn-class="" />
            @if($camp['status_control'] && ! $camp->survey_status_id?->isAtLeast(\App\Enums\SurveyStatus::ErsteRundeOffen))
                <x-surveys.survey-button text="Erste Selbsteinschätzung freigeben" :has-surveys="$hasSurveys" />
            @else
                @if(!$camp['secondsurveyopen'])
                    <x-surveys.survey-button text="Zweite Selbsteinschätzung freigeben" :has-surveys="$hasSurveys" />
                @endif
            @endif
        </div>
        @if($hasSurveys)
            @php
                $table = [
                    'url' => route('surveys.table'),
                    'label' => 'Qualifikationen',
                    'columns' => [
                        ['key' => 'name', 'label' => 'Teilnehmer', 'link' => 'profile_url', 'linkTitle' => 'Zum Profil'],
                        ['key' => 'leader', 'label' => 'Leiter'],
                        ['key' => 'camp', 'label' => 'Kurs'],
                        ['key' => 'group', 'label' => 'Abteilung'],
                        ['key' => 'steps', 'label' => 'Status', 'type' => 'steps'],
                        ['key' => 'actions', 'label' => 'TN Qualifikation', 'type' => 'actions'],
                    ],
                    'defaultSort' => [['id' => 'name', 'desc' => false]],
                    'mobile' => ['title' => 'name', 'kicker' => 'group', 'lines' => ['leader', 'steps'], 'details' => ['camp']],
                ];
            @endphp
            <div class="my-4" data-vue-component="data-table" data-props="{{ json_encode($table) }}">
                <noscript>Die Liste braucht JavaScript.</noscript>
            </div>
        @else
            <div class="flex flex-col items-center justify-center text-center bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xs" style="padding: 3rem; margin-top: 1rem;">
                <svg class="text-purple-400" style="width: 4rem; height: 4rem; margin-bottom: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <h3 class="font-bold text-gray-800 dark:text-white" style="margin-bottom: 0.5rem; font-size: 1.25rem;">Noch keine Qualifikationen vorhanden</h3>
                <p class="text-gray-500 dark:text-gray-400" style="margin-bottom: 1.5rem;">Es wurden noch keine Qualifikationen für die Teilnehmenden dieses Kurses erstellt.</p>
                <a href="javascript:;" class="create inline-flex items-center focus:outline-hidden text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900 transition-all" role="button">
                    <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem; margin-left: -0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Qualifikationen erstellen
                </a>
            </div>
        @endif
    </div>
@endsection


@push('scripts')
    <script type="application/json" id="surveys-index-config">@json(['createUrl' => route('surveys.store'), 'openSurveyUrl' => route('admin.camps.opensurvey')])</script>
    @vite('resources/js/pages/surveys-index.js')
    @if($hasSurveys)
        @vite('resources/js/islands.js')
    @endif
@endpush
