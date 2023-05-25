@extends('admin.layout.app')
@section('title', 'Batches History')
@section('datatablecss')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/pages/app-invoice.css') }}">
@stop

@section('datepickercss')
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css') }}">
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

        .order-table thead th {
            font-size: 0.7vw
        }
        .popup {
            position: relative;
            display: inline-block;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        /* The actual popup */
        .popup .popuptext {
            visibility: visible;
            min-width: 150px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 4px 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 35%;
            margin-left: -55px;
            font-size: 12px;
            font-weight: bold;
            height: 25px;
        }
        /* Popup arrow */
        .popup .popuptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }
    </style>
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">Batches</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Batches
                                    </li>
                                    <li class="breadcrumb-item"><a href="/orders">Batches History</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                        @if (Auth::user()->hasRole('admin'))
                            <div class="col-3 addBtnClass">
                                <a href="{{route('orders.create')}}" style="float:right;margin-right:15px;"
                                    class="btn btn-primary waves-effect waves-float waves-light">Add New Batches</a>

                            </div>
                        @endif
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
                        {{-- <div class="form-group col-sm-12 col-md-2">
                            <label for="min">Start</label>
                            <input type="text"
                                class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile"
                                placeholder="MM/DD/YYYY" id="min" name="min">
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="min">End</label>
                            <input type="text"
                                class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile"
                                placeholder="MM/DD/YYYY" id="max" name="max">
                        </div> --}}
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="customer">Customer</label>
                            <select name="customer" id="customer" data-select="customer" class="select2 form-select arrow">
                                <option value="">All</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ ucwords($customer->customer_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="brand">Brands</label>
                            <select name="brand" id="brand" class="select2 form-select arrow">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="status">Batch Status</label>
                            <select name="status" id="status" class="form-control arrow">
                                <option value="">All</option>
                                <option value="0">New Order</option>
                                <option value="1">In Process</option>
                                <option value="2">Shipped</option>
                                <option value="3">Delivered</option>
                                <option value="4">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-3">
                            <label for="status">Select Dates</label>
                            <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <div class="panel-body">
                            </form>
                        </div>
                    </div>
                    <table class="table data-table order-table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 2%"><input type="checkbox" id="checkAllOrders" /></th>
                                <th style="width: 13%">Date</th>
                                <th style="width: 13%">Customer Name</th>
                                <th style="width: 16%">Brand Name</th>
                                {{-- <th>Batches</th> --}}
                                <th style="width: 5%">Orders</th>
                                <th style="width: 10%">Notes</th>
                                <th style="width: 3%">Total</th>
                                <th style="width: 5%">Status</th>
                                <th style="width: 5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="font-size: 14px; width: 2%"></th>
                                <th style="font-size: 14px; width: 13%"></th>
                                <th style="font-size: 14px; width: 13%"><span class=""></span></th>
                                <th style="font-size: 14px; width: 16%"><span class=""></span></th>
                                <th style="font-size: 14px; width: 5%"><span class="batches-total"></span><span class="batch-all-total"> </span></th>
                                <th style="font-size: 14px; width: 10%"><span class=""></span></th>
                                <th style="font-size: 14px; width: 3%"><span class="total-cost"></span><span class="batch-all-total-cost"> </span></th>
                                <th style="font-size: 14px; width: 5%"><span class=""></span></th>
                                <th style="font-size: 14px; width: 5%"></th>
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
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/tables/range_dates.js') }}"></script>
@stop
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/polyfill.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@stop
@section('page_js')

    <script type="text/javascript">
        var minDate, maxDate;
        function myFunction() {
            var r = confirm("Are you Sure?");
            if (r == true) {
                return true;
            } else {
                return false;
            }
            document.getElementById("demo").innerHTML = txt;
        }
        $(document).ready(function() {
            @if (session('success'))
                $('.toast .me-auto').html('Success');
                $('.toast .toast-header').addClass('bg-success');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{session('success')}}");
                $('#toast-btn').click();
            @elseif (session('error'))
                $('.toast .me-auto').html('Error');
                $('.toast .toast-header').addClass('bg-danger');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{session('error')}}");
                $('#toast-btn').click();
            @endif
            $(document).on('mouseenter', '.popup', function () {
                $(this).closest('tr').find('.popuptext').removeClass('d-none');
            });
            $(document).on('mouseleave', '.popup', function () {
                $(this).closest('tr').find('.popuptext').addClass('d-none');
            });
            calculateTotalBatches();
            setTimeout(function(){
                filterBrands($('#customer').val());
            }, 500);
            if (localStorage.getItem('from') != '') {
                $('#min').val(localStorage.getItem('from'));
            }
            if (localStorage.getItem('to') != '') {
                $('#max').val(localStorage.getItem('to'));
            }
            if (localStorage.getItem('reportrange-orders') != '') {
                $('#reportrange').val(localStorage.getItem('reportrange-orders'));
            }
            if (localStorage.getItem('batch_status') != '') {
                $('#status').val(localStorage.getItem('batch_status'));
            }
            if (localStorage.getItem('customer_id') != '') {
                var customer = $('#customer');
                customer.find('option[value="'+localStorage.getItem('customer_id')+'"]').attr('selected', 'selected').change();
                if (localStorage.getItem('brand_id') != '') {
                    var brand = $('#brand');
                    setTimeout(function(){
                        brand.find('option[value="'+localStorage.getItem('brand_id')+'"]').attr('selected', 'selected').change();
                    }, 1000);
                }
            }
            $(document).on('change', '#min, #max, #status, #customer, #brand, #reportrange', function(e) {
                // Reset Values
                localStorage.setItem('from', '');
                localStorage.setItem('to', '');
                localStorage.setItem('reportrange-orders', '');
                localStorage.setItem('batch_status', '');
                localStorage.setItem('customer_id', '');
                localStorage.setItem('brand_id', '');
                localStorage.setItem('data_time', '');
                // Set Values
                localStorage.setItem('from', $('#min').val());
                localStorage.setItem('to', $('#max').val());
                localStorage.setItem('reportrange-orders', $('#reportrange').val());
                localStorage.setItem('batch_status', $('#status').val());
                localStorage.setItem('customer_id', $('#customer').val());
                localStorage.setItem('brand_id', $('#brand').val());
                var date = new Date().getTime();
                localStorage.setItem('data_time', date);
                if ($(this).attr('data-select') != undefined) {
                    if ($(this).attr('data-select') == 'customer') {
                        filterBrands($(this).val());
                    }
                }
                calculateTotalBatches();
            });
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                // ordering: false,
                paging: true,
                stateSave: true,
                //ajax: "{{ route('order/listing') }}",
                ajax: {
                    url: "{{ route('order/listing') }}",
                    data: function(d) {
                        let dates = $('#reportrange').val();
                        dates = dates.split(' - ');
                        let from_date = dates[0];
                        let to_date = dates[1];
                        d.search_status = $('#search_status').val();
                        // d.min_date = $('#min').val();
                        // d.max_date = $('#max').val();
                        d.min_date = from_date;
                        d.max_date = to_date;
                        d.order_status = $('#status').val();
                        d.customer = $('#customer').val();
                        d.brand = $('#brand').val();
                    }
                },
                columns: [{
                        data: 'order_checkbox',
                        name: 'order_checkbox',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        orderable: false,
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name',
                        width: '15%',
                        orderable: false,
                    },
                    {
                        data: 'mailerqty', render: $.fn.dataTable.render.number( ',', 2),
                        name: 'mailerqty', render: $.fn.dataTable.render.number( ',', 2),
                        width: '15%',
                        orderable: true,
                    },
                    {
                        data: 'notes',
                        name: 'notes',
                        width: '15%',
                        orderable: false,
                    },
                    {
                        data: 'total_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                        name: 'total_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                        orderable: true,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                            'number' ? i : 0;
                    };
                    pageTotal1 = api
                        .column(4, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(4).footer()).find('.batches-total').html(pageTotal1.toLocaleString());
                    pageTotal2 = api
                        .column(6, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(6).footer()).find('.total-cost').html('$'+(Number(pageTotal2.toFixed(2)).toLocaleString()));
                },
                "drawCallback": function(settings) {
                    feather.replace();
                },
                'order': [
                    [1, 'desc']
                ],
                "stateLoadParams": function (settings, data) {
                    data.start = "0";
                },
            });
            // Custom filtering function which will search data in column four between two values
            $('#searchRecords').on('click', function(e) {
                table.draw(false);
                e.preventDefault();
            });
            // $(".flatpickr-input").flatpickr({
            //     dateFormat: 'm/d/Y'
            // });
            // // Create date inputs
            // minDate = new Date($('#min'), {
            //     format: 'MMMM Do YYYY'
            // });
            // maxDate = new Date($('#max'), {
            //     format: 'MMMM Do YYYY'
            // });
            // Refilter the table
            $('#min, #max, #reportrange').on('change', function() {
                table.draw(false);
                calculateTotalBatches();
                table.ajax.reload();
            });
            // Refilter the table by status
            $('#status').on('change', function() {
                table.draw(false);
                calculateTotalBatches();
            });
            // Refilter the table by customer
            $('#customer, #brand').on('change', function() {
                table.draw(false);
                table.ajax.reload();
            });
            window.datatable = table;
            $(document).on("change", ".order_status_id", function(e) {
                e.preventDefault();
                var alert = myFunction();
                if (alert == true) {
                    var statusId = $(this).val();
                    var orderId = $(this).data('order-id');
                    $.ajax({
                        type: 'POST',
                        url: '/updateOrderStatus',
                        data: {
                            "order_id": orderId,
                            "status_id": statusId,
                            _token: "{{ csrf_token() }}",
                            dataType: "JSON",
                        },
                        success: function(response) {
                            calculateTotalBatches();
                            if (response.success == true) {
                                $('.toast .me-auto').html('Success');
                                $('.toast .toast-header').addClass('bg-success');
                                $('.toast .text-muted').html('Now');
                                $('.toast .toast-body').html(response.message);
                                $('#toast-btn').click();
                                window.datatable.draw(false);
                            } else if (response.error == true) {
                                $('.toast .me-auto').html('Error');
                                $('.toast .toast-header').addClass('bg-danger');
                                $('.toast .text-muted').html('Now');
                                $('.toast .toast-body').html(response.message);
                                $('#toast-btn').click();
                            }
                        }
                    });
                }
            });
            $(document).on('click', '.delete_order', function(e) {
                // e.preventDefault();
                var alert = myFunction();
                if (alert == true) {
                    var orderId = $(this).attr('data-order-id');
                    var url = "{{ route('delete_order', ':id') }}";
                    url = url.replace(':id', orderId);
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success:function(res) {
                            if (res.status == true) {
                                $('.toast .toast-header').removeClass('bg-danger');
                                $('.toast .me-auto').html('Success');
                                $('.toast .toast-header').addClass('bg-success');
                                $('.toast .text-muted').html('Now');
                                $('.toast .toast-body').html("Deleted Successfully");
                                $('#toast-btn').click();
                                window.datatable.draw(false);
                            } else {
                                $('.toast .toast-header').removeClass('bg-success');
                                $('.toast .me-auto').html('Error');
                                $('.toast .toast-header').addClass('bg-danger');
                                $('.toast .text-muted').html('Now');
                                $('.toast .toast-body').html("Something went wrong");
                                $('#toast-btn').click();
                            }
                        }
                    });
                }
            });
        });
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        $(function() {
            var reportRange = localStorage.getItem('reportrange-orders');
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
        function calculateTotalBatches() {
            setTimeout(() => {
                let customerID = $('#customer').val();
                if (customerID == null) {
                    customerID = localStorage.getItem('customer_id');
                }
                let dates = $('#reportrange').val();
                dates = dates.split(' - ');
                let from_date = dates[0];
                let to_date = dates[1];
                $.ajax({
                    url: "{{ route('get_customer_total_batches') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer: customerID,
                        min_date: from_date,
                        max_date: to_date,
                        status: $('#status').val(),
                        brand: $('#brand').val()
                    },
                    success:function(resp) {
                        if (resp.status == true) {
                            $('.batch-all-total').html('(Total '+Number(resp.mailerqty).toLocaleString()+')');
                            $('.batch-all-total-cost').html('(Total $'+Number(resp.total_cost).toLocaleString()+')');
                        }
                    }
                });
            }, 500);
        }
        function filterBrands(customerId) {
            $.get('/customer/'+customerId+'/brand', function(result) {
                if(result.status == "success") {
                    var options = "<option value=''>Select Brand</option>";
                    result.data.forEach(element => {
                        options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                    });
                    $('#brand').html(options);
                } else {
                    $('#brand').html("<option value=''>Select Brand</option>");
                }
            });
        }
        $("body").on("change", "#checkAllOrders", function() {
            if (this.checked) {
                $('.singleOrderCheck').prop("checked", true);
            }
        });
        $("body").on("change", "#all_order_status_change", function() {
            var statusId = $(this).val();
            if ($('#checkAllOrders').is(':checked')) {
                $('.singleOrderCheck').each(function() {
                    if ($(this).is(':checked')) {
                        var orderId = $(this).data('singleorder-id');
                        $.ajax({
                            type: 'POST',
                            url: '/updateOrderStatus',
                            data: {
                                "order_id": orderId,
                                "status_id": statusId,
                                _token: "{{ csrf_token() }}",
                                dataType: "JSON",
                            },
                            success: function(data) {
                                console.log('success');
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection
