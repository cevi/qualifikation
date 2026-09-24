@extends('layouts.layout')
@section('survey_content')
    {{--@include('includes.tinyeditor')--}}
    <x-layouts.page-title :title="$title" :help="$help" :subtitle="$subtitle" :header=false/>
    <div class="card border-primary">
        <div class="card-body cardbody-navtabs">
            <div class="flex items-center justify-between w-full mt-4 mb-4">
                <!-- Previous Button -->
                <a href="{{route('home.profile', $previousUser->slug)}}" class="inline-flex items-center text-body bg-neutral-secondary-medium border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                    <svg class="w-4 h-4 me-1.5 -ms-0.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/></svg>
                    {{$previousUser->username}} {{$previousUser->group['shortname'] ?? ''}}
                </a>

                <!-- Next Button -->
                <a href="{{route('home.profile', $nextUser->slug)}}" class="inline-flex items-center text-body bg-neutral-secondary-medium border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                    {{$nextUser->username}} {{$nextUser->group['shortname'] ?? ''}}
                    <svg class="w-4 h-4 ms-1.5 -me-0.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/></svg>
                </a>
            </div>
            <p>
                Diese Seite ist für Teilnehmer nicht sichtbar.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <div class="text-center-profile mbl">
                        <img src="{{$user->getAvatar()}}" alt="img"
                                class="img-circle img-bor">
                    </div>
                    <div class="profile_user">
                        <h3 class="user_name_max">{{$user->username}} {{$user->group['shortname'] ?? ''}}</h3>
                        <p>{{$user->leader ? $user->leader->username : ''}}</p>
                    </div>
                    <div class="text-center-profile mbl">
                        <div class="ampel" id="ampel">
                            <a href="javascript:" class="ampel-btn"
                                data-color="{{config('status.classification_red')}}"
                                data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_red')])}}'>
                                <div
                                    class="circle {{$camp_user->classification_id == config('status.classification_red') ? 'red' : ''}}"></div>
                            </a>
                            <a href="javascript:" class="ampel-btn"
                                data-color="{{config('status.classification_yellow')}}"
                                data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_yellow')])}}'>
                                <div
                                    class="circle {{$camp_user->classification_id == config('status.classification_yellow') ? 'yellow' : ''}}"></div>
                            </a>
                            <a href="javascript:" class="ampel-btn"
                                data-color="{{config('status.classification_green')}}"
                                data-remote='{{route('users.changeClassifications', ['id' => $user->id, 'color' => config('status.classification_green')])}}'>
                                <div
                                    class="circle {{$camp_user->classification_id == config('status.classification_green') ? 'green' : ''}}"></div>
                            </a>
                        </div>
                    </div>
                    @foreach($surveys as $survey)
                        <x-surveys.radar-chart/>
                    @endforeach

                </div>
                <div>
                    <x-posts.post :posts="$posts" :showLeader="true" :title="'Rückmeldungen'" :user="$user"/>
                    <button type="button" id="new-feedback-btn" class="btn btn-primary mt-3">
                        Neue Rückmeldung
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between w-full mt-4">
                <!-- Previous Button -->
                <a href="{{route('home.profile', $previousUser->slug)}}" class="inline-flex items-center text-body bg-neutral-secondary-medium border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                    <svg class="w-4 h-4 me-1.5 -ms-0.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/></svg>
                    {{$previousUser->username}} {{$previousUser->group['shortname'] ?? ''}}
                </a>

                <!-- Next Button -->
                <a href="{{route('home.profile', $nextUser->slug)}}" class="inline-flex items-center text-body bg-neutral-secondary-medium border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                    {{$nextUser->username}} {{$nextUser->group['shortname'] ?? ''}}
                    <svg class="w-4 h-4 ms-1.5 -me-0.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/></svg>
                </a>
            </div>
        </div>
    </div>

    @include('home.feedback_modal', ['route' => route('profile.post.store', $user), 'questions' => $questions])
@endsection


@push('scripts')
    @include('home.radar')
    @include('home.post_delete')
    <script type="module">
        $('.ampel-btn').on('click', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            var url = $(this).data('remote');
            var color = $(this).data('color');
            $.ajax({
                url: url,
                type: 'PATCH',
                data: {},
                success: function (res) {
                    location.reload();
                }
            });
        });

    </script>
@endpush

