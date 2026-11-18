{{-- «Erste Schritte» der Kursleitung (App\Services\CampChecklist). Ausgeblendet bleibt eine Leiste mit Fortschritt. --}}
@props(['checklist', 'hidden' => false])
@php([$done, $total] = [$checklist['done'], $checklist['total']])
<section id="erste-schritte" class="panel mb-6 scroll-mt-24 overflow-hidden" aria-labelledby="erste-schritte-titel">
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 px-4 py-3 {{ $hidden ? '' : 'border-b border-gray-200 dark:border-gray-700' }}">
        <h2 id="erste-schritte-titel" class="m-0 text-base font-semibold text-gray-900 dark:text-white">Erste Schritte</h2>
        <div class="flex min-w-40 flex-1 items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
            <span>{{ $done }} von {{ $total }} erledigt</span>
            <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700" role="progressbar"
                  aria-label="Fortschritt" aria-valuemin="0" aria-valuemax="{{ $total }}" aria-valuenow="{{ $done }}">
                <span class="block h-full bg-green-500" style="width: {{ $total ? round($done / $total * 100) : 0 }}%"></span>
            </span>
        </div>
        <form method="POST" action="{{ route('admin.camps.checklist') }}">
            @csrf
            <input type="hidden" name="hidden" value="{{ $hidden ? 0 : 1 }}">
            <button type="submit" class="text-sm text-blue-600 underline underline-offset-2 hover:no-underline dark:text-blue-500">
                {{ $hidden ? 'Einblenden' : 'Ausblenden' }}
            </button>
        </form>
    </div>

    @unless ($hidden)
        <div class="grid lg:grid-cols-[1.35fr_1fr_0.8fr]">
            @foreach ($checklist['steps']->groupBy('group') as $group => $steps)
                <div class="min-w-0 px-4 pt-3 pb-1 max-lg:not-first:border-t lg:not-first:border-l border-gray-200 dark:border-gray-700">
                    <h3 class="mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400">{{ $group }}</h3>
                    <ol class="m-0 list-none p-0">
                        @foreach ($steps as $step)
                            <li class="grid grid-cols-[1.25rem_1fr] gap-3 py-2" data-step="{{ $step['key'] }}" @if ($step['next']) aria-current="step" @endif>
                                @if ($step['done'])
                                    <span class="mt-0.5 grid size-5 place-items-center rounded-full bg-green-500 text-white" aria-hidden="true">
                                        <svg class="size-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M2.5 6.5 5 9l4.5-6"/></svg>
                                    </span>
                                @else
                                    <span class="mt-0.5 size-5 rounded-full border-2 {{ $step['next'] ? 'border-blue-600 ring-4 ring-blue-100 dark:border-blue-500 dark:ring-blue-900' : 'border-gray-300 dark:border-gray-500' }} {{ $step['optional'] ? 'border-dashed' : '' }}" aria-hidden="true"></span>
                                @endif
                                <div class="min-w-0">
                                    <p class="m-0 {{ $step['done'] ? 'text-gray-500 dark:text-gray-400' : ($step['next'] ? 'font-semibold text-gray-900 dark:text-white' : 'font-medium text-gray-900 dark:text-white') }}">
                                        {{ $step['title'] }}
                                        @if ($step['optional'])
                                            <span class="ms-1 rounded border border-dashed border-gray-300 px-1 text-xs font-normal text-gray-500 dark:border-gray-500 dark:text-gray-400">optional</span>
                                        @endif
                                        @if ($step['done'])
                                            <span class="sr-only">(erledigt)</span>
                                        @endif
                                    </p>
                                    <p class="m-0 text-sm text-gray-500 dark:text-gray-400">{{ $step['detail'] }}</p>
                                    @if ($step['actions'])
                                        <div class="mt-2 flex flex-wrap">
                                            @foreach ($step['actions'] as $action)
                                                @php($buttonClass = $action['variant'] === 'primary' ? 'button-primary' : 'button-secondary')
                                                @if ($action['method'] === 'post')
                                                    <form method="POST" action="{{ $action['url'] }}">
                                                        @csrf
                                                        <input type="hidden" name="from" value="dashboard">
                                                        <button type="submit" class="{{ $buttonClass }}"
                                                                @isset($action['confirm']) data-confirm="{{ $action['confirm'] }}" @endisset
                                                                @isset($action['confirm_text']) data-confirm-text="{{ $action['confirm_text'] }}" @endisset>
                                                            {{ $action['label'] }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ $action['url'] }}" class="{{ $buttonClass }}">{{ $action['label'] }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endforeach
        </div>
    @endunless
</section>
