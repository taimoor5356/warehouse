@extends('admin.layout.app')
@section('title', 'Add Brand Products')
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
                    <h2 class="content-header-title float-start mb-0">Add Brand Products</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item">Customers
                            </li>
                            <li class="breadcrumb-item"><a href="/customers">Manage Customers</a>
                            </li>
                            <li class="breadcrumb-item">Add Brand Products
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
    <!-- Basic Tables end -->
    <!-- END: Content-->
    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Brand Products</h4>
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
                                                <th style="width: 15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="products_table">
                                            
                                        </tbody>
                                    </table>
                                    <br>
                                </div>
                                <form>
                                    {{-- <div class="col-12 mb-1">
                                        <div class="row p-2"> --}}
                                            <div class="col-md-2">
                                                <p>Add Products</p>
                                            </div>
                                            <div class="col-md-10">
                                                <form id="form_ID">
                                                    <table class="table table-hover table-bordered table-striped products-table">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50%">Product</th>
                                                                <th style="width: 10%" class="text-center">Weight</th>
                                                                <th style="width: 10%" class="">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="addProductTable">
                                                            <tr>
                                                                <td>
                                                                    <select name="products[]" class="product form-select select2" id="brand_prod_list" required>
                                                                        <option>Select Product</option>
                                                                        
                                                                    </select>
                                                                    <div class="product-error text-danger d-none">Required</div>
                                                                </td>
                                                                <td>
                                                                    <div class="total-weight font-weight-bold text-success text-center"><span class="product-weight">0.00 </span><span class="unit">oz</span></div>
                                                                    <input type="hidden" name="" class="p-weight" value="">
                                                                </td>
                                                                <td>
                                                                    <button class="m-auto btn btn-primary shadow add_brand_prod_btn" type="button">Add</button>
                                                                </td>
                                                                
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </form>
                                            </div>
                                        {{-- </div>
                                    </div> --}}
                                </form>
                            {{-- </div> --}}
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
                    <h4 class="modal-title" id="myModalLabel34">Add Labels</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        
                        <label>Labels Cost: </label>
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
            @if (session('success'))
                $('.toast .me-auto').html('Success');
                $('.toast .toast-header').addClass('bg-success');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{session('success')}}");
                $('#toast-btn').click();
            @elseif (session('error'))
                $('.toast .me-auto').html('Success');
                $('.toast .toast-header').addClass('bg-danger');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{session('error')}}");
                $('#toast-btn').click();
            @endif
            $(document).on('change', '#brand_id', function() {
                var _this = $(this);
                var brand_id = _this.val();
                var customer_id = $('#customer_id').val();
                $('#brand_prod_list').empty();
                var url = '{{ route("get_cust_brand_prods") }}';
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
                            {data: 'action', name: 'action'}
                        ],
                        "drawCallback": function( settings ) {
                            feather.replace();
                        },
                    });
                    window.dataTable = table;
                });
                $.ajax({
                    url: '{{ route("get_cust_brand_remaining_prods") }}',
                    type: 'GET',
                    data: {
                        customer_id: customer_id,
                        brand_id: brand_id
                    },
                    success:function(response) {
                        $('#brand_prod_list').append('<option value="">Select Product</option>');
                        for(var i = 0; i < response.length; i++) {
                            $('#brand_prod_list').append('<option value="'+response[i].prod_id+'">'+response[i].prod_name+'</option>');
                        }
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
            if(prodIds[0] == '') {
                $('.product-error').removeClass('d-none');
                return false;
            } else {
                $('.product-error').addClass('d-none');
            }
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
                    // if (response.success == true) {
                        
                    // } else if (response.error == true) {
                        
                    // }
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
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/delete_customer_brand_product/`+customer_id+`/`+brand_id+`/`+response.data.product_id+`">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        <span>Delete</span>
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

