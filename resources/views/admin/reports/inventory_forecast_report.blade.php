@extends('admin.layout.app')
@section('title', 'Inventory Forecast Report')
@section('datatablecss')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.0/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

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

        td.details-control {
            background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.details-control {
            background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
        }

        .lds-dual-ring {
            display: inline-block;
            width: 10px;
            height: 10px;
        }

        .lds-dual-ring:after {
            content: " ";
            display: block;
            width: 14px;
            height: 14px;
            margin: 0px 10px;
            border-radius: 50%;
            border: 2px solid #000;
            border-color: #000 transparent #000 transparent;
            animation: lds-dual-ring 1.2s linear infinite;
        }

        @keyframes lds-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">Inventory Forecast Report</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Reports
                                    </li>
                                    <li class="breadcrumb-item">Inventory Forecast Report
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-3 addBtnClass">
                            {{-- <a href="/units/create" style="margin-left:auto;"  class="btn btn-primary waves-effect waves-float waves-light">Add New Unit</a> --}}

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row w-100">
                        <form>
                            @csrf
                            <div class="row w-100">
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="min">Categories</label>
                                    <select name="category_id" id="category_id" class="form-control select2 category_id">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="form-group col-sm-12 col-md-3">
                                                <label for="time_duration">Select Duration</label>
                                                <select name="time_duration" id="time_duration" class="form-control select2">
                                                    <option value="">- Select Duration -</option>
                                                    <option value="yesterday" @isset($duration) @if ($duration == 'yesterday') selected @endif @endisset>Yesterday</option>
                                                    <option value="last_week" @isset($duration) @if ($duration == 'last_week') selected @endif @endisset>Last Week</option>
                                                    <option value="last_month" @isset($duration) @if ($duration == 'last_month') selected @endif @endisset>Last Month</option>
                                                    <option value="last_six_months" @isset($duration) @if ($duration == 'last_six_months') selected @endif @endisset>Last 6 Months</option>
                                                    <option value="last_year" @isset($duration) @if ($duration == 'last_year') selected @endif @endisset>Last Year</option>
                                                </select>
                                            </div> --}}
                                {{-- <div class="form-group col-sm-12 col-md-2">
                                    <button class="btn btn-primary submit" type="button"
                                        style="margin-top: 20px">Submit</button>
                                </div> --}}
                            </div>
                            <br>
                            {{-- <div class="row w-100">
                                            <div class="form-group col-sm-12 col-md-3">
                                                <label for="min" class="mb-1">Expand All</label>
                                                <div class="form-check form-check-primary form-switch">
                                                    <span class="loader d-none"><div class="lds-dual-ring"></div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Loading...</span><input type="checkbox" name="show_all" value="0" class="form-check-input" id="show_all" disabled>
                                                </div>
                                            </div>
                                        </div> --}}
                        </form>
                    </div>
                </div>
                <div class="p-2">
                    <hr>
                    <div class="table-responsive">
                        <table class="table products-table table-bordered">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Product Name</th>
                                    <th class="text-center">Forecast Statuses</th>
                                    <th class="text-center">Forecast Days</th>
                                    <th>Available Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
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
@endsection
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
    <!-- Date Range Picker -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@stop
@section('page_js')
    <script type="text/javascript">
        var minDate, maxDate;
        $(document).ready(function() {
            @if (session('success'))
                $('.toast .me-auto').html('Success');
                $('.toast .toast-header').addClass('bg-success');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{ session('success') }}");
                $('#toast-btn').click();
            @endif
            $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                var min = start.format('DD-MM-YYYY');
                var max = end.format('DD-MM-YYYY');
            });
            var table = $('.products-table').DataTable({
                // ordering: true,
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('inventory_forecast_report') }}",
                    data: function(d) {
                        d.category_id = $('#category_id').val();
                        d.duration = $('#time_duration').val();
                        d.date_range = $('#daterange').val();
                    }
                },
                columns: [{
                        data: 'category_name',
                        name: 'category_name', 
                        orderable: false
                    },
                    {
                        'className': 'tbl_clr',
                        data: 'name',
                        name: 'name', 
                        orderable: false
                    },
                    {
                        data: 'forecast_statuses',
                        name: 'forecast_statuses', 
                        orderable: false
                    },
                    {
                        data: 'forecast_val',
                        name: 'forecast_val', 
                        orderable: true
                    },
                    {
                        data: 'pqty', render: $.fn.dataTable.render.number( ',' ),
                        name: 'pqty', render: $.fn.dataTable.render.number( ',' ), 
                        orderable: false
                    },
                ],
                'order': [
                    [3, 'asc']
                ],
            });
            $(document).on('change', '#category_id', function() {
                table.draw();
            });
            $(document).on('click', '.submit', function() {
                // alert($('#daterange').val());
                table.draw();
            });
            // $(document).on('change', '#daterange', function(){
            //     var value = $(this).val();
            //     value = value.split(" - ");
            //     min = value[0];
            //     max = value[1];
            //     var minDate = getMinDate(min);
            //     var maxDate = getMinDate(max);
            // });
        });

        function getMinDate(min) {
            var newMinDate = new Date(min);
            var d1 = newMinDate.getDate();
            var m1 = newMinDate.getMonth() + 1;
            var y1 = newMinDate.getFullYear();
            var minDate = y1 + '-' + m1 + '-' + d1;
            return minDate;
        }

        function getMaxDate(max) {
            var newMaxDate = new Date(max);
            var d2 = newMaxDate.getDate();
            var m2 = newMaxDate.getMonth() + 1;
            var y2 = newMaxDate.getFullYear();
            var maxDate = y2 + '-' + m2 + '-' + d2;
            return maxDate;
        }
    </script>
@endsection
