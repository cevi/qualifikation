@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Umfragen</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Umfragen</h1>
            </header>
            <div class="row" style="margin-bottom: 10px;">
                <a href="{{route('surveys.create')}}" class="btn btn-info" role="button">Umfragen erstellen</a>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Leiter</th>
                            <th scope="col">TN-Umfrage</th>
                            <th scope="col">Leiter-Umfrage</th>
                            @if ((Auth::user()->isAdmin()))
                                <th scope="col">Lager</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($surveys)
                            @foreach ($surveys as $survey)
                            <tr>
                                <td>{{$survey->user['username']}}</td>
                                <td>{{$survey->responsible['username']}}</td>
                                <td><a href="{{route('surveys.edit', $survey->id)}}">Zur Umfrage</a></td>
                                @if ((Auth::user()->isAdmin()))
                                    <td>{{$survey->user->camp['name']}}</td>
                                @endif
                            </tr>    
                            @endforeach

                        @endif

                    </tbody>
                </table>
            </div>
        </div>  
    </section>
@endsection