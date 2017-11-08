<!doctype html>
<html lang="en">
    @include('layouts.header')
    <div id="app">
        <body class="container-fluid p-0">
            @includeWhen((isset($app_indicator['indicator']) && $app_indicator['indicator'] != ''), 'layouts.app_indicator')
            @include('layouts.top_nav')
            <div class="wrapper">
                @include('layouts.side_nav')
                <div class="content">
                    @yield('content')
                </div>
            </div>     
        </body>
        @include('layouts.footer')
    </div>
    @include('layouts.scripts')
    @stack('scripts')
</html>