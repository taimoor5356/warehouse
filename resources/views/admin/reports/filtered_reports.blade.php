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
        background-color: rgb(255, 255, 136);
    }
</style>

<!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                    <div class="col-10">
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
                    <div class="col-2 addBtnClass">
                        <a href="/all-products-report" style="margin-left:auto;"  class="btn btn-primary waves-effect waves-float waves-light">View All</a> 
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
                        <form action="{{ route('filter_product_report') }}" method="POST">
                            @csrf
                            <div class="row w-100">
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="time_duration">Select Duration</label>
                                    <select name="time_duration" id="time_duration" class="form-control select2">
                                        <option value="">- Select Duration -</option>
                                        <option value="yesterday" @if($time_duration == 'yesterday') selected @endif>Yesterday</option>
                                        <option value="last_week" @if($time_duration == 'last_week') selected @endif>Last Week</option>
                                        <option value="last_month" @if($time_duration == 'last_month') selected @endif>Last Month</option>
                                        <option value="last_six_months" @if($time_duration == 'last_six_months') selected @endif>Last 6 Months</option>
                                        <option value="last_year" @if($time_duration == 'last_year') selected @endif>Last Year</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="min">Select Date Range</label>
                                    <input type="text" class="form-control date_range" placeholder="Start Date" name="daterange" value="">
                                    <input type="hidden" id="from-date" value="@isset($from){{ date('m/d/Y',  strtotime($from)) }}@endisset">
                                    <input type="hidden" id="to-date" value="@isset($to){{ date('m/d/Y',  strtotime($to)) }}@endisset">
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="days_count">Select Days</label>
                                    <select name="days_count" id="days_count" class="form-control select2">
                                        <option value="">- Select Days -</option>
                                        <option @isset($selectedDaysCount) @if($selectedDaysCount == 7) selected @endif @endisset value="7">7 Days</option>
                                        <option @isset($selectedDaysCount) @if($selectedDaysCount == 10) selected @endif @endisset value="10">10 Days</option>
                                        <option @isset($selectedDaysCount) @if($selectedDaysCount == 15) selected @endif @endisset value="15">15 Days</option>
                                        <option @isset($selectedDaysCount) @if($selectedDaysCount == 20) selected @endif @endisset value="20">20 Days</option>
                                        <option @isset($selectedDaysCount) @if($selectedDaysCount == 30) selected @endif @endisset value="30">30 Days</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-2">
                                    <button class="btn btn-primary" type="submit" style="margin-top: 20px">Submit</button>
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
                            {{-- <button type="button" id="loadMore" class="btn btn-primary my-2">Load</button> --}}
                            <div class="table-responsive">
                                <table class="table data-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><div style="width: 100px">Date/AVG</div></th>
                                            @foreach ($products as $product)
                                                @isset($product) 
                                                    <th class="text-center">
                                                        <div style="width: {{Str::length($product->name) * 11}}px">{{ $product->name }}</div>
                                                    </th>
                                                @endisset
                                            @endforeach
                                            <th>
                                                Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="all_rows">
                                        <tr class="avg_data">
                                            <td class="text-center">Day Avg</td>
                                            @php $totalDaysAvg = 0;@endphp
                                            @foreach ($averages as $average)
                                                <td class="text-center">
                                                    {{ number_format($average['dayAvg']) }}
                                                </td>
                                                @php $totalDaysAvg += $average['dayAvg']; @endphp
                                            @endforeach
                                            <td class="text-center">
                                                {{ number_format($totalDaysAvg) }}
                                            </td>
                                        </tr>
                                        <tr class="avg_data">
                                            <td class="text-center">Month Avg</td>
                                            @php $totalMonthAvg = 0; @endphp
                                            @foreach ($averages as $average)
                                                <td class="text-center">
                                                    {{ number_format($average['monthAvg']) }}
                                                </td>
                                                @php $totalMonthAvg += $average['monthAvg']; @endphp
                                            @endforeach
                                            <td class="text-center">
                                                {{ number_format($totalMonthAvg) }}
                                            </td>
                                        </tr>
                                        @foreach ($dateWiseData as $key => $dates)
                                            <tr>
                                                <td class="text-center"> {{date('M, d-Y', strtotime($dates['date']))}} </td>
                                                @php $sumOfQty = 0; @endphp
                                                @foreach ($dates['products'] as $product)
                                                    <td class="text-center"> {{$product['qty']}} </td>
                                                    @php $sumOfQty += $product['qty']; @endphp
                                                @endforeach
                                                <td class="text-center">
                                                    {{ number_format($sumOfQty) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
            $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                var min = start.format('DD-MM-YYYY');
                var max = end.format('DD-MM-YYYY');
                $('.min').val('');
                $('.max').val('');
            });

            var fromDate = $('#from-date').val();
            var toDate = $('#to-date').val();
            $('input[name="daterange"]').val(fromDate+' - '+toDate);

            $(document).on('change', '#time_duration', function (){
                $('.date_range').val('');
            });
            $(document).on('change', 'input[name="daterange"]', function (){
                $('#time_duration').val('- Select Duration -').prop('selected', true).trigger('change.select2');
            });
        });
        function loadmore() {
            var val = document.getElementById("row_no").value;
            $.ajax({
                type: 'GET',
                url: '{{route("all_products_report")}}',
                data: {
                    rowlimit:val
                },
                success: function (response) {
                    $('#all_rows').html('');
                    var html = '';
                    response.forEach(element => {
                        html += `
                            <tr>
                                <td>
                                    `+element.date+`    
                                </td>`;
                                    element.products.forEach(e => {
                                        html += `<td>`+e+`</td>`;
                                    });
                                html += `
                            </tr>
                        `;
                    });
                    $('#all_rows').html(html);
                    document.getElementById("row_no").value = Number(val)+10;
                }
            });
        }
    </script>
@endsection