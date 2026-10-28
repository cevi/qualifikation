<section class="content">
    <h3 class="text-3xl font-bold dark:text-white">{{$title}}</h3>
    <div class="text-[0.9em]">
        @foreach ($posts as $post)
            {{-- Breit: Text | Angaben untereinander | Knöpfe; mittel: Angaben in drei Spalten; Handy: Karte mit Angaben als Fusszeile --}}
            <div class="post-card grid grid-cols-12 gap-x-4 py-2 max-sm:flex max-sm:flex-wrap max-sm:items-end">
                <div class="col-span-12 min-w-0 max-sm:w-full lg:col-span-8">
                    {!! nl2br(e($post->comment)) !!}
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
                            {!! Form::model($post, ['method' => 'DELETE', 'action'=>['PostController@destroy',$post]]) !!}
                                {!! Form::button(' <i class="fas fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'button-icon confirm', 'title' => 'Rückmeldung löschen', 'data-confirm' => 'Rückmeldung löschen?', 'data-confirm-text' => 'Willst du die Rückmeldung wirklich löschen?', 'data-confirm-icon' => 'info'])!!}
                            {!! Form::close()!!}
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
            <hr class="m-0 h-px border-0 bg-gray-200 max-sm:hidden dark:bg-gray-600">
        @endforeach
    </div>
</section>
