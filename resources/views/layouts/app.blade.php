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
<script src="{{ asset('js/libs.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.js"></script>
<script src="https://unpkg.com/flowbite@1.5.4/dist/flowbite.js"></script>
@yield('scripts')
</body>
</html>
