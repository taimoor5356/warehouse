@extends('admin.layout.app')
@section('title', 'Returned Reports')
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

        tr:hover {
            background: rgb(223, 226, 255);
        }
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
        /**! ColResize 2.6.0
                     * ©2017 Steven Masala
                     */

        .dt-colresizable-table-wrapper {
            overflow-x: auto;
            overflow-y: hidden;
            /* width: 100%; */
            position: relative;
        }

        .word-wrap {
            word-wrap: break-word;
        }

        .dt-colresizable {
            height: 0;
            position: relative;
            top: 0;
            z-index: 999;
        }

        .dt-colresizable .dt-colresizable-col {
            display: block;
            position: absolute;

            width: 5px;
            cursor: ew-resize;
            z-index: 1000;
        }

        .dt-colresizable-table-wrapper.dt-colresizable-with-scroller {
            overflow-x: auto;
            overflow-y: hidden;
        }

        .dt-colresizable-scroller-wrapper {
            position: absolute;
            overflow-y: hidden;
            overflow-x: hidden;
            /** FF **/
            /* width: 100%; */
            right: 0;
        }

        .dt-colresizable-scroller-content-wrapper {
            /* width: 100%; */
        }

        .dt-colresizable-scroller-content {
            /* width: 100%; */
        }

        .dt-colresizable-with-scroller table thead,
        .dt-colresizable-with-scroller table tbody tr {
            table-layout: fixed;
            /* width: 100%;     */
        }

        .dt-colresizable-with-scroller table tbody {
            overflow-y: hidden;
        }

        table.data-table {
            table-layout: fixed;
            margin: 0;
        }

        table.data-table,
        table.data-table th,
        table.data-table td {}

        table.data-table thead th,
        table.data-table tbody td,
        table.data-table tfoot td {
            /* overflow: hidden; */
        }
        td.tbl_clr {
            background-color: rgb(253, 253, 253);
        }
    

        .order-return-menu, .order-return-menu .show{
            position: absolute !important;
            width: 215px;
            transform: translate(-200px, 15px) !important;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .dropdownTable .dt-colresizable-table-wrapper {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .order-return-menu .dropdown-item{
            display: initial;
        }

        th.tbl_clr1 {
            background-color: rgb(253, 253, 253);
        }

        th.tbl_clr2 {
            background-color: rgb(253, 253, 253);
            left: 50px !important;
        }

        th.tbl_clr3 {
            background-color: rgb(253, 253, 253);
            left: 150px !important;
        }

        td.tbl_clr1 {
            background-color: rgb(253, 253, 253);
        }

        td.tbl_clr2 {
            background-color: rgb(253, 253, 253);
            left: 50px !important;
        }

        td.tbl_clr3 {
            background-color: rgb(253, 253, 253);
            left: 150px !important;
        }
        .dropdown-menu .dropdown-item {
            display: block !important;
        }

    </style>
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">Returned Reports</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Customers
                                    </li>
                                    <li class="breadcrumb-item">Returns List
                                    </li>
                                </ol>
                            </div>
                        </div>
                        @canany(['return_order_create', 'return_order_view_all'])
                            <div class="col-3 addBtnClass">
                                <a href="/create_return_order" style="float:right;margin-right:15px;"
                                    class="btn btn-primary waves-effect waves-float waves-light">Create Return Order</a>
                            </div>
                        @endcanany
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
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="po_box_number">Select Po Box</label>
                            <select name="po_box_number" id="po_box_number" class="form-control select2">
                                <option value="all">All</option>
                                @foreach ($po_box_numbers as $po_box_number)
                                    <option value="{{ $po_box_number }}">{{ $po_box_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="order">Select Order Number</label>
                            <select name="order" id="order" class="form-control select2">
                                <option value="">All</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->order_number }}">
                                        @if ($order->order_number == null)
                                            -- NIL --
                                        @else
                                            {{ $order->order_number }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- @if(Auth::user()->hasRole('admin')) --}}
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="customer">Select Customer</label>
                            <select name="customer" id="customer" class="form-control select2">
                                <option value="all">All</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- @endif --}}
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="brand">Select Brand</label>
                            <div class="invoice-customer">
                                <select name="brand" id="brand" class="form-select select2" required>
                                    <option value="">All</option>
                                    @if (Auth::user()->hasRole('customer'))
                                        @foreach ($brands as $brand)
                                            <option value="{{$brand->id}}">{{$brand->brand}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('brand')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                                <div id="brand-error" class="text-danger font-weight-bold"></div>
                            </div>
                        </div>
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
                            <label for="product">Select Dates</label>
                            <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
                        </div>
                    </div>
                </div>
                <div class="p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" id="returned-orders-tab" data-bs-toggle="pill" href="#returned-orders" aria-expanded="false">Returned Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="product-weekly-report-tab" data-bs-toggle="pill" href="#product-weekly-report" aria-expanded="false">Product Weekly Report</a>
                        </li>
                    </ul>
                    <hr>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="returned-orders" role="tabpanel" aria-labelledby="returned-orders-tab" aria-expanded="false">
                                    <div class="table-height" style="overflow-y: hidden; overflow-x: hidden">
                                        <table class="table data-table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="tbl_clr1" style="z-index: 1">
                                                        <center>
                                                        </center>
                                                    </th>
                                                    <th class="tbl_clr2" style="z-index: 1">Date</th>
                                                    <th class="tbl_clr3" style="z-index: 1">Order No</th>
                                                    <th>Customer</th>
                                                    <th>Brand</th>
                                                    <th>Status</th>
                                                    <th>Name</th>
                                                    <th>State</th>
                                                    <th>Total Products</th>
                                                    <th>Selling Cost Credit</th>
                                                    <th>Return Service Charges</th>
                                                    <th>Total Credit</th>
                                                    {{-- <th>OWE</th> --}}
                                                    <th>CS Status</th>
                                                    <th>Notes</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="total-products"><span class="page-total-products"></span><span class="all-pages-total-products"></span></th>
                                                <th class="selling-cost-credit"><span class="page-total-selling-cost-credit"></span><span class="all-pages-selling-cost-credit"></span></th>
                                                <th class="return-service-charges"><span class="page-total-return-charges"></span><span class="all-pages-return-charges"></span></th>
                                                <th class="total-credit"><span class="page-total-credit"></span><span class="all-pages-total-credit"></span></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="product-weekly-report" role="tabpanel" aria-labelledby="product-weekly-report-tab" aria-expanded="true">
                                    <div class="table">
                                        <table class="table product-weekly-report-data-table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Product</th>
                                                    <th>Returned Qty</th>
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
                </div>
            </div>
        </div>
    </div>
    <!-- Basic Tables end -->
    <!-- END: Content-->
@endsection
@section('modal')
    <div class="modal fade text-start show" id="addNotes" tabindex="-1" aria-labelledby="myModalLabel34" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel34">Add Notes</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- <form action="{{ route('update_product_notes') }}" enctype="multipart/form-data" method="post"> --}}
                    {{-- @csrf --}}
                    <div class="modal-body">
                        <label>Note: </label>
                        <div class="mb-1">
                            <textarea name="notes" id="description" cols="55" rows="6"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="order_id" id="modalOrderId" value="">
                    <input type="hidden" name="product_id" value="">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light submit_add_notes" data-product-id="" data-order-id="" data-bs-dismiss="modal">Submit</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>
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

@section('page_js')
    <script type="text/javascript">
        var minDate, maxDate;
        const dt_state = {
            "time": "1651073437697",
            "order": [
                [
                    "1",
                    "asc"
                ]
            ],
            "start": "0",
            "length": "10",
            "search": {
                "regex": "false",
                "smart": "true",
                "search": null,
                "caseInsensitive": "true"
            },
            "columns": [
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                },
                {
                    "search": {
                        "regex": "false",
                        "smart": "true",
                        "search": null,
                        "caseInsensitive": "true"
                    },
                    "visible": "true"
                }
            ],
            "colResize": {
                "widths": [
                    "75",
                    "122",
                    "105",
                    "242",
                    "135",
                    "143",
                    "86",
                    "56.6667",
                    "91",
                    "204",
                    "145",
                    "177",
                    "65.6667"
                ]
            }
        };
        function myFunction() {
            var r = confirm("Do you really want to delete this order?");
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
                $('.toast .me-auto').html('Success');
                $('.toast .toast-header').addClass('bg-danger');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{session('error')}}");
                $('#toast-btn').click();
            @endif
            calculateTotalReturnOrders();
            var table = $('.data-table').DataTable({
                searchDelay: 200,
                scrollCollapse: true,
                fixedColumns: {
                    left: 3,
                },
                processing: true,
                serverSide: true,
                // ordering: true,
                autoWidth: true,
                fixedHeader: true,
                stateSave: true,
                stateDuration: -1,
                stateLoadCallback: function(settings) {
                    var o;
                    $.ajax({
                        url: '{{ route("get_return_order_state") }}',
                        async: false,
                        dataType: 'json',
                        success: function(json) {
                            o = json;
                        }
                    });
                    if (o.length !== undefined)
                        return JSON.parse(o);
                    return dt_state;
                },
                stateSaveCallback: function(settings, stateData) {
                    $.ajax({
                        url: '{{ route("update_return_order_state") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            return_order_state: stateData
                        },
                        dataType: 'json'
                    })
                },
                colResize: {
                    resizeTable: true
                },
                bDestroy: true,
                ajax: {
                    url: "{{ route('orders.orderReturn') }}",
                    data: function(d) {
                        let dates = $('#reportrange').val();
                        dates = dates.split(' - ');
                        var min = dates[0];
                        var max = dates[1];
                        d.search_status = $('#search_status').val();
                        d.min_date = min;
                        d.max_date = max;
                        d.order_status = $('#status').val();
                        d.customer = $('#customer').val();
                        d.brand = $('#brand').val();
                        d.order_number = $('#order').val();
                        d.po_box_number = $("#po_box_number").val();
                    }
                },
                columns: [
                    {data: 'btn', name: 'btn', 'className': 'tbl_clr1', orderable: false},
                    {
                        'className': 'tbl_clr2',
                        data: 'date',
                        name: 'date',
                        orderable: true
                    },
                    {
                        'className': 'tbl_clr3',
                        data: 'order_number',
                        name: 'order_number',
                        orderable: false
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        orderable: false
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false
                    },
                    {
                        data: 'state',
                        name: 'state',
                        orderable: false
                    },
                    {
                        data: 'product_counts',
                        name: 'product_counts',
                        orderable: true
                    },
                    {
                        data: 'selling_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                        name: 'selling_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                        orderable: true
                    },
                    {
                        data: 'return_service_charges', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                        name: 'return_service_charges', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                        orderable: true
                    },
                    {
                        data: 'cog_credit', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                        name: 'cog_credit', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                        orderable: true
                    },
                    // {
                    //     data: 'owe', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                    //     name: 'owe', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                    // },
                    {
                        data: 'cs_status',
                        name: 'cs_status',
                        orderable: false
                    },
                    {
                        data: 'notes',
                        name: 'notes',
                        orderable: false
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
                        .column(8, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(8).footer()).find('.page-total-products').html(pageTotal1);
                    pageTotal2 = api
                        .column(9, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            if (!($.isNumeric(a))) {
                                a = 0;
                            }
                            if (!($.isNumeric(b))) {
                                b = 0;
                            }
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(9).footer()).find('.page-total-selling-cost-credit').html('$'+pageTotal2.toFixed(2));
                    pageTotal2 = api
                        .column(10, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(10).footer()).find('.page-total-return-charges').html('$'+pageTotal2.toFixed(2));
                    pageTotal2 = api
                        .column(11, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(11).footer()).find('.page-total-credit').html('$'+pageTotal2.toFixed(2));
                },
                "drawCallback": function(settings) {
                    feather.replace();
                },
                'order': [
                    [1, 'desc']
                ],
            });

            window.myDataTable = table;

            $(document).on('click', '#returned-orders-tab', function() {
                table.draw(false);
            });

            $(document).on('click', '.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                $('#modalOrderId').val('');

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    // Open this row
                    $(this).attr('src', 'https://datatables.net/examples/resources/details_open.png');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    feather.replace();
                    tr.addClass('shown');
                    // Close this row
                    $(this).attr('src', 'https://datatables.net/examples/resources/details_close.png');
                    window.newDataTable = $('.newDataTable').DataTable({
                        searching: false,
                        ordering: false,
                        paging: false,
                        retrieve: true,
                        info: false,
                        stateSave: true,
                    });
                }
            });

            $(document).on('click', '#product-weekly-report-tab', function() {
                var returnedProductTable = $('.product-weekly-report-data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    bDestroy: true,
                    stateSave: true,
                    ajax: {
                        url: '{{ route("product_return_weekly_report") }}'
                    },
                    columns: [
                        {
                            data: 'date',
                            name: 'date',
                        },
                        {
                            data: 'product_name',
                            name: 'product_name',
                        },
                        {
                            data: 'returned_qty',
                            name: 'returned_qty',
                        },
                    ],
                    "drawCallback": function(settings) {
                        feather.replace();
                    },
                    'order': [
                        [1, 'asc']
                    ],
                });
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
                var date = new Date().getTime();
                localStorage.setItem('data_time', date);
                localStorage.setItem('reportrange_order_return', '');
                localStorage.setItem('reportrange_order_return', $('#reportrange').val());
                calculateTotalReturnOrders();
                table.draw(false);
            });

            // Refilter the table by status
            $('#status').on('change', function() {
                table.draw(false);
            });

            // Refilter the table by customer
            $('#customer').on('change', function() {
                calculateTotalReturnOrders();
                table.draw(false);
            });
            // refilter data on basis of table 
            $('#po_box_number').on('change', function() {
                table.draw(false);
            });

            // Refilter the table by brand
            $('#brand').on('change', function() {
                table.draw(false);
            });

            // Refilter the table by order number
            $('#order').on('change', function() {
                table.draw(false);
            });
            $(document).on('change', '#customer', function() {
                $('.customer-error').html('');
                var id = $(this).val();
                if (id == "all") {
                    $.get('/getAllBrands', function(result) {
                        if (result.status == "success") {
                            var options = "<option value=''>All</option>";
                            result.data.forEach(element => {
                                options += "<option value='" + element.id + "'>" + element.brand +
                                    "</option>"
                            });

                            $('#brand-error').html('');
                            $('#brand').html(options);
                        } else {
                            $('#brand').html("<option value=''>All</option>");
                            $('#brand-error').html(result.message);
                        }
                    });
                } else {
                    $.get('/customer/' + id + '/brand', function(result) {
                        if (result.status == "success") {
                            var options = "<option value=''>All</option>";
                            result.data.forEach(element => {
                                options += "<option value='" + element.id + "'>" + element.brand +
                                    "</option>"
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
            $("body").on("change", "#order_status_id", function() {

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
                    success: function(data) {
                        $('.toast .me-auto').html('Success');
                        $('.toast .toast-header').addClass('bg-success');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html("Order status has been updated");
                        $('#toast-btn').click();
                    }
                });
            });
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
            $(document).on('change', '.cs_status', function(){
                var _this = $(this);
                var csStatus = _this.val();
                var orderId = _this.data('order-id');
                var url = '{{ route("update_cs_status", ":order_id") }}';
                url = url.replace(':order_id', orderId);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        cs_status: csStatus
                    },
                    success:function(response) {
                        if (response.success == true) {
                            $('#labelsModal').modal('hide');
                            // window.location.reload();
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                            // window.dataTable.draw();
                            window.myDataTable.draw();
                        } else if (response.error == true) {
                            $('#labelsModal').modal('hide');
                            $('.toast .me-auto').html('Error');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                            window.myDataTable.draw();
                        }
                    }
                });
            });
            $(document).on('click', '#addNoteOrderId', function() {
                $('#modalOrderId').val('');
                $('.submit_add_notes').attr('data-order-id', '');
                $('.submit_add_notes').attr('data-product-id', '');
                var getOrderId = $(this).attr('data-order-id');
                var getProductId = $(this).attr('data-product-id');
                $('#modalOrderId').val(getOrderId);
                $('.submit_add_notes').attr('data-order-id', getOrderId);
                $('.submit_add_notes').attr('data-product-id', getProductId);
            });
            $(document).on('click', '.submit_add_notes', function(){
                var orderId = $(this).attr('data-order-id');
                var productId = $(this).attr('data-product-id');
                var description = $('#description').val();
                var url = '{{ route("update_product_notes") }}'
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        order_id: orderId,
                        product_id: productId,
                        description: description
                    },
                    success:function(response)
                    {
                        table.draw(false);
                    }
                });
            });
        });
        /* Formatting function for row details - modify as you need */
        function format(d) {
            var html = '';
            html += `
                <div class="row" style="overflow-x: hidden">
                    <div class="col-md-4 dropdownTable" style="overflow-x: hidden">
                        <h5 class="mt-1">Returned Products Details</h5>
                        <table class="table table-bordered w-50 newDataTable" style="overflow-x: hidden">
                            <thead>
                                <th style="padding:5px">Status</th>
                                <th style="padding:5px">Product</th>
                                <th style="padding:5px">Qty</th>
                                <th style="padding:5px">Notes</th>
                                <th style="padding:5px">Action</th>
                            </thead>
                            <tbody style="overflow-x: hidden">`;
                                for (let index = 0; index < d.order_return_details_with_trashed.length; index++) {
                                    var product = d.order_return_details_with_trashed[index].product;
                                    html += `<tr style="border: 1px solid rgb(210, 210, 210);">
                                        <td>`;
                                            if (d.order_return_details_with_trashed[index].item_status == 1) {
                                                html += `Return to Sender`;
                                            } else if (d.order_return_details_with_trashed[index].item_status == 2) {
                                                html += `Damaged`;
                                            } else if (d.order_return_details_with_trashed[index].item_status == 3) {
                                                html += `Opened`;
                                            } else if (d.order_return_details_with_trashed[index].item_status == 4) {
                                                html += `Invalid Address`;
                                            } else {
                                                html += `No Status`;
                                            }
                                            html +=`
                                        </td>
                                        <td>
                                            `+product.name+`
                                        </td>
                                        <td>
                                            `+d.order_return_details_with_trashed[index].qty+`
                                        </td>
                                        <td>`+d.order_return_details_with_trashed[index].description+`</td>
                                        <td rowspan="1">
                                            <div class="dropup">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a href="#" class="dropdown-item" id="addNoteOrderId" data-order-id="`+d.order_return_details_with_trashed[index].order_return_id+`" data-product-id="`+product.id+`" data-bs-toggle="modal" data-bs-target="#addNotes"><i data-feather="plus"></i><span>Add Notes</span></a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>`;
                                }
                                html +=`
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            return html;
        }
        function calculateTotalReturnOrders() {
            setTimeout(() => {
                let customerID = $('#customer').val();
                if (customerID == "all") {
                    customerID = '';
                }
                $.ajax({
                    url: "{{ route('total_return_orders') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer: customerID,
                        min_date: $('#min').val(),
                        max_date: $('#max').val(),
                        status: $('#status').val()
                    },
                    success:function(resp) {
                        if (resp.status == true) {
                            $('.all-pages-total-products').html('(Total: '+resp.total_returned_products+')');
                            $('.all-pages-selling-cost-credit').html('(Total: $'+resp.total_selling_cost.toFixed(2)+')');
                            $('.all-pages-return-charges').html('(Total: $'+resp.total_return_charges.toFixed(2)+')');
                            $('.all-pages-total-credit').html('(Total: $'+resp.total_credit.toFixed(2)+')');
                        }
                    }
                });
            }, 500);
        }
        $(function() {
            var reportRange = localStorage.getItem('reportrange_order_return');
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
    </script>
@endsection

@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/tables/range_dates.js') }}"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/resize.js') }}"></script>
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