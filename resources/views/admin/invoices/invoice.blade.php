@extends('admin.layout.app')
@section('title', 'New Invoices')
@section('datatablecss')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

@stop


@section('datepickercss')
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
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

        .rowColor {
            background-color: rgb(245, 245, 255) !important;
        }
        .rowClass {
            background-color: rgb(245, 245, 255) !important;
        }

        input[type="checkbox"]:checked:after {
            background-color: rgb(149, 46, 46);
        }

        input[type="checkbox"]:disabled:after {
            background-color: rgb(186, 90, 90);
        }

        .item:visited {
            color: #a49fe6;
        }
        .loader {
            border: 2px solid #f3f3f3;
            border-radius: 50%;
            border-top: 2px solid transparent;
            width: 15px;
            height: 15px;
            -webkit-animation: spin 1s linear infinite; /* Safari */
            animation: spin 1s linear infinite;
        }
        .merging-loader {
            border: 2px solid #312e85;
            border-radius: 50%;
            border-top: 2px solid transparent;
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
                        <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">New Invoices</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Invoices
                                    </li>
                                    <li class="breadcrumb-item">New Invoices
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-6 addBtnClass">
                            <button type="button" disabled style="margin-left:auto;"
                                class="btn btn-warning waves-effect waves-float waves-light merge_btn">
                                Merge Invoices &nbsp; <div class="loader d-none" style="float: right"></div>
                            </button>
                            <a href="{{ route('view_merged_invoices') }}" target="_blank" style="margin-left:auto;"
                                class="btn btn-primary waves-effect waves-float waves-light">View Merged
                                Invoices</a>
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
                    {{-- <div class="col-sm-1 offset-sm-5">
                        <label class="col-form-label" for="catselect">Select Status</label>
                    </div> --}}
                    <div class="row w-100">
                        {{-- <div class="col-sm-3">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="from_date">From:</label>
                                </div>
                                <div class="col-sm-12">
                                    <input type="text" id="from_date"
                                        class="form-control flatpickr-basic flatpickr-input from_date" name="from_date"
                                        value="{{ old('from_date') }}" placeholder="MM/DD/YYYY" />
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
                                    <input type="text" id="to_date"
                                        class="form-control flatpickr-basic flatpickr-input to_date" name="to_date"
                                        value="{{ old('to_date') }}" placeholder="MM/DD/YYYY" />
                                    @error('to_date')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-sm-3">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="customer">Customer:</label>
                                </div>
                                <div class="col-sm-12">
                                    <select name="customer" id="customer" class="select2 form-select arrow">
                                        <option value="">All</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">
                                                {{ ucwords($customer->customer_name) }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer')
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
                                    <select name="brand" id="brand" class="select2 form-select arrow">
                                        <option value="">All</option>
                                        {{-- @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">
                                                {{ ucwords($brand->brand) }}</option>
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
                                    <label class="col-form-label" for="from_date">Select Dates:</label>
                                </div>
                                <div class="col-sm-12">
                                    <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
                                </div>
                            </div>
                        </div>
                        @php
                            // Helper function
                            $jobDetails = getInvoicesJobsDetails('App\\Jobs\\MergeInvoiceJob', 'App\\Jobs\\MergeCustomerAllInvoicesJob');
                            $refreshMessage = '';
                            if ($jobDetails == 'exists') {
                                $refreshMessage = 'Please refresh the page to check merged status';
                            }
                        @endphp
                        <small class="text-danger mt-1 job-message"></small>
                    </div>
                    {{-- <select id="all_order_status_change" class="form-select" >
                            <option value="">Select Status</option>
                            <option value="0">New Order</option>
                            <option value="1">In Process</option>
                            <option value="2">Shipped</option>
                            <option value="3">Delivered</option>
                            <option value="4">Cancelled</option>
                            
                        </select> --}}

                    <!-- <a href="orders/create" style="float:right;margin-right:15px;"  class="btn btn-primary waves-effect waves-float waves-light">Add New Order</a> -->


                </div>
                <div class="table-responsive">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAllOrders" /></th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Brand</th>
                                <th>Invoice No</th>
                                <th>Total Cost</th>
                                <th>Paid</th>
                                <th>OWE</th>
                                <th>Paid Date</th>
                                {{-- <th>Order Number</th> --}}
                                <th>
                                    <div style="width: 160px">Is Paid</div>
                                </th>
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
                            <!-- Total Cost -->
                            <th><span class="current-total"></span><span class="all-time-total"></span></th>
                            <!-- Total Paid -->
                            <th><span class="current-paid"></span><span class="all-time-paid"></span></th>
                            <!-- Total owe -->
                            <th><span class="current-owe"></span><span class="all-time-owe"></span></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@section('modal')
    <div class="modal fade text-start show" id="isPaidModal" tabindex="-1" aria-labelledby="myModalLabel36" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel36">Add Partial Amount</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- <form action="" method="post"> --}}
                <div class="modal-body">

                    <label>Add Amount: </label>
                    <div class="mb-1">
                        <input type="amount" min="1" class="partial-amount form-control"
                            placeholder="Add Amount">
                        <input type="hidden" class="invoice-id">
                        <input type="hidden" class="total-cost">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-primary waves-effect waves-float waves-light submit_partial_amount"
                        data-bs-dismiss="modal">Submit</button>
                    <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                        data-bs-dismiss="modal">Close</button>
                </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="updateIsPaidStatusModal" tabindex="-1" aria-labelledby="myModalLabel37"
        style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel37">Update Partial Payments</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="update-partial-payment-form">
                    @csrf
                    <div class="modal-body">
                        <label>Enter Password: </label>
                        <div class="mb-1">
                            <input type="password" name="password" min="1" class="password form-control"
                                placeholder="Enter Password" required>
                            <small class="error-message text-danger"></small>
                        </div>
                        <label>Select Status: </label>
                        <div class="mb-1">
                            <select name="is_paid" class="is_paid_status form-control" required>
                                <option value="0">Un Paid</option>
                                <option value="1">Paid</option>
                                <option value="2">Partially Paid</option>
                            </select>
                        </div>
                        <label>Add Amount: </label>
                        <div class="mb-1">
                            <input type="amount" name="amount" min="1" class="partial-amount form-control"
                                placeholder="Add Amount" required>
                            <input type="hidden" name="" class="invoice-id">
                            <input type="hidden" name="" class="total-cost">
                        </div>
                        <hr>
                        <div class="mb-1 d-flex">
                            <input type="checkbox" name="del_prev_amounts" value="1"
                                class="delete-previouse-amounts form-check" placeholder="Add Amount" required> &nbsp;
                            <span class="text-primary">Delete previous amounts and then update.</span>
                        </div>
                    </div>
                    <div class="modal-footer d-inline">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <div class="">
                                    <small class="fields-error text-danger">
                                    </small>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 ms-auto text-end">
                                <button type="button"
                                    class="btn btn-primary waves-effect waves-float waves-light update_partial_payments">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('modals.modal')
    {{--  --}}
    {{-- Invoice Detail Modal --}}
    {{-- end modal move to stock --}}
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
<!-- Basic Tables end -->

<!-- END: Content-->

<script type="text/javascript">
    $(document).ready(function() {
        calculateInvoiceTotal();
        checkJobsStatus();
        setInterval(function(){checkJobsStatus();}, 3000);
    });
    $(function() {
        if (localStorage.getItem('from_date') != '') {
            $('#from_date').val(localStorage.getItem('from_date'));
        }
        if (localStorage.getItem('to_date') != '') {
            $('#to_date').val(localStorage.getItem('to_date'));
        }
        if (localStorage.getItem('reportrange-invoices') != '') {
            $('#reportrange').val(localStorage.getItem('reportrange-invoices'));
        }
        if (localStorage.getItem('customer') != '') {
            var customer = $('#customer');
            customer.find('option[value="'+localStorage.getItem('customer')+'"]').attr('selected', 'selected').change();
        }
        if (localStorage.getItem('brand') != '') {
            var customer = $('#brand');
            customer.find('option[value="'+localStorage.getItem('brand')+'"]').attr('selected', 'selected').change();
        }
        $(document).on('change', '#from_date, #to_date, #brand, #customer, #reportrange', function(e) {
            calculateInvoiceTotal();
            // Reset Values
            localStorage.setItem('from_date', '');
            localStorage.setItem('to_date', '');
            localStorage.setItem('reportrange-invoices', '');
            localStorage.setItem('brand', '');
            localStorage.setItem('customer', '');
            localStorage.setItem('data_time', '');
            // Set Values
            localStorage.setItem('from_date', $('#from_date').val());
            localStorage.setItem('to_date', $('#to_date').val());
            localStorage.setItem('reportrange-invoices', $('#reportrange').val());
            localStorage.setItem('customer', $('#customer').val());
            localStorage.setItem('brand', $('#brand').val());
            var date = new Date().getTime();
            localStorage.setItem('data_time', date);
        });
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            // ordering: true,
            stateSave: true,
            bDestroy: true,
            paging: true,
            pageLength: 10,
            ajax: {
                url: "{{ route('invoice/listing') }}",
                data: function(d) {
                    let dates = $('#reportrange').val();
                    dates = dates.split(' - ');
                    let from_date = dates[0];
                    let to_date = dates[1];
                    d.search_status = $('#search_status').val();
                    // d.from_date = $('#from_date').val();
                    // d.to_date = $('#to_date').val();
                    d.from_date = from_date;
                    d.to_date = to_date;
                    d.order_status = $('#status').val();
                    d.customer = $('#customer').val();
                    d.brand = $('#brand').val();
                    d.batch_no = $('#batch_no').val();
                }
            },
            columns: [
                {
                    data: 'check_box',
                    name: 'check_box',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at', 
                    orderable: true
                },
                {
                    data: 'customer_name',
                    name: 'customer_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'brand_name',
                    name: 'brand_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'invoice_number',
                    name: 'invoice_number',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'grand_total', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                    name: 'grand_total', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ), 
                    orderable: true
                },
                {
                    data: 'paid',
                    name: 'paid',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'remaining',
                    name: 'remaining',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'paid_date',
                    name: 'paid_date',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'is_paid',
                    name: 'is_paid',
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
            createdRow: function ( row, data, index ) {
                if ( data['rowClass'] == 'merged' ) {
                    $('td', row).addClass('rowClass');
                } else {
                    $('td', row).removeClass('rowClass');
                }
            },
            "drawCallback": function(settings) {
                feather.replace();
            },
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i ===
                        'number' ? i : 0;
                };
                pageTotal1 = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                // Update footer
                $(api.column(5).footer()).find('.current-total').html('$'+Number(pageTotal1.toFixed(2)).toLocaleString());
                pageTotal2 = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                // Update footer
                $(api.column(6).footer()).find('.current-paid').html('$'+Number(pageTotal2.toFixed(2)).toLocaleString());
                pageTotal3 = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                // Update footer
                $(api.column(7).footer()).find('.current-owe').html('$'+Number(pageTotal3.toFixed(2)).toLocaleString());
            },
            'order': [
                [1, 'desc']
            ],
            "rowCallback": function(row, data, index) {
                // if (data['id'] == "97") {
                //     $('td', row).css('background-color', 'Red');
                // } else if (data[3] == "4") {
                //     $('td', row).css('background-color', 'Orange');
                // }
            },
        });
        // $(".flatpickr-basic").flatpickr({
        //     dateFormat: 'm/d/Y'
        // });
        // Refilter the table
        $('#reportrange').on('change', function() {
            table.draw(false);
        });
        $(document).on('change', '#customer', function() {
            var id = $(this).val();
            if(id=="") {
                $.get('/getAllBrands', function(result) {
                    if(result.status == "success") {
                        var options = "<option value=''>All</option>";
                        result.data.forEach(element => {
                            options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                        });
                        
                        $('#brand-error').html('');
                        $('#brand').html(options);
                    } else {
                        $('#brand').html("<option value=''>All</option>");
                        $('#brand-error').html(result.message);
                    }
                });
            } else {
                $.get('/customer/'+id+'/brand', function(result) {
                    if(result.status == "success") {
                        var options = "<option value=''>All</option>";
                        result.data.forEach(element => {
                            options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                        });
                        
                        $('#brand-error').html('');
                        $('#brand').html(options);
                    } else {
                        $('#brand').html("<option value=''>All</option>");
                        $('#brand-error').html(result.message);
                    }
                });
            }
            if ($(this).val() != '') {
                $('#checkAllOrders').prop('disabled', false);
            } else {
                $('#checkAllOrders').prop('disabled', true);
            }
            table.draw(false);
        });
        $(document).on('change', '#brand', function() {
            table.draw(false);
        });
        window.table = table;
    });
    $(document).on('change', '#from_date', function() {
        //
    });
    $(document).on("change", "#invoice_is_paid", function(e) {
        e.preventDefault();
        var _this = $(this);
        if (_this.val() == 0) {
            let text = "Are you Sure you want to choose UnPaid?";
            if (confirm(text) == true) {
                addPayment(_this);
            } else {
                return false;
            }
        } else if (_this.val() == 1) {
            let text = "Are you Sure you want to choose Paid?";
            if (confirm(text) == true) {
                addPayment(_this);
            } else {
                return false;
            }
        } else if (_this.val() == 2) {
            addPayment(_this);
        }
    });
    $(document).on('click', '.add-partial-payment', function() {
        var _this = $(this);
        addPayment(_this);
    });
    $(document).on('click', '.submit_partial_amount', function() {
        var partialAmount = $('.partial-amount').val();
        var totalCost = $('.total-cost').val();
        var isPaidId = 2;
        var invoiceId = $('.invoice-id').val();
        if (Number(partialAmount) > Number(totalCost)) {
            alert('Check amounts');
            return false;
        }
        submitIsPaid(partialAmount, totalCost, invoiceId, isPaidId);
        window.table.draw(false);
    });
    $(document).on('click', '.singleOrderCheck', function() {
        var checkLength = 0;
        $('.singleOrderCheck').each(function() {
            if ($(this).is(':checked')) {
                checkLength++;
            }
        });
        if (checkLength > 52) {
            alert('Only 52 invoices allowed to merge at once');
            return false;
        }
        let length = 0;
        let invoiceIdsArr = [];
        let orderIdsArr = [];
        let customerIdsArr = [];
        let invoiceNumberArr = [];
        let invNumberArr = [];
        $('.singleOrderCheck').each(function() {
            let _this = $(this);
            if (_this.is(':checked')) {
                length += 1;
                let invoiceId = _this.attr('data-invoice-id');
                let orderId = _this.attr('data-invoice-order-id');
                let customerId = _this.attr('data-invoice-customer-id');
                let invoiceNumber = _this.attr('data-invoice-number');
                let invNumber = _this.attr('data-inv-no');
                invoiceIdsArr.push(invoiceId);
                orderIdsArr.push(orderId);
                customerIdsArr.push(customerId);
                invoiceNumberArr.push(invoiceNumber);
                invNumberArr.push(invNumber);
                window.invoiceIdsArr = invoiceIdsArr;
                window.orderIdsArr = orderIdsArr;
                window.customerIdsArr = customerIdsArr;
                window.invoiceNumberArr = invoiceNumberArr;
                window.invNumberArr = invNumberArr;
            }
        });
        if (length >= 1) {
            $('.merge_btn').prop('disabled', false);
            $('.merge_btn').removeClass('btn-warning');
            $('.merge_btn').addClass('btn-success');
        } else {
            $('.merge_btn').prop('disabled', true);
            $('.merge_btn').removeClass('btn-success');
            $('.merge_btn').addClass('btn-warning');
        }
    });
    $(document).on("change", "#checkAllOrders", function() {
        let _this = $(this);
        if (_this.is(':checked')) {
            $('.singleOrderCheck').each(function() {
                let _this = $(this);
                _this.prop('checked', true);
            });
            $('.singleOrderCheck').prop("checked", true);
            $('.merge_btn').prop('disabled', false);
            $('.merge_btn').removeClass('btn-warning');
            $('.merge_btn').addClass('btn-success');
        } else {
            $('.singleOrderCheck').prop("checked", false);
            $('.merge_btn').prop('disabled', true);
            $('.merge_btn').removeClass('btn-success');
            $('.merge_btn').addClass('btn-warning');
        }
    });
    $(document).on('click', '.merge_btn', function() {
        var _this = $(this);
        _this.prop('disabled', true);
        $('.loader').removeClass('d-none');
        if ($('#checkAllOrders').is(':checked')) {
            let customerID = $('#customer').val();
            let brandID = $('#brand').val();
            let dates = $('#reportrange').val();
            dates = dates.split(' - ');
            let from_date = dates[0];
            let to_date = dates[1];
            $.ajax({
                url: '{{ route("merge_customer_all_invoices") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    customer_id: customerID,
                    brand: brandID,
                    from: from_date,
                    to: to_date
                },
                success:function(response) {
                    $('.loader').addClass('d-none');
                    if (response.message == true) {
                        alert(response.data);
                        location.reload();
                    } else if (response.message == false) {
                        _this.prop('disabled', false);
                        alert(response.data);
                    }
                }
            });
        } else {
            $.ajax({
                url: '{{ route("create_merge_invoice") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    invoiceIds: window.invoiceIdsArr,
                    orderIds: window.orderIdsArr,
                    customerIds: window.customerIdsArr,
                    invoiceNumbers: window.invoiceNumberArr,
                    invNumbers: window.invNumberArr
                },
                success: function(response) {
                    $('.loader').addClass('d-none');
                    if (response.message == true) {
                        alert(response.data);
                        location.reload();
                    } else if (response.message == false) {
                        _this.prop('disabled', false);
                        alert(response.data);
                    }
                }
            });
        }
    });
    $(document).on("change", "#all_order_status_change", function() {
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
    $('.mergedToolTip').tooltip({
        show: {
            effect: 'none',
            delay: 0
        }
    });
    $(function() {
        var reportRange = localStorage.getItem('reportrange-invoices');
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
    function checkJobsStatus() {
        let jobName1 = 'App\\Jobs\\MergeInvoiceJob';
        let jobName2 = 'App\\Jobs\\MergeCustomerAllInvoicesJob';
        $.ajax({
            url: "{{ route('check_job_status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                job_name1: jobName1,
                job_name2: jobName2
            },
            success:function (response) {
                if (response.status == true) {
                    $('.job-message').html(response.msg);
                } else {
                    $('.job-message').html('');
                    $('.data-table tbody tr').each(function () {
                        let _this = $(this);
                        if (_this.find('td div.removeable div').attr('data-loader-type') == 'merging') {
                            _this.find('td div.removeable').html('');
                            _this.find('td div.removeable').html('<input type="checkbox" checked disabled title="Already Merged" class="mergedToolTip" style="border: 1px solid red"/>');
                        }
                    });
                }
            }
        })
    }
    function myFunction() {
        var r = confirm("Do you really want to delete this invoice?");
        if (r == true) {
            return true;
        } else {
            return false;
        }
        document.getElementById("demo").innerHTML = txt;
    }
    function addPayment(_this) {
        if (_this.val() == '0') {
            var partialAmount = 0;
            var totalCost = 0;
            var isPaidId = _this.val();
            var invoiceId = _this.data('invoice-id');
            var remaining = _this.data('total-cost');
            var paid = 0;
            submitIsPaid(partialAmount, totalCost, invoiceId, isPaidId);
            window.table.draw(false);
        } else if (_this.val() == '2') {
            $('.invoice-id').val(_this.data('invoice-id'));
            $('.total-cost').val(_this.data('total-cost'));
            $('#isPaidModal').modal('show');
        } else {
            var partialAmount = 0;
            var totalCost = _this.data('total-cost');
            var isPaidId = _this.val();
            var invoiceId = _this.data('invoice-id');
            var remaining = 0;
            var paid = totalCost;
            submitIsPaid(partialAmount, totalCost, invoiceId, isPaidId);
            window.table.draw(false);
        }
    }
    function submitIsPaid(partialAmount, totalCost, invoiceId, isPaidId) {
        $.ajax({
            type: 'POST',
            url: '/updateInvoiceStatus',
            data: {
                amount: partialAmount,
                total_cost: totalCost,
                invoice_id: invoiceId,
                is_paid: isPaidId,
                _token: "{{ csrf_token() }}",
                dataType: "JSON",
            },
            success: function(data) {
                if (data.status == true) {
                    $('.toast .me-auto').html('Success');
                    $('.toast .toast-header').removeClass('bg-danger');
                    $('.toast .toast-header').addClass('bg-success');
                    $('.toast .text-muted').html('Now');
                    $('.toast .toast-body').html(data.message);
                    $('#toast-btn').click();
                    setTimeout(function() {
                        location.reload(true);
                    }, 1500);
                } else if (data.status == false) {
                    $('.toast .me-auto').html('Error');
                    $('.toast .toast-header').removeClass('bg-success');
                    $('.toast .toast-header').addClass('bg-danger');
                    $('.toast .text-muted').html('Now');
                    $('.toast .toast-body').html(data.message);
                    $('#toast-btn').click();
                }
            }
        });
    }
    function calculateInvoiceTotal() {
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
                url: "{{ route('footer_invoice_total') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    customer: customerID,
                    min_date: from_date,
                    max_date: to_date,
                    brand: $('#brand').val()
                },
                success:function(resp) {
                    if (resp.status == true) {
                        var totalInvoiceAmount = Number(resp.invoice_total).toFixed(2);
                        var totalPaidAmount = Number(resp.paid_total).toFixed(2);
                        var totalRemainingAmount = Number(resp.remaining_total).toFixed(2);
                        $('.all-time-total').html('(Total $'+(Number(totalInvoiceAmount).toLocaleString())+')');
                        $('.all-time-paid').html('(Total $'+(Number(totalPaidAmount).toLocaleString())+')');
                        $('.all-time-owe').html('(Total $'+(Number(totalRemainingAmount).toLocaleString())+')');
                    }
                }
            });
        }, 500);
    }
</script>
@endsection
@section('datatablejs')
<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop
@section('datepickerjs')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@stop
