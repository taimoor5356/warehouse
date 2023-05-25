@extends('admin.layout.app')
@section('title', 'Profit Report')
@section('datatablecss')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}"> --}}
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

        .dt-control {
            /* background: url('{{ asset('images/details_open.png') }}') no-repeat center center; */
            /* z-index: 50000 !important; */
            cursor: pointer;
        }
        .table-bordered > :not(caption) > * {
            border-width: 0px 0;
        }
    </style>

    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">Profit Report</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Reports
                                    </li>
                                    <li class="breadcrumb-item">Profit
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
                        <div class="form-group col-sm-12 col-md-4">
                            <label for="min">Select Dates</label>
                            <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
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
                        </div>
                        <div class="form-group col-sm-12 col-md-1 offset-md-7">
                            <label for="min" class="mb-1">Show All</label>
                            <div class="form-check form-check-primary form-switch">
                                <input type="checkbox" name="show_all" value="0" class="form-check-input"
                                    id="show_all">
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" id="customer-tab" data-bs-toggle="pill" href="#customer"
                                aria-expanded="false">Profit by Customer</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="sku-tab" data-bs-toggle="pill" href="#sku"
                                aria-expanded="false">Profit by SKU</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="category-tab" data-bs-toggle="pill" href="#category"
                                aria-expanded="true">Profit by Category</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="product-tab" data-bs-toggle="pill" href="#product"
                                aria-expanded="true">Profit by Products</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" id="show-all-profit-tab" data-bs-toggle="pill" href="#show-all-profit"
                                aria-expanded="true">Detailed Profit Report</a>
                        </li> --}}
                    </ul>
                    <hr>
                    <div class="tab-content">
                        <div class="tab-pane active table-responsive" id="customer" role="tabpanel" aria-labelledby="customer-tab"
                            aria-expanded="false">
                            <table class="table customer-table table-bordered" style="border: 1px solid rgb(235,235,235)">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer Name</th>
                                        <th>Total Batches</th>
                                        <th>Tracking Orders</th>
                                        <th>Product Sales</th>
                                        <th>Cost of Goods</th>
                                        <th>COG Profit</th>
                                        <th>Labels</th>
                                        <th>Pick/Pack</th>
                                        <th>Mailer</th>
                                        <th>Postage</th>
                                        <th>Discounted Postage Total</th>
                                        {{-- <th>Discounted Postage</th> --}}
                                        <th>Discounted Postage Profit</th>
                                        <th>Return</th>
                                        <th>Profit</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="font-size: 14px"></th>
                                        <th style="font-size: 14px"></th>
                                        <th style="font-size: 14px"><span class="current_batches"></span></th>
                                        <th style="font-size: 14px"><span class="current_mailers"></span></th>
                                        <th style="font-size: 14px"><span class="current_sales"></span></th>
                                        <th style="font-size: 14px"><span class="current_purchases"></span></th>
                                        <th style="font-size: 14px"><span class="cog_profit"></span></th>
                                        <th style="font-size: 14px"></th><!-- labels -->
                                        <th style="font-size: 14px"><span class="current_pick_pack"></span></th>
                                        <th style="font-size: 14px"></th><!-- mailer -->
                                        <th style="font-size: 14px"></th><!-- postage -->
                                        <th style="font-size: 14px"></th><!-- postage -->
                                        {{-- <th style="font-size: 14px"></th><!-- discounted postage --> --}}
                                        <th style="font-size: 14px"></th><!-- discounted postage -->
                                        <th style="font-size: 14px"><span class="current_return"></span></th>
                                        <th style="font-size: 14px"><span class="current_profit"></span><span class="total-current-profit">(${{ $dataValues['total_profit'] }})</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="sku" aria-labelledby="sku-tab" aria-expanded="true">
                            <table class="table sku-table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SKU Name</th>
                                        <th>Brand</th>
                                        <th>Customer</th>
                                        <th>Total Batches</th>
                                        <th>Sales</th>
                                        <th>Cost of Goods</th>
                                        <th>Pick/Pack</th>
                                        <th>Return Charges</th>
                                        <th>Profit</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="product" role="tabpanel" aria-labelledby="product-tab"
                            aria-expanded="false">
                            <table class="table product-table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Total Batches</th>
                                        <th>Profit</th>
                                        {{-- <th>Labels Profit</th> --}}
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="category" role="tabpanel" aria-labelledby="category-tab"
                            aria-expanded="false">
                            <table class="table category-table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Total Batches</th>
                                        <th>Profit</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="show-all-profit" role="tabpanel" aria-labelledby="show-all-profit-tab"
                            aria-expanded="false">
                            <table class="table show-all-profit-table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Unit Price</th>
                                        <th>Sold Qty</th>
                                        <th>Purchasing Price</th>
                                        {{-- <th>Qty</th> --}}
                                        <th>Selling Price</th>
                                        <th>Profit/Loss</th>
                                        {{-- <th>Labels Profit</th> --}}
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
@endsection

@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{-- <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script> --}}
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
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
            // by default load profit by customer
            window.customer_table = $('.customer-table').DataTable({
                order: [
                    [12, "desc"]
                ],
                processing: true,
                serverSide: true,
                searching: true,
                info: true,
                lengthChange: true,
                // fixedHeader: true,
                stateSave: true,
                scrollX: true,
                ajax: {
                    url: "/reports/profit/customer",
                    data: function(d) {
                        let dates = $('#reportrange').val();
                        dates = dates.split(' - ');
                        let from_date = dates[0];
                        let to_date = dates[1];
                        d.min_date = from_date;
                        d.max_date = to_date;
                        d.show_all = ($('#show_all').is(":checked")) ? true : false;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                    },
                    {
                        data: 'total_orders',
                        name: 'total_orders'
                    },
                    {
                        data: 'total_mailers',
                        name: 'total_mailers'
                    },
                    {
                        data: 'selling_amount',
                        name: 'selling_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')
                    },
                    {
                        data: 'purchasing_amount',
                        name: 'purchasing_amount',
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')
                    },
                    {
                        data: 'cog_profit',
                        name: 'cog_profit',
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')
                    },
                    // labels
                    {
                        data: 'labels',// labels
                        name: 'labels',// labels
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')// labels
                    },
                    {
                        data: 'pick_pack',
                        name: 'pick_pack',
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')
                    },
                    // mailer
                    {
                        data: 'mailer_charges',// mailer
                        name: 'mailer_charges',// mailer
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')// mailer
                    },
                    // postage
                    {
                        data: 'postage_charges',// postage
                        name: 'postage_charges',// postage
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')// postage
                    },
                    // discounted_postage_total_cost
                    {
                        data: 'discounted_postage_total_cost',// discounted_postage_total_cost
                        name: 'discounted_postage_total_cost',// discounted_postage_total_cost
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')// discounted_postage_total_cost
                    },
                    {
                        data: 'discounted_postage_profit',// postage
                        name: 'discounted_postage_profit',// postage
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')// postage
                    },
                    {
                        data: 'return_service_charges',
                        name: 'return_service_charges',
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')
                    },
                    {
                        data: 'profit',
                        className: 'sum',
                        name: 'profit',
                        render: $.fn.dataTable.render.number(',', '.', 2, '$')
                    },
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
                        .column(2)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(2, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(2).footer()).html(pageTotal.toLocaleString());
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
                    $(api.column(3).footer()).html(pageTotal.toLocaleString());
                    // Total over all pages
                    total = api
                        .column(4)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(4, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(4).footer()).html('$' + pageTotal.toLocaleString());
                    // Total over all pages
                    total = api
                        .column(5)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(5, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(5).footer()).html('$' + pageTotal.toLocaleString());
                    // Total over all pages
                    total = api
                        .column(6)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(6, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(6).footer()).html('$' + pageTotal.toLocaleString());
                    // Total over all pages
                    total = api
                        .column(7)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(7, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(7).footer()).html('$' + pageTotal.toLocaleString());
                    // Total over all pages
                    total = api
                        .column(8)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(8, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(8).footer()).html('$' + pageTotal.toLocaleString());
                    // Total over all pages
                    total = api
                        .column(9)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(9, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(9).footer()).html('$' + pageTotal.toLocaleString());
                    // Total over all pages
                    total = api
                        .column(10)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(10, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(10).footer()).html('$' + pageTotal.toLocaleString());
                    // Total over all pages
                    total = api
                        .column(11)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(11, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(11).footer()).html('$' + pageTotal.toLocaleString());
                    // Total over all pages
                    total = api
                        .column(12)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Total over this page
                    pageTotal = api
                        .column(12, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(12).footer()).html('$' + pageTotal.toLocaleString());
                    pageTotal = api
                        .column(13, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(13).footer()).html('$' + pageTotal.toLocaleString());
                    pageTotal = api
                        .column(14, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer
                    $(api.column(14).footer()).find('.current_profit').html('$' + pageTotal.toLocaleString());
                },
                "drawCallback": function(settings) {
                    feather.replace();
                },
                'order': [
                    [13, 'desc']
                ],
            });
            $(document).on('click', '.nav-link', function() {
                var id = $(this).attr('id');
                if (id == 'customer-tab') {
                    loadDataTable('customer-table');
                } else if (id == 'sku-tab') {
                    alert('Under Construction');
                    return false;
                    loadDataTable('sku-table');
                } else if (id == 'product-tab') {
                    alert('Under Construction');
                    return false;
                    loadDataTable('product-table');
                } else if (id == 'category-tab') {
                    alert('Under Construction');
                    return false;
                    loadDataTable('category-table');
                } else if (id == 'show-all-profit-tab') {
                    alert('Under Construction');
                    return false;
                    loadDataTable('show-all-profit-table');
                }
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
            $('#min, #max, #show_all, #reportrange').on('change', function() {
                var date = new Date().getTime();
                localStorage.setItem('data_time', date);
                localStorage.setItem('reportrange-profit', '');
                localStorage.setItem('reportrange-profit', $('#reportrange').val());
                var active_table_id = $('.nav-link.active').attr('id');
                if (active_table_id == 'customer-tab') {
                    let dates = $('#reportrange').val();
                    dates = dates.split(' - ');
                    let from_date = dates[0];
                    let to_date = dates[1];
                    getFilteredProfit(from_date, to_date);
                    window.customer_table.draw();
                } else if (active_table_id == 'sku-tab') {
                    window.sku_table.draw();
                } else if (active_table_id == 'product-tab') {
                    window.product_table.draw();
                } else if (active_table_id == 'category-tab') {
                    window.category_table.draw();
                }
            });
            // Add event listener for opening and closing details
            $(document).on('click', '.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = window.show_all_profit_table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).attr('src', 'https://datatables.net/examples/resources/details_open.png');

                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                    $(this).attr('src', 'https://datatables.net/examples/resources/details_close.png');

                }
                window.formatDt = $('.formatDt').DataTable({
                    stateSave: true,
                    searching: false,
                    bDestroy: true
                });
            });
            $(document).on('click', '.dt-control2', function() {
                var tr = $(this).closest('tr');
                var row2 = window.formatDt.row(tr);
                var customerId = $(this).data('customer-id');
                var productId = $(this).data('product-id');
                if (row2.child.isShown()) {
                    // This row is already open - close it
                    row2.child.hide();
                    tr.removeClass('shown');
                    $(this).attr('src', 'https://datatables.net/examples/resources/details_open.png');
                } else {
                    // Open this row
                    $.ajax({
                        url: '{{ route('get_customer_brand_profit_details') }}',
                        type: 'GET',
                        data: {
                            product_id: productId,
                            customer_id: customerId
                        },
                        success: function(response) {
                            window.format2Data = response.data;
                            row2.child(format2(window.format2Data)).show();
                            tr.addClass('shown');
                        }
                    });
                    $(this).attr('src', 'https://datatables.net/examples/resources/details_close.png');
                }
            });
        });
        $(function() {
            var reportRange = localStorage.getItem('reportrange-profit');
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
        function loadDataTable(selector) {
            var status = $.fn.dataTable.isDataTable("." + selector);
            if (status) {
                if (selector == 'customer-table') {
                    window.customer_table.ajax.reload();
                } else if (selector == 'sku-table') {
                    window.sku_table.ajax.reload();
                } else if (selector == 'product-table') {
                    window.product_table.ajax.reload();
                } else if (selector == 'category-table') {
                    window.category_table.ajax.reload();
                } else if (selector == 'show-all-profit-table') {
                    window.show_all_profit_table.ajax.reload();
                }
            } else {
                if (selector == 'customer-table') {
                    var table = $('.' + selector).DataTable({
                        order: [
                            [2, "desc"]
                        ],
                        processing: true,
                        serverSide: true,
                        searching: true,
                        info: true,
                        lengthChange: true,
                        processing: true,
                        fixedHeader: true,
                        stateSave: true,
                        ajax: {
                            url: "/reports/profit/" + selector.substring(0, selector.indexOf('-')),
                            data: function(d) {
                                let dates = $('#reportrange').val();
                                dates = dates.split(' - ');
                                let from_date = dates[0];
                                let to_date = dates[1];
                                d.min_date = from_date;
                                d.max_date = to_date;
                                d.show_all = ($('#show_all').is(":checked")) ? true : false;
                            }
                        },
                        columns: [{
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'total_orders',
                                name: 'total_orders'
                            },
                            {
                                data: 'profit',
                                name: 'profit',
                                render: $.fn.dataTable.render.number(',', '.', 2, '$')
                            },
                        ],
                        "drawCallback": function(settings) {
                            feather.replace();
                        },
                    });
                    window.customer_table = table;
                } else if (selector == 'sku-table') {
                    var table = $('.' + selector).DataTable({
                        order: [
                            [4, "desc"]
                        ],
                        processing: true,
                        serverSide: true,
                        searching: true,
                        info: true,
                        lengthChange: true,
                        processing: true,
                        fixedHeader: true,
                        stateSave: true,
                        ajax: {
                            url: "/reports/profit/" + selector.substring(0, selector.indexOf('-')),
                            data: function(d) {
                                let dates = $('#reportrange').val();
                                dates = dates.split(' - ');
                                let from_date = dates[0];
                                let to_date = dates[1];
                                d.min_date = from_date;
                                d.max_date = to_date;
                                d.show_all = ($('#show_all').is(":checked")) ? true : false;
                            }
                        },
                        columns: [{
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'brand',
                                name: 'brand'
                            },
                            {
                                data: 'customer',
                                name: 'customer'
                            },
                            {
                                data: 'total_orders',
                                name: 'total_orders'
                            },
                            {
                                data: 'sales',
                                name: 'sales'
                            },
                            {
                                data: 'purchases',
                                name: 'purchases'
                            },
                            {
                                data: 'pick_pack',
                                name: 'pick_pack'
                            },
                            {
                                data: 'return_charges',
                                name: 'return_charges'
                            },
                            {
                                data: 'profit',
                                name: 'profit',
                                render: $.fn.dataTable.render.number(',', '.', 2, '$')
                            },
                        ],
                        "drawCallback": function(settings) {
                            feather.replace();
                        },
                    });
                    window.sku_table = table;
                } else if (selector == 'product-table') {
                    var table = $('.' + selector).DataTable({
                        order: [
                            [3, "desc"]
                        ],
                        processing: true,
                        serverSide: true,
                        searching: true,
                        info: true,
                        lengthChange: true,
                        processing: true,
                        fixedHeader: true,
                        stateSave: true,
                        ajax: {
                            url: "/reports/profit/" + selector.substring(0, selector.indexOf('-')),
                            data: function(d) {
                                let dates = $('#reportrange').val();
                                dates = dates.split(' - ');
                                let from_date = dates[0];
                                let to_date = dates[1];
                                d.min_date = from_date;
                                d.max_date = to_date;
                                d.show_all = ($('#show_all').is(":checked")) ? true : false;
                            }
                        },
                        columns: [{
                                data: 'category',
                                name: 'category'
                            },
                            {
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'total_orders',
                                name: 'total_orders'
                            },
                            {
                                data: 'profit',
                                name: 'profit'
                            },
                            // {data: 'labels_profit', name: 'labels_profit'},
                        ],
                        "drawCallback": function(settings) {
                            feather.replace();
                        },
                    });
                    window.product_table = table;
                } else if (selector == 'category-table') {
                    var table = $('.' + selector).DataTable({
                        order: [
                            [2, "desc"]
                        ],
                        processing: true,
                        serverSide: true,
                        searching: true,
                        info: true,
                        lengthChange: true,
                        processing: true,
                        fixedHeader: true,
                        stateSave: true,
                        ajax: {
                            url: "/reports/profit/" + selector.substring(0, selector.indexOf('-')),
                            data: function(d) {
                                let dates = $('#reportrange').val();
                                dates = dates.split(' - ');
                                let from_date = dates[0];
                                let to_date = dates[1];
                                d.min_date = from_date;
                                d.max_date = to_date;
                                d.show_all = ($('#show_all').is(":checked")) ? true : false;
                            }
                        },
                        columns: [{
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'total_orders',
                                name: 'total_orders'
                            },
                            {
                                data: 'profit',
                                name: 'profit'
                            },
                        ],
                        "drawCallback": function(settings) {
                            feather.replace();
                        },
                    });
                    window.category_table = table;
                } else if (selector == 'show-all-profit-table') {
                    var table = $('.' + selector).DataTable({
                        order: [
                            [2, "asc"]
                        ],
                        processing: true,
                        serverSide: true,
                        searching: true,
                        info: true,
                        lengthChange: true,
                        processing: true,
                        fixedHeader: true,
                        stateSave: true,
                        ajax: {
                            url: '{{ route('show_all_profit') }}'
                        },
                        // success:function(response) {
                        //     var html = '';
                        //     if (response.success == true) {
                        //         for (let index = 0; index < response.data.length; index++) {
                        //             html += `
                    //                 <tr>
                    //                     <td>`+response.data[index].category_name+`</td>
                    //                     <td>`+response.data[index].category_name+`</td>
                    //                     <td>`+response.data[index].category_name+`</td>
                    //                     <td>`+response.data[index].category_name+`</td>
                    //                     <td>`+response.data[index].category_name+`</td>
                    //                     <td>`+response.data[index].category_name+`</td>
                    //                 </tr>
                    //             `;
                        //         }
                        //     }
                        // }
                        columns: [

                            {
                                data: 'btn',
                                name: 'btn'
                            },
                            {
                                data: 'category_name',
                                name: 'category_name'
                            },
                            {
                                data: 'product_name',
                                name: 'product_name'
                            },
                            {
                                data: 'unit_price',
                                name: 'unit_price'
                            },
                            {
                                data: 'totalOrders',
                                name: 'totalOrders'
                            },
                            {
                                data: 'total_products_purchasing_cost',
                                name: 'total_products_purchasing_cost'
                            },
                            {
                                data: 'total_products_selling_cost',
                                name: 'total_products_selling_cost'
                            },
                            {
                                data: 'profit',
                                name: 'profit'
                            }
                        ],
                        "drawCallback": function(settings) {
                            feather.replace();
                        },
                        'order': [
                            [6, 'desc']
                        ],
                    });
                    window.show_all_profit_table = table;
                }
            }
        }
        /* Formatting function for row details - modify as you need */
        function format(data) {
            // `data` is the original data object for the row
            var customers = data.customers;
            var html = `
            Product's Customers
            <table class="table table-bordered formatDt">
                <thead>
                    <th style="width: 8%">
                        #
                    </th>
                    <th>
                        Customer Name
                    </th>
                    <th>
                        Sold Qty
                    </th>
                    <th>
                        Selling Price
                    </th>
                    <th>
                        Profit / Loss
                    </th>
                </thead>
                <tbody>`;
            for (let index = 0; index < customers.length; index++) {
                html +=
                    `
                            <tr>
                                <td class="text-center">
                                            <center>
                                                <img class="dt-control2" src="{{ asset('images/details_open.png') }}" alt="" data-customer-id="` +
                    customers[index].customer_id + `" data-product-id="` + customers[index].product_id + `">
                                            </center></td>
                                <td class="">` + customers[index].customer_name + `</td>
                                <td class="">` + customers[index].total_counts + `</td>
                                <td class="">` + customers[index].selling_price + `</td>`;
                if (customers[index].profit > 0) {
                    html +=
                        `<td class="badge rounded-pill m-1" style="background-color: green; color: white; padding: 5px">$` +
                        customers[index].profit + `</td>`;
                } else {
                    html +=
                        `<td class="badge rounded-pill m-1" style="background-color: red; color: white; padding: 5px">$` +
                        customers[index].profit + `</td>`;
                }
                html += `</tr>`;
            }
            html += `
                </tbody>
            </table>`;
            return html;
        }
        /* Formatting function for row details - modify as you need */
        function format2(data2) {
            var brands = data2;
            console.log(brands);
            var html = '';
            html += `
            Customer's Brands
            <table class="table table-bordered formatDt">
                <thead>
                    <th>
                        Brand Name
                    </th>
                    <th>
                        Sold Qty
                    </th>
                    <th>
                        Brand Selling Price
                    </th>
                    <th>
                        Profit / Loss
                    </th>
                </thead>
                <tbody>`;
            for (let index = 0; index < brands.length; index++) {
                html += `
                        <tr>
                            <td>
                                ` + brands[index].brand_name + `
                            </td>
                            <td>
                                ` + brands[index].brand_sales_count + `
                            </td>
                            <td>
                                ` + brands[index].brand_sales_price + `
                            </td>`;
                if (brands[index].brand_profit > 0) {
                    html +=
                        `<td class="badge rounded-pill m-1" style="background-color: green; color: white; padding: 5px">$` +
                        brands[index].brand_profit + `</td>`;
                } else {
                    html +=
                        `<td class="badge rounded-pill m-1" style="background-color: red; color: white; padding: 5px">$` +
                        brands[index].brand_profit + `</td>`;
                }
                html += `</tr>
                        `;
            }
            html += `
                </tbody>
            </table>`;
            return html;
        }
        function getFilteredProfit(from, to) {
            $.ajax({
                url: "{{ route('get_filtered_profit') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    from: from,
                    to: to
                },
                success:function(response) {
                    if (response.status == true) {
                        $('.total-current-profit').html("($"+response.total_profit.toLocaleString()+")");
                    }
                }
            });
        }
    </script>
@endsection
