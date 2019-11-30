<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Zopfaktion">
        <meta name="author" content="Jérôme Sigg">
        <meta name="robots" content="all,follow">

        <title>Qualifikations Zentrale</title>

        <!-- Bootstrap Core CSS -->
        <link href="{{asset('css/libs.css')}}" rel="stylesheet">
        <script src="https://kit.fontawesome.com/da9e6dcf22.js" crossorigin="anonymous"></script>
        {{-- <link href="{{asset('css/app.css')}}" rel="stylesheet"> --}}

        @yield('styles')

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>
        @include('includes/admin_sidenav')
        
        @include('includes/admin_topnav')

        @yield('content')
        </div>
        <footer class="main-footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6 text-right">
                        <p>Design by <a href="https://bootstrapious.com/p/bootstrap-4-dashboard" class="external">Bootstrapious</a></p>
                        <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions and it helps me to run Bootstrapious. Thank you for understanding :)-->
                    </div>
                </div>
            </div>
        </footer>
        

        <!-- jQuery -->
        <script src="{{asset('js/libs.js')}}"></script>
        @yield('scripts')
        
    </body>

</html>