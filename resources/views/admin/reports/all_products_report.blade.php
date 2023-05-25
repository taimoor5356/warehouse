@extends('admin.layout.app')
@section('title', 'All Products Report')
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
        background-color: rgb(255, 255, 136) !important;
    }
    .table:not(.table-dark):not(.table-light) thead:not(.table-dark) th.avg_data, .table:not(.table-dark):not(.table-light) tfoot:not(.table-dark) th.avg_data {
        background-color: rgb(255, 255, 136) !important;
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
                    <h2 class="content-header-title float-start mb-0">All Products Report</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item">Reports
                            </li>
                            <li class="breadcrumb-item">All Products Report
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
                        <form>
                            <div class="row w-100">
                                <div class="form-group col-sm-12 col-md-3 mt-1">
                                    <label for="category">Select Category</label>
                                    <select name="category" id="category_id" class="form-control select2 toggle-vis">
                                        <option value="" selected>- Select Category -</option>
                                        @foreach($categories as $category)
                                            @isset($category)
                                                <option value="{{ $category->id }}" data-column="">{{ $category->name }}</option>
                                            @endisset
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-3 mt-1">
                                    <label for="product">Select Product</label>
                                    <select name="product" id="product" class="form-control select2 toggle-vis">
                                        <option value="" selected>- Select Product -</option>
                                        {{-- @foreach($categories as $product)
                                            @isset($product)
                                                <option value="{{ $product->id }}" data-column="">{{ $product->name }}</option>
                                            @endisset
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-3 mt-1">
                                    <label for="min">Select Dates</label>
                                    <input id="reportrange" class="form-control mydaterangepicker" style="cursor: pointer; background-color: white" readonly>
                                    {{-- <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile" placeholder="MM/DD/YYYY" id="min" name="min"> --}}
                                </div>
                                {{-- <div class="form-group col-sm-12 col-md-3 mt-1"> --}}
                                    {{-- <label for="min">End</label> --}}
                                    {{-- <input type="text" class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile" placeholder="MM/DD/YYYY" id="max" name="max"> --}}
                                {{-- </div> --}}
                                {{-- <div class="form-group col-sm-12 col-md-3 mt-1">
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
                                </div> --}}
                                <input type="hidden" name="days_count" id="days_count" value="">
                                {{-- <div class="form-group col-sm-12 col-md-3 mt-1">
                                    <label for="days_count">Select Days</label>
                                    <select name="days_count" id="days_count" class="form-control select2">
                                        <option value="" selected>- Select Days -</option>
                                        <option value="7">7 Days</option>
                                        <option value="10">10 Days</option>
                                        <option value="15">15 Days</option>
                                        <option value="20">20 Days</option>
                                        <option value="30">30 Days</option>
                                    </select>
                                </div> --}}
                                {{-- <div class="form-group col-sm-12 col-md-3 mt-1">
                                    <label for="product">Select Product</label>
                                    <select name="product" id="product" class="form-control select2 toggle-vis">
                                        <option value="-1" selected>- Select Product -</option>
                                        @php $column = 1; @endphp
                                        @foreach($products as $product)
                                            @isset($product)
                                                <option class="opt-val" value="{{$column++}}" data-column="{{$column++}}">{{ $product->name }}</option>
                                            @endisset
                                        @endforeach
                                    </select>
                                </div> --}}
                                <div class="form-group col-sm-12 col-md-3 mt-1">
                                    <button class="btn btn-primary submit" type="button" style="margin-top: 20px"><div class="loader d-none" style="float: left"></div>&nbsp; Show</button>
                                    <button class="btn btn-success submit1" type="button" style="margin-top: 20px"><div class="loader1 d-none" style="float: left"></div>&nbsp; Show & Mail</button>
                                    <button class="btn btn-success submit2 d-none" type="button" style="margin-top: 20px"><div class="loader d-none" style="float: left"></div>&nbsp; Send Mail</button>
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
                        <div class="tab-pane active" id="product" role="tabpanel" aria-labelledby="product-tab" aria-expanded="false">
                            <div class="table-responsive">
                                <input type="hidden" id="row_no" value="10">
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
        $(document).ready(function(){
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
            $(document).on('change', '#category_id', function() {
                var id = $(this).val();
                if(id=="") {
                    $('#product').html('<option value="">Select Product</option>');
                } else {
                    $.get('/category/'+id+'/products', function(result) {
                        if(result.status == "success") {
                            var options = "<option value=''>Select Product</option>";
                            result.data.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.name+"</option>"
                            });
                            $('#product').html(options);
                        } else {
                            $('#product').html("<option value=''>Select Product</option>");
                        }
                    });
                }
            });
            $(document).on('change', '#time_duration', function () {
                if ($(this).val() == 'last_six_months' || $(this).val() == 'this_year' || $(this).val() == 'last_year') {
                    $('.submit').prop('disabled', true);
                    $('.submit').addClass('d-none');
                    $('.submit1').prop('disabled', true);
                    $('.submit1').addClass('d-none');
                    $('.submit2').prop('disabled', false);
                    $('.submit2').removeClass('d-none');
                    $('#min').val('');
                    $('#min').html('');
                    $('#max').val('');
                    $('#max').html('');
                } else {
                    $('.submit').prop('disabled', false);
                    $('.submit').removeClass('d-none');
                    $('.submit1').prop('disabled', false);
                    $('.submit1').removeClass('d-none');
                    $('.submit2').prop('disabled', true);
                    $('.submit2').addClass('d-none');
                    $('#min').val('');
                    $('#min').html('');
                    $('#max').val('');
                    $('#max').html('');
                }
            });
            $(document).on('change', '#min,#max, #reportrange', function() {
                var date = new Date().getTime();
                localStorage.setItem('data_time', date);
                localStorage.setItem('reportrange_all_products_report', '');
                localStorage.setItem('reportrange_all_products_report', $('#reportrange').val());
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
                // var start = $('#min').val();
                // var end = $('#max').val();
                let dates = $('#reportrange').val();
                dates = dates.split(' - ');
                var start = dates[0];
                var end = dates[1];
                if (end == '' || end == null) {
                    var d = new Date();
                    var endDate = (d.getMonth()+1) + "/" + d.getDate() + "/" + d.getFullYear();
                    end = endDate;
                }
                var days = (daysdifference(start, end))+Number(1);
                window.daysDiff = days;
                if (days > 31) {
                    $('.submit').prop('disabled', true);
                    $('.submit').addClass('d-none');
                    $('.submit1').prop('disabled', true);
                    $('.submit1').addClass('d-none');
                    $('.submit2').prop('disabled', false);
                    $('.submit2').removeClass('d-none');
                } else {
                    $('.submit').prop('disabled', false);
                    $('.submit').removeClass('d-none');
                    $('.submit1').prop('disabled', false);
                    $('.submit1').removeClass('d-none');
                    $('.submit2').prop('disabled', true);
                    $('.submit2').addClass('d-none');
                }
            });
            $(document).on('click', '.submit', function() {
                if ($('#category_id').val() == '') {
                    alert('Please Select Category');
                    return false;
                }
                var _this = $(this);
                var sendMail = 'false';
                sendAndMail(_this, sendMail);
            });
            $(document).on('click', '.submit1', function() {
                if ($('#category_id').val() == '') {
                    alert('Please Select Category');
                    return false;
                }
                var _this = $(this);
                var sendMail = 'true';
                sendAndMail(_this, sendMail);
            });
            $(document).on('click', '.submit2', function() {
                if ($('#category_id').val() == '') {
                    alert('Please Select Category');
                    return false;
                }
                var _this = $(this);
                var sendMail = 'true';
                setTimeout(() => {
                    $('.toast .toast-header').removeClass('bg-danger');
                    $('.toast .me-auto').html('Success');
                    $('.toast .toast-header').addClass('bg-success');
                    $('.toast .text-muted').html('Now');
                    $('.toast .toast-body').html("You will receive mail shortly");
                    $('#toast-btn').click();
                }, 100);
                var timeDuration = '';
                // var min = $('#min').val();
                // var max = $('#max').val();
                var daysCount = $('#days_count').val();

                let dates = $('#reportrange').val();
                dates = dates.split(' - ');
                var min = dates[0];
                var max = dates[1];

                var categoryID = $('#category_id').val();
                var product = $('#product').val();
                $.ajax({
                    url: "{{ route('show_report_and_mail') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        time_duration: timeDuration,
                        min: min,
                        max: max,
                        days_count: daysCount,
                        category_id: categoryID,
                        product: product,
                        send_mail: sendMail
                    },
                    success:function(response) {
                        if (response.status == true) {
                            // setTimeout(() => {
                            //     $('.toast .toast-header').removeClass('bg-danger');
                            //     $('.toast .me-auto').html('Success');
                            //     $('.toast .toast-header').addClass('bg-success');
                            //     $('.toast .text-muted').html('Now');
                            //     $('.toast .toast-body').html("Check your Mail");
                            // }, 100);
                        } else {
                            $('.toast .toast-header').removeClass('bg-success');
                            $('.toast .me-auto').html('Error');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html("Something went wrong");
                        }
                    }
                });
            });
            // $(".flatpickr-input").flatpickr({
            //     dateFormat: 'm/d/Y'
            // });
            function sendAndMail(newThis, sendMail) {
                newThis.prop('disabled', true);
                $('.submit,.submit1').prop('disabled', true);
                if (sendMail == 'true') {
                    $('.loader1').removeClass('d-none');
                    $('.loader').addClass('d-none');
                    setTimeout(() => {
                        $('.toast .toast-header').removeClass('bg-danger');
                        $('.toast .me-auto').html('Success');
                        $('.toast .toast-header').addClass('bg-success');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html("You will receive mail shortly");
                        $('#toast-btn').click();
                    }, 100);
                } else {
                    $('.loader').removeClass('d-none');
                    $('.loader1').addClass('d-none');
                }
                var timeDuration = '';
                // var min = $('#min').val();
                // var max = $('#max').val();
                var daysCount = $('#days_count').val();

                let dates = $('#reportrange').val();
                dates = dates.split(' - ');
                var min = dates[0];
                var max = dates[1];

                var categoryID = $('#category_id').val();
                var product = $('#product').val();
                $.ajax({
                    url: "{{ route('show_report_and_mail') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        time_duration: timeDuration,
                        min: min,
                        max: max,
                        days_count: daysCount,
                        category_id: categoryID,
                        product: product,
                        send_mail: sendMail
                    },
                    success:function(response) {
                        $('.loader1').addClass('d-none');
                        $('.loader').addClass('d-none');
                        $('.submit,.submit1').prop('disabled', false);
                        $('.table-responsive').empty();
                        if (response.status == true) {
                            if (sendMail == 'true') {
                                $('.toast .toast-header').removeClass('bg-danger');
                                $('.toast .me-auto').html('Success');
                                $('.toast .toast-header').addClass('bg-success');
                                $('.toast .text-muted').html('Now');
                                $('.toast .toast-body').html("Check your Mail");
                                $('#toast-btn').click();
                            }
                            newThis.prop('disabled', false);
                            var html = '';
                            html += `
                                <table class="table data-table table-bordered mb-1">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><div class="text-center" style="width: 100px; margin: 0px auto"><center>Date/AVG</center></div></th>`;
                                            response.products.forEach(element => {
                                                html += `<th class="text-center"><div class="text-center" style="width: `+((element.name.length) * (11))+`px; margin: 0px auto"><center>`+element.name+`</center></div></th>`;
                                            });
                                        
                                            html += `
                                            <th class="text-center" style="margin: 0px auto">
                                                Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="avg_data">
                                            <td class="text-center"><center>Day Avg</center></td>`;
                                            let sumOfDayAvg = 0;
                                            response.day_avg.forEach(dayAvg => {
                                                html += `<td class="text-center">`+Math.round(dayAvg)+`</td>`;
                                                sumOfDayAvg += Math.round(dayAvg);
                                            });
                                        var arrData = response.data.reverse();
                                        html +=`<td class="text-center">`+Math.round(sumOfDayAvg)+`</td></tr>
                                        <tr class="avg_data">
                                            <td class="text-center">Month Avg</td>`;
                                            let sumOfMonthAvg = 0;
                                            response.month_avg.forEach(monthAvg => {
                                                html += `<td class="text-center">`+Math.round(monthAvg)+`</td>`;
                                                sumOfMonthAvg += Math.round(monthAvg);
                                            });
                                        html +=`<td class="text-center">`+Math.round(sumOfMonthAvg)+`</td></tr>
                                        <tr class="avg_data">
                                            <td class="text-center">Inventory</td>`;
                                        var length = arrData.length;
                                        var checkLength = 0;
                                        for (let index = 0; index < arrData.length; index++) {
                                            var sumOfInventoryQty = 0;
                                            var products = arrData[index].products;
                                            for (let index2 = 0; index2 < products.length; index2++) {
                                                checkLength++;
                                                html += `<td class="text-center">`+Math.round(products[index2].inventory_qty)+`</td>`;
                                                sumOfInventoryQty += Math.round(products[index2].inventory_qty);
                                                if (checkLength >= products.length) {
                                                    break;
                                                }
                                            }
                                            if (checkLength >= products.length) {
                                                break;
                                            }
                                        }
                                        html += `<td class="text-center">`+Math.round(sumOfInventoryQty)+`</td>`;
                                        html += `</tr>`;
                                        arrData.forEach(ele => {
                                            html += `<tr>
                                                    <td class="text-center">`+ele.date+`</td>`;
                                                var sumOfQty = 0;
                                                ele.products.forEach(e => {
                                                    html += `
                                                        <td class="text-center">`+Math.round(e.qty)+`</td>
                                                    `;
                                                    sumOfQty += Math.round(e.qty);
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
                                        footTotal += Math.round(f);
                                    });
                                    html += `
                                        <th class="text-center">`+footTotal+`</th>
                                    </tfoot>
                                </table>
                            `;
                            $('.table-responsive').append(html);
                        } else {
                            $('.loader, .loader1').addClass('d-none');
                            newThis.prop('disabled', false);
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
        $(function() {
            var reportRange = localStorage.getItem('reportrange_all_products_report');
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
        function daysdifference(firstDate, secondDate) {
            var startDay = new Date(firstDate);
            var endDay = new Date(secondDate);
            var millisBetween = startDay.getTime() - endDay.getTime();
            var days = millisBetween / (1000 * 3600 * 24);
            return Math.round(Math.abs(days));
        }
    </script>
@endsection