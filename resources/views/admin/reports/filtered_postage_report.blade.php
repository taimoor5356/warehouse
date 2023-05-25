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
                            <a href="/postage-report" style="margin-left:auto;"  class="btn btn-primary waves-effect waves-float waves-light">View All</a> 
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
                        <form action="{{ route('filter_postage_reporting') }}" method="POST">
                            @csrf
                            <div class="row w-100">
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="time_duration">Select Duration</label>
                                    <select name="time_duration" id="time_duration" class="form-control select2">
                                        <option value="" selected disabled>- Select Duration -</option>
                                        <option value="all">All</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="last_week">Last Week</option>
                                        <option value="last_month">Last Month</option>
                                        <option value="last_six_months">Last 6 Months</option>
                                        <option value="last_year">Last Year</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="min">Select Date Range</label>
                                    <input type="text" class="form-control date_range" placeholder="Start Date" name="daterange" value="{{ old('daterange') }}">
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
                                        <th class="text-center"><div style="width: 100px">Date</div></th>
                                        @foreach ($customers as $customer)
                                            @isset($customer)
                                                <th class="text-center">
                                                    <div 
                                                    {{-- @if (Str::length($product->name) > 18)
                                                    style="width: 200px" 
                                                    @else
                                                    style="width: 100px"
                                                    @endif  --}}
                                                    style="width: {{Str::length($customer->customer_name) * 11}}px">{{ $customer->customer_name }}</div>
                                                </th>
                                            @endisset
                                        @endforeach
                                        <th>
                                            Total
                                        </th>
                                    </thead>
                                    <tbody>
                                        @isset($dateWisePostage)
                                            <tr>
                                                <td class="text-center"> Total</td>
                                                @php $totalCount = 0; @endphp
                                                @foreach ($customerPostageTotal as $custPostage)
                                                    <td class="text-center"> 
                                                        {{ $custPostage['qty'] }}
                                                    </td>
                                                    @php $totalCount += $custPostage['qty']; @endphp
                                                @endforeach
                                                <td class="text-center">
                                                    {{ $totalCount }}
                                                </td>
                                            </tr>
                                            @foreach($dateWisePostage as $key => $data)
                                                <tr>
                                                    <td class="text-center"> {{date('M, d-Y', strtotime($data['date']))}} </td>
                                                    @php $total = 0; @endphp
                                                    @isset($data['customers'])
                                                        @foreach($data['customers'] as $cust)
                                                        <td class="text-center"> {{ $cust['qty'] }}</td>
                                                            @php $total += $cust['qty']; @endphp
                                                        @endforeach
                                                    @endisset
                                                    <td class="text-center"> 
                                                        {{ $total }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endisset
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
        $(document).ready(function() {
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
    </script>
@endsection