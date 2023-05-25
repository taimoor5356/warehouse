@extends('admin.layout.app')
@section('title', 'Product In/Out Report')
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

        thead,
        tbody,
        tfoot,
        tr,
        td,
        th {
            border-color: #ebe9f1;
            border-bottom: none;
        }
    .loader, .loader1 {
        border: 2px solid #f3f3f3;
        border-radius: 50%;
        border-top: 2px solid #4B4B4B;
        width: 15px;
        height: 15px;
        -webkit-animation: spin 1s linear infinite; /* Safari */
        animation: spin 1s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
    </style>

    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">Product In/Out Report</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Reports
                                    </li>
                                    <li class="breadcrumb-item">Product In/Out Report
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
                        <form method="POST" id="product-submit-form">
                            @csrf
                            <div class="row w-100">
                                <div class="form-group col-sm-12 col-md-2 mt-1">
                                    <label for="customer">Select Customer</label>
                                    <select name="customer" id="customer" class="form-control select2 toggle-vis customer">
                                        <option value="" selected>- Select Customer -</option>
                                        @foreach($customers as $customer)
                                            @isset($customer)
                                                <option value="{{ $customer->id }}" data-column="">{{ $customer->customer_name }}</option>
                                            @endisset
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-2 mt-1">
                                    <label for="brand">- Select Brand -</label>
                                    <select name="brand" id="brand" class="form-control select2 toggle-vis brand">
                                        <option value="" selected>- Select Brand -</option>
                                    </select>
                                    <span id="brand_required" class="text-danger d-none">Required</span>
                                </div>
                                <div class="form-group col-sm-12 col-md-2 mt-1">
                                    <label for="product">- Select Product -</label>
                                    <select name="product" id="product" class="form-control select2 toggle-vis product">
                                        <option value="" selected>- Select Product -</option>
                                        @foreach($products as $product)
                                            @isset($product)
                                                <option value="{{ $product->id }}" data-column="">{{ $product->name }}</option>
                                            @endisset
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-3 mt-1">
                                    <label for="product">Select Dates</label>
                                    <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
                                </div>
                                {{-- <div class="form-group col-sm-12 col-md-2 mt-1">
                                    <label for="time_duration">Select Duration</label>
                                    <select name="time_duration" id="time_duration" class="form-control select2">
                                        <option value="" selected>- Select Duration -</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="this_week">This Week</option>
                                        <option value="last_week">Last Week</option>
                                        <option value="this_month">This Month</option>
                                        <option value="last_month">Last Month</option>
                                        <option value="last_six_months">Last 6 Months</option>
                                        <option value="this_year">This Year</option>
                                        <option value="last_year">Last Year</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-2 mt-1">
                                    <label for="from_date">Start</label>
                                    <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile from_date" placeholder="MM/DD/YYYY" id="from_date" name="from_date">
                                </div>
                                <div class="form-group col-sm-12 col-md-2 mt-1">
                                    <label for="to_date">End</label>
                                    <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile to_date" placeholder="MM/DD/YYYY" id="to_date" name="to_date">
                                </div> --}}
                                {{-- <div class="form-group col-sm-12 col-md-2 mt-1">
                                    <label for="category">Select Category</label>
                                    <select name="category" id="category_id" class="form-control select2 toggle-vis">
                                        <option value="" selected>- Select Category -</option>
                                        
                                    </select>
                                </div> --}}
                                <div class="form-group col-sm-12 col-md-2 mt-1">
                                    <button class="btn btn-primary product-submit" type="button"
                                        style="margin-top: 20px"><div class="loader d-none" style="float: left"></div>&nbsp; Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="p-2">
                    <ul class="nav nav-pills">
                    </ul>
                    <hr>
                    <div class="tab-content">
                        <div class="tab-pane active" id="product" role="tabpanel" aria-labelledby="product-tab"
                            aria-expanded="false">
                            <div class="table-responsive">
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
            @elseif (session('error'))
                $('.toast .me-auto').html('Failed');
                $('.toast .toast-header').addClass('bg-danger');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{ session('error') }}");
                $('#toast-btn').click();
            @endif
            $(document).on('change', '#time_duration', function () {
                $('#from_date').val('');
                $('#from_date').html('');
                $('#to_date').val('');
                $('#to_date').html('');
            });
            $(document).on('change', '#from_date,#to_date', function() {
                $('#time_duration').val('');
                $('#time_duration').html(`
                    <option value="" selected>- Select Duration -</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="last_six_months">Last 6 Months</option>
                    <option value="this_year">This Year</option>
                    <option value="last_year">Last Year</option>
                `);
            });
            if (localStorage.getItem('from_date') != '') {
                $('#from_date').val(localStorage.getItem('from_date'));
            }
            if (localStorage.getItem('to_date') != '') {
                $('#to_date').val(localStorage.getItem('to_date'));
            }
            if (localStorage.getItem('time_duration') != '') {
                var time_duration = $('#time_duration');
                time_duration.find('option[value="' + localStorage.getItem('time_duration') + '"]').attr('selected',
                    'selected').change();
            }
            if (localStorage.getItem('product') != '') {
                var product = $('#product');
                product.find('option[value="' + localStorage.getItem('product') + '"]').attr('selected', 'selected')
                    .change();
            }
            if (localStorage.getItem('customer') != '') {
                var customer = $('#customer');
                customer.find('option[value="' + localStorage.getItem('customer') + '"]').attr('selected', 'selected')
                    .change();
            }
            $(document).on('change', '#from_date, #to_date, #time_duration, #product, #customer, #reportrange', function(e) {
                // Reset Values
                localStorage.setItem('from_date', '');
                localStorage.setItem('to_date', '');
                localStorage.setItem('reportrange_products_in_out_report', '');
                localStorage.setItem('time_duration', '');
                localStorage.setItem('product', '');
                localStorage.setItem('customer', '');
                localStorage.setItem('data_time', '');
                // Set Values
                localStorage.setItem('from_date', $('#from_date').val());
                localStorage.setItem('to_date', $('#to_date').val());
                localStorage.setItem('reportrange_products_in_out_report', $('#reportrange').val());
                localStorage.setItem('time_duration', $('#time_duration').val());
                localStorage.setItem('product', $('#product').val());
                localStorage.setItem('customer', $('#customer').val());
                var date = new Date().getTime();
                localStorage.setItem('data_time', date);
            });
            // $(".flatpickr-input").flatpickr({
            //     dateFormat: 'm/d/Y'
            // });
            $(document).on('change', '#customer', function () {
                let id = $(this).val();
                if(id=="") {
                    id = 'all';
                    $.get('/get-customer-products/'+id, function(result) {
                        if(result.status == true) {
                            let options = "<option value=''>- Select Product -</option>";
                            result.products.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.name+"</option>"
                            });
                            $('#product').html(options);
                        } else {
                            $('#product').html("<option value=''>- Select Product -</option>");
                        }
                    });
                    $('#brand').html("<option value=''>- Select Brand -</option>");
                } else {
                    $.get('/customer/'+id+'/brand', function(result) {
                        if(result.status == "success") {
                            var options = "<option value=''>- Select Brand -</option>";
                            result.data.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                            });
                            $('#brand-error').html('');
                            $('#brand').html(options);
                        } else {
                            $('#brand').html("<option value=''>- Select Brand -</option>");
                            $('#brand-error').html(result.message);
                        }
                    });
                    $.get('/get-customer-products/'+id, function(result) {
                        if(result.status == true) {
                            let options = "<option value=''>- Select Product -</option>";
                            result.products.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.name+"</option>"
                            });
                            $('#product').html(options);
                        } else {
                            $('#product').html("<option value=''>- Select Product -</option>");
                        }
                    });
                }
            });
            $(document).on('change', '#brand', function () {
                let id = $(this).val();
                let customerId = $('#customer').val();
                if(id=="") {
                    id = 'all';
                    $.get('/get-customer-products/'+customerId, function(result) {
                        if(result.status == true) {
                            let options = "<option value=''>- Select Product -</option>";
                            result.products.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.name+"</option>"
                            });
                            $('#product').html(options);
                        } else {
                            $('#product').html("<option value=''>- Select Product -</option>");
                        }
                    });
                } else {
                    $.get('/get-customer-brand-products/'+customerId+'/'+id, function(result) {
                        if(result.status == true) {
                            let options = "<option value=''>- Select Product -</option>";
                            result.products.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.name+"</option>"
                            });
                            $('#product').html(options);
                        } else {
                            $('#product').html("<option value=''>- Select Product -</option>");
                        }
                    });
                }
            });
            $(document).on('click', '.product-submit', function() {
                if ($('#product').val() == '') {
                    alert('One Product should must be selected');
                    return false;
                }
                $('.loader').removeClass('d-none');
                $('.product-submit').prop('disabled', true);
                let dates = $('#reportrange').val();
                dates = dates.split(' - ');
                let from_date = dates[0];
                let to_date = dates[1];
                $.ajax({
                    url: "{{ route('product_submit_report') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        time_duration: $('#time_duration').val(),
                        from_date: from_date,
                        to_date: to_date,
                        product_id: $('#product').val(),
                        customer: $('#customer').val()
                    },
                    success:function(response) {
                        var productCustomersLength = response.productCustomers;
                        $('.loader').addClass('d-none');
                        $('.product-submit').prop('disabled', false);
                        if (response.status == true) {
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').removeClass('bg-danger');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.msg);
                            $('#toast-btn').click();
                            $('.table-responsive').empty();
                            var html = '';
                            html += `
                            <table class="table product-table table-bordered data-table" style="border: 1px solid lightgray">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product Name</th>
                                        <th>Incoming</th>`;
                                        var productCustomers = response.productCustomers;
                                        productCustomers.forEach(e => {
                                            if (e != null) {
                                                html += `<th>`+e.customer.customer_name+`</th>`;
                                            }
                                        });
                                    html +=`<th>Total</th></tr>
                                </thead>
                                <tbody>`;
                                    var data = response.data;
                                    for (var i = 0; i < data.length; i++) {
                                        html += `<tr>
                                            <td>`+data[i]['date']+`</td>
                                            <td>`+data[i]['product_name']+`</td>
                                            <td>`+data[i]['incoming']+`</td>`;
                                            let sumOfOrders = 0;
                                            data[i]['customers'].forEach(ele => {
                                                html += `<td>`+ele.total_customer_order+`</td>`;
                                                sumOfOrders += Number(ele.total_customer_order);
                                            });
                                        html += `<td style="font-weight: bold">`+sumOfOrders+`</td></tr>`;
                                    }
                                html += `</tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Total</th>
                                        <th></th>`;
                                        var productCustomers = response.productCustomers;
                                        productCustomers.forEach(e => {
                                            if (e != null) {
                                                html += `<th></th>`;
                                            }
                                        });
                                    html +=`<th></th></tr>
                                </tfoot>
                            </table>
                            `;
                            $('.table-responsive').append(html);
                            $('.product-table').DataTable({
                                // scrollX: true,
                                searching: false,
                                paging:false,
                                order: [[0, 'desc']],
                                footerCallback: function(row, data, start, end, display) {
                                    var columnsLength = productCustomersLength.length;
                                    var api = this.api();
                                    // Remove the formatting to get integer data for summation
                                    var intVal = function(i) {
                                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                                            'number' ? i : 0;
                                    };
                                    for (index = 0; index <= (columnsLength+3); index++) {
                                        if (index > 1) {
                                            pageTotal1 = api
                                                .column(index, {
                                                    page: 'current'
                                                })
                                                .data()
                                                .reduce(function(a, b) {
                                                    return intVal(a) + intVal(b);
                                                }, 0);
                                            // Update footer
                                            $(api.column(index).footer()).html(pageTotal1);
                                        }
                                    }
                                },
                            });
                        } else {
                            $('.toast .me-auto').html('Failed');
                            $('.toast .toast-header').removeClass('bg-success');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.msg);
                            $('#toast-btn').click();
                        }
                    }
                });
            });
        });
        $(function() {
            var reportRange = localStorage.getItem('reportrange_products_in_out_report');
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
