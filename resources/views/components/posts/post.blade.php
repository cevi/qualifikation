<section class="content">
    <h3 class="text-3xl font-bold dark:text-white">{{$title}}</h3>
    <div class="text-[0.9em]">
        @foreach ($posts as $post)
            {{-- Breit: Text | Angaben untereinander | Knöpfe; mittel: Angaben in drei Spalten; Handy: alles untereinander --}}
            <div class="grid grid-cols-12 gap-x-4 py-2">
                <div class="col-span-12 min-w-0 lg:col-span-8">
                    {!! nl2br(e($post->comment)) !!}
                </div>
                <div class="col-span-12 min-w-0 text-right sm:col-span-10 lg:col-span-3">
                    <div class="grid gap-x-4 sm:grid-cols-3 lg:grid-cols-1">
                        <div>
                            @if($showLeader)
                                {{$post->leader ? $post->leader['username'] : ''}}
                            @else
                                {{$post->campUser ? $post->campUser->user['username'] : ''}}
                            @endif
                        </div>
                        <div>
                            {{$post->created_at ? $post->created_at->isoFormat('L') : 'no date'}}
                        </div>
                        <div>
                            {{$post->show_on_survey ? 'Auf Quali' : ''}}
                        </div>
                        <div>
                            {{$post->question->number ?? ''}} - {{$post->question->competence ?? ''}}
                        </div>
                        @if ($post->file)
                            <div class="min-w-0 break-all">
                                <a href="{{route('downloadFile',$post['uuid'] ?? '0')}}"
                                   target="_blank" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$post->filename()}}</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-span-12 text-right sm:col-span-2 lg:col-span-1">
                    @if ($post->leader && ($post->leader['id'] === Auth::user()->id) && $editable)
                        <div class="flex flex-wrap justify-end gap-1 lg:flex-col lg:items-end">
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
            <hr class="m-0 h-px border-0 bg-gray-200 dark:bg-gray-600">
        @endforeach
    </div>
</section>
