<!doctype html>
<html lang="en">
    @include('layouts.header')
    <div id="app">
        <body>
            @includeWhen((isset($app_indicator['indicator']) && $app_indicator['indicator'] != ''), 'layouts.app_indicator')
            <div class="wrapper">
                <div class="content">
                    @yield('content')
                </div>
            </div>     
        </body>
    </div>
    @include('layouts.scripts')
    @stack('scripts')
</html>