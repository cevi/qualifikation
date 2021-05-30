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

    <title>{{ config('app.name', 'Cevi Qualifikationen') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link href="{{asset('css/libs.css')}}" rel="stylesheet">
</head>
<body>
    <div id="app" class="page mainpage">
        @include('includes/topnav')
        

        <main class="py-4">
            @yield('content')
        </main>
        @guest
        @else
            <footer class="main-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6 text-right">
                            <p>Made by Amirli, V3.1</p>
                    </div>
                    </div>
                </div>
            </footer>
        @endif
    </div>
    <!-- jQuery -->
    <script src="{{ asset('js/libs.js') }}"></script>
    @yield('scripts')
</body>
</html>
