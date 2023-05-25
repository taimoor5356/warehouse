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
    td.details-control {
    background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
    cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
    }
    .lds-dual-ring {
    display: inline-block;
    width: 10px;
    height: 10px;
    }
    .lds-dual-ring:after {
    content: " ";
    display: block;
    width: 14px;
    height: 14px;
    margin: 0px 10px;
    border-radius: 50%;
    border: 2px solid #000;
    border-color: #000 transparent #000 transparent;
    animation: lds-dual-ring 1.2s linear infinite;
    }
    @keyframes lds-dual-ring {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
    }
</style>

<!-- BEGIN: Content-->
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="row">
                            <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">Reports</h2>
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
                                    <form action="{{ route('product_submit_report') }}" method="POST">
                                        @csrf
                                        <div class="row w-100">
                                            <div class="form-group col-sm-12 col-md-3">
                                                <label for="min">Product</label>
                                                    <select name="product_id" id="product_id" class="form-control select2 product_id">
                                                        <option value="">Select Product</option>
                                                        @isset($selected)
                                                        <option value="all" selected><span style="font-weight: bold">Show All</span></option>
                                                        @foreach($products as $prodInven)
                                                            <option value="@isset($product) @if($prodInven->id == $product->id) {{ $product->id }} @else {{ $prodInven->id }} @endif @else {{ $prodInven->id }} @endisset" @isset($product) @if($prodInven->id == $product->id) selected @endif @endisset>{{ $prodInven->name }}</option>
                                                        @endforeach
                                                        @endisset
                                                    </select>
                                            </div>
                                            <div class="form-group col-sm-12 col-md-3">
                                                <label for="min">Select Date Range</label>
                                                @php $current_date = \Carbon\Carbon::now(); $current_date = $current_date->format('m/d/Y'); @endphp
                                                <input type="text" class="form-control date_range" placeholder="Start Date" name="daterange" value="@isset($date1) {{ $date1 }} @endisset - @isset($date2) {{ $date2 }} @endisset">
                                                {{-- <input type="hidden" class="min" name="min" value="@isset($date1) {{ \Carbon\Carbon::parse($date1)->format('m-d-Y 00:00:00')}} @endisset">
                                                <input type="hidden" class="max" name="max" value="@isset($date2) {{ \Carbon\Carbon::parse($date2)->format('m-d-Y 00:00:00')}} @endisset"> --}}
                                            </div>
                                            <div class="form-group col-sm-12 col-md-2">
                                                <button class="btn btn-primary" type="submit" style="margin-top: 20px">Submit</button>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row w-100">
                                            <div class="form-group col-sm-12 col-md-3">
                                                <label for="min" class="mb-1">Expand All</label>
                                                <div class="form-check form-check-primary form-switch">
                                                    <span class="loader d-none"><div class="lds-dual-ring"></div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Loading...</span><input type="checkbox" name="show_all" value="0" class="form-check-input" id="show_all" disabled>
                                                </div>
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
                                            <table class="table single-product-table table-bordered">
                                                <thead>
                                                    <tr>
                                                        
                                                        <th style="width: 12%"></th>
                                                        {{-- <th>Category Name</th> --}}
                                                        <th>Product Name</th>
                                                        <th>Total Sale</th>
                                                        <th>Qty</th>
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
        $('.all_values').each(function() {
            if($(this).val() == 0) {
                $(this).closest('tr').css('display', 'none');
            }
        });
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                var min = start.format('DD-MM-YYYY');
                var max = end.format('DD-MM-YYYY');
            });
        });
        var table = $('.single-product-table').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            stateSave: true,
            bDestroy: true,
            "ajax": {
            "url": "{{ route('show_single_product_report') }}"
            },
            'columns': [
                {'className': 'details-control', 'orderable': false, 'data': null, 'defaultContent': ''},
                {data: 'name', name: 'name'},
                {data: 'price', name: 'price'},
                {data: 'pqty', name: 'pqty'}
            ],
            "drawCallback": function( settings ) {
                feather.replace();
            },
            "initComplete":function( settings, json){
                $('#show_all').attr('disabled', false);
            },
            columnDefs: [
                {
                    targets: 3,
                    render: $.fn.dataTable.render.number(',', '')
                }
            ],
            'order': [[2, 'desc']],
        });
        
        $(document).on('click', '#show_all', function(e) {
            $('.loader').removeClass('d-none');
            var _this = $(this);
            var length = $('.single-product-table tbody td.details-control').length;
            // setTimeout(() => {
            var count = 0;
            $('.single-product-table tbody td.details-control').each(function(i){
                var tr = $(this).closest('tr');
                var row = table.row( tr );
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');                
                    $('.loader').addClass('d-none');
                }
                else {
                    // Open this row
                    format(row.data(),row,tr,count,length);
                    count++;
                }
            });
            // }, 300);
            $('.all_values').each(function() {
                if($(this).val() == 0) {
                    $(this).closest('tr').css('display', 'none');
                }
            });
        });
        // Add event listener for opening and closing details
        $('.single-product-table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                // row.child( format(row.data()) ).show();
                // tr.addClass('shown');
                format(row.data(),row,tr);
            }
        });
    });
    /* Formatting function for row details - modify as you need */
    function format (d,row,tr,count,length) {
        // `d` is the original data object for the row
        var date_range = $('.date_range').val();
        var product_id = d.id;
        var html = '';
        $.ajax({
            url: '{{ route("showsingleproductreport") }}',
            type: 'GET',
            async: true,
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: product_id,
                daterange: date_range
            },
            success:function(data) {
                html += data;
                row.child( html ).show();
                tr.addClass('shown');
                $('.all_values').each(function() {
                    if($(this).val() == 0) {
                        $(this).closest('tr').css('display', 'none');
                    }
                });
                // if(count == length) {
                //     $('.loader').addClass('d-none');
                // }
            },
            complete:function(res) {
                $('.loader').addClass('d-none');
                $('.all_values').each(function() {
                    if($(this).val() == 0) {
                        $(this).closest('tr').css('display', 'none');
                    }
                });
            }
        });
        // .done((data) => {
        //     html += data;
        //     row.child( html ).show();
        //     tr.addClass('shown');
        //     // return html;
        // });
        // return html;
    }
      
</script>
@endsection
