@extends('admin.layout.app')
@section('title', 'Postage Reporting')
@section('datatablecss')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.0/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

@stop
@section('content')
<style type="text/css">
    .dataTables_length{float: left;padding-left: 20px;}
    .dataTables_filter{padding-right:20px;}
    .dataTables_info{padding-left: 20px !important; padding-bottom:30px !important;}
    .dataTables_paginate{padding-right: 20px !important;}
    th,
    td {
        border: 1px solid lightgray;
    }
    .clearfix:after {
        content:"";
        display:block;
        clear:both;
    }
    .avg_data{
        background-color: rgb(255, 255, 82);
    }
</style>

<!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                    <div class="col-9">
                    <h2 class="content-header-title float-start mb-0">Postage Reporting</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item">Reports
                            </li>
                            <li class="breadcrumb-item">Postage Reporting
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
                        {{-- <div class="form-group col-sm-12 col-md-2 mt-1">
                            <label for="time_duration">Select Duration</label>
                            <select name="time_duration" id="time_duration" class="form-control select2">
                                <option value="" selected disabled>- Select Duration -</option>
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
                            <label for="min">Start</label>
                            <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile" placeholder="MM/DD/YYYY" id="min" name="min">
                        </div>
                        <div class="form-group col-sm-12 col-md-2 mt-1">
                            <label for="min">End</label>
                            <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile" placeholder="MM/DD/YYYY" id="max" name="max">
                        </div> --}}
                        <div class="form-group col-sm-12 col-md-2 mt-1">
                            <label for="product">Select Dates</label>
                            <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
                        </div>
                        <div class="form-group col-sm-12 col-md-2 mt-1">
                            <label for="min">Customers</label>
                            <select name="customer" id="customer" class="form-control select2">
                                <option value="" selected>- All -</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-2 mt-1">
                            <button class="btn btn-primary submit-report" type="button" style="margin-top: 20px">Submit</button>
                        </div>
                    </div>
                </div>
                <div class="p-2">
                    <ul class="nav nav-pills">
                    </ul>
                    <hr>
                    <div class="tab-content">
                        <div class="tab-pane active" id="product" role="tabpanel" aria-labelledby="product-tab" aria-expanded="false">
                            {{-- <button type="button" id="loadMore" class="btn btn-primary my-2">Load</button> --}}
                            <div class="table-responsive">
                                {{-- <table class="table data-table">
                                    <thead>
                                        <th class="text-center"><div style="width: 100px">Date</div></th>
                                        <th>
                                            Total
                                        </th>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                                <input type="hidden" id="row_no" value="10"> --}}
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
    <script>
        $(document).ready(function() {
            $(document).on('change', '#time_duration', function () {
                $('#min').val('');
                $('#min').html('');
                $('#max').val('');
                $('#max').html('');
            });
            $(document).on('change', '#min,#max,#reportrange', function() {
                var date = new Date().getTime();
                localStorage.setItem('data_time', date);
                localStorage.setItem('reportrange_postage', '');
                localStorage.setItem('reportrange_postage', $('#reportrange').val());
                $('#time_duration').val('');
                $('#time_duration').html(`
                    <option value="" selected disabled>- Select Duration -</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last_week">Last Week</option>
                    <option value="last_month">Last Month</option>
                    <option value="last_six_months">Last 6 Months</option>
                    <option value="last_year">Last Year</option>
                `);
                var start = $('#min').val();
                var end = $('#max').val();
                if (end == '' || end == null) {
                    var d = new Date();
                    var endDate = (d.getMonth()+1) + "/" + d.getDate() + "/" + d.getFullYear();
                    end = endDate;
                }
                // var days = (daysdifference(start, end))+Number(1);
                // window.daysDiff = days;
            });
            var html = '';
            $(document).on('click', '.submit-report', function () {
                var timeDuration = $('#time_duration').val();
                // var min = $('#min').val();
                // var max = $('#max').val();
                let dates = $('#reportrange').val();
                dates = dates.split(' - ');
                var min = dates[0];
                var max = dates[1];
                var customer = $('#customer').val();
                $.ajax({
                    url: "{{ route('postage_reporting') }}",
                    // type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        time_duration: timeDuration,
                        min: min,
                        max: max,
                        customer: customer
                    },
                    success:function(response) {
                        $('.loader1').addClass('d-none');
                        $('.loader').addClass('d-none');
                        $('.submit,.submit1').prop('disabled', false);
                        $('.table-responsive').empty();
                        if (response.status == true) {
                            console.log(response.customers);
                            var html = '';
                            html += `
                                <table class="table data-table table-bordered mb-1">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><div class="text-center" style="width: 100px; margin: 0px auto"><center>Date</center></div></th>`;
                                            response.customers.forEach(element => {
                                                html += `<th class="text-center"><div class="text-center" style="width: `+((element.customer_name.length) * (11))+`px; margin: 0px auto"><center>`+element.customer_name+`</center></div></th>`;
                                            });
                                        
                                            html += `
                                            <th class="text-center" style="margin: 0px auto">
                                                Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                                        var arrData = response.data.reverse();
                                        arrData.forEach(ele => {
                                            html += `<tr>
                                                    <td class="text-center">`+ele.date+`</td>`;
                                                var sumOfQty = 0;
                                                ele.customers.forEach(e => {
                                                    html += `
                                                        <td class="text-center">`+e.qty+`</td>
                                                    `;
                                                    sumOfQty += e.qty;
                                                });
                                                html += `<td class="text-center">`+sumOfQty+`</td>`
                                            html += `</tr>`;
                                        });
                                    html += `</tbody>
                                    <tfoot>
                                        <th class="text-center">
                                            Total
                                        </th>`;
                                    var footData = response.data[0];
                                    var footTotal = 0;
                                    footData['total'].forEach(f => {
                                        html += `
                                        <th class="text-center">
                                            `+f+`
                                        </th>
                                        `;
                                        footTotal += f;
                                    });
                                    html += `
                                        <th class="text-center">`+footTotal+`</th>
                                    </tfoot>
                                </table>
                            `;
                            $('.table-responsive').append(html);
                        } else {
                            $('.loader, .loader1').addClass('d-none');
                            $('.toast .toast-header').removeClass('bg-success');
                            $('.toast .me-auto').html('Error');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html("Something went wrong");
                            $('#toast-btn').click();
                        }
                    }
                });
            });
            $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                var min = start.format('DD-MM-YYYY');
                var max = end.format('DD-MM-YYYY');
                $('.min').val(min);
                $('.max').val(max);
            });
            $(document).on('change', '#time_duration', function (){
                $('.date_range').val('');
            });
            $(document).on('change', 'input[name="daterange"]', function (){
                $('#time_duration').val('- Select Duration -').prop('selected', true).trigger('change.select2');
            });
        });
        $(function() {
            var reportRange = localStorage.getItem('reportrange_postage');
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