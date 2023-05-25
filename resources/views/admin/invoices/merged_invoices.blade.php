@extends('admin.layout.app')
@section('title', 'Merged Invoices')
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

    </style>
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">Merged Invoices</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Invoices
                                    </li>
                                    <li class="breadcrumb-item">Merged Invoices
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
                <div class="card-header">
                    <div class="row w-100">
                        {{-- <div class="col-sm-4">
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
                        <div class="col-sm-4">
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
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="col-form-label" for="to_date">Select Dates:</label>
                                </div>
                                <div class="col-sm-12">
                                    <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                {{-- <th><input type="checkbox" id="checkAllOrders" /></th> --}}
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Dates (From - To)</th>
                                <th>Total Invoices</th>
                                <th>Total Cost</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                @php 
                                    $mergedInvoices = \App\Models\MergedInvoice::query();
                                    $mergedTotalCost = $mergedInvoices->sum('total_cost');
                                    $totalQty = 0;
                                    foreach ($mergedInvoices->get() as $key => $invoice) {
                                        $invoices = explode(',', $invoice->invoice_ids);
                                        $totalQty += count($invoices);
                                    }
                                @endphp
                                <th><span class="invoices-total"></span><span class="total-invoices"></span><span class="total-invoice-qty"></span></th>
                                <th><span class="current-total-cost"></span><span class="total-cost"></span><span class="total-invoice-cost"></span></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Basic Tables end -->

    <!-- END: Content-->
<script>
    $(document).ready(function(){
        calculateTotalMergedInvoices();
        var table = $('.data-table').DataTable({
            searchDelay: 1500,
            stateSave: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("view_merged_invoices") }}',
                data: function (d) {
                    let dates = $('#reportrange').val();
                    dates = dates.split(' - ');
                    let from_date = dates[0];
                    let to_date = dates[1];
                    d.customer = $('#customer').val();
                    d.from_date = from_date;
                    d.to_date = to_date;
                }
            },
            columns: [
                {
                    data: 'date',
                    name: 'date', 
                    orderable: true
                },
                {
                    data: 'customer_name',
                    name: 'customer_name', 
                    orderable: false
                },
                {
                    data: 'invoices_dates',
                    name: 'invoices_dates', 
                    orderable: false
                },
                {
                    data: 'total_invoices',
                    name: 'total_invoices', 
                    orderable: true
                },
                {
                    data: 'total_cost',
                    name: 'total_cost', 
                    orderable: true
                },
                {
                    data: 'action',
                    name: 'action', 
                    orderable: false
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
                $(api.column(3).footer()).find('.invoices-total').html(pageTotal);
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
                $(api.column(4).footer()).find('.current-total-cost').html('$' + pageTotal.toFixed(2));
                // Total over all pages
            },
            drawCallback: function(settings) {
                feather.replace();
            },
            'order': [
                [0, 'desc']
            ],
        });
        $(document).on('change', '#customer, .from_date, .to_date, #reportrange', function () {
            var date = new Date().getTime();
            localStorage.setItem('data_time', date);
            localStorage.setItem('reportrange-merged-invoices', '');
            localStorage.setItem('reportrange-merged-invoices', $('#reportrange').val());
            table.draw(false);
            calculateTotalMergedInvoices();
        });
        // $(".flatpickr-basic").flatpickr({
        //     dateFormat: 'm/d/Y'
        // });
    });
    
    $(function() {
        var reportRange = localStorage.getItem('reportrange-merged-invoices');
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
    function calculateTotalMergedInvoices() {
            setTimeout(() => {
                let customerID = $('#customer').val();
                let dates = $('#reportrange').val();
                dates = dates.split(' - ');
                let from_date = dates[0];
                let to_date = dates[1];
                $.ajax({
                    url: "{{ route('get_customer_total_invoices') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer: customerID,
                        min_date: from_date,
                        max_date: to_date,

                    },
                    success:function(resp) {
                        if (resp.status == true) {
                            $('.total-invoice-qty').html('(Total '+resp.total_qty+')');
                            $('.total-invoice-cost').html('(Total $'+resp.total_cost.toFixed(2)+')');
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
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>


@stop
