@extends('layouts.layout')

@section('survey_content')
    <x-layouts.page-title :title="$title" :help="$help" :header=false/>
    @if (! $camp)
        <p>Du bist noch keinem Kurs zugeteilt.</p>
    @else
        <p class="-mt-6 mb-6 text-sm text-gray-500 dark:text-gray-400">{{ $camp->name }} · {{ $camp->camp_type['name'] ?? '' }}</p>

        @if ($steps)
            <x-dashboard.checklist :checklist="$steps" :hidden="$stepsHidden"/>
        @endif

        @unless ($aktUser->isTeilnehmer())
            @php
                $toClose = $surveys->filter(fn ($survey) => $survey->status() === \App\Enums\SurveyStatus::TnAbgeschlossen)->count();
                $counters = [
                    ['count' => $surveys->count(), 'label' => 'Teilnehmende', 'link' => '#teilnehmende'],
                    ['count' => $toClose, 'label' => 'Zum Abschliessen', 'link' => '#teilnehmende'],
                    ['count' => $postCounts->sum(), 'label' => 'Rückmeldungen', 'link' => '/posts'],
                ];
            @endphp
            <div class="mb-6 grid grid-cols-3 gap-2 sm:max-w-xl">
                @foreach ($counters as $counter)
                    <a href="{{ $counter['link'] }}" class="rounded-lg border border-gray-200 bg-white px-2 py-3 text-center shadow-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <span class="block text-2xl font-bold text-gray-900 dark:text-white">{{ $counter['count'] }}</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $counter['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endunless

        @if ($tasks->isNotEmpty())
            <section id="aufgaben" class="panel scroll-mt-24 overflow-hidden">
                <h2 class="panel-header m-0 text-sm font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400">
                    {{ $aktUser->isTeilnehmer() ? 'Aufgabe' : 'Aufgaben · '.$tasks->count() }}
                </h2>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($tasks->take(5) as $task)
                        <x-dashboard.task :title="$task['title']" :subtitle="$task['subtitle']" :level="$task['level']" :link="$task['link']"/>
                    @endforeach
                </ul>
                @if ($tasks->count() > 5)
                    <details class="group border-t border-gray-200 dark:border-gray-700">
                        <summary class="cursor-pointer list-none px-4 py-3 text-center text-sm text-blue-600 group-open:hidden dark:text-blue-500">
                            + {{ $tasks->count() - 5 }} weitere anzeigen
                        </summary>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($tasks->skip(5) as $task)
                                <x-dashboard.task :title="$task['title']" :subtitle="$task['subtitle']" :level="$task['level']" :link="$task['link']"/>
                            @endforeach
                        </ul>
                    </details>
                @endif
            </section>
        @endif

        <section id="teilnehmende" class="scroll-mt-24">
            <h2 class="mb-3 text-sm font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400">
                @if ($aktUser->isTeilnehmer())
                    Meine Qualifikation
                @elseif ($aktUser->isCampleader())
                    Teilnehmende
                @else
                    Meine Teilnehmenden
                @endif
            </h2>
            @if ($surveys->isEmpty())
                <p class="mb-8">
                    {{ $aktUser->isTeilnehmer() ? 'Für dich ist noch keine Qualifikation erstellt.' : 'Hier erscheinen die Qualifikationen, sobald sie erstellt und dir Teilnehmende zugeteilt sind.' }}
                </p>
            @else
                <div class="mb-8 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($surveys as $survey)
                        <x-surveys.participant-card :survey="$survey" :akt-user="$aktUser" :post-count="$postCounts[$survey->camp_user_id] ?? 0"/>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
@endsection
