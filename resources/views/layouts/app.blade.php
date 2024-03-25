<!DOCTYPE html>
<html lang="{{ app()->getLocale()}}">
@include('includes/header')
<body class="text-dark__black">
<div id="app" class="page mainpage">
    @include('includes/topnav')


    <main class="py-4">
        @yield('content')
    </main>
    <x-footer/>
</div>
<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
@stack('scripts')
</body>
</html>
