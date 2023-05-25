@extends('admin.layout.app')
@section('title', 'Sales Report')
@section('datatablecss')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

@stop
@section('content')
<style type="text/css">
    .dataTables_length{float: left;padding-left: 20px;}
    .dataTables_filter{padding-right:20px;}
    .dataTables_info{padding-left: 20px !important; padding-bottom:30px !important;}
    .dataTables_paginate{padding-right: 20px !important;}
</style>

<!-- BEGIN: Content-->
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="row">
                            <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">Sales Report</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Reports
                                    </li>
                                    <li class="breadcrumb-item">Sales
                                    </li>
                                </ol>
                            </div>
                            </div>
                            <div class="col-3 addBtnClass">
                                {{--  <a href="/units/create" style="margin-left:auto;"  class="btn btn-primary waves-effect waves-float waves-light">Add New Unit</a>  --}}
                            
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
                                        <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile" placeholder="MM/DD/YYYY" id="min" name="min">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-2">
                                        <label for="min">End</label>
                                        <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile" placeholder="MM/DD/YYYY" id="max" name="max">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-1 offset-md-7">
                                        <label for="min" class="mb-1">Show All</label>
                                        <div class="form-check form-check-primary form-switch">
                                            <input type="checkbox" name="show_all" value="0" class="form-check-input" id="show_all">
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="customer-tab" data-bs-toggle="pill" href="#customer" aria-expanded="false">Sales by Customer</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="sku-tab" data-bs-toggle="pill" href="#sku" aria-expanded="false">Sales by SKU</a>
                                    </li>
                                     <li class="nav-item">
                                        <a class="nav-link" id="category-tab" data-bs-toggle="pill" href="#category" aria-expanded="true">Sales by Category</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="product-tab" data-bs-toggle="pill" href="#product" aria-expanded="true">Sales by Products</a>
                                    </li> 
                                    {{-- <li class="nav-item">
                                        <a class="nav-link" id="daily-sale-tab" data-bs-toggle="pill" href="#dailysaletable" aria-expanded="true">Daily Sale Report</a>
                                    </li>  --}}
                                </ul>
                                <hr>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="customer" role="tabpanel" aria-labelledby="customer-tab" aria-expanded="false">
                                        <table class="table customer-table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Customer Name</th>
                                                    <th>Total Batches</th>
                                                    <th>Total Sales</th>
                                                </tr>
                                            </thead>
                                            <tbody>
        
                                            </tbody>
                                            <tfoot>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
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
                                                    <th>Total Sales</th>
                                                </tr>
                                            </thead>
                                            <tbody>
        
                                            </tbody>
                                            <tfoot>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="product" role="tabpanel" aria-labelledby="product-tab" aria-expanded="false">
                                        <table class="table product-table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Category</th>
                                                    <th>Total Sales</th>
                                                </tr>
                                            </thead>
                                            <tbody>
        
                                            </tbody>
                                            <tfoot>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="dailysaletable" role="tabpanel" aria-labelledby="daily-sale-tab" aria-expanded="false">
                                        <table class="table dailysaletable table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Product</th>
                                                    <th>Category</th>
                                                    <th>Total Sale</th>
                                                </tr>
                                            </thead>
                                            <tbody>
        
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="category" role="tabpanel" aria-labelledby="category-tab" aria-expanded="false">
                                        <table class="table category-table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Total Sales</th>
                                                </tr>
                                            </thead>
                                            <tbody>
        
                                            </tbody>
                                            <tfoot>
                                                <th></th>
                                                <th></th>
                                            </tfoot>
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

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop

@section('page_js')
    
<script type="text/javascript">
    var minDate, maxDate;

    $(document).ready(function(){
        @if (session('success'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-success');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('success')}}");
            $('#toast-btn').click();
        @endif

        // by default load profit by customer

        window.customer_table = $('.customer-table').DataTable({
            order: [[ 3, "desc" ]],
            processing: true,
            serverSide: true,
            searching: true,
            info: true,
            lengthChange: true,
            processing: true,
            fixedHeader: true,
            stateSave: true,
            ajax: {
                url: "/reports/sales/customer",
                data: function (d) {
                    let dates = $('#reportrange').val();
                    dates = dates.split(' - ');
                    let from_date = dates[0];
                    let to_date = dates[1];
                    d.min_date = from_date;
                    d.max_date = to_date;
                    d.show_all = ($('#show_all').is(":checked")) ? true : false;
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name', orderable: false},
                {data: 'total_orders', name: 'total_orders', orderable: true},
                {data: 'selling_amount', name: 'selling_amount', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ), orderable: true}
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
                $(api.column(2).footer()).html(pageTotal);
                
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
                $(api.column(3).footer()).html('$'+pageTotal.toFixed(2));
            },
            "drawCallback": function( settings ) {
                feather.replace();
            },
        });

        $(document).on('click', '.nav-link', function(){
            var id = $(this).attr('id');
            if (id == 'customer-tab') {
                loadDataTable('customer-table');
            } else if (id == 'sku-tab') {
                loadDataTable('sku-table');
            } else if (id == 'product-tab') {
                loadDataTable('product-table');
            } else if (id == 'daily-sale-tab') {
                // alert('Under Construction');
                // return false;
                loadDataTable('dailysaletable');
            } else if (id == 'category-tab') {
                // alert('Under Construction');
                // return false;
                loadDataTable('category-table');
            }
        });

        // Refilter the table
        $('#min, #max, #show_all, #reportrange').on('change', function () {
            var date = new Date().getTime();
            localStorage.setItem('data_time', date);
            localStorage.setItem('reportrange-sales', '');
            localStorage.setItem('reportrange-sales', $('#reportrange').val());
            var active_table_id = $('.nav-link.active').attr('id');
            if (active_table_id == 'customer-tab') {
                window.customer_table.draw();
            } else if (active_table_id == 'sku-tab') {
                window.sku_table.draw();
            } else if (active_table_id == 'product-tab') {
                window.product_table.draw();
            } else if (active_table_id == 'daily-sale-tab') {
                window.dailysaletable.draw();
            } else if (active_table_id == 'category-tab') {
                window.category_table.draw();
            }
        });
    });
    $(function() {
        var reportRange = localStorage.getItem('reportrange-sales');
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
    function loadDataTable(selector)
    {
        var status = $.fn.dataTable.isDataTable("."+selector);
        if(status) {
            if (selector == 'customer-table') {
                window.customer_table.ajax.reload();
            } else if (selector == 'sku-table') {
                window.sku_table.ajax.reload();
            } else if (selector == 'product-table') {
                window.product_table.ajax.reload();
            } else if (selector == 'dailysaletable') {
                window.dailysaletable.ajax.reload();
            } else if (selector == 'category-table') {
                window.category_table.ajax.reload();
            }
        } else {
            if (selector == 'customer-table') {
                var table = $('.'+selector).DataTable({
                    order: [[ 1, "desc" ]],
                    processing: true,
                    serverSide: true,
                    searching: true,
                    info: true,
                    lengthChange: true,
                    processing: true,
                    fixedHeader: true,
                    stateSave: true,
                    ajax: { 
                        url: "/reports/sales/"+selector.substring(0, selector.indexOf('-')),
                        data: function (d) {
                            let dates = $('#reportrange').val();
                            dates = dates.split(' - ');
                            let from_date = dates[0];
                            let to_date = dates[1];
                            d.min_date = from_date;
                            d.max_date = to_date;
                            d.show_all = ($('#show_all').is(":checked")) ? true : false;
                        }
                    },
                    columns: [
                        {data: 'name', name: 'name'},
                        {data: 'sales', name: 'sales'}
                    ],
                    "drawCallback": function( settings ) {
                        feather.replace();
                    },
                });
                window.customer_table = table;
            } else if (selector == 'sku-table') {
                var table = $('.'+selector).DataTable({
                    order: [[ 3, "desc" ]],
                    processing: true,
                    serverSide: true,
                    searching: true,
                    info: true,
                    lengthChange: true,
                    processing: true,
                    fixedHeader: true,
                    stateSave: true,
                    ajax: {
                        url: "/reports/sales/"+selector.substring(0, selector.indexOf('-')),
                        data: function (d) {
                            let dates = $('#reportrange').val();
                            dates = dates.split(' - ');
                            let from_date = dates[0];
                            let to_date = dates[1];
                            d.min_date = from_date;
                            d.max_date = to_date;
                            d.show_all = ($('#show_all').is(":checked")) ? true : false;
                        }
                    },
                    columns: [
                        {data: 'name', name: 'name'},
                        {data: 'brand', name: 'brand'},
                        {data: 'customer', name: 'customer'},
                        {data: 'sales', name: 'sales', render: $.fn.dataTable.render.number(',', '.', 2, '$')}
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
                        $(api.column(3).footer()).html('$'+pageTotal.toFixed(2));
                    },
                    "drawCallback": function( settings ) {
                        feather.replace();
                    },
                });
                window.sku_table = table;
            } else if (selector == 'product-table') {
                var table = $('.'+selector).DataTable({
                    order: [[ 2, "desc" ]],
                    processing: true,
                    serverSide: true,
                    searching: true,
                    info: true,
                    lengthChange: true,
                    processing: true,
                    fixedHeader: true,
                    stateSave: true,
                    ajax: {
                        url: "/reports/sales/"+selector.substring(0, selector.indexOf('-')),
                        data: function (d) {
                            let dates = $('#reportrange').val();
                            dates = dates.split(' - ');
                            let from_date = dates[0];
                            let to_date = dates[1];
                            d.min_date = from_date;
                            d.max_date = to_date;
                            d.show_all = ($('#show_all').is(":checked")) ? true : false;
                        }
                    },
                    columns: [
                        {data: 'product_name', name: 'product_name'},
                        {data: 'category_name', name: 'category_name'},
                        {data: 'sales', name: 'sales', render: $.fn.dataTable.render.number(',', '.', 2, '$')}
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
                        $(api.column(2).footer()).html('$'+pageTotal.toFixed(2));
                    },
                    "drawCallback": function( settings ) {
                        feather.replace();
                    },
                });
                window.product_table = table;
            } else if (selector == 'dailysaletable') {
                var table = $('.'+selector).DataTable({
                    order: [[ 2, "desc" ]],
                    processing: true,
                    serverSide: true,
                    searching: true,
                    info: true,
                    lengthChange: true,
                    processing: true,
                    fixedHeader: true,
                    paging: false,
                    stateSave: true,
                    ajax: {
                        url: "/reports/sales/"+selector,
                        data: function (d) {
                            let dates = $('#reportrange').val();
                            dates = dates.split(' - ');
                            let from_date = dates[0];
                            let to_date = dates[1];
                            d.min_date = from_date;
                            d.max_date = to_date;
                            d.show_all = ($('#show_all').is(":checked")) ? true : false;
                        }
                    },
                    columns: [
                        {data: 'date', name: 'date'},
                        {data: 'name', name: 'name'},
                        {data: 'category', name: 'category'},
                        {data: 'daily_sale', name: 'daily_sale'},
                    ],
                    "drawCallback": function( settings ) {
                        feather.replace();
                    },
                });
                window.dailysaletable = table;
            }  else if (selector == 'category-table') {
                var table = $('.'+selector).DataTable({
                    order: [[ 1, "desc" ]],
                    processing: true,
                    serverSide: true,
                    searching: true,
                    info: true,
                    lengthChange: true,
                    processing: true,
                    fixedHeader: true,
                    stateSave: true,
                    ajax: {
                        url: "/reports/sales/"+selector.substring(0, selector.indexOf('-')),
                        data: function (d) {
                            let dates = $('#reportrange').val();
                            dates = dates.split(' - ');
                            let from_date = dates[0];
                            let to_date = dates[1];
                            d.min_date = from_date;
                            d.max_date = to_date;
                            d.show_all = ($('#show_all').is(":checked")) ? true : false;
                        }
                    },
                    columns: [
                        {data: 'category_name', name: 'category_name'},
                        {data: 'sales', name: 'sales', render: $.fn.dataTable.render.number(',', '.', 2, '$')}
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
                            .column(1)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        // Total over this page
                        pageTotal = api
                            .column(1, {
                                page: 'current'
                            })
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        // Update footer
                        $(api.column(1).footer()).html('$'+pageTotal.toFixed(2));
                    },
                    "drawCallback": function( settings ) {
                        feather.replace();
                    },
                });
                window.category_table = table;
            }
        }
    }
      
</script>
@endsection
