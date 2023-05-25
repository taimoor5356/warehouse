@extends('admin.layout.app')
@section('title', 'Product Notes')
@section('datatablecss')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
@stop
@section('datepickercss')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css') }}">
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

        .order-table thead th {
            font-size: 0.7vw
        }

    </style>
    <!-- BEGIN: Content-->
    <input type="hidden" value="{{ $orderId }}" id="order_id">
    <input type="hidden" value="" id="product_id">
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">Product Notes</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Returned Products
                                    </li>
                                    <li class="breadcrumb-item">Product Notes
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-3 ">
                            <a href="#" style="float:right;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNotes">Add Notes</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header text-right" style="display:block !important;">
                    <div class="row d-flex">
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="status">Select Status</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="all">All</option>
                                <option value="1">Return to Sender</option>
                                <option value="2">Damaged</option>
                                <option value="3">Opened</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="min">Start</label>
                            <input type="text"
                                class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile"
                                placeholder="Start Date" id="min" name="min">
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="min">End</label>
                            <input type="text"
                                class="form-control flatpickr-human-friendly flatpickr-input flatpickr-mobile"
                                placeholder="End Date" id="max" name="max">
                        </div>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="table-responsive">
                        <div class="panel-body">
                        </div>
                    </div>
                    <table class="table data-table order-table table-bordered">
                        <thead>
                            <tr>
                                {{-- <th>Status</th> --}}
                                <th>Order No</th>
                                {{-- <th>Name</th> --}}
                                {{-- <th>State</th> --}}
                                {{-- <th>Product</th> --}}
                                {{-- <th>Qty</th> --}}
                                <th>Notes</th>
                                {{-- <th><input type="checkbox" id="checkAllOrders"/></th> --}}
                                {{-- <th>Date</th> --}}
                                {{-- <th>Order Number</th>
                                        <th>Customer Name</th>
                                        <th>Brand Name</th> --}}
                                {{-- <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Image</th>
                                <th>SPECIAL NOTES (IF APPLICABLE)</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Basic Tables end -->
    <!-- END: Content-->
@endsection
@section('modal')
    <div class="modal fade text-start show" id="addNotes" tabindex="-1" aria-labelledby="myModalLabel34" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel34">Add Notes</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('update_product_notes') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <label>Note: </label>
                        <div class="mb-1">
                            <textarea name="notes" id="" cols="55" rows="6"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="order_id" value="{{ $orderId }}">
                    <input type="hidden" name="product_id" value="">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">Submit</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/polyfill.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js') }}"></script>
@stop
@section('page_js') 
<script type="text/javascript">
    $(document).ready(function(){
        @if (session('success'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-success');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('success')}}");
            $('#toast-btn').click();
        @elseif (session('error'))
            $('.toast .me-auto').html('Error');
            $('.toast .toast-header').addClass('bg-danger');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('error')}}");
            $('#toast-btn').click();
        @endif
        var orderId = $('#order_id').val();
        var productId = $('#product_id').val();
        var url = "{{ route('product_notes', [':order_id', ':id']) }}";
        url = url.replace(':order_id', orderId).replace(':id', productId);
        var table = $('.data-table').DataTable({
            stateSave: true,
            "ajax": {
                "url": url,
            },
            'columns': [
                {
                    data: 'order_number',
                    name: 'order_number'
                },
                // {
                //     data: 'product_name',
                //     name: 'product_name'
                // },
                {
                    data: 'notes',
                    name: 'notes'
                },
            ],
        });
    });
</script>
@endsection