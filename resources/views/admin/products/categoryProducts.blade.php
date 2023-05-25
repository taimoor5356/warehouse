@extends('admin.layout.app')
@section('title', 'Category Products')
@section('datatablecss')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

@stop
@section('datepickercss')
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
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

        td.details-control {
            background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.details-control {
            background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
            z-index: 50000 !important;
        }

        .data-table tr:hover {
            background: rgb(223, 226, 255);
        }

        .dbRowColor {
            background-color: rgb(255, 232, 236);
        }

        .dbRowColor {
            background-color: rgb(255, 232, 236);
        }

        .dt-colresizable-table-wrapper {
            overflow: auto;
            width: 100%;
            position: relative;
        }

        .word-wrap {
            word-wrap: break-word;
        }

        .dt-colresizable {
            height: 0;
            position: relative;
            top: 0;
            z-index: 999;
        }

        .dt-colresizable .dt-colresizable-col {
            display: block;
            position: absolute;

            width: 5px;
            cursor: ew-resize;
            z-index: 1000;
        }

        .dt-colresizable-table-wrapper.dt-colresizable-with-scroller {
            overflow-x: auto;
            overflow-y: hidden;
        }

        .dt-colresizable-scroller-wrapper {
            position: absolute;
            overflow-y: hidden;
            overflow-x: hidden;
            /** FF **/
            width: 100%;
            right: 0;
        }

        .dt-colresizable-scroller-content-wrapper {
            width: 100%;
        }

        .dt-colresizable-scroller-content {
            width: 100%;
        }

        .dt-colresizable-with-scroller table thead,
        .dt-colresizable-with-scroller table tbody tr {
            table-layout: fixed;
            width: 100%;
        }

        .dt-colresizable-with-scroller table tbody {
            overflow-y: hidden;
        }

        table.data-table {
            table-layout: fixed;
            margin: 0;
        }

        table.data-table,
        table.data-table th,
        table.data-table td {}

        table.data-table thead th,
        table.data-table tbody td,
        table.data-table tfoot td {
            /* overflow: hidden; */
        }

        th.tbl_clr1 {
            background-color: rgb(253, 253, 253);
        }

        th.tbl_clr2 {
            background-color: rgb(253, 253, 253);
            left: 50px !important;
        }

        th.tbl_clr3 {
            background-color: rgb(253, 253, 253);
            left: 150px !important;
        }

        td.tbl_clr1 {
            background-color: rgb(253, 253, 253);
        }

        td.tbl_clr2 {
            background-color: rgb(253, 253, 253);
            left: 50px !important;
        }

        td.tbl_clr3 {
            background-color: rgb(253, 253, 253);
            left: 150px !important;
        }

        .products-dropdown-menu, .products-dropdown-menu .show{
            position: absolute !important;
            width: 215px;
            transform: translate(-202px, 15px) !important;
        }

        .product-image {
            transition: transform .2s; /* Animation */
            /* width: 200px;
            height: 200px; */
            /* margin: 0 auto; */
        }
        .product-image:hover {
        transform: scale(3); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
        }

    </style>
    <!-- BEGIN: Content-->
    <input type="hidden" id="c_id" value="{{ $cat_id }}" />
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-6 d-flex">
                    <h2 class="content-header-title float-start mb-0">Category Products</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item">Inventory
                            </li>
                            <li class="breadcrumb-item"><a href="/category">Categories</a>
                            </li>
                            <li class="breadcrumb-item">Category Products
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="col-6 addBtnClass">
                    @can('product_create')
                        <a href="/add_category_product/{{ $cat_id }}" style="margin-left:auto;"
                            class="btn btn-primary waves-effect waves-float waves-light">Add New Product</a>
                    @endcan
                    <a href="/reset_table" class="btn btn-success">Reset Table Size</a>
                    {{-- @can('product_delete')
                                <a href="/products/trash" style="margin-left:auto;"  class="btn btn-danger waves-effect waves-float waves-light">View Trash</a>
                            @endcan --}}
                </div>
            </div>
        </div>

    </div>
    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">

                </div>
                <div class="table-height" style="overflow-y: hidden; overflow-x: hidden">
                    <table class="table data-table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center tbl_clr1" style="z-index: 1">
                                        <img class="show_all_btn" src="{{ asset('images/details_open.png') }}" alt="">
                                </th>
                                <th class="tbl_clr2" style="z-index: 1">Category </th>
                                <th class="tbl_clr3" style="z-index: 1">Products</th>
                                <th class="text-center">Forecast Days</th>
                                <th class="text-center">Forecast Statuses</th>
                                <th>Inventory</th>
                                <th>Ordered</th>
                                <th>Otw</th>
                                <th>Weight</th>
                                <th>Unit</th>
                                <th>COG</th>
                                <th>Shipping Cost</th>
                                <th>Total Cost</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                            {{-- <tr>
                                <th style="z-index: 1">
                                    <center>
                                        <img class="show_all_btn" src="{{ asset('images/details_open.png') }}" alt="">
                                    </center>
                                </th>
                                <th style="z-index: 1">Category </th>
                                <th style="z-index: 1">Products</th>
                                <th>Forecast Days</th>
                                <th>Forecast Statuses</th>
                                <th>Inventory</th>
                                <th>Ordered</th>
                                <th>OTW</th>
                                <th>Weight</th>
                                <th>Unit</th>
                                <th>COG</th>
                                <th>Shipping Cost</th>
                                <th>Total Cost</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr> --}}
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
@section('modal')
    {{-- Add Inventory Modal --}}
    <div class="modal fade text-start show" id="inventoryModal" tabindex="-1" aria-labelledby="myModalLabel33" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Inventory</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <label>Inventory: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="Inventory" class="form-control">
                        </div>
                        <label>Description: </label>
                        <div class="mb-1">
                            <textarea name="description" id="add-inventory-description" class="form-control description-input-field" placeholder="Enter Notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                            id="add-inventory" data-bs-dismiss="modal">Add</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- / Add Inventory Modal --}}

    {{-- upcoming Inventory Modal --}}
    <div class="modal fade text-start show" id="upcomingInventoryModel" tabindex="-1" aria-labelledby="myModalLabel33"
        style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Purchase Order</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <button class="btn btn-primary add_upcoming_product" data-type="add_upcoming_inventory"
                        data-bs-toggle="modal" data-bs-target="#add_upcoming_inventory" data-product-id=""
                        style="float: right">Add Purchase Order</button>
                    <div class="clear-fix"></div>
                    <br>
                    <br>
                    <br>
                    <table class="table table-bordered" id="upcomingInventoryModalTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Purchase Date</th>
                                <th class="word-wrap">Supplier/Notes</th>
                                {{-- <th>Days Left</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="upcomingInventoryModalTableBody">

                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Otw Inventory Modal --}}
    <div class="modal fade text-start show" id="otwInventoryModel" tabindex="-1" aria-labelledby="myModalLabel37" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel37">OTW Inventory</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <button class="btn btn-primary add_upcoming_product" data-type="add_otw_inventory"
                        data-bs-toggle="modal" data-bs-target="#add_upcoming_inventory" data-product-id=""
                        style="float: right">Add OTW</button>
                    <div class="clear-fix"></div>
                    <br>
                    <br>
                    <br>
                    <table class="table table-bordered" id="otwInventoryModalTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="word-wrap">Supplier/Notes</th>
                                <th>Quantity</th>
                                <th>Purchase Date</th>
                                <th>Expected Delivery Date</th>
                                <th>Days Left</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="otwInventoryModalTableBody">

                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                        data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    {{-- \Otw Inventory Modal --}}
    <div class="modal fade text-start show" id="add_upcoming_inventory" tabindex="1" aria-labelledby="myModalLabel36"
        style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form class="form form-horizontal">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel36">Add Inventory</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Quantity</label>
                                    </div>
                                    <div class="input-group input-group-lg col-9 w-25 upcoming_data">
                                        <input type="number" id="qty" value="1" class="touchspin2 w-25 upcoming_input_field"
                                            name="qty" />
                                        <div class="error_msg text-danger d-none">This field is Required</div>
                                        @error('qty')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="hidden_product_id" value="">
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="description">Supplier/Notes</label>
                                    </div>
                                    <div class="col-sm-9 upcoming_data">
                                        <textarea name="description" id="description" cols="10" rows="2"
                                            class="form-control upcoming_input_field"></textarea>
                                        <div class="error_msg text-danger d-none">This field is Required</div>
                                        @error('description')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Purchase Date</label>
                                    </div>
                                    <div class="col-sm-9 upcoming_data">
                                        <input type="text" id="modal_date"
                                            class="form-control flatpickr-basic upcoming_input_field" name="date"
                                            placeholder="YYYY-MM-DD" />
                                        <div class="error_msg text-danger d-none">This field is Required</div>
                                        @error('date')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 expected_d_none d-none">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Expected Delivery Date</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" id="modal_expected_date" class="form-control flatpickr-basic"
                                            name="expected_delivery_date" placeholder="YYYY-MM-DD" />
                                        {{-- <div class="error_msg text-danger d-none">This field is Required</div> --}}
                                        @error('date')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-sm-9 offset-sm-3"> --}}
                            {{-- <button type="reset" class="btn btn-outline-secondary">Reset</button> --}}
                            {{-- </div> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary me-1" id="submit_upcoming_qty"
                            data-type="">Submit</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- \upcoming Inventory Modal --}}

    <div class="modal fade text-start show" id="reduceinventoryModal" tabindex="-1" aria-labelledby="myModalLabel35"
        style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel35">Reduce Inventory</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">

                        <label>Reduce Inventory: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="Inventory" class="form-control">
                        </div>
                        <label>Description: </label>
                        <div class="mb-1">
                            <textarea class="form-control description-input-field" name="reduce-inventory-description" id="reduce-inventory-description" placeholder="Enter Notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                            id="reduce-inventory" data-bs-dismiss="modal">Reduce</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('modals.modal')
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
    {{-- otw incentory modal --}}
    <div class="modal-size-default d-inline-block">
        <!-- Modal -->
        <div class="modal fade text-start" id="move_to_otw_modal" tabindex="-1" aria-labelledby="myModalLabel18"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel18">Move This to OTW</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="form form-horizontal" enctype='multipart/form-data'
                            action="{{ route('otw_inventory.store') }}" method="post">
                            {{ @csrf_field() }}
                            <div class="row">

                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <input type="hidden" name="product_id" id="productIdupcoming" />
                                            <input type="hidden" name="row_id" id="row_id_upcoming" />
                                            <input type="hidden" name="upcoming_quantity" id="upcoming_quantity" />
                                            <label class="col-form-label" for="first-name">Quantity</label>
                                        </div>
                                        <div class="input-group input-group-lg">
                                            <input type="number" id="qty" value="1" min="1" class="touchspin2"
                                                name="qty" />
                                            {{-- error message if qty greater then upcoming --}}
                                            <p class="text-danger text-bold d-none" id="error_message_otw_qty">Quantity Not
                                                be greater than Upcoming</p>
                                            @error('qty')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <label class="col-form-label" for="description">Supplier/Notes</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea name="description" id="description" cols="10" rows="2"
                                                class="form-control" value="{{ old('description') }}"></textarea>
                                            @error('description')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <label class="col-form-label" for="first-name">Purchase Date</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="fp-default" class="form-control flatpickr-basic"
                                                name="date" value="{{ old('date') }}" placeholder="YYYY-MM-DD" />
                                            @error('date')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <label class="col-form-label" for="first-name">Expected Delivery Date</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="fp-default" class="form-control flatpickr-basic"
                                                name="expected_delivery_date" value="{{ old('expected_delivery_date') }}"
                                                placeholder="YYYY-MM-DD" />
                                            @error('expected_delivery_date')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary me-1" id="move_to_otw">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- /otw modal end --}}
    {{-- move to stock modal --}}
    <div class="modal-size-default d-inline-block">
        <!-- Modal -->
        <div class="modal fade text-start" id="move_to_stock_modal" tabindex="-1" aria-labelledby="myModalLabel18"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel18">Move This to Stock</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="form form-horizontal" enctype='multipart/form-data'
                        action="{{ url('otw_inventory/storeStock') }}" method="post">
                        <div class="modal-body">
                            {{ @csrf_field() }}
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <input type="hidden" name="product_id" id="productIdotw" />
                                            <input type="hidden" name="row_id" id="row_id_otw" />
                                            <input type="hidden" name="otw_quantity" id="otw_quantity" />
                                            <label class="col-form-label" for="first-name">Quantity</label>
                                        </div>
                                        <div class="input-group input-group-lg">
                                            <input type="number" id="qty_stock" value="1" class="touchspin2"
                                                name="qty" />
                                            {{-- error message if qty_stock greater then otw --}}
                                            <p class="text-danger text-bold d-none" id="error_message_stock_qty">Quantity
                                                No be greater than Otw</p>
                                            @error('qty')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-3">
                                            <label class="col-form-label" for="first-name">Arrival Date</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="fp-default" class="form-control flatpickr-basic"
                                                name="date" value="{{ old('date') }}" placeholder="YYYY-MM-DD" />
                                            @error('date')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary me-1" id="move_to_stock">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- end modal move to stock --}}
@endsection
<script type="text/javascript">
    $(document).on('ready', function() {
        var table = $('.data-table').DataTable({
            searchDelay: 1000,
            scrollCollapse: true,
            fixedColumns: {
                left: 3,
            },
            searching: true,
            processing: true,
            serverSide: true,
            // ordering: true,
            stateSave: true,
            stateDuration: -1,
            stateSaveCallback: function(settings, stateData) {
                $.ajax({
                    url: '{{ route('settings.update') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        state: stateData
                    },
                    dataType: 'json' // json type data
                })
            },
            stateLoadCallback: function(settings) {
                var o;
                $.ajax({
                    url: '{{ route('settings_state') }}',
                    async: false,
                    dataType: 'json',
                    success: function(json) {
                        o = json;
                    }
                });
                if (o.length !== undefined)
                    return JSON.parse(o);
                return dt_state;
            },
            colResize: {
                // scrollY: 200,
                resizeTable: true
            },
            iDisplayLength: 50,
            "ajax": {
                "url": "{{ route('categorywise/products') }}",
                "data": {
                    "cid": $("#c_id").val(),
                }
            },
            columns: [
                {
                    data: 'btn',
                    name: 'btn',
                    'className': 'tbl_clr1',
                    orderable: false,
                },
                {
                    'className': 'tbl_clr2',
                    data: 'category_name',
                    name: 'category_name',
                    orderable: false,
                },
                {
                    'className': 'tbl_clr3',
                    data: 'name',
                    name: 'name',
                    orderable: false,
                },
                {
                    data: 'forecast_val',
                    name: 'forecast_val',
                    orderable: true,
                },
                {
                    data: 'forecast_statuses',
                    name: 'forecast_statuses',
                    orderable: false,
                },
                {
                    data: 'pqty',
                    name: 'pqty',
                    orderable: false,
                },
                {
                    data: 'ordered', render: $.fn.dataTable.render.number( ',', 2),
                    name: 'pqty', render: $.fn.dataTable.render.number( ',', 2),
                    orderable: false,
                },
                {
                    data: 'otw', render: $.fn.dataTable.render.number( ',', 2),
                    name: 'pqty', render: $.fn.dataTable.render.number( ',', 2),
                    orderable: false,
                },
                {
                    data: 'weight',
                    name: 'weight',
                    orderable: false,
                },
                {
                    data: 'value',
                    name: 'value',
                    orderable: false,
                },
                {
                    data: 'cog', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                    name: 'cog', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                    orderable: false,
                },
                {
                    data: 'shipping_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                    name: 'shipping_cost', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                    orderable: false,
                },
                {
                    data: 'price', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                    name: 'price', render: $.fn.dataTable.render.number( ',', '.', 2, '$' ),
                    orderable: false,
                },
                {
                    data: 'image',
                    name: 'image',
                    orderable: false,
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            "columnDefs": [{
                "targets": [0, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                "searchable": false
            }],
            "drawCallback": function(settings) {
                feather.replace();
            },
            'order': [
                [3, 'asc']
            ],
        });
        window.myDataTable = table;
        // upcoming inventory
        $(document).on('click', '.upcoming-inventory-each-item', function(e) {
            let product_id = $(this).data('prod-id');
            $('.add_upcoming_product').attr('data-product-id', product_id);
            // $('.add_product_upcoming').attr('data-product-id', product_id);
            // $('.add_product_upcoming').attr('href', '/upcoming_inventory/create/'+product_id);
            if (product_id != '') {
                let url = '{{ route('upcoming-inventory-by-item', ':id') }}';
                url = url.replace(':id', product_id);
                var upcomingTable = $('#upcomingInventoryModalTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    bDestroy: true,
                    stateSave: true,
                    ajax: {
                        url: url,
                    },
                    'columns': [
                        {
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'qty',
                            name: 'qty'
                        },
                        {
                            data: 'shipping_date',
                            name: 'shipping_date'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        // {
                        //     data: 'days_left',
                        //     name: 'days_left'
                        // },
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
                    'order': [
                        [2, 'desc']
                    ],
                });
            }
            window.upComingTable = upcomingTable;
        });
        // /upcoming inventory
        // otw inventory
        $(document).on('click', '.otw-inventory-each-item', function(e) {
            let product_id = $(this).data('prod-id');
            $('.add_upcoming_product').attr('data-product-id', product_id);
            if (product_id != '') {
                let url = '{{ route('otw-inventory-by-item', ':id') }}';
                url = url.replace(':id', product_id);
                var otwInventory = $('#otwInventoryModalTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    bDestroy: true,
                    stateSave: true,
                    ajax: {
                        url: url,
                    },
                    'columns': [{
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'qty',
                            name: 'qty'
                        },
                        {
                            data: 'shipping_date',
                            name: 'shipping_date'
                        },
                        {
                            data: 'expected_delivery_date',
                            name: 'expected_delivery_date'
                        },
                        {
                            data: 'days_left',
                            name: 'days_left'
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
                    'order': [
                        [2, 'desc']
                    ],
                });
            }
            window.otw_Inventory = otwInventory;
        });
        //otw inventory
        $(document).on('click', '.add_upcoming_product', function() {
            var type = $(this).attr('data-type');
            $('#submit_upcoming_qty').attr('data-type', type);
        });
        $(document).on('change', '#modal_product_id', function() {
            window.product = $(this).val();
        });
        $(document).on('change', '#modal_date', function() {
            window.date = $(this).val();
        });
        $(document).on('change', '#modal_expected_date', function() {
            window.expected_date = $(this).val();
        });
        $('.add_upcoming_product').click(function() {
            $('#hidden_product_id').val($(this).attr('data-product-id'));
            if ($(this).attr('data-type') == 'add_otw_inventory') {
                $('.expected_d_none').removeClass('d-none');
            } else {
                $('.expected_d_none').addClass('d-none');
            }
        });
        $(document).on('click', '#submit_upcoming_qty', function(e) {
            e.preventDefault();
            var _this = $(this);
            var product = $('#hidden_product_id').val();
            var qty = $('#qty').val();
            var description = $('#description').val();
            var expected_date = window.expected_date;
            var date = window.date;
            var count = 1;
            $('.upcoming_input_field').each(function() {
                if ($(this).val() == '') {
                    $(this).closest('.upcoming_data').find('.error_msg').removeClass('d-none');
                    count = 0;
                    return false;
                } else {
                    $(this).closest('.upcoming_data').find('.error_msg').addClass('d-none');
                    count = 1;
                }
            });
            if (count == 1) {
                var url = '';
                if (_this.attr('data-type') == 'add_upcoming_inventory') {
                    url = "{{ route('upcoming_inventory.store') }}";
                } else if (_this.attr('data-type') == 'add_otw_inventory') {
                    url = "{{ route('otw_inventory.store') }}";
                }
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_id: product,
                        qty: qty,
                        description: description,
                        date: date,
                        expected_delivery_date: expected_date
                    },
                    success: function(result) {
                        $('#add_upcoming_inventory').modal('hide');
                        if (_this.attr('data-type') == 'add_upcoming_inventory') {
                            url = "{{ route('upcoming_inventory.store') }}";
                            window.upComingTable.draw();
                            window.myDataTable.draw();
                        } else if (_this.attr('data-type') == 'add_otw_inventory') {
                            url = "{{ route('otw_inventory.store') }}";
                            window.otw_Inventory.draw();
                            window.myDataTable.draw();
                        }
                    }
                });
            }
        });
        $(document).on('click', '.show_all_btn', function() {
            var _this = $(this);
            $('.data-table tbody td.details-control').each(function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    _this.attr('src',
                        'https://datatables.net/examples/resources/details_open.png');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                    _this.attr('src',
                        'https://datatables.net/examples/resources/details_close.png');
                }
            });
        });
        // Add event listener for opening and closing details
        $(document).on('click', '.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                // Open this row
                $(this).attr('src', 'https://datatables.net/examples/resources/details_open.png');
            } else {
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
                // Close this row
                $(this).attr('src', 'https://datatables.net/examples/resources/details_close.png');
            }
        });
        $('body').click(function() {
            $('.user-page').css('display', 'none'); //hide modal
        });
        $('.user-notes, .user-page').click(function(e) {
            e.stopPropagation; // don't close modal by clicking inside modal and by clicking btn
        });
        $(document).on('click', '.add-inventory', function(e) {
            $("#add-inventory").data('prod-id', $(this).data('prod-id'));
            $('.description-input-field').val('');
            $('#inventoryModal form input[type="number"]').focus();
        });
        $(document).on('click', '#add-inventory', function(e) {
            e.preventDefault();
            var prod_id = $(this).data('prod-id');
            var url = '/product/' + prod_id + '/inventory';
            var inventory = $('#inventoryModal form input[type="number"]').val();
            var description = $('#add-inventory-description').val();
            $.post(url, {
                inventory: inventory,
                description: description,
                _token: '{{ csrf_token() }}'
            }, function(result) {
                if (result.status == "success") {
                    $('.toast .me-auto').html('Success');
                    $('.toast .toast-header').addClass('bg-success');
                } else {
                    $('.toast .me-auto').html('Error');
                    $('.toast .toast-header').addClass('bg-danger');
                }
                $('.data-table').DataTable().draw(false);
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html(result.message);
                $('#toast-btn').click();
                $('#inventoryModal form input[type="number"]').val('');
            });
        });
        $(document).on('click', '.reduce-inventory', function(e) {
            $("#reduce-inventory").data('prod-id', $(this).data('prod-id'));
            $('.description-input-field').val('');
            $('#reduceinventoryModal form input[type="number"]').focus();
        });
        $(document).on('click', '#reduce-inventory', function(e) {
            e.preventDefault();
            var prod_id = $(this).data('prod-id');
            var url = '/product/' + prod_id + '/reduce_inventory';
            var inventory = $('#reduceinventoryModal form input[type="number"]').val();
            var description = $('#reduce-inventory-description').val();
            $.post(url, {
                inventory: inventory,
                description: description,
                _token: '{{ csrf_token() }}'
            }, function(result) {
                if (result.status == "success") {
                    $('.toast .me-auto').html('Success');
                    $('.toast .toast-header').addClass('bg-success');
                } else {
                    $('.toast .me-auto').html('Error');
                    $('.toast .toast-header').addClass('bg-danger');
                }
                $('.data-table').DataTable().draw(false);
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html(result.message);
                $('#toast-btn').click();
                $('#reduceinventoryModal form input[type="number"]').val('');
            });
        });
        $(document).on('click', '.enter-pincode', function() {
            $('#enter_pin_Modal form input[type="password"]').focus();
            var href = $(this).attr('href');
            var type = $(this).data('type');
            var title = $(this).data('title_name');
            $('#enter-pin-code').attr('href', href);
            $('#enter-pin-code').data('type', type);
            if (type == 'delete') {
                $('.modal-title').html('Please Confirm if you want to Delete ' + title);
            }
        });
        $(document).on('click', '#enter-pin-code', function(e) {
            e.preventDefault();
            var pin_code = $('#inputPinCode').val();
            var type = $(this).data('type');
            if (pin_code != '') {
                $.ajax({
                    url: '{{ route('pin_code.check_pin') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        'pin_code': pin_code
                    },
                    success: function(reponse) {
                        if (type == 'add') {
                            if (reponse.status == 'success') {
                                // confirmAdd(e);
                                $('#enter_pin_Modal').modal('toggle');
                                $('#inventoryModal').modal('toggle');
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'reduce') {
                            if (reponse.status == 'success') {
                                // confirmAdd(e);
                                $('#enter_pin_Modal').modal('toggle');
                                $('#reduceinventoryModal').modal('toggle');
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'edit') {
                            if (reponse.status == 'success') {
                                confirmEdit(e);
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'delete') {
                            if (reponse.status == 'success') {
                                confirmDelete(e);
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        }
                    }
                });
            } else {
                $('#pin_error').html('Pin Required');
            }
        });
        $(document).on('click', '#enter-pin-code', function(e) {
            e.preventDefault();
            var pin_code = $('#inputPinCode').val();
            var type = $(this).data('type');
            if (pin_code != '') {
                $.ajax({
                    url: '{{ route('pin_code.check_pin') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        'pin_code': pin_code
                    },
                    success: function(reponse) {
                        if (type == 'add') {
                            if (reponse.status == 'success') {
                                // confirmAdd(e);
                                // $('#enter_pin_Modal').modal('toggle');
                                $('#inventoryModal').modal('toggle');
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'reduce') {
                            if (reponse.status == 'success') {
                                // confirmAdd(e);
                                // $('#enter_pin_Modal').modal('toggle');
                                $('#reduceinventoryModal').modal('toggle');
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'edit') {
                            if (reponse.status == 'success') {
                                confirmEdit(e);
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'delete') {
                            if (reponse.status == 'success') {
                                confirmDelete(e);
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        }
                    }
                });
            } else {
                $('#pin_error').html('Pin Required');
            }
        });
        $(document).on('keypress', '#enter-pin-code', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                var pin_code = $('#inputPinCode').val();
                var type = $(this).data('type');
                if (pin_code != '') {
                    $.ajax({
                        url: '{{ route('pin_code.check_pin') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            'pin_code': pin_code
                        },
                        success: function(reponse) {
                            if (type == 'add') {
                                if (reponse.status == 'success') {
                                    // confirmAdd(e);
                                    $('#enter_pin_Modal').modal('toggle');
                                    $('#inventoryModal').modal('toggle');
                                } else {
                                    $('#pin_error').html(reponse.msg);
                                }
                            }
                            if (type == 'reduce') {
                                if (reponse.status == 'success') {
                                    // confirmAdd(e);
                                    $('#enter_pin_Modal').modal('toggle');
                                    $('#reduceinventoryModal').modal('toggle');
                                } else {
                                    $('#pin_error').html(reponse.msg);
                                }
                            } else if (type == 'edit') {
                                if (reponse.status == 'success') {
                                    confirmEdit(e);
                                } else {
                                    $('#pin_error').html(reponse.msg);
                                }
                            } else if (type == 'delete') {
                                if (reponse.status == 'success') {
                                    confirmDelete(e);
                                } else {
                                    $('#pin_error').html(reponse.msg);
                                }
                            }
                        }
                    });
                } else {
                    $('#pin_error').html('Pin Required');
                }
            }
        });
        $(document).on('click', '.reset-inventory', function() {
            let text;
            if (confirm("Want to RESET Inventory quantity?") == true) {
                var _this = $(this);
                var productId = _this.attr('data-prod-id');
                $.ajax({
                    url: '{{ route('reset-inventory-qty') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        qty: 0,
                        inventory: 0,
                    },
                    success: function(response) {
                        if (response.success == true) {
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                            window.myDataTable.draw();
                        } else if (response.error == true) {
                            $('.toast .me-auto').html('Error');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                        }
                    }
                });
            } else {
                
            }
        });
        // on change of quantity in move to otw 
        $(document).on("keyup , change", "#qty", function() {
            // let qty = $(this).val();
            // let upcoming_quantity = $('#upcoming_quantity').val();
            // if (Number(qty) > Number(upcoming_quantity)) {
            //     $("#error_message_otw_qty").removeClass('d-none');
            //     $("#move_to_otw").attr("disabled", "disabled");
            // } else {
            //     $("#error_message_otw_qty").addClass('d-none');
            //     $("#move_to_otw").removeAttr("disabled");
            // }
            // alert(qty);
        });
        //                   
        // on change of quantity in move to stock 
        $(document).on("keyup , change", "#qty_stock", function() {
            // let qty = $(this).val();
            // let otw_quantity = $('#otw_quantity').val();
            // if (Number(qty) > Number(otw_quantity)) {
            //     $("#error_message_stock_qty").removeClass('d-none');
            //     $("#move_to_stock").attr("disabled", "disabled");
            // } else {
            //     $("#error_message_stock_qty").addClass('d-none');
            //     $("#move_to_stock").removeAttr("disabled");
            // }
            // alert(qty);
        });
        //  
        @if (session('success'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-success');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{ session('success') }}");
            $('#toast-btn').click();
        @endif
    });
    /* Formatting function for row details - modify as you need */
    /* Formatting function for row details - modify as you need */
    function format(data) {
        var inventoryHistoryURL = "{{ route('inventory_history', ':id') }}";
        inventoryHistoryURL = inventoryHistoryURL.replace(':id', data.id);
        var html = '';
        // `d` is the original data object for the row
        html += `
            <div class="row">
                <div class="col-md-4">
                    <table class="table table-bordered w-100">
                        <thead>
                            <tr>
                                <span style="font-weight: bold">Inventory History <a style="text-decoration: underline; float: right" href="`+inventoryHistoryURL+`" target="_blank">View Inventory History</a></span>`;
                        html += `</tr>
                                                <th style="padding:5px">Date</th>
                                                <th style="padding:5px">Manual Add</th>
                                                <th style="padding:5px">Batch Edited</th>
                                                <th style="padding:5px">Cancelled Order</th>
                                                <th style="padding:5px">Supplier Inventory Received</th>
                                                <th style="padding:5px">Returned</th>
                                                <th style="padding:5px">Return Edited</th>
                                                <th style="padding:5px">Manual Deduct</th>
                                                <th style="padding:5px">Sales</th>
                                                <th style="padding:5px">Total Inventory</th>
                                        </thead>
                                        <tbody>
                                            `;
                $.ajax({
                    async: false,
                    url: "{{ route('get_product_history') }}",
                    type: 'GET',
                    data: {
                        product_id: data.id
                    },
                    success:function(d)
                    {
                        var totalSale = 0;
                        for (var i = 0; i < d.getarr.length; i++) {
                            if (d.getarr[i].manual_add > 0) {
                                totalSale += d.getarr[i].manual_add;
                            }
                            if (d.getarr[i].edit_batch_qty > 0) {
                                totalSale += d.getarr[i].edit_batch_qty;
                            }
                            if (d.getarr[i].cancel_order_add > 0) {
                                totalSale += d.getarr[i].cancel_order_add;
                            }
                            if (d.getarr[i].supplier_inventory_received > 0) {
                                totalSale += d.getarr[i].supplier_inventory_received;
                            }
                            if (d.getarr[i].return_add > 0) {
                                totalSale += d.getarr[i].return_add;
                            }
                            if (d.getarr[i].return_edited > 0) {
                                totalSale += d.getarr[i].return_edited;
                            }
                            if (d.getarr[i].manual_reduce > 0) {
                                totalSale -= d.getarr[i].manual_reduce;
                            }
                            if (d.getarr[i].sales > 0) {
                                totalSale -= d.getarr[i].sales;
                            }
                            html += `
                                                <tr>
                                                    <td style="padding:5px">`+d.getarr[i].date+`</td>
                                                    <td style="padding:5px">`+d.getarr[i].manual_add+`</td>
                                                    <td style="padding:5px">`+d.getarr[i].edit_batch_qty+`</td>
                                                    <td style="padding:5px">`+d.getarr[i].cancel_order_add+`</td>
                                                    <td style="padding:5px">`+d.getarr[i].supplier_inventory_received+`</td>
                                                    <td style="padding:5px">`+d.getarr[i].return_add+`</td>
                                                    <td style="padding:5px">`+d.getarr[i].return_edited+`</td>
                                                    <td style="padding:5px">`+d.getarr[i].manual_reduce+`</td>`;
                                let editBatchQty = Number(d.getarr[i].edit_batch_qty);
                                let cancelOrder = Number(d.getarr[i].cancel_order_add);
                                let sales = Number(d.getarr[i].sales);
                                let resultantSale = sales - cancelOrder - editBatchQty;
                                if (resultantSale < 0) {
                                    resultantSale = 0;
                                }
                            html += `<td style="padding:5px">`+Number(resultantSale)+`</td>
                                                    <td style="padding:5px">
                                                        `+d.getarr[i].total+`
                                                    </td>
                                                </tr>
                                            `;
                        }
                        return html;
                    }
                });
                html += `</tbody>
                            </table>
                        </div>`;
        html += `<div class="col-md-2">
            <table class="table table-bordered w-100">
                <thead>
                    <tr>
                        <span style="font-weight: bold">Last 12 days sales history</span>
                        <th style="padding:5px">Date</th>
                        <th style="padding:5px">Qty</th>
                    </tr>
                </thead>
                <tbody>`;
        var url = '{{ route('get_product_sales', ':id') }}';
        url = url.replace(':id', data.id);
        $.ajax({
            async: false,
            url: url,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                productId: data.id,
                forecastDays: data.forecastDays,
            },
            success: function(resp) {
                for (var j = 0; j < resp.length; j++) {
                    html += `
                                            <tr>
                                                <td style="padding:5px">` + resp[j].date + `</td>
                                                <td style="padding:5px">` + resp[j].sale + `</td>
                                            </tr>
                                        `;
                }
                return html;
            }
        });
        html += `
                        </tbody>
                    </table>
                </div>
            </div>
            `;
        return html;
    }

    function confirmDelete(e) {
        var url = e.currentTarget.getAttribute('href');
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this product!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-outline-secondary ms-1'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                window.location.replace(url);
            }
        });
    }

    $(document).ready(function() {
        $('.touchspin2').TouchSpin({
            min: 1,
            max: 100000000,
            step: 1,
        });
    });
    // for check to move into otw
    $('body').on("click", ".moveotw", function() {

        var productId = $(this).data('product-id-upcoming');
        var rowId = $(this).data('row-id-upcoming');
        var upcomingQuantity = $(this).data('upcoming-quantity');
        $("#productIdupcoming").val(productId);
        $("#row_id_upcoming").val(rowId);
        $("#upcoming_quantity").val(upcomingQuantity);
    });
    //    move otw to stock
    $('body').on("click", ".movestock", function() {
        var productIdotw = $(this).data('product-id-otw');
        var rowId = $(this).data('row-id-otw');
        var otwQuantity = $(this).data('otw-quantity');
        $("#productIdotw").val(productIdotw);
        $("#row_id_otw").val(rowId);
        $("#otw_quantity").val(otwQuantity);
    });

    function myFunction() {

        var r = confirm("Do you really want to delete this inventory?");
        if (r == true) {
            return true;
        } else {
            return false;
        }
        document.getElementById("demo").innerHTML = txt;
    }
</script>

@endsection


@section('datatablejs')
<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>

<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script src="https://cdn.datatables.net/fixedheader/3.2.1/js/dataTables.fixedHeader.min.js"></script>

<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>

<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/resize.js') }}"></script>
@stop
@section('datepickerjs')
<script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
@stop
