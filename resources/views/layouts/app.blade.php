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
        <link rel="stylesheet" href="css/style.css">
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
        <script src="js/off-canvas.js"></script>
        <script src="js/hoverable-collapse.js"></script>
        <script src="js/template.js"></script>
        <script src="js/settings.js"></script>
        <script src="js/todolist.js"></script>
        <!-- endinject -->
        <!-- plugin js for this page -->
        <script src="vendors/progressbar.js/progressbar.min.js"></script>
        <script src="vendors/chart.js/Chart.min.js"></script>
        <script src="vendors/flot/jquery.flot.js"></script>
        <script src="vendors/flot/curvedLines.js"></script>
        <!-- End plugin js for this page -->
        <!-- Custom js for this page-->
        <script src="js/dashboard.js"></script>
        <!-- End custom js for this page-->
    </body>
</html>