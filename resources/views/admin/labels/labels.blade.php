@extends('admin.layout.app')
@section('title', 'Manage Brands')
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
    <!-- BEGIN: Content-->
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">Manage Brands</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Customers
                                    </li>
                                    <li class="breadcrumb-item">Manage Brands
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-6 addBtnClass">
                            @can('labels_delete')
                                <a href="labels/trash" style="float:right;"
                                    class="btn btn-danger waves-effect waves-float waves-light"><i data-feather="trash-2"></i>
                                    View Trash</a>
                            @endcan
                            @can('labels_create')
                                <a href="/brands/create" style="float:right;margin-right:15px;"
                                    class="btn btn-primary waves-effect waves-float waves-light">Add New Brand</a>
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
                <div class="card-header text-right" style="display:block !important;">
                    <div class="row d-flex flex-row-reverse">
                        {{-- <div class="form-group col-sm-12 col-md-2" id="search">
                                    <label for="search"></label>
                                    <input type="submit" class="form-control btn btn-primary" value="Filter">
                                </div> --}}
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="brand">Select Brand</label>
                            <div class="invoice-customer">
                                <select name="brand" id="brand" class="form-select select2" required>
                                    <option value="">All</option>
                                    {{-- @foreach ($brands as $brand)
                                                <option value="{{$brand->id}}">{{$brand->brand}}</option>
                                            @endforeach --}}
                                </select>
                                @error('brand')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                                <div id="brand-error" class="text-danger font-weight-bold"></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label for="customer">Select Customer</label>
                            <select name="customer" id="customer" class="form-control select2">
                                <option value="all">All</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <div class="table-responsive">
                    <table class="table data-table table-bordered">
                        <thead>
                            <tr>
                                {{-- <th></th> --}}
                                <th>Customer</th>
                                <th>Brand</th>
                                <th>Mailer Charges</th>
                                {{-- <th>Labels </th> --}}
                                <th>Date</th>
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

@section('modal')
    {{-- Add Inventory Modal --}}
    <div class="modal fade text-start show" id="labelsModal" tabindex="1" aria-labelledby="myModalLabel33" style=""
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
                            <input type="number" min="1" placeholder="" name="label_qty" class="form-control"
                                id="qtyPrice">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-labels"
                            data-brand-id="" data-customer_id="" data-bs-dismiss="" data-product-id="">Add</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="reducelabelsModal" tabindex="-1" aria-labelledby="myModalLabel35" style=""
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
                            <input type="number" min="1" placeholder="" name="label_qty" class="form-control"
                                id="reduceqtyPrice">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                            id="reduce-labels" data-brand-id="" data-customer_id="" data-bs-dismiss=""
                            data-product-id="">Submit</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="labelCostModal" tabindex="-1" aria-labelledby="myModalLabel34" style=""
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel34">Add Labels</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">

                        <label>Labels Cost: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="" name="label_cost" class="form-control"
                                id="labelCost">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                            id="add-label-cost" data-brand-id="" data-customer_id="" data-bs-dismiss=""
                            data-product-id="">Add</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="show_brand_product_modal" tabindex="-1"
        aria-labelledby="show_brand_product_modal33" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="show_brand_product_modal33">Brand's Products</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="mb-1 row">
                            <div class="col-md-3">
                                <label for="col-form-label">Products</label>
                            </div>
                            <div class="col-md-9">
                                <table class="table brand_data-table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%">Product</th>
                                            <th style="width: 25%" class="">Charges Per Label</th>
                                            <th style="width: 12%" class="">Label Qty</th>
                                            <th style="width: 20%" class="">Forecast Days</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products_table">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <p>Add Product</p>
                            </div>
                            <div class="col-md-9">
                                {{-- {{Request::route()->id}} --}}
                                <div id="form_ID">
                                    <table class="table table-hover table-bordered table-striped products-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 47%">Product</th>
                                                {{-- <th style="width: 25%" class="text-center">Purchasing Cost</th> --}}
                                                <th style="width: 30%" class="text-center">Weight</th>
                                                {{-- <th style="width: 20%" class="text-center">Selling Cost</th> --}}
                                                {{-- <th style="width: 10%" class="text-center">Label Status</th> --}}
                                                {{-- <th style="width: 20px" class="text-center">Labels</th> --}}
                                                {{-- <th style="width: 200px" class="text-center">Service Charges</th> --}}
                                                <th style="width: 20%" class="">Action</th>
                                                {{-- <th style="width: 30px" class="moreHeader d-none">More</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody id="addProductTable">
                                            <tr>
                                                <td>
                                                    <select name="products[]" class="product form-select select2"
                                                        id="product-list">
                                                        <option value="">Select Product</option>

                                                    </select>
                                                    <div class="product-error text-danger"></div>
                                                </td>
                                                {{-- <td>
                                                        <div class="purchasing-price font-weight-bold text-success text-center">$ 0.00 </div>
                                                    </td> --}}
                                                <td>
                                                    <div class="total-weight font-weight-bold text-success text-center">
                                                        <span class="product-weight">0.00 </span><span
                                                            class="unit">oz</span></div>
                                                    <input type="hidden" name="" class="p-weight" value="">
                                                </td>
                                                {{-- <td>
                                                        <div class="input-icon">
                                                            <input type="number" name="selling_price[]" class="selling_price form-control" step="0.01" placeholder="Selling Cost" value="">
                                                            <i>$</i>
                                                        </div>
                                                        <div class="sellingMsg d-none" style="color: red; font-size: 12px">Should be greater than Purchasing Cost</div>
                                                    </td> --}}
                                                {{-- <td>
                                                        <div class="col-sm-9">
                                                            <div class="form-check form-check-primary form-switch">
                                                                <input type="checkbox" name="is_active[]" class="form-check-input labelSwitch">
                                                            </div>
                                                        </div>
                                                    </td> --}}
                                                <td>
                                                    <button class="m-auto btn btn-primary shadow add_brand_prod_btn"
                                                        data-customer_id="" data-brand_id="" type="button">Add</button>
                                                </td>
                                                <input type="hidden" id="brand_id" value="">
                                                <input type="hidden" id="customer_id" value="">
                                            </tr>
                                        </tbody>
                                    </table>
                                    {{-- <br>
                                        <button type="button" class="saveBtn btn btn-primary me-1" style="float: right">Submit</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-labels" data-brand-id="" data-customer_id="" data-bs-dismiss="" data-product-id="">Add</button>
                            <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                        </div> --}}
                </form>
            </div>
        </div>
    </div>

    <!-- Basic toast -->
    <button class="btn btn-outline-primary toast-basic-toggler mt-2" id="toast-btn"></button>
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
<script type="text/javascript">
    $(document).ready(function() {
        // get customer brand
        $('#show_brand_product_modal').on('hidden.bs.modal', function(e) {
            window.brand_product_table.destroy();
        });
        $(document).on('change', '#customer', function() {
            $('.customer-error').html('');
            var id = $(this).val();
            if (id == "all") {
                $.get('/getAllBrands', function(result) {
                    if (result.status == "success") {
                        var options = "<option value=''>All</option>";
                        result.data.forEach(element => {
                            options += "<option value='" + element.id + "'>" + element
                                .brand + "</option>"
                        });

                        $('#brand-error').html('');
                        $('#brand').html(options);
                    } else {
                        $('#brand').html("<option value=''>All</option>");
                        $('#brand-error').html(result.message);
                    }
                });
            } else {
                $.get('/customer/' + id + '/brand', function(result) {
                    if (result.status == "success") {
                        var options = "<option value=''>All</option>";
                        result.data.forEach(element => {
                            options += "<option value='" + element.id + "'>" + element
                                .brand + "</option>"
                        });

                        $('#brand-error').html('');
                        $('#brand').html(options);
                    } else {
                        $('#brand').html("<option value=''>All</option>");
                        $('#brand-error').html(result.message);
                    }
                });
            }
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
                    $('#reducelabelsModal').hide();
                    window.location.reload();
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
                    $('#labelsModal').hide();
                    window.location.reload();
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
                    $('#labelCostModal').hide();
                    window.location.reload();
                }
            });
        });
        $(document).on('click', '.add_brand_products', function() {
            var customer_id = $(this).attr('data-customer_id');
            var brand_id = $(this).attr('data-brand_id');
            $('.add_brand_prod_btn').attr('data-customer_id', customer_id);
            $('.add_brand_prod_btn').attr('data-brand_id', brand_id);
            var brand_prod_table = $('.brand_data-table').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                stateSave: true,
                // bFilter: false,
                // ajax: "{{ route('label/admin') }}",
                ajax: {
                    url: "{{ route('edit_customer_brand') }}",
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
                ],
                "drawCallback": function(settings) {
                    feather.replace();
                }
            });
            window.brand_product_table = brand_prod_table;
            var html = '';
            $.ajax({
                url: "{{ route('get_brand_products') }}",
                data: {
                    customer_id: customer_id,
                    brand_id: brand_id,
                },
                success: function(response) {
                    $('.product').empty();
                    html += `<option>Select Product</option>`;
                    for (var i = 0; i < response.length; i++) {
                        html += `<option value="` + response[i].prod_id + `">` + response[i]
                            .prod_name + `</option>`;
                    }
                    $('.product').append(html);
                }
            });
        });
        $(document).on('change', '.product', function() {
            var id = $(this).val();
            var context = $(this);
            var customer_id = $(this).attr('data-customer_id');
            var brand_id = $(this).attr('data-brand_id');
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
        $(document).on('click', '.add_brand_prod_btn', function() {
            var This = $(this);
            var customer_id = This.attr('data-customer_id');
            var brand_id = This.attr('data-brand_id');
            var prodIds = [];
            var html = '';
            var url = '{{ route('save_customer_brand_product') }}';
            $('.product').each(function() {
                prodIds.push($(this).val());
            });
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    customer_id: customer_id,
                    brand_id: brand_id,
                    prod_ids: prodIds,
                },
                success: function(response) {
                    html += `
                                <tr>
                                    <td>` + response.data.product_name + `</td>
                                    <td>
                                        <div class="">$ 0.00</div>
                                    </td>
                                    <td>
                                        <div class="">0</div>
                                    </td>
                                    <td>
                                        <center><span class="badge me-1" style="background-color: pink; color: red">0d</span></center>
                                    </td>
                                </tr>
                            `;
                    $('#products_table').append(html);
                    $('.product').each(function() {
                        $(this).find('option:selected').remove();
                        $(this).closest('tr').find('.purchasing-price').html(
                            '$ 0.00');
                        $(this).closest('tr').find('.product-weight').html('0.00');
                    });
                    _this.attr('disabled', false);
                }
            });

        });
        @if (session('success'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-success');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{ session('success') }}");
            $('#toast-btn').click();
        @endif
    });
    $(function() {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            // ordering: true,
            stateSave: true,
            bDestroy: true,
            // ajax: "{{ route('label/admin') }}",
            ajax: {
                url: "{{ route('label/admin') }}",
                data: function(d) {
                    if ($('#customer :selected').text() == 'All') {
                        customername = ''
                    } else {
                        customername = $('#customer :selected').text();
                    }
                    if ($('#brand :selected').text() == 'All') {
                        brandname = ''
                    } else {
                        brandname = $('#brand :selected').text();
                    }
                    d.customer = customername;
                    d.brand = brandname;
                }
            },
            columns: [
                //   {'className': 'details-control', 'orderable': false, 'data': null, 'defaultContent': ''},
                {
                    data: 'customer_name',
                    name: 'customer_name',
                    orderable: true,
                },
                {
                    data: 'brand',
                    name: 'brand',
                    orderable: false,
                },
                {
                    data: 'mailer_cost',
                    name: 'mailer_cost',
                    orderable: false,
                },
                //   {data: 'qty', name: 'qty'},
                {
                    data: 'date',
                    name: 'date'
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
        $('#customer, #brand').on('change', function() {
            table.draw(false);
        });
        // Refilter the table
        $('#search').on('click', function() {
            table.draw(false);
        });
    });
    /* Formatting function for row details - modify as you need */
    function format(data) {
        var html = '';
        $.ajax({
            async: false,
            url: '{{ route('get_labels_data') }}',
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                customer_id: data.customer.id,
                brand_id: data.id
            },
            success: function(response) {
                // for(var i = 0; i < response.length; i++) {
                html += `
                        <div class="col-12">
                            <div class="mb-1 row mt-1">
                                <center>
                                <div class="col-md-11">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <th style="width: 250px">Product</th>
                                            <th style="width: 150px">Charges Per Label</th>
                                            <th style="width: 80px">Label Qty</th>
                                            <th style="width: 50px">Forecast Days</th>
                                            <th style="width: 50px">More</th>
                                        </thead>
                                        <tbody>`;
                response.forEach(element => {
                    html += `
                                                    <tr>`;
                    if (element.products.length > 0)
                        element.products.forEach(product => {
                            html += `
                                                                <tr>
                                                                    <td>
                                                                        ` + product.prod_name + `
                                                                    </td>
                                                                    <td>
                                                                        $ ` + product.label_cost + `
                                                                    </td>
                                                                    <td>
                                                                        ` + product.labels_qty + `
                                                                    </td>
                                                                    <td>
                                                                        ` + product.forecast_labels +
                                `
                                                                    </td>
                                                                    <td>
                                                                        <input type="hidden" class="productId" value="` + product.prod_id + `" name="prodId[]">
                                                                        <input type="hidden" class="brandId" value="` +
                                element.brand_id +
                                `">
                                                                        <div class="dropdown">
                                                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="` +
                                data.customer.id +
                                `" data-product-id="" data-bs-toggle="dropdown">
                                                                            . . .
                                                                            </button>
                                                                            <div class="dropdown-menu">
                                                                                <a class="dropdown-item add-labels" href="#labelsModal" data-brand-id="` +
                                element.brand_id +
                                `" data-bs-target="#labelsModal" data-customer_id="" data-product-id="" data-bs-toggle="modal">
                                                                                    <span>Add Labels</span>
                                                                                </a>
                                                                                <a class="dropdown-item reduce-labels" href="#reducelabelsModal" data-brand-id="` +
                                element.brand_id +
                                `" data-customer_id="" data-bs-target="#reducelabelsModal" data-product-id="" data-bs-toggle="modal">
                                                                                    <span>Reduce Labels</span>
                                                                                </a>
                                                                                <a class="dropdown-item add-label-cost" href="#labelCostModal" data-brand-id="` +
                                element.brand_id + `" data-bs-target="#labelCostModal" data-customer_id="" data-product-id="" data-bs-toggle="modal">
                                                                                    <span>Add Label Charges</span>
                                                                                </a>
                                                                                <a class="dropdown-item history_more_btn" href="" data-product-id="">
                                                                                    <span>Show History</span>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>`;
                        });
                    html += `            
                                                    </tr>
                                                    `;
                });
                html += `
                                        </tbody>
                                    </table>
                                </div>
                                </center>
                            </div>
                        </div>
                        `;
                // }
                return html;
            }
        });
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
</script>
@endsection
@section('datatablejs')
<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop
