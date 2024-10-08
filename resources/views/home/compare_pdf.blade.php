<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Qualifikation">
    <meta name="author" content="Jérôme Sigg v/o Amigo">
    <meta name="robots" content="all,follow">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">

    <title>{{isset($title) ? $title . ' - ' : ''}}{{config('app.name')}}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <!-- Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'])
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
            <div>
                <h2 class="display">Bemerkung:</h2>
                {!! nl2br($survey['comment']) !!}
            </div>
            <br>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    @include('home.radar')

</body>

</html>
