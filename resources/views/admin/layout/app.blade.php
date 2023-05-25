<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Warehousesystem Corporation">
    <meta name="keywords" content="Warehousesystem Corporation, Warehousesystem, warehouse">
    <meta name="author" content="Warehousesystem Corporation">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    <title>@yield('title')</title>
    <link rel="icon" href="{!! asset('images/titlelogo.jpg') !!}"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
        rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" href="{{ asset('admin/app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">
    @yield('datatablecss')

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" href="{{ asset('admin/app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/app-assets/css/colors.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/app-assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/app-assets/css/themes/dark-layout.css') }}">

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">

    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">

    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/animate/animate.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" href="{{ asset('admin/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <!-- END: Page CSS-->
    @yield('datepickercss')
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
    <!-- END: Custom CSS-->

    @yield('page_cs')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        .dataTables_wrapper .dataTables_processing {
            border: 1px solid #4B4B4B;
            font-weight: bold;
            background-color: #4B4B4B;
            /* opacity: 0.8; */
            color: white;
        }
    </style>
</head>

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static" data-open="click"
    data-menu="vertical-menu-modern" data-col="">
    <!-- Main navbar -->
    @include('admin.includes.header')
    {{-- @if (Auth::user()->hasRole('admin')) --}}
    @include('admin.includes.sidebar')
    {{-- @endif --}}
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-fluid p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
                @yield('content')
                <!-- Dashboard Ecommerce ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">

    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->
    @yield('modal')
    <script type="text/javascript" src="{{ URL::asset('admin/app-assets/vendors/js/vendors.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin/app-assets/vendors/js/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/form-number-input.js') }}"></script>
    @yield('datatablejs')
    @yield('datepickerjs')
    <script type="text/javascript" src="{{ URL::asset('admin/app-assets/js/core/app-menu.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin/app-assets/js/core/app.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/components/components-bs-toast.js') }}"></script>
    <!-- BEGIN: Page Vendor JS-->
    {{-- select 2 --}}
    <script src="{{ URL::asset('admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/form-select2.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    @yield('page_js')
    <script>
    var date = new Date();
    var currentTime = date.getTime();
    var storedDate = localStorage.getItem('data_time');
    var diff = (currentTime - storedDate)/60000;
    var mintdiff = Math.abs(Math.round(diff));
    if (mintdiff > 300) {
        localStorage.setItem('from', '');
        localStorage.setItem('to', '');
        localStorage.setItem('from_date', '');
        localStorage.setItem('to_date', '');
        localStorage.setItem('time_duration', '');
        localStorage.setItem('batch_status', '');
        localStorage.setItem('customer', '');
        localStorage.setItem('customer_id', '');
        localStorage.setItem('brand', '');
        localStorage.setItem('brand_id', '');
        localStorage.setItem('product', '');
        localStorage.setItem('product_id', '');
        localStorage.setItem('reportrange-orders', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange-invoices', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange-merged-invoices', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange-sales', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange-profit', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange_inventory_history_report', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange-brands', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange_all_products_report', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange_products_in_out_report', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange_postage', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange_order_return', '01/01/2021 - 01/01/2021');
        localStorage.setItem('reportrange_inventory_history', '01/01/2021 - 01/01/2021');
    }
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    });
    </script>
</body>

</html>
