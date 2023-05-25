@extends('admin.layout.app')
@section('title', 'Labels Forecast')
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
                        <h2 class="content-header-title float-start mb-0">Labels Forecast</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">Home</a>
                                </li>
                                <li class="breadcrumb-item">Reports
                                </li>
                                <li class="breadcrumb-item">Labels Forecast
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
            <div class="card-header text-right" style="display:block !important;">
                <div class="row d-flex flex-row-reverse">
                    {{-- <div class="form-group col-sm-12 col-md-2" id="search">
                                <label for="search"></label>
                                <input type="submit" class="form-control btn btn-primary" value="Filter">
                            </div> --}}
                    <div class="form-group col-sm-12 col-md-2">
                        <label for="days-filter">Select Days</label>
                        <div class="dayfilter">
                            <select name="days_filter" id="days-filter" class="form-select select2" required>
                                <option value="">All</option>
                                <option value="under">Under 365 days</option>
                                <option value="over">Over 365 days</option>
                                <option value="no_sale">No Sale for 12 days</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-12 col-md-2">
                        <label for="brand">Select Brand</label>
                        <div class="invoice-customer">
                            <select name="brand" id="brand" class="form-select select2" required>
                                <option value="">All</option>
                            </select>
                            @error('brand')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div id="brand-error" class="text-danger font-weight-bold"></div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12 col-md-2">
                        <label for="customer">Select Customer</label>
                        <select name="customer" id="customer" class="form-control select2">
                            <option value="">All</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
            <div class="table-responsive" style="min-height: 350px">
                <table class="table data-table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Brand Name</th>
                            <th>Product</th>
                            <th>Labels</th>
                            <th>Forecast Days</th>
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
            $(document).on('change', '#customer', function() {
                $('.customer-error').html('');
                var id = $(this).val();
                if (id == "") {
                    $('#brand').html("<option value=''>All</option>");
                } else {
                    $.get('/customer/' + id + '/brand', function(result) {
                        if (result.status == "success") {
                            var options = "<option value=''>All</option>";
                            result.data.forEach(element => {
                                options += "<option value='" + element.id + "'>" + element
                                    .brand + "</option>"
                            });

                            $('#brand-error').html('');
                            $('#brand').html(options);
                        } else {
                            $('#brand').html("<option value=''>All</option>");
                            $('#brand-error').html(result.message);
                        }
                    });
                }
            });
        });
        $(function () {
            var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    // ordering: true,
                    stateSave: true,
                    bDestroy: true,
                // ajax: "{{ route('labelsforecast') }}",
                ajax: {
                    url: "{{ route('labelsforecast') }}",
                    data: function (d) {
                        d.min_date = $('#min').val();
                        d.max_date = $('#max').val();
                        d.customer = $('#customer').val();
                        d.brand = $('#brand').val();
                        d.days_filter = $('#days-filter').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'cust_name', name: 'cust_name', orderable: false},
                    {data: 'brand_name', name: 'brand_name', orderable: false},
                    {data: 'prod_name', name: 'prod_name', orderable: false},
                    {data: 'cust_has_label_qty', name: 'cust_has_label_qty', orderable: true},
                    {data: 'forecast_days', name: 'forecast_days', orderable: true},
                    // {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                'order': [
                    [5, 'asc']
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
            $('#customer, #brand').on('change', function() {
                table.draw(false);
            });
            $('#days-filter').on('change', function() {
                table.draw(false);
            });
        });
    </script>
@endsection