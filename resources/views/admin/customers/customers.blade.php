@extends('admin.layout.app')
@section('title', 'Manage Customers')
@section('datatablecss')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

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

        /* Hide scrollbar for Chrome, Safari and Opera */

        .customer_products_table th {
            border: 1px solid lightgray;
        }

        .customer_products_table tr {
            border: 1px solid lightgray;
        }

        .customer_products_table td {
            border: 1px solid lightgray;
        }

        /* Hide scrollbar for IE, Edge and Firefox */

        /* .dataTables_length{float: left;padding-left: 20px;}
        .dataTables_filter{padding-right:20px;}
        .dataTables_info{padding-left: 20px !important; padding-bottom:30px !important;}
        .dataTables_paginate{padding-right: 20px !important;} */
        td.details-control {
            background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.details-control {
            background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
        }

        input[type="checkbox"] {
            background-color: #fff;
        }

    </style>
    @include('modals.modal')
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-7">
                            <h2 class="content-header-title float-start mb-0">Manage Customers</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Customers
                                    </li>
                                    <li class="breadcrumb-item">Manage Customers
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-5 addBtnClass">
                            @can('customer_create')
                                <a href="/customers/create" style="margin-left:auto;"
                                    class="btn btn-primary waves-effect waves-float waves-light">Add New
                                    Customer</a>
                            @endcan
                            @can('customer_delete')
                                <a href="/customers/trash" style="margin-left:auto;"
                                    class="btn btn-danger waves-effect waves-float waves-light">View Trash</a>
                            @endcan


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

                </div>
                <div class="table-responsive" style="min-height: 350px">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                {{-- <th style="width: 12%"><center><img class="show_all_btn" src="https://datatables.net/examples/resources/details_open.png" alt=""></center></th> --}}
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th class="text-center">Service Charges</th>
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
    <div class="modal fade text-start show" id="labelsModal" tabindex="" aria-labelledby="myModalLabel33" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Labels</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <label>Labels: </label>
                        <div class="mb-1">
                            <input type="number" maxlength="4"
                                onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required min="1"
                                placeholder="" name="label_qty" class="form-control" id="qtyPrice">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-labels"
                            data-brand-id="" data-customer_id="" data-bs-dismiss="modal" data-product-id="">Add</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="reducelabelsModal" tabindex="" aria-labelledby="myModalLabel35" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel35">Reduce Labels</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">

                        <label>Labels: </label>
                        <div class="mb-1">
                            <input type="number" maxlength="4"
                                onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required min="1"
                                placeholder="" name="label_qty" class="form-control" id="reduceqtyPrice">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                            id="reduce-labels" data-brand-id="" data-customer_id="" data-bs-dismiss="modal"
                            data-product-id="">Submit</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="labelCostModal" tabindex="" aria-labelledby="myModalLabel34" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel34">Add Label Charges</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">

                        <label>Labels Cost: </label>
                        <div class="mb-1">
                            <input type="number" maxlength="4"
                                onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required min="1"
                                placeholder="" name="label_cost" class="form-control" id="labelCost">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                            id="add-label-cost" data-brand-id="" data-customer_id="" data-bs-dismiss="modal"
                            data-product-id="">Add</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
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
    <div class="modal fade text-start show" id="show_cust_product_modal" tabindex="-1"
        aria-labelledby="show_cust_product_modal33" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="show_cust_product_modal33">Customer's Products</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="col-12 mb-1">
                        <div class="mb-1 row p-2">
                            <div class="col-md-2">
                                <label for="col-form-label">Products</label>
                            </div>
                            <div class="col-md-10">
                                <table class="table table-bordered table-striped cust_prod_data_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%">Product</th>
                                            <th style="width: 20%">Purchasing Cost</th>
                                            <th style="width: 10%">Weight</th>
                                            <th style="width: 15%">Selling Cost</th>
                                            <th style="width: 15%">Seller Cost</th>
                                            <th style="width: 25%">Label Status</th>
                                            <th style="width: 15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cust_products_table">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row p-2">
                            <div class="col-md-2">
                                <p>Add Products</p>
                            </div>
                            <div class="col-md-10">
                                <form id="form_ID">
                                    <table class="table table-bordered table-striped products-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%">Product</th>
                                                <th style="width: 20%">Purchasing Cost</th>
                                                <th style="width: 10%">Weight</th>
                                                <th style="width: 15%">Selling Cost</th>
                                                <th style="width: 15%">Seller Cost</th>
                                                <th style="width: 25%">Label Status</th>
                                                <th style="width: 15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="addProductTable">
                                            <tr>
                                                <td>
                                                    <select name="products[]" class="cust_product form-select select2"
                                                        id="cust_product-list">

                                                    </select>
                                                    <div class="product-error text-danger"></div>
                                                </td>
                                                <td>
                                                    <div class="cust_prod_purchasing_price font-weight-bold text-success">$
                                                        0.00 </div>
                                                </td>
                                                <td>
                                                    <div class="total-weight font-weight-bold text-success"><span
                                                            class="product-weight">0.00 </span><span
                                                            class="unit">oz</span></div>
                                                    <input type="hidden" name="" class="p-weight" value="">
                                                </td>
                                                <td>
                                                    <div class="input-icon">
                                                        <input type="number" maxlength="4"
                                                            onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;"
                                                            required name="selling_price[]"
                                                            class="selling_price form-control add_selling_price" step="0.01"
                                                            placeholder="Selling Cost" value="">
                                                        <i>$</i>
                                                    </div>
                                                    <div class="sellingMsg d-none" style="color: red; font-size: 12px">
                                                        Should be greater than Purchasing Cost</div>
                                                </td>
                                                <td>
                                                    <div class="col-sm-9">
                                                        <div class="form-check form-switch form-check-primary">
                                                            <input type="checkbox" name="seller_cost_status[]" checked
                                                                class="form-check-input addSellerCostSwitch"
                                                                style="font-size: 30px">
                                                            <label class="form-check-label">
                                                                <span class="switch-icon-left"
                                                                    style="font-size: 9px; margin-top: 6px;">ON</span>
                                                                <span class="switch-icon-right"
                                                                    style="font-size: 9px; margin-top: 6px; margin-left: -6px; color: black">OFF</span>
                                                            </label>
                                                        </div>
                                                        {{-- <div class="form-check form-check-primary form-switch">
                                                            <input type="checkbox" name="seller_cost_status[]" checked class="form-check-input addSellerCostSwitch">
                                                        </div> --}}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-sm-9">
                                                        <div class="form-check form-check-primary form-switch">
                                                            <input type="checkbox" name="is_active[]"
                                                                class="form-check-input labelSwitch">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="m-auto btn btn-primary shadow add_cust_prod_btn"
                                                        data-customer_id="" type="button">Add</button>
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start" id="cust_prod_selling_price" tabindex="-1"
        aria-labelledby="cust_prod_selling_price12" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="cust_prod_selling_price12">Selling Price</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <label>Selling Price: </label>
                        <div class="mb-1">
                            <input type="number" maxlength="4"
                                onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required min="0"
                                placeholder="Enter Selling Price" name="selling_price" class="form-control"
                                id="modal_selling_price" data-product_id="" data-customer_id="">
                        </div>
                        <div class="selling_price_err text-danger d-none">Can't add negative value</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                            id="save_product_selling_price" data-bs-dismiss="modal">Save</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start" id="add_brand_modal" tabindex="-1" aria-labelledby="add_brand_modal12" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_brand_modal12">Customer's Brand</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="row">
                    <div class="col-md-12 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Brand</h4>
                            </div>
                            <div class="card-body">
                                <form class="form form-horizontal" id="brand_form" enctype='multipart/form-data'
                                    action="{{ route('brands.store') }}" method="post">
                                    {{ @csrf_field() }}
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="mb-1 row">
                                                <div class="col-sm-3">
                                                    <label class="col-form-label" for="catselect">Customer Name</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <select name="customer_id" class="form-select select2 customer_select"
                                                        id="basicSelect">
                                                        <option selected value=""></option>
                                                    </select>
                                                    @error('customer_id')
                                                        <p class="text-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1 row">
                                                <div class="col-sm-3">
                                                    <label class="col-form-label" for="catselect">Enter Brand Name</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="brand" value=""
                                                        name="brand" />
                                                    @error('brand')
                                                        <p class="text-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="button" data-bs-dismiss="modal" id="submit_btn"
                                                class="btn btn-primary me-1">Submit</button>
                                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop

@section('modal')
@endsection

@section('page_js')

    <script type="text/javascript">
        $(document).ready(function() {
            @if (session('success'))
                $('.toast .toast-header').removeClass('bg-danger')
                $('.toast .me-auto').html('Success');
                $('.toast .toast-header').addClass('bg-success');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{ session('success') }}");
                $('#toast-btn').click();
            @elseif (session('error'))
                $('.toast .toast-header').removeClass('bg-success')
                $('.toast .me-auto').html('Under Construction');
                $('.toast .toast-header').addClass('bg-danger');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{ session('error') }}");
                $('#toast-btn').click();
            @endif
            $('#show_product_modal').on('hidden.bs.modal', function(e) {
                window.view_product_datatable.destroy();
            });
            $('#show_cust_product_modal').on('hidden.bs.modal', function(e) {
                window.view_cust_product_datatable.destroy();
            });
            $(document).on('click', '.add_cust_brand', function() {
                var customer_id = $(this).attr('data-customer_id');
                var customer_name = $(this).attr('data-customer_name');
                $('.customer_select').html('<option value="' + customer_id + '">' + customer_name +
                    '</option>');
            });
            $(document).on('click', '#submit_btn', function() {
                $('#brand_form').submit();
            });
            $(document).on('click', '.edit_product_selling_price', function() {
                $('#modal_selling_price').attr('data-customer_id', '');
                $('#modal_selling_price').attr('data-product_id', '');
                var customer_id = $(this).attr('data-customer_id');
                var product_id = $(this).attr('data-product_id');
                $('#modal_selling_price').attr('data-customer_id', customer_id);
                $('#modal_selling_price').attr('data-product_id', product_id);
            });
            $(document).on('click', '#save_product_selling_price', function(e) {
                e.preventDefault();
                var selling_price = $('#modal_selling_price').val();
                var customer_id = $('#modal_selling_price').attr('data-customer_id');
                var product_id = $('#modal_selling_price').attr('data-product_id');
                if (Number(selling_price) < 0) {
                    $('.selling_price_err').removeClass('d-none');
                    return false;
                } else {
                    $('#edit_product').hide();
                    $.ajax({
                        url: '{{ route('update_customer_prod_selling_price') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            selling_price: selling_price,
                            customer_id: customer_id,
                            product_id: product_id
                        },
                        success: function(response) {
                            window.view_cust_product_datatable.draw(false);
                        }
                    });
                }
            });
            // Pin Code
            $(document).on('click', '.enter-pincode', function() {
                $('#enter_pin_Modal form input[type="password"]').focus();
                var href = $(this).attr('href');
                var type = $(this).data('type');
                var title = $(this).data('title_name');
                $('#enter-pin-code').attr('href', href);
                $('#enter-pin-code').data('type', type);
                if (type == 'delete') {
                    $('.modal-title').html('Please Confirm if you want to Delete ' + title);
                } else {
                    $('.modal-title').html('Enter Pin');
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
                            if (type == 'delete') {
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
                                        $('#enter_pin_Modal').modal('toggle');
                                        $('#inventoryModal').modal('toggle');
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
            // Modal with Products
            $(document).on('click', '.view_products', function() {
                var customer_id = $(this).attr('data-customer_id');
                var brand_id = $(this).attr('data-brand_id');
                $('.add_prod_btn').attr('data-customer_id', customer_id);
                $('.add_prod_btn').attr('data-brand_id', brand_id);
                var url = '{{ route('edit_customer_brand') }}';
                var url_2 = 'customer/' + customer_id + '/brand/' + brand_id + '/edit';
                var html = '';
                $.ajax({
                    url: url_2,
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: customer_id,
                        brand_id: brand_id
                    },
                    success: function(res) {
                        html += `
                                <option>Select Product</option>`;
                        for (var i = 0; i < res.length; i++) {
                            html += `
                                <option value="` + res[i].prod_id + `">` + res[i].prod_name + `</option>
                            `;
                        }
                        $('.product').html(html);
                    }
                });
                var html = '';
                var table = $('.data_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    stateSave: true,
                    bDestroy: true,
                    ajax: {
                        url: url,
                        data: {
                            customer_id: customer_id,
                            brand_id: brand_id
                        }
                    },
                    columns: [{
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'label_cost',
                            name: 'label_cost'
                        },
                        {
                            data: 'labels_qty',
                            name: 'labels_qty'
                        },
                        {
                            data: 'forecast_labels',
                            name: 'forecast_labels'
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
                    }
                });
            });
            // Select Product to Add
            $(document).on('change', '.product', function() {
                var id = $(this).val();
                var context = $(this);
                var customer_id = $('#customer_id').val();
                var brand_id = $('#brand_id').val();
                var url = '{{ route('get_product_labels', ':id') }}';
                var url2 = '{{ route('getProductstoCustomer') }}';
                url2 = url2.replace(':id', id);
                url = url.replace(':id', id);
                if (id == "") {

                } else {
                    $.ajax({
                        url: url2,
                        type: 'GET',
                        data: {
                            _token: '{{ csrf_token() }}',
                            'product_id': id
                        },
                        success: function(res) {
                            if (res) {
                                context.closest('tr').find('.purchasing-price').html('$ ' +
                                    Number(res.price).toFixed(2));
                                // context.closest('tr').find('.selling_price').val(Number(res.price).toFixed(2));
                                context.closest('tr').find('.product-weight').html(Number(res
                                    .weight).toFixed(2));
                                context.closest('tr').find('.p-weight').val(Number(res.weight)
                                    .toFixed(2));
                            } else {
                                $('.product-error').html(res.message);
                            }
                        }
                    });
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: {
                            _token: '{{ csrf_token() }}',
                            'customer_id': customer_id,
                            'brand_id': brand_id,
                        },
                        success: function(response) {
                            if (response > 0) {
                                context.closest('tr').find('.label_qty').html(response);
                                context.closest('tr').find('.label-qty-val').val(response);
                            } else {
                                context.closest('tr').find('.label_qty').html(response);
                                context.closest('tr').find('.label-qty-val').val(0);
                            }
                        }
                    });
                }
            });
            $(document).on('change', '.cust_product', function() {
                var id = $(this).val();
                var context = $(this);
                var customer_id = $('#customer_id').val();
                var brand_id = $('#brand_id').val();
                var url = '{{ route('get_product_labels', ':id') }}';
                var url2 = '{{ route('getProductstoCustomer') }}';
                url2 = url2.replace(':id', id);
                url = url.replace(':id', id);
                if (id == "") {

                } else {
                    $.ajax({
                        url: url2,
                        type: 'GET',
                        data: {
                            _token: '{{ csrf_token() }}',
                            'product_id': id
                        },
                        success: function(res) {
                            if (res) {
                                context.closest('tr').find('.cust_prod_purchasing_price').html(
                                    '$ ' + Number(res.price).toFixed(2));
                                context.closest('tr').find('.selling_price').val('0.00');
                                context.closest('tr').find('.product-weight').html(Number(res
                                    .weight).toFixed(2));
                                context.closest('tr').find('.p-weight').val(Number(res.weight)
                                    .toFixed(2));
                            } else {
                                $('.product-error').html(res.message);
                            }
                        }
                    });
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: {
                            _token: '{{ csrf_token() }}',
                            'customer_id': customer_id,
                            'brand_id': brand_id,
                        },
                        success: function(response) {
                            if (response > 0) {
                                context.closest('tr').find('.label_qty').html(response);
                                context.closest('tr').find('.label-qty-val').val(response);
                            } else {
                                context.closest('tr').find('.label_qty').html(response);
                                context.closest('tr').find('.label-qty-val').val(0);
                            }
                        }
                    });
                }
            });
            // Switches / Buttons to Add Product
            $(document).on('keyup', '.selling_price', function() {
                var purchasing_cost = $(this).closest('tr').find(
                    '.purchasing-price, .cust_prod_purchasing_price').html();
                purchasing_cost = purchasing_cost.substring(1);
                purchasing_cost = purchasing_cost.trim(purchasing_cost);
                // if ($(this).closest('tr').find('.addSellerCostSwitch').prop('checked') == true) {
                //     if (Number($(this).val()) <= Number(purchasing_cost)) {
                //         $(this).closest('tr').find('.selling_price').css('border', '1px solid red');
                //         $(this).closest('tr').find('.sellingMsg').removeClass('d-none');
                //         count = 0;
                //     } else {
                //         $(this).closest('tr').find('.selling_price').css('border', '1px solid lightgray');
                //         $(this).closest('tr').find('.sellingMsg').addClass('d-none');
                //     }
                // }
            });
            $(document).on('change', '.addSellerCostSwitch', function() {
                var _this = $(this);
                var purchasingCost = _this.closest('tr').find('.cust_prod_purchasing_price').html();
                purchasingCost = purchasingCost.substring(1);
                purchasingCost = purchasingCost.trim(purchasingCost);
                var sellingPrice = _this.closest('tr').find('.add_selling_price').val();
                if (_this.prop('checked') == true) {
                    _this.closest('tr').find('.add_selling_price').attr('disabled', false);
                    // if (Number(sellingPrice) <= Number(purchasingCost)) {
                    //     _this.closest('tr').find('.add_selling_price').css('border', '1px solid red');
                    //     _this.closest('tr').find('.sellingMsg').removeClass('d-none');
                    // } else {
                    //     _this.closest('tr').find('.add_selling_price').css('border', '1px solid lightgray');
                    //     _this.closest('tr').find('.sellingMsg').addClass('d-none');
                    // }
                } else {
                    _this.closest('tr').find('.add_selling_price').attr('disabled', true);
                    _this.closest('tr').find('.add_selling_price').val('0.00');
                    _this.closest('tr').find('.add_selling_price').css('border', '1px solid lightgray');
                    _this.closest('tr').find('.sellingMsg').addClass('d-none');
                }
            });
            $(document).on('change', '.labelSwitch', function() {
                var customer_id = $(this).data('customer_id');
                var prodId = $(this).data('labelpid');
                var status = 1; // 1 = off
                if ($(this).prop('checked') == true) { // if on
                    status = 0;
                    $.ajax({
                        url: '{{ route('set_label_status') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_id: customer_id,
                            product_id: prodId,
                            status: status
                        },
                        success: function(response) {

                        }
                    });
                } else {
                    status = 1;
                    $.ajax({
                        url: '{{ route('set_label_status') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_id: customer_id,
                            // brand_id: brandId,
                            product_id: prodId,
                            status: status
                        },
                        success: function(response) {

                        }
                    });
                }
            });
            $(document).on('click', '.add_cust_prod_btn', function(e) {
                e.preventDefault();
                var _this = $(this);
                var checkProd = 1;
                $('.cust_product').each(function() {
                    if ($(this).val() == '' || $(this).val() == null || $(this).val() ==
                        'Select Product') {
                        $(this).closest('tr').find('.product-error').html('Required');
                        checkProd = 0;
                        return false;
                    } else {
                        $(this).closest('tr').find('.product-error').html('');
                    }
                });
                if (checkProd == 0) {
                    return false;
                }
                var count = 1;
                var count2 = 1;
                $('.cust_prod_purchasing_price').each(function() {
                    var This = $(this);
                    var selling_cost = This.closest('tr').find('.selling_price').val();
                    var purchasing_cost = This.html();
                    purchasing_cost = purchasing_cost.substring(1);
                    purchasing_cost = purchasing_cost.trim(purchasing_cost);
                    if (This.closest('tr').find('.addSellerCostSwitch').prop('checked') == true) {
                        // if (Number(selling_cost) <= Number(purchasing_cost)) {
                        //     This.closest('tr').find('.selling_price').css('border', '1px solid red');
                        //     This.closest('tr').find('.sellingMsg').removeClass('d-none');
                        //     count = 0;

                        // } 
                        // else {
                        //     This.closest('tr').find('.selling_price').css('border', '1px solid lightgray');
                        //     This.closest('tr').find('.sellingMsg').addClass('d-none');
                        // }
                    }
                });
                if (count == 1) {
                    var customer_id = _this.attr('data-customer_id');
                    var prodIds = [];
                    var labelsStatuses = [];
                    var sellerCostStatuses = [];
                    var selling_costs = [];
                    var labelStatus = 1;
                    var sellerCostStatus = 1;
                    var html = '';
                    var url = '{{ route('save_customer_product', ':id') }}';
                    url = url.replace(':id', customer_id);
                    $('.cust_product').each(function() {
                        prodIds.push($(this).val());
                        if ($(this).closest('tr').find('.labelSwitch').prop('checked') == true) {
                            labelStatus = 0;
                        } else {
                            labelStatus = 1;
                        }
                        if ($(this).closest('tr').find('.addSellerCostSwitch').prop('checked') ==
                            true) {
                            sellerCostStatus = 1;
                        } else {
                            sellerCostStatus = 0;
                        }
                        labelsStatuses.push(labelStatus);
                        sellerCostStatuses.push(sellerCostStatus);
                        if ($(this).closest('tr').find('.sellerCostStatus').prop('checked') ==
                            false) {
                            selling_costs.push('0');
                        } else {
                            selling_costs.push($(this).closest('tr').find('.selling_price').val());
                        }
                    });
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_id: customer_id,
                            prod_ids: prodIds,
                            labelStatus: labelsStatuses,
                            sellerCostStatus: sellerCostStatuses,
                            selling_cost: selling_costs
                        },
                        success: function(response) {
                            html += `
                            <tr>
                                <td>` + response.data.product_name + `</td>
                                <td>
                                    <div class="font-weight-bold">$ ` + response.data.purchasing_cost + ` </div>
                                </td>
                                <td>
                                    <div class="font-weight-bold">` + response.data.weight + ` oz</div>
                                </td>
                                <td>
                                    <div class="font-weight-bold"> $ ` + response.data.selling_price + `</div>
                                </td>
                                <td>
                                    <div class="col-sm-9">
                                        <div class="form-check form-switch form-check-primary">
                                            <input type="checkbox"`;
                            if (response.data.seller_cost_status == 1) {
                                html += ` checked `;
                            }
                            html +=
                                `name="seller_cost_status[]" class="form-check-input sellingCostStatus" data-labelPid="` +
                                prodIds[0] +
                                `" style="font-size: 30px">
                                            <label class="form-check-label">
                                                <span class="switch-icon-left" style="font-size: 9px; margin-top: 6px">ON</span>
                                                <span class="switch-icon-right" style="font-size: 9px; margin-top: 6px; margin-left: -6px; color: black">OFF</span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="` +
                                customer_id + `" data-product_id="` + prodIds[0] +
                                `" data-bs-toggle="dropdown">
                                        . . .
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item edit_product" href="#" data-bs-target="#edit_product" data-customer_id="` + customer_id +
                                `" data-product_id="` + prodIds[0] + `" data-selling_price="` +
                                response.data.selling_price + `"`;
                            if (response.data.seller_cost_status == 0) {
                                html += `style="cursor: not-allowed"`;
                            } else {
                                html += `data-bs-toggle="modal"`;
                            }
                            html += `>
                                                <span>Edit Selling Price</span>
                                            </a>
                                            <a href="/delete_customer_prod/` + customer_id + `/` + prodIds[0] +
                                `" onclick="confirmDelete(event)" class="dropdown-item">Delete Product</a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="` + customer_id + `" data-product_id="` +
                                prodIds[0] +
                                `" data-bs-toggle="dropdown">
                                        . . .
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item edit_product" href="#" data-bs-target="#edit_product" data-customer_id="` +
                                customer_id + `" data-product_id="` + prodIds[0] +
                                `" data-selling_price="` + response.data.selling_price + `"`;
                            if (response.data.seller_cost_status == 0) {
                                html += `style="cursor: not-allowed"`;
                            } else {
                                html += `data-bs-toggle="modal"`;
                            }
                            html += `>
                                                <span>Edit Selling Price</span>
                                            </a>
                                            
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                            $('#cust_products_table').append(html);
                            $('.cust_product').each(function() {
                                $(this).find('option:selected').remove();
                                $(this).closest('tr').find(
                                    '.cust_prod_purchasing_price').html('$ 0.00');
                                $(this).closest('tr').find('.product-weight').html(
                                    '0.00');
                                $(this).closest('tr').find('.selling_price').val('');
                                $(this).closest('tr').find('.labelSwitch').prop(
                                    'checked', false);
                                $(this).closest('tr').find('.addSellerCostSwitch').prop(
                                    'checked', true);
                                $(this).closest('tr').find('.selling_price').attr(
                                    'disabled', false);
                            });
                            $('.cust_prod_data_table').DataTable().draw();
                        }
                    });
                }
            });
            // Labels / Label charges
            $(document).on('click', '.add-labels', function() {
                var product_id = $(this).data('product-id');
                var brand_id = $(this).data('brand-id');
                var customer_id = $(this).data('customer_id');
                $('#add-labels').data('product-id', product_id);
                $('#add-labels').data('brand-id', brand_id);
                $('#add-labels').data('customer_id', customer_id);
            });
            $(document).on('click', '.reduce-labels', function() {
                var product_id = $(this).data('product-id');
                var brand_id = $(this).data('brand-id');
                var customer_id = $(this).data('customer_id');
                $('#reduce-labels').data('product-id', product_id);
                $('#reduce-labels').data('brand-id', brand_id);
                $('#reduce-labels').data('customer_id', customer_id);
            });
            $(document).on('click', '#reduce-labels', function() {
                var customer_id = $(this).data('customer_id');
                var brand_id = $(this).data('brand-id');
                var product_id = $(this).data('product-id');
                var qty = $('#reduceqtyPrice').val();
                if (qty < 0) {
                    $('.negative_err').removeClass('d-none');
                    return false;
                } else {
                    $('.negative_err').addClass('d-none');
                }
                var url = '{{ route('reduce_label_to_product') }}';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        'customer_id': customer_id,
                        'brand_id': brand_id,
                        'product_id': product_id,
                        'qty': qty,
                    },
                    success: function(response) {
                        window.view_product_datatable.draw(false);
                    }
                });
            });
            $(document).on('click', '#add-labels', function() {
                var customer_id = $(this).data('customer_id');
                var brand_id = $(this).data('brand-id');
                var product_id = $(this).data('product-id');
                var qty = $('#qtyPrice').val();
                if (qty < 0) {
                    $('.negative_err').removeClass('d-none');
                    return false;
                } else {
                    $('.negative_err').addClass('d-none');
                }
                var url = '{{ route('add_label_to_product') }}';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        'customer_id': customer_id,
                        'brand_id': brand_id,
                        'product_id': product_id,
                        'qty': qty,
                    },
                    success: function(response) {
                        // window.location.reload();
                        window.view_product_datatable.draw(false);
                    }
                });
            });
            $(document).on('click', '.add-label-cost', function() {
                var product_id = $(this).data('product-id');
                var brand_id = $(this).data('brand-id');
                var customer_id = $(this).data('customer_id');
                $('#add-label-cost').data('product-id', product_id);
                $('#add-label-cost').data('brand-id', brand_id);
                $('#add-label-cost').data('customer_id', customer_id);
            });
            $(document).on('click', '#add-label-cost', function() {
                var customer_id = $(this).data('customer_id');
                var brand_id = $(this).data('brand-id');
                var product_id = $(this).data('product-id');
                var label_cost = $('#labelCost').val();
                if (label_cost < 0) {
                    $('.negative_err').removeClass('d-none');
                    return false;
                } else {
                    $('.negative_err').addClass('d-none');
                }
                var url = '{{ route('add_label_cost_to_product') }}';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        'customer_id': customer_id,
                        'brand_id': brand_id,
                        'product_id': product_id,
                        'label_cost': label_cost,
                    },
                    success: function(response) {
                        // window.location.reload();
                        window.view_product_datatable.draw(false);
                    }
                });
            });
            $(document).on('change', '.sellerCostSwitch', function() {
                var customer_id = $(this).data('customer_id');
                var prodId = $(this).data('labelpid');
                var status = 1;
                if ($(this).prop('checked') == true) {
                    status = 1;
                    $.ajax({
                        url: '{{ route('set_seller_cost_status') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_id: customer_id,
                            // brand_id: brandId,
                            product_id: prodId,
                            status: status
                        },
                        success: function(response) {
                            $('.cust_prod_data_table').DataTable().draw();
                        }
                    });
                } else {
                    $(this).closest('tr').find('.selling_price').css('border', '1px solid lightgray');
                    $(this).closest('tr').find('.sellingMsg').addClass('d-none');
                    status = 0;
                    $.ajax({
                        url: '{{ route('set_seller_cost_status') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_id: customer_id,
                            // brand_id: brandId,
                            product_id: prodId,
                            status: status
                        },
                        success: function(response) {
                            $('.cust_prod_data_table').DataTable().draw();
                        }
                    });
                }
            });
            // View Products in Modal
            $(document).on('click', '.view_cust_products', function() {
                var customer_id = $(this).attr('data-customer_id');
                $('.add_prod_btn').attr('data-customer_id', customer_id);
                $('.add_cust_prod_btn').attr('data-customer_id', customer_id);
                var html = '';
                var url = '{{ route('show_all', ':id') }}';
                url = url.replace(':id', customer_id);
                var custtable = $('.cust_prod_data_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    stateSave: true,
                    bDestroy: true,
                    ajax: {
                        url: url,
                    },
                    columns: [{
                            data: 'product_name',
                            name: 'product_name'
                        },
                        {
                            data: 'purchasing_cost',
                            name: 'purchasing_cost'
                        },
                        {
                            data: 'weight',
                            name: 'weight'
                        },
                        {
                            data: 'selling_cost',
                            name: 'selling_cost'
                        },
                        {
                            data: 'seller_cost_status',
                            name: 'seller_cost_status'
                        },
                        {
                            data: 'label_status',
                            name: 'label_status'
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
                    }
                });
                window.view_cust_product_datatable = custtable;
                var html = '';
                var url2 = '{{ route('show_all_products', ':id') }}';
                url2 = url2.replace(':id', customer_id);
                $.ajax({
                    url: url2,
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        $('.cust_product').empty();
                        html += `<option value="">Select Poduct</option>`;
                        for (var i = 0; i < res.length; i++) {
                            html += `
                            <option value="` + res[i].prod_id + `">` + res[i].prod_name + `</option>
                        `;
                        }
                        $('.cust_product').append(html);
                    }
                });
            });
            $(document).on('click', '.more_btn', function() {
                var customer_id = $(this).attr('data-customer_id');
                var brand_id = $(this).closest('tr').find('.brandId').val();
                var product_id = $(this).closest('tr').find('.productId').val();
                $(this).data('product-id', product_id);
                $('.add-labels').data('product-id', product_id);
                $('.add-labels').data('customer_id', customer_id);
                $('.reduce-labels').data('product-id', product_id);
                $('.reduce-labels').data('customer_id', customer_id);
                $('.add-label-cost').data('product-id', product_id);
                $('.add-label-cost').data('customer_id', customer_id);
                $('.history_more_btn').data('product-id', product_id);
                $('.history_more_btn').attr('href', '/customer/' + customer_id + '/brand/' + brand_id +
                    '/product/' + product_id + '/labels');
                $('.forecast_more_btn').data('product-id', product_id);
                $('.forecast_more_btn').attr('href', '/customer/' + customer_id + '/brand/' + brand_id +
                    '/product/' + product_id + '/labelforecast');
                $('.negative_err').addClass('d-none');
            });
        });
        $(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                // stateSave: true,
                bDestroy: true,
                // ajax: "{{ route('customers/admin') }}",
                ajax: {
                    url: "{{ route('customers/admin') }}",
                },
                columns: [
                    // {'className': 'details-control', 'orderable': false, 'data': null, 'defaultContent': ''},
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        orderable: true,
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        orderable: false,
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                    },
                    {
                        data: 'address',
                        name: 'address',
                        orderable: false,
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: false,
                    },
                    {
                        data: 'charges',
                        name: 'charges',
                        // orderable: false,
                        // searchable: false,
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        // searchable: false
                    },
                ],
                "drawCallback": function(settings) {
                    feather.replace();
                },
                'order': [
                    [0, 'asc']
                ],
            });
            // Add event listener for opening and closing details
            $('.data-table tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });
        });
        /* Formatting function for row details - modify as you need */
        function format(customer) {
            // `d` is the original data object for the row
            var html = '';
            html += `
            <span style="font-weight: bold; margin-left: 170px; text-decoration: underline">Customer Brands</span>
            <center>
                <table class="table table-bordered w-75 mt-1">
                    <thead>`;
            html += `   <th class="" style="padding:5px">Brand</th>
                        <th class="" style="padding:5px">Date</th>
                        <th class="" style="padding:5px">Action</th>
                    </thead>
                    <tbody>`;
            customer.brands.forEach(element => {
                html += `
                    <tr>
                        <td>` + element.brand + `</td>
                        <td>` + element.date +
                    `</td>
                        <td>
                            <a class="btn btn-primary btn-sm add-labels view_products" id="" href="#show_product_modal" data-brand_id="` +
                    element.id + `" data-bs-target="#show_product_modal" data-customer_id="` + customer.id + `" data-product-id="" data-bs-toggle="modal">
                                <span>Manage Labels</span>
                            </a>
                        </td>
                    </tr>
                `;
            });
            html += `</tbody>
                </table>
            </center>`;
            return html;
        }

        function confirmDelete(e) {
            var url = e.currentTarget.getAttribute('href');
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete?",
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
    </script>
@endsection
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop
