@extends('admin.layout.app')
@section('title', 'Products Brand Report')
@section('datatablecss')

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/pages/app-invoice.css') }}">
@stop

@section('datepickercss')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css') }}">
@stop

@section('content')

<style type="text/css">
    .dataTables_length{float: left;padding-left: 20px;}
    .dataTables_filter{padding-right:20px;}
    .dataTables_info{padding-left: 20px !important; padding-bottom:30px !important;}
    .dataTables_paginate{padding-right: 20px !important;}
    /* Hide scrollbar for Chrome, Safari and Opera */
    .modaldimensions::-webkit-scrollbar {
    display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .modaldimensions {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
    }
</style>
<!-- BEGIN: Content-->
<div class="content-header row">
    <div class="content-header-left col-md-12 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <div class="row">
                    <div class="col-7">
                        <h2 class="content-header-title float-start mb-0">Products Brand Report</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">Home</a>
                                </li>
                                <li class="breadcrumb-item">Reports
                                </li>
                                <li class="breadcrumb-item">Products Brand Report
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" id="basic-table">
    <div class="col-12">
        <div class="card">
            {{-- <div class="card-header">
                <div class="row w-100">
                    <div class="form-group col-sm-12 col-md-2">
                        <label for="min">Start</label>
                        <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile" placeholder="Start Date" id="min" name="min">
                    </div>
                    <div class="form-group col-sm-12 col-md-2">
                        <label for="min">End</label>
                        <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile" placeholder="End Date" id="max" name="max">
                    </div>
                </div>
            </div> --}}
            <div class="table-responsive" style="min-height: 350px">
                <table class="table data-table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Brand Name</th>
                            <th>Customer</th>
                            <th>Labels</th>
                            {{-- <th>Forecast Days</th> --}}
                            {{-- <th>Actions</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Basic Tables end -->       
<!-- END: Content-->
@endsection

@section('modal')
    <!-- Basic toast -->
    <button class="btn btn-outline-primary toast-basic-toggler mt-2" id="toast-btn">ab</button>
    <div class="toast-container">
        <div class="toast basic-toast position-fixed top-0 end-0 m-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="icon" data-feather="check"></i> &nbsp;&nbsp;&nbsp;
                <strong class="me-auto">Vue Admin</strong>
                <small class="text-muted">11 mins ago</small>
                <button type="button" class="ms-1 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">Hello, world! This is a toast message. Hope you're doing well.. :)</div>
        </div>
    </div>
    @include('modals.modal')
@endsection

@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/tables/range_dates.js') }}"></script>
@stop
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/polyfill.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js') }}"></script>
    
@stop

@section('modal')
@endsection

@section('page_js')
    
    <script type="text/javascript">
        $(document).ready(function(){
        });
        $(function () {
            var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    stateSave: true,
                    bDestroy: true,
                // ajax: "{{ route('labelsforecast') }}",
                ajax: {
                    url: "{{ route('product_brands_report') }}",
                    // data: function (d) {
                    //     d.min_date = $('#min').val();
                    //     d.max_date = $('#max').val();
                    // }
                },
                columns: [
                    {data: 'product_name', name: 'product_name'},
                    {data: 'brand_name', name: 'brand_name'},
                    {data: 'customer_name', name: 'customer_name'},
                    {data: 'label_qty', name: 'label_qty'},
                    // {data: 'forecast_days', name: 'forecast_days'},
                    // {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "drawCallback": function( settings ) {
                    feather.replace();
                },
            });
            // Create date inputs
            minDate = new Date($('#min'), {
                format: 'MMMM Do YYYY'
            });
            maxDate = new Date($('#max'), {
                format: 'MMMM Do YYYY'
            });

            // Refilter the table
            $('#min, #max').on('change', function () {
                table.draw(false);
            });
        });
    </script>
@endsection