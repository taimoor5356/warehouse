@extends('admin.layout.app')
@section('title', 'Add Customer Labels')
@section('datatablecss')

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">

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
    input[type="checkbox"]{
       background-color: #fff;
    }
</style>
@php    
    $customer_id = Request::route()->id;
@endphp
<input type="hidden" name="" id="customer_id" value="{{ $customer_id }}">
<!-- BEGIN: Content-->
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="row">
                            <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Add Customer Labels</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Customers
                                    </li>
                                    <li class="breadcrumb-item"><a href="/customers">Manage Customers</a>
                                    </li>
                                    <li class="breadcrumb-item">Add Labels
                                    </li>
                                </ol>
                            </div>
                             </div>

                             {{-- <div class="col-6 addBtnClass">
                                @can('trash_view',\App\AdminModels\Labels::class)
                                <a href="#" style="float:right;"  class="btn btn-danger waves-effect waves-float waves-light"><i data-feather="trash-2"></i> View Trash</a>
                                @endcan
                                 @can('create',\App\AdminModels\Labels::class)
                                <a href="#" style="float:right;margin-right:15px;"  class="btn btn-primary waves-effect waves-float waves-light">Add New Brand</a>
                                @endcan
                            </div> --}}
                    </div>

                        </div>
                    </div>
                </div>
                
            </div>
            {{-- <div class="row" id="basic-table">
                <div class="col-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 12%"><center><img class="show_all_btn" src="https://datatables.net/examples/resources/details_open.png" alt=""></center></th>
                                        <th>Brand</th>
                                        <th>Customer</th>
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
            </div> --}}
                <!-- Basic Tables end -->
       
    <!-- END: Content-->

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Labels</h4>
                </div>
                <div class="card-body">
                    <form class="form form-horizontal" enctype='multipart/form-data' action="/customer/{{$customer_id}}/brand/store" method="post">
                       {{@csrf_field()}}
                        <div class="row">
                          <input type="hidden" id="customer_id" value="{{ $customer_id }}">
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="catselect">Select Customer</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{App\AdminModels\Customers::where('id', $customer_id)->first()->customer_name}}" class="form-control" id="customer_id" readonly>
                                        @error('customer_id')
                                              <p class="text-danger">{{ $message }}</p>
                                          @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="catselect">Brand Name</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="brand" id="brand_id" class="select2 form-control">
                                            <option>Select Brand</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->brand }}</option>    
                                            @endforeach
                                        </select>
                                        @error('brand')
                                              <p class="text-danger">{{ $message }}</p>
                                          @enderror
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{-- <div class="mb-1 row"> --}}
                                <div class="col-md-2">
                                    <label for="col-form-label">Products</label>
                                </div>
                                <div class="col-md-10">
                                    <table class="table data_table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%">Product</th>
                                                <th style="width: 10%">Label Qty</th>
                                                <th style="width: 15%">Label Charges</th>
                                                <th style="width: 15%">Label Status</th>
                                                <th style="width: 10%" class="">More</th>
                                            </tr>
                                        </thead>
                                        <tbody id="products_table">
                                            
                                        </tbody>
                                    </table>
                                    <br>
                                </div>
                            <hr>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
    @section('modal')
    <div class="modal fade text-start show" id="labelsModal" tabindex="-1" aria-labelledby="myModalLabel33" style="" aria-modal="true" role="dialog">
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
                            <input type="number" min="1" placeholder="" name="label_qty" class="form-control" id="qtyPrice">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-labels" data-brand-id="" data-customer_id="" data-bs-dismiss="" data-product-id="">Add</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="reducelabelsModal" tabindex="-1" aria-labelledby="myModalLabel35" style="" aria-modal="true" role="dialog">
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
                            <input type="number" min="1" placeholder="" name="label_qty" class="form-control" id="reduceqtyPrice">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="reduce-labels" data-brand-id="" data-customer_id="" data-bs-dismiss="" data-product-id="">Submit</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="labelCostModal" tabindex="-1" aria-labelledby="myModalLabel34" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel34">Label Cost</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        
                        <label>Add Labels Cost: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="" name="label_cost" class="form-control" id="labelCost">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-label-cost" data-brand-id="" data-customer_id="" data-bs-dismiss="" data-product-id="">Add</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="show_brand_product_modal" tabindex="-1" aria-labelledby="show_brand_product_modal33" style="" aria-modal="true" role="dialog">
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
                                            <th style="width: 20%" class="">More</th>
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
                                <div id="form_ID">
                                    <table class="table table-hover table-bordered table-striped products-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 47%">Product</th>
                                                <th style="width: 30%" class="text-center">Weight</th>
                                                <th style="width: 20%" class="">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="addProductTable">
                                            <tr>
                                                <td>
                                                    <select name="products[]" class="product form-select select2" id="product-list">
                                                        <option value="">Select Product</option>
                                                    </select>
                                                    <div class="product-error text-danger"></div>
                                                </td>
                                                <td>
                                                    <div class="total-weight font-weight-bold text-success text-center"><span class="product-weight">0.00 </span><span class="unit">oz</span></div>
                                                    <input type="hidden" name="" class="p-weight" value="">
                                                </td>
                                                <td>
                                                    <button class="m-auto btn btn-primary shadow add_brand_prod_btn" data-customer_id="" data-brand_id="" type="button">Add</button>
                                                </td>  
                                                <input type="hidden" id="brand_id" value="">
                                                <input type="hidden" id="customer_id" value="">
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
                            </div>
                        </div>
                    </div>
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
            $(document).on('change', '#brand_id', function() {
                var _this = $(this);
                var brand_id = _this.val();
                var customer_id = $('#customer_id').val();
                $('#brand_prod_list').empty();
                var url = '{{ route("get_cust_brand_prod") }}';
                $(function() {
                    var table = $('.data_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ordering: true,
                        bFilter: false,
                        paging: false,
                        bDestroy: true,
                        stateSave: true,
                        ajax: {
                            url: url,
                            data: {
                                customer_id: customer_id,
                                brand_id: brand_id
                            }
                        },
                        columns: [
                            {data: 'product', name: 'product'},
                            {data: 'label_qty', name: 'label_qty'},
                            {data: 'label_cost', name: 'label_cost'},
                            {data: 'status', name: 'status'},
                            {data: 'action', name: 'action', orderable: false, searchable: false},
                        ],
                        "drawCallback": function( settings ) {
                            feather.replace();
                        },
                    });
                    window.dataTable = table;
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
            $('.history_more_btn').attr('href', '/customer/'+customer_id+'/brand/'+brand_id+'/product/'+product_id+'/labels');
            $('.forecast_more_btn').data('product-id', product_id);
            $('.forecast_more_btn').attr('href', '/customer/'+customer_id+'/brand/'+brand_id+'/product/'+product_id+'/labelforecast');
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
            var url = '{{ route("reduce_label_to_product") }}';
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
                success:function(response) {
                    if (response.success == true) {
                        $('#reducelabelsModal').modal('hide');
                        // window.location.reload();
                        $('.toast .me-auto').html('Success');
                        $('.toast .toast-header').addClass('bg-success');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                        window.dataTable.draw();
                    } else if (response.error == true) {
                        $('#reducelabelsModal').modal('hide');
                        $('.toast .me-auto').html('Error');
                        $('.toast .toast-header').addClass('bg-danger');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                    }
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
            var url = '{{ route("add_label_to_product") }}';
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
                success:function(response) {
                    if (response.success == true) {
                        $('#labelsModal').modal('hide');
                        // window.location.reload();
                        $('.toast .me-auto').html('Success');
                        $('.toast .toast-header').addClass('bg-success');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                        window.dataTable.draw();
                    } else if (response.error == true) {
                        $('#labelsModal').modal('hide');
                        $('.toast .me-auto').html('Error');
                        $('.toast .toast-header').addClass('bg-danger');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                    }
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
            var url = '{{ route("add_label_cost_to_product") }}';
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
                success:function(response) {
                    if (response.success == true) {
                        $('#labelCostModal').modal('hide');
                        // window.location.reload();
                        $('.toast .me-auto').html('Success');
                        $('.toast .toast-header').addClass('bg-success');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                        window.dataTable.draw();
                    } else if (response.error == true) {
                        $('#labelCostModal').modal('hide');
                        $('.toast .me-auto').html('Error');
                        $('.toast .toast-header').addClass('bg-danger');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                    }
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
                columns: [
                    {data: 'product_name', name: 'product_name'},
                    {data: 'label_cost', name: 'label_cost'},
                    {data: 'labels_qty', name: 'labels_qty'},
                    {data: 'forecast_labels', name: 'forecast_labels'},
                ],
                "drawCallback": function( settings ) {
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
                success:function(response) {
                    $('.product').empty();
                    html += `<option>Select Product</option>`;
                    if(response.length > 0) {
                        for(var i = 0; i < response.length; i++) {
                            html += `<option value="`+response[i].prod_id+`">`+response[i].prod_name+`</option>`;
                        }
                    }
                    $('.product').append(html);
                }
            });
        });
        $(document).on('change', '.product', function() {
            var id = $(this).val();
            var context = $(this);
            // var customer_id = $(this).attr('data-customer_id');
            // var brand_id = $(this).attr('data-brand_id');
            var url = '{{ route("get_product_labels", ":id") }}';
            var url2 = '{{ route("getProductstoCustomer") }}';
            // url = url.replace(':id', id);
            if(id=="") {
                
            } else {
                url2 = url2.replace(':id', id);
                $.ajax({
                    url: url2,
                    type: 'GET',
                    data: {
                        _token: '{{csrf_token()}}',
                        'product_id': id
                    },
                    success:function(res) {
                        if(res) {
                            // context.closest('tr').find('.purchasing-price').html('$ '+Number(res.price).toFixed(2));
                            // context.closest('tr').find('.selling_price').val(Number(res.price).toFixed(2));
                            context.closest('tr').find('.product-weight').html(Number(res.weight).toFixed(2));
                            context.closest('tr').find('.p-weight').val(Number(res.weight).toFixed(2));
                        } else {
                            $('.product-error').html(res.message);
                        }
                    }
                });
            }
        });
        $(document).on('click', '.add_brand_prod_btn', function() {
            var This = $(this);
            var customer_id = $('#customer_id').val();
            var brand_id = $('#brand_id').val();
            var prodIds = [];
            var html = '';
            var url = '{{ route("save_customer_brand_product") }}';
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
                    prod_ids : prodIds,
                },
                success:function(response) {
                    html += `
                        <tr>
                            <td>`+response.data.product_name+`</td>
                            <td>
                                <div class="">0</div>
                            </td>
                            <td>
                                <div class="">$ 0.00</div>
                            </td>
                            <td>
                                <input type="hidden" class="productId" value="`+response.data.product_id+`" name="prodId[]">
                                <input type="hidden" class="brandId" value="`+brand_id+`">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="`+customer_id+`" data-product-id="" data-bs-toggle="dropdown">
                                    . . .
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item add-labels" href="#labelsModal" data-brand-id="`+brand_id+`" data-bs-target="#labelsModal" data-customer_id="" data-product-id="" data-bs-toggle="modal">
                                            <span>Add Labels</span>
                                        </a>
                                        <a class="dropdown-item reduce-labels" href="#reducelabelsModal" data-brand-id="`+brand_id+`" data-customer_id="" data-bs-target="#reducelabelsModal" data-product-id="" data-bs-toggle="modal">
                                            <span>Reduce Labels</span>
                                        </a>
                                        <a class="dropdown-item add-label-cost" href="#labelCostModal" data-brand-id="`+brand_id+`" data-bs-target="#labelCostModal" data-customer_id="" data-product-id="" data-bs-toggle="modal">
                                            <span>Add Label Charges</span>
                                        </a>
                                        <a class="dropdown-item history_more_btn" href="" data-product-id="">
                                            <span>Show History</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                    $('#products_table').append(html);
                    $('.product').each(function(){
                        $(this).find('option:selected').remove();
                        $(this).closest('tr').find('.product-weight').html('0.00');
                    });
                    _this.attr('disabled', false);
                    
                }
            });
        });
        });
        </script>
@endsection
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop

