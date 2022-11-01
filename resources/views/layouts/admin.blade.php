<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Qualifikation">
        <meta name="author" content="Jérôme Sigg">
        <meta name="robots" content="all,follow">

        <title>{{isset($title) ? $title . ' - ' : ''}}{{config('app.name')}}</title>

        <!-- Bootstrap Core CSS -->
        <link href="{{asset('css/libs.css')}}" rel="stylesheet">
        <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>

        @yield('styles')
    </head>

    <body>
        <div class="page">
            @include('includes/admin_topnav')
            @include('includes/admin_sidenav')

            @yield('content')
            <footer class="main-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6 text-right">
                            <p>Made by Amirli, {{config('app.version')}}</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <script src="{{asset('js/libs.js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        @yield('scripts')
    </body>
</html>
