<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config("app.name") }} - @yield('title')</title>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        @include('layouts.css')
    </head>
    <body>
        <div id="app">
            @include('layouts.navbar')
            <main class="py-4">
                @yield('content')
            </main>
            @include('layouts.js')
            @include('common.alert')
        </div>

        <!-- Modal -->
        <div class="modal fade subPanelModel" id="subPanelModel" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content" id="subPanelContent">
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade subPanelModelLG" id="subPanelModelLG" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id="subPanelContentLG">
                </div>
            </div>
        </div>
    </body>
</html>