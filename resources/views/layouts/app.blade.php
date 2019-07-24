<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>CCHouse</title>
        <!-- base:css -->
        <link rel="stylesheet" href="vendors/typicons/font/typicons.css">
        <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
        <!-- endinject -->
        <!-- plugin css for this page -->
        <!-- End plugin css for this page -->
        <!-- inject:css -->
        <link rel="stylesheet" href="css/app.css">
        <!-- endinject -->
    </head>
    <body>
        <div class="container-scroller">
            @include('layouts.nav')
            <div class="container-fluid page-body-wrapper">
                <div class="main-panel">
                    @yield('content')
                </div>
                <!-- main-panel ends -->
            </div>
        </div>
        <!-- container-scroller -->
        <!-- base:js -->
        <script src="vendors/js/vendor.bundle.base.js"></script>
        <!-- endinject -->
        <!-- Plugin js for this page-->
        <!-- End plugin js for this page-->
        <!-- inject:js -->
        <script src="js/manifest.js"></script>
        <script src="js/vendor.js"></script>
        <!-- endinject -->
        <!-- plugin js for this page -->
        <script src="vendors/progressbar.js/progressbar.min.js"></script>
        <script src="vendors/chart.js/Chart.min.js"></script>
        <script src="vendors/flot/jquery.flot.js"></script>
        <script src="vendors/flot/curvedLines.js"></script>
        <!-- End plugin js for this page -->
    </body>
</html>