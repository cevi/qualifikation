<!DOCTYPE html>
<html lang="{{ app()->getLocale()}}">
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
        @auth
            <footer class="main-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6 text-right">
                            <p>Made by Amirli, {{config('app.version')}}</p>
                    </div>
                    </div>
                </div>
            </footer>
            
        @else
            <footer class="main-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-4 text-left">
                            <p>Finde weitere Lösungen auf <a href="http://www.cevi.tools">cevi.tools</a></p>
                        </div>
                        <div class="col-sm-4 text-center">
                            <p>Made by Amirli, {{config('app.version')}}</p>
                        </div>
                        <div class="col-sm-4 text-right">
                            <p>Finde uns auch auf <a href="https://github.com/cevi/qualifikation">Github</a></p>
                        </div>
                    </div>
                </div>
            </footer>
        @endauth
    </div>
   <!-- jQuery -->
    <script src="{{ asset('js/libs.js') }}"></script>

    
    @yield('scripts')
</body>
</html>
