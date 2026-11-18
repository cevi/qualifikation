{{--
    card (Quali, Vergleich, Rückmeldungen, Profil): als Karte wie «Bemerkung», ohne Rückmeldungen gar nicht angezeigt,
    ausser die Karte hat Knöpfe (<x-slot:actions>, Profil). clamp: lange Texte auf drei Zeilen mit «Mehr».
    fill: Karte füllt die Höhe des Elternelements, die Liste scrollt darin (Profil am Computer).
--}}
@if (! $card || count($posts) > 0 || isset($actions))
<section {{ $attributes->class([
    'content' => ! $card,
    'mb-4 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' => $card,
    'flex flex-col overflow-hidden' => $card && $fill,
]) }}>
    @if ($card)
        <div class="flex shrink-0 items-center gap-2 border-b border-gray-200 px-4 py-3 md:px-5 dark:border-gray-700">
            <h2 class="m-0 text-base font-bold text-gray-900 md:text-lg dark:text-white">{{$title}}</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ count($posts) }}</span>
            @isset($actions)
                <div class="ms-auto flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
        @if (count($posts) === 0)
            <p class="m-0 px-4 py-6 text-center text-sm text-gray-600 md:px-5 dark:text-gray-400">Noch keine Rückmeldungen.</p>
        @endif
    @else
        <h2 class="mt-4 mb-2 text-2xl font-bold dark:text-white">{{$title}}</h2>
    @endif
    <div @class(['text-[0.9em]', 'divide-y divide-gray-100 dark:divide-gray-700' => $card, 'min-h-0 flex-1 overflow-y-auto' => $card && $fill])>
        @foreach ($posts as $post)
            {{-- Breit: Text | Angaben untereinander | Knöpfe; mittel: Angaben in drei Spalten; Handy: Karte mit Angaben als Fusszeile (in der Karte: Trennlinien) --}}
            <div @class([
                'grid grid-cols-12 gap-x-4 max-sm:flex max-sm:flex-wrap max-sm:items-end',
                'post-card py-2' => ! $card,
                'px-4 py-3 md:px-5 dark:text-gray-200' => $card,
            ])>
                <div class="relative col-span-12 min-w-0 max-sm:w-full lg:col-span-8">
                    @if ($clamp && (mb_strlen($post->comment) > 220 || substr_count($post->comment, "\n") >= 3))
                        {{-- «Mehr» ohne Skript: Checkbox vor dem Text, Labels zum Auf- und Zuklappen. Die Checkbox (sr-only = absolute) braucht
                             das relative Elternelement oben: sonst liegt sie ausserhalb der scrollenden Liste, und beim Klick auf «Mehr»
                             scrollt der Browser die ganze Karte (overflow-hidden) zu ihr - Kopf weg, Liste wirkt kleiner (Profil). --}}
                        <input type="checkbox" id="mehr-{{ $post->id }}" class="peer sr-only">
                        <div class="line-clamp-3 peer-checked:line-clamp-none">{!! nl2br(e($post->comment)) !!}</div>
                        <label for="mehr-{{ $post->id }}" class="mt-0.5 inline-block cursor-pointer text-sm font-medium text-blue-700 peer-checked:hidden peer-focus-visible:underline hover:underline dark:text-blue-400">Mehr</label>
                        <label for="mehr-{{ $post->id }}" class="mt-0.5 hidden cursor-pointer text-sm font-medium text-blue-700 peer-checked:inline-block peer-focus-visible:underline hover:underline dark:text-blue-400">Weniger</label>
                    @else
                        {!! nl2br(e($post->comment)) !!}
                    @endif
                </div>
                <div class="col-span-12 min-w-0 text-right max-sm:flex-1 sm:col-span-10 lg:col-span-3">
                    <div class="post-meta grid gap-x-4 sm:grid-cols-3 lg:grid-cols-1">
                        <div>
                            <i class="far fa-user me-1 sm:hidden"></i>
                            @if($showLeader)
                                {{$post->leader ? $post->leader['username'] : ''}}
                            @else
                                {{$post->campUser ? $post->campUser->user['username'] : ''}}
                            @endif
                        </div>
                        <div>
                            <i class="far fa-calendar me-1 sm:hidden"></i>
                            {{$post->created_at ? $post->created_at->isoFormat('L') : 'no date'}}
                        </div>
                        {{-- Leere Angaben fallen in der Fusszeile auf dem Handy weg --}}
                        <div @class(['max-sm:hidden' => !$post->show_on_survey])>
                            <i class="far fa-circle-check me-1 sm:hidden"></i>
                            {{$post->show_on_survey ? 'Auf Quali' : ''}}
                        </div>
                        <div @class(['max-sm:hidden' => !$post->question])>
                            @if ($post->question)
                                <i class="fas fa-tag me-1 sm:hidden"></i>
                                {{$post->question->number}} - {{$post->question->competence}}
                            @endif
                        </div>
                        @if ($post->file)
                            <div class="min-w-0 break-all">
                                <i class="fas fa-paperclip me-1 sm:hidden"></i>
                                <a href="{{route('downloadFile',$post['uuid'] ?? '0')}}"
                                   target="_blank" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$post->filename()}}</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-span-12 text-right sm:col-span-2 lg:col-span-1">
                    @if ($post->leader && ($post->leader['id'] === Auth::user()->id) && $editable)
                        <div class="flex flex-wrap justify-end gap-1 max-sm:flex-nowrap lg:flex-col lg:items-end">
                            <x-forms.form method="DELETE" :action="action('PostController@destroy', $post)">
                                <x-forms.button type="submit" variant="icon" class="confirm" title="Rückmeldung löschen" data-confirm="Rückmeldung löschen?" data-confirm-text="Willst du die Rückmeldung wirklich löschen?"><i class="fas fa-trash-alt"></i></x-forms.button>
                            </x-forms.form>
                            @if($user)
                                <button type="button" class="edit-post-btn button-icon" title="Rückmeldung bearbeiten"
                                    data-post="{{ json_encode([
                                        'id' => $post->id,
                                        'comment' => $post->comment,
                                        'showOnSurvey' => $post->show_on_survey ? true : false,
                                        'file' => $post->file ? true : false,
                                        'filename' => $post->file ? $post->filename() : null,
                                    ]) }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @else
                                <a href="{{ route('posts.edit', $post) }}" class="button-icon" title="Rückmeldung bearbeiten">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @unless ($card)
                <hr class="m-0 h-px border-0 bg-gray-200 max-sm:hidden dark:bg-gray-600">
            @endunless
        @endforeach
    </div>
</section>
@endif
