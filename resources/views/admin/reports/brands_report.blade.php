@extends('admin.layout.app')
@section('title', 'Brand Report')
@section('datatablecss')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.0/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@stop

@section('datepickercss')
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> --}}
    {{-- <link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css') }}"> --}}
@stop

@section('content')

    <style type="text/css">
        .dataTables_length {
            float: left;
            padding-left: 20px;
        }

        .dataTables_filter {
            padding-right: 20px;
        }

        .dataTables_info {
            padding-left: 20px !important;
            padding-bottom: 30px !important;
        }

        .dataTables_paginate {
            padding-right: 20px !important;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .modaldimensions::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .modaldimensions {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
        .form-control {
            /* line-height: 1.85 !important; */
        }
    </style>
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-7">
                            <h2 class="content-header-title float-start mb-0">Brands Report</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Reports
                                    </li>
                                    <li class="breadcrumb-item">Brands Report
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
                    <div class="row d-flex">
                        {{-- <div class="col-sm-3">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="from_date">From:</label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text"
                                        class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile from_date"
                                        placeholder="MM/DD/YYYY" id="from_date" name="from_date">
                                    @error('from_date')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="to_date">To:</label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text"
                                        class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile to_date"
                                        placeholder="MM/DD/YYYY" id="to_date" name="to_date">
                                    @error('to_date')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-sm-3">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="customer">Customers:</label>
                                </div>
                                <div class="col-sm-12">
                                    <select name="customer" id="customer" class="form-select select2" required>
                                        <option value="">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('brand')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="brand">Brands:</label>
                                </div>
                                <div class="col-sm-12">
                                    <select name="brand" id="brand" class="form-select select2" required>
                                        <option value="">All</option>
                                        {{-- @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->brand }}</option>
                                        @endforeach --}}
                                    </select>
                                    @error('brand')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="customer">Select Dates:</label>
                                </div>
                                <div class="col-sm-12">
                                    <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
                                        {{-- <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i>
                                    </input> --}}
                                </div>
                            </div>
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
                                {{-- <th>Product</th> --}}
                                {{-- <th>Labels</th> --}}
                                {{-- <th>Forecast Days</th> --}}
                                {{-- <th>Actions</th> --}}
                                <th>Orders</th>
                                {{-- <th>Sales</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th><span id="page-total"></span>(Total: <span id="grand-total"></span>)</th>
                            </tr>
                        </tfoot>
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
        <div class="toast basic-toast position-fixed top-0 end-0 m-2" role="alert" aria-live="assertive"
            aria-atomic="true">
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
    {{-- <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script> --}}
    {{-- <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script> --}}
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
    <!-- Date Range Picker -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@stop
@section('datepickerjs')
    {{-- <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/polyfill.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js') }}"></script> --}}
    {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> --}}
    {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}

@stop

@section('modal')
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
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
        $(function() {
            var reportRange = localStorage.getItem('reportrange-brands');
            if (reportRange == '01/01/2021 - 01/01/2021') {
                var start = moment().startOf('month');
                var end = moment();
            } else {
                reportRange = reportRange.split(' - ');
                var start = moment(reportRange[0]);
                var end = moment(reportRange[1]);
            }
            function cb(start, end) {
                $('#reportrange span').html(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
            }
            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                'All Time': ['01/01/2021', moment()],
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                }
            }, cb);
            cb(start, end);
        });
        $(function() {
            getBrandTotalOrders($('#customer').val(), $('#brand').val());
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('brands_report') }}",
                    data: function(d) {
                        let dates = $('#reportrange').val();
                        dates = dates.split(' - ');
                        let from_date = dates[0];
                        let to_date = dates[1];
                        d.from_date = from_date;
                        d.to_date = to_date;
                        d.customer = $('#customer').val();
                        d.brand = $('#brand').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'customer_name', name: 'customer_name', orderable: false},
                    {
                        data: 'brand',
                        name: 'brand_name',
                        orderable: false
                    },
                    // {data: 'product_name', name: 'product_name', orderable: true},
                    // {data: 'label_qty', name: 'label_qty', orderable: true},
                    // {data: 'forecast_days', name: 'forecast_days', orderable: true},
                    {
                        data: 'total_orders', render: $.fn.dataTable.render.number( ',', 2),
                        name: 'total_orders', render: $.fn.dataTable.render.number( ',', 2),
                        orderable: true
                    },
                    // {data: 'sales', name: 'sales', orderable: true},
                    // {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                'order': [
                    [3, 'desc']
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                            'number' ? i : 0;
                    };
                    // Total over all pages
                    total = api
                        .column(3)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(3, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $($('#page-total')).html(pageTotal.toLocaleString());
                },
                "drawCallback": function(settings) {
                    feather.replace();
                },
            });
            // $(".flatpickr-input").flatpickr({
            //     dateFormat: 'm/d/Y'
            // });
            // Refilter the table
            // $('#reportrange').on('change', function() {
            //     // let from_date = $('#reportrange').val();
            //     // alert(from_date);
            //     // // table.draw(false);
            // });
            $(document).on('change', '#reportrange', function () {
                // let dates = $('#reportrange').val();
                // dates = dates.split(' - ');
                // let from_date = dates[0];
                // let to_date = dates[1];
                var date = new Date().getTime();
                localStorage.setItem('data_time', date);
                localStorage.setItem('reportrange-brands', '');
                localStorage.setItem('reportrange-brands', $('#reportrange').val());
                table.draw(false);
                getBrandTotalOrders($('#customer').val(), $('#brand').val());
            });
            $('#customer, #brand, #forecast-days-status').on('change', function() {
                table.draw(false);
                getBrandTotalOrders($('#customer').val(), $('#brand').val());
            });
        });
        function getBrandTotalOrders(customerID, brandID)
        {
            let dates = $('#reportrange').val();
            dates = dates.split(' - ');
            let from_date = dates[0];
            let to_date = dates[1];
            $.ajax({
                url: "{{ route('get_brand_total_orders') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    customer: customerID,
                    brand: brandID,
                    from_date: from_date,
                    to_date: to_date
                },
                success:function(response)
                {
                    if (response.status == true) {
                        $('#grand-total').html(response.total_orders.toLocaleString());
                    }
                }
            });
        }
    </script>
@endsection
