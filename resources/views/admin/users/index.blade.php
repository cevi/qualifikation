@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Teilnehmer</li>
            </ul>
        </div>
    </div>
    @if (Session::has('deleted_user'))
        <p class="bg-danger">{{session('deleted_user')}}</p> 
    @endif
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Teilnehmer</h1>
            </header>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Rolle</th>
                            <th scope="col">Leiter</th>
                            <th scope="col">Umfrage</th>
                            @if ((Auth::user()->isAdmin()))
                                <th scope="col">Lager</th>
                            @endif
                            <th scope="col">Status</th>
                            <th scope="col">Created</th>
                            <th scope="col">Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($users)
                            @foreach ($users as $user)
                            <tr>
                                <td><a href="{{route('users.edit', $user->id)}}">{{$user->username}}</a></td>
                                <td>{{$user->role['name']}}</td>
                                <td>{{$user->leader['username']}}</td>
                                <td><a href="{{route('users.edit', $user->id)}}">Umfrage</a></td>
                                @if ((Auth::user()->isAdmin()))
                                    <td>{{$user->group['name']}}</td>
                                @endif
                                <td>{{$user->is_active == 1 ? "Aktiv" : "Nicht Aktiv"}}</td>
                                <td>{{$user->created_at->diffForHumans()}}</td>
                                <td>{{$user->updated_at->diffForHumans()}}</td>
                            </tr>    
                            @endforeach

                        @endif

                    </tbody>
                </table>
                <a href="{{route('users.create')}}" class="btn btn-info" role="button">Leiter hinzufügen</a>
            </div>
        </div>  
    </section>
@endsection