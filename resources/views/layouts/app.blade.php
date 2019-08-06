<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CCHouse') }}</title>
        <!-- base:css -->
        
        <link rel="stylesheet" href={{ asset('vendors/typicons/font/typicons.css') }}>
        <link rel="stylesheet" href={{ asset('vendors/css/vendor.bundle.base.css') }}>
        <!-- endinject -->
        <!-- plugin css for this page -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
        <link rel="stylesheet" href={{ asset('css/datatables.css') }}>
        <link rel="stylesheet" href={{ asset('css/vendor.css') }}>
        <!-- End plugin css for this page -->
        <!-- inject:css -->
        <link rel="stylesheet" href={{ asset('css/app.css') }}>
        <!-- endinject -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
        </script>
        <script src={{ asset('vendors/js/vendor.bundle.base.js') }}></script>
        <script src={{ asset('js/manifest.js') }}></script>
        <script src={{ asset('js/vendor.js') }}></script>
        <script src={{ asset('js/app.js') }}></script>
    </head>
    <body>
        <div class="container-scroller">
            <?php if(Auth::check()): ?> 
                @include('layouts.nav')
            <?php endif; ?>
            <div class="container-fluid page-body-wrapper">
                <div class="main-panel">
                    @yield('content')
                </div>
                <!-- main-panel ends -->
            </div>
        </div>
        <!-- container-scroller -->
        <!-- base:js -->
        <script>
        $(document).ready( function () {

            // Realtime search module
            realtimeSelect($('[data-toggle=selectize]'))

            // Auto assigned value on each data resource select element
            $('select').each(function(index, element){
            var selected_value = element.getAttribute('value')
            var options = element.options
            for( var i = 0 ; i < options.length; i++ ){
                if( selected_value == options[i].value){
                    options[i].selected = true
                }
            }
        })


        } );        
        </script>
        <!-- endinject -->
        <!-- Plugin js for this page-->
        <!-- End plugin js for this page-->
        <!-- inject:js -->
    
        <!-- endinject -->
        <!-- plugin js for this page -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
        </script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
        </script>
        <!-- End plugin js for this page -->
    </body>
</html>