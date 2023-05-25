@extends('admin.layout.app')
@section('title', 'Returned Products')
@section('datatablecss')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/pages/app-invoice.css') }}">
@stop

@section('datepickercss')
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css') }}">
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
    <input type="hidden" value="{{ $id }}" id="order_id">
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <h2 class="content-header-title float-start mb-0">Returned Products</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Customers
                                    </li>
                                    <li class="breadcrumb-item">Returned Products
                                    </li>
                                </ol>
                            </div>
                        </div>
                        {{-- <div class="col-3 ">
                            <a href="#" style="float:right;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNotes">Add Notes</a>
                        </div> --}}
                        {{-- <div class="col-3 ">
                            <a href="{{ route('get-return-pdf', [$id, '']) }}" style="float:right;"
                                class="btn btn-primary">Invoice View</a>

                            <a href="{{ route('get-return-pdf', [$id, 'download']) }}" class="btn btn-primary">Invoice
                                Download</a>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-right" style="display:block !important;">
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
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <div class="panel-body">
                        </div>
                    </div>
                    <table class="table data-table order-table table-bordered">
                        <thead>
                            <tr>
                                <th>Status</th>
                                {{-- <th>Order No</th>
                                <th>Name</th>
                                <th>State</th> --}}
                                <th>Product</th>
                                <th>Qty</th>
                                <th>COG</th>
                                <th>Selling Cost</th>
                                {{-- <th>Notes</th> --}}
                                {{-- <th><input type="checkbox" id="checkAllOrders"/></th> --}}
                                {{-- <th>Date</th> --}}
                                {{-- <th>Order Number</th>
                                        <th>Customer Name</th>
                                        <th>Brand Name</th> --}}
                                {{-- <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Image</th> --}}
                                <th>SPECIAL NOTES (IF APPLICABLE)</th>
                                <th>Actions</th>
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
                            <textarea name="description" id="" cols="55" rows="6"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="order_id" value="{{ $id }}">
                    <input type="hidden" name="product_id" class="modalProductId" value="">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">Submit</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="uploadFile" tabindex="-1" aria-labelledby="myModalLabel34" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel34">Enter Note</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('stored_files.store') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <label>Note: </label>
                        <div class="mb-1">
                            <textarea name="" id="" cols="54" rows="5"></textarea>
                            <input type="hidden" name="" value="">
                        </div>
                    </div>
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
    <script src="{{ URL::asset('admin/app-assets/js/scripts/tables/range_dates.js') }}"></script>
@stop
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/extensions/polyfill.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js') }}"></script>
@stop
@section('page_js')
    <script type="text/javascript">
        var minDate, maxDate;

        function myFunction() {
            var r = confirm("Do you really want to delete this order?");
            if (r == true) {
                return true;
            } else {
                return false;
            }
            document.getElementById("demo").innerHTML = txt;
        }
        $(document).ready(function() {
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
            var url = "{{ route('view_returned_products', ':id') }}";
            url = url.replace(':id', orderId);
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                stateSave: true,
                bDestroy: true,
                ajax: {
                    url: url,
                    data: function(d) {
                        d.status = $('#status').val();
                        d.min_date = $('#min').val();
                        d.max_date = $('#max').val();
                        d.customer = $('#customer').val();
                        d.brand = $('#brand').val();
                        d.order_number = $('#order').val();
                    }
                },
                columns: [
                    
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        width: '15%'
                    },
                    {
                        data: 'price', // cog
                        name: 'price' // cog
                    },
                    {
                        data: 'selling_cost', // selling cost
                        name: 'selling_cost' // selling cost
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                "drawCallback": function(settings) {
                    feather.replace();
                },
            });

            $(document).on('click', '.addNoteButton', function() {
                $('.modalProductId').val($(this).attr('data-product-id'));
            });

            // Custom filtering function which will search data in column four between two values

            $('#searchRecords').on('click', function(e) {
                table.draw(false);
                e.preventDefault();
            });
            // Create date inputs
            minDate = new Date($('#min'), {
                format: 'MMMM Do YYYY'
            });
            maxDate = new Date($('#max'), {
                format: 'MMMM Do YYYY'
            });

            // Refilter the table
            $('#min, #max').on('change', function() {
                table.draw(false);
            });

            // Refilter the table by status
            $('#status').on('change', function() {
                table.draw(false);
            });

            // Refilter the table by customer
            $('#customer').on('change', function() {
                table.draw(false);
            });

            // Refilter the table by status
            $('#status').on('change', function() {
                table.draw(false);
            });
        });
    </script>
@endsection
