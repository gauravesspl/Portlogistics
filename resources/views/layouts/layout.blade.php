<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon"/>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Port Logistics</title>

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="{{theme('plugins/fontawesome-free/css/all.min.css')}}">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="{{theme('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
        <link rel="stylesheet" href="{{theme('plugins/select2/css/select2.min.css')}}">
        <!-- DataTable CSS -->
        <link rel="stylesheet" href="{{theme('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
        <link rel="stylesheet" href="{{theme('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
        <link rel="stylesheet" href="{{theme('plugins/datatables-select/css/select.bootstrap4.min.css')}}">
        <!-- Jquery UI -->
        <link rel="stylesheet" href="{{theme('plugins/jquery-ui/jquery-ui.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{theme('dist/css/adminlte.min.css')}}">
        <link rel="stylesheet" href="{{css('custom')}}">
        <link rel="stylesheet" href="{{theme('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">

        <link rel="stylesheet" href="{{css('app_css')}}">
        <link rel="stylesheet" href="{{css('bootstrap_multiselect_css')}}">
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9.17.1/dist/sweetalert2.min.css">
       
        @stack('style')
    </head>

    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div id="sectionLoader">
            <div class="loader">
            <div class="loader-item"></div>
                <div><h2>Processing...</h2></div>
            </div>
        </div>
        <div class="wrapper">
            @if(Auth::check())
            @include('layouts.components.header')

                @if(Auth::User()->auth_browser == false)
                @include('layouts.components.leftmenu')
                @endif
            @endif

            @yield('content')
            <!-- Main Footer -->
            @include('layouts.components.footer')

        </div>
        <!-- ./wrapper -->

        <!-- REQUIRED SCRIPTS -->
        <!-- jQuery -->
        <script src="{{theme('plugins/jquery/jquery.min.js')}}"></script>
        <!-- Bootstrap -->
        <script src="{{theme('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{theme('plugins/select2/js/select2.full.min.js')}}"></script>
        <!-- DataTable CSS -->
        <script src="{{theme('plugins/datatables/jquery.dataTables.min.js')}}"></script>
        <script src="{{theme('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
        <script src="{{theme('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
        <script src="{{theme('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
        <script src="{{theme('plugins/datatables-select/js/dataTables.select.min.js')}}"></script>
        <!-- Datetimepicker -->
        <script src="{{theme('plugins/moment/moment.min.js')}}"></script>
        <script src="{{theme('plugins/jquery-ui/jquery-ui.js')}}"></script>
        <script src="{{theme('plugins/jquery-validation/jquery.validate.min.js')}}"></script>
        <script src="{{theme('plugins/jquery-validation/additional-methods.min.js')}}"></script>
        <!-- overlayScrollbars -->
        <script src="{{theme('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
        <!-- AdminLTE App -->
        <script src="{{theme('dist/js/adminlte.js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.all.min.js" integrity="sha256-3c8+zl+f7KU8SCc2tH1SAmyiy6kyg1fx9X4x75+xqzc=" crossorigin="anonymous"></script>
        <script src="{{theme('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
        <script src="{{theme('plugins/moment/moment.min.js')}}"></script>
        <script>
            var APP_URL = {!!json_encode(url('/')) !!};
        </script>
        <script src="{{js('app_js')}}"></script>
        <script src="{{js('bootstrap_multiselect_js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha256-t9UJPrESBeG2ojKTIcFLPGF7nHi2vEc7f5A2KpH/UBU=" crossorigin="anonymous"></script>

        @stack('script')
    </body>
</html>