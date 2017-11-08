<!doctype html>
<html lang="en">
    @include('layouts.header')
    <div id="app">
        <body class="container-fluid p-0">
            @includeWhen((isset($app_indicator['indicator']) && $app_indicator['indicator'] != ''), 'layouts.app_indicator')
            @include('layouts.top_nav', ['order' => true])
            <div class="wrapper">
                <div class="content-order m-0 p-0">
                    @yield('content')
                </div>
            </div>     
        </body>
        @include('layouts.footer', ['fixed_footer' => true])
    </div>
    @include('layouts.scripts')
    @stack('scripts')
</html>