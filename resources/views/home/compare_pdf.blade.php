<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Qualifikation">
    <meta name="author" content="Jérôme Sigg">
    <meta name="robots" content="all,follow">

    <title>{{ config('app.name', 'Cevi Qualifikationen') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link href="{{asset('css/libs.css')}}" rel="stylesheet">
</head>

<body>
<section>
    @foreach($surveys as $survey)
        <div class="container-fluid" style="page-break-inside:avoid; page-break-after:always;">
            <!-- Page Header-->
            <header>
                <h2>Teilnehmer: {{$survey->campuser->user['username']}}</h2>
                <h3>Leiter: {{$survey->campuser->user->leader ? $survey->campuser->user->leader['username'] : ''}} </h3>
            </header>
            @foreach ($survey->chapters as $chapter)

                <h2 class="display">
                    {{$chapter->chapter['number']}}. {{$chapter->chapter['name']}}
                </h2>

                <table class="table">
                    <thead>
                    <tr>
                        <th rowspan="2" width="50px">Nr.</th>
                        <th rowspan="2" width="150px">Kompetenz</th>
                        <th rowspan="2" width="300px">Thema</th>
                        <th colspan="2" width="250">1. Selbsteinschätzung</th>
                        <th colspan="2" width="250">2. Selbsteinschätzung</th>
                        <th colspan="2" width="250">Leiter</th>
                    </tr>
                    <tr>
                        <th width="50px"></th>
                        <th width="200px">Kommentar</th>
                        <th width="50px"></th>
                        <th width="200px">Kommentar</th>
                        <th width="50px"></th>
                        <th width="200px">Kommentar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($chapter->questions as $question)
                        <tr class="{{$question->competence_text() ? 'core_competence':''}}">
                            <td width="50px">{{$question->question['number']}}</td>
                            <td width="150px">{{$question->question['competence']}}</td>
                            <td width="300px">{{$question->question['name']}}</td>
                            <td width="50px">{{$question->answer_first['name']}}</td>
                            <td width="200px">{{$question['comment_first']}}</td>
                            <td width="50px">{{$question->answer_second['name']}}</td>
                            <td width="200px">{{$question['comment_second']}}</td>
                            <td width="50px">{{$question->answer_leader['name']}}</td>
                            <td width="200px">{{$question['comment_leader']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <br><br>
            @endforeach
            <div class="pagebreak">
                <x-radar-chart :id="$loop->iteration" :name="$survey->campuser->user['username']"/>
            </div>

        </div>
        @if(count($surveys)>1)
            <div class="pagebreak"> </div>
        @endif
    @endforeach
</section>


<!-- jQuery -->
<script src="{{asset('js/libs.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.js"></script>
@include('home.radar')

</body>

</html>
