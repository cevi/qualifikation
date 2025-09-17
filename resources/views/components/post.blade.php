<section class="content">
    <h3 class="text-3xl font-bold dark:text-white">{{$title}}</h3>
    <div class="profile-table">
        @foreach ($posts as $post)
            <div class="row">
                <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                    {!! nl2br($post->comment) !!}
                </div>
                <div class="col-lg-3 col-md-10 col-sm-10 col-xs-10 text-right">
                    <div class="row">
                        <div
                            class="col-lg-12 col-md-4 col-sm-4 col-xs-4 text-right">
                            @if($showLeader)
                                {{$post->leader ? $post->leader['username'] : ''}}
                            @else
                                {{$post->campUser ? $post->campUser->user['username'] : ''}}
                            @endif
                        </div>
                        <div
                            class="col-lg-12 col-md-4 col-sm-4 col-xs-4 text-right">
                            {{$post->created_at ? $post->created_at->isoFormat('L') : 'no date'}}
                        </div>
                        <div
                            class="col-lg-12 col-md-4 col-sm-4 col-xs-4 text-right">
                            {{$post->show_on_survey ? 'Auf Quali' : ''}}
                        </div>
                        @if ($post->file)
                            <div
                                class="col-lg-12 col-md-4 col-sm-4 col-xs-4 text-right">
                                <a href="{{route('downloadFile',$post['uuid'] ?? '0')}}"
                                   target="_blank" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{$post->filename()}}</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2 text-right">
                    @if ($post->leader && ($post->leader['id'] === Auth::user()->id) && $editable)
                        <div class="row">
                            <div
                                class="col-lg-12 col-md-6 col-sm-6 col-xs-6 text-right">
                                {!! Form::model($post, ['method' => 'DELETE', 'action'=>['PostController@destroy',$post], 'id'=> "DeleteForm"]) !!}
                                <div class="form-group">
                                    {!! Form::button(' <i class="fas fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-sm confirm'])!!}
                                </div>
                                {!! Form::close()!!}
                            </div>
                            <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6 text-right">
                                <a href="{{$user ? route('profile.post.edit', [$user, $post]) : route('posts.edit', $post)}}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <hr class="h-px bg-gray-400 border-0 dark:bg-gray-200">
        @endforeach
    </div>
</section>


