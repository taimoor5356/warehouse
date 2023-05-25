@extends('admin.layout.app')
@section('title', 'Quick Setup')
@section('page_cs')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/plugins/charts/chart-apex.css')}}">
    
@endsection

@section('content')
@php
    $customer_id = Request::route()->id;
@endphp
<!-- BEGIN: Content-->
<div class="content-header row">
    <div class="content-header-left col-md-12 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <div class="row">
                    <div class="col-7">
                        <h2 class="content-header-title float-start mb-0">Quick Setup</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">Home</a>
                                </li>
                                <li class="breadcrumb-item">Customers
                                </li>
                                <li class="breadcrumb-item"><a href="/customers">Manage Customer</a>
                                </li>
                                <li class="breadcrumb-item">Quick Setup
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<section id="dashboard-ecommerce">
    <div class="card">
        <div class="card-body">
            <h4 style="text-decoration: underline">Customer Control Panel <small>(Only Admin can control)</small></h4>
            <h6 style="">Newly Added Customer: <span style="font-weight: bold;">{{ App\AdminModels\Customers::where('id', $customer_id)->first()->customer_name }}</span></h6>
            <hr>
            <center>
            <div class="row">
                <center>- Step 1 -</center>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2">
                    <a class="btn btn-primary text-center py-2" onclick="window.open('/customers/{{ $customer_id }}/edit', '', 'width=1300, height=700');" style="font-size: 20px; width: 450px; font-weight: bold">
                        Edit Customer Info
                    </a>
                </div>
                <center>- Step 2 -</center>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2">
                    <a class="btn btn-primary view_products text-center py-2" id="" href="#" data-brand_id="" data-bs-target="#show_product_modal" data-customer_id="{{ $customer_id }}" data-product-id="" data-bs-toggle="modal" style="font-size: 20px; width: 450px; font-weight: bold">
                        Add Product
                    </a>
                    <input type="hidden" id="custId" value="{{ $customer_id }}">
                </div>
                <center>- Step 3 -</center>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2">
                    <a class="btn btn-primary text-center py-2" onclick="window.open('/customer/{{$customer_id}}/brand/create', '', 'width=1300, height=700');" data-brand_id="" data-bs-target="#" data-bs-toggle="modal" style="font-size: 20px; width: 450px; font-weight: bold">
                        Add Brand
                    </a>
                </div>
                <center>- Step 4 -</center>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2">
                    <a class="btn btn-primary text-center py-2" onclick="window.open('/add_brand_products/{{$customer_id}}', '', 'width=1300, height=700');" data-brand_id="" data-bs-target="#" data-bs-toggle="modal" style="font-size: 20px; width: 450px; font-weight: bold">
                        Add Brand Products
                    </a>
                </div>
                <center>- Step 5 -</center>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2">
                    <a class="btn btn-primary text-center py-2" onclick="window.open('/add_customer_label/{{ $customer_id }}', '', 'width=1200, height=700');" style="font-size: 20px; width: 450px; font-weight: bold">
                        Add Label
                    </a> 
                </div>
                <center>- Step 6 -</center>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2">
                    <a class="btn btn-primary text-center py-2" onclick="window.open('/sku/create?customer_id={{ $customer_id }}', '', 'width=1200, height=700');" style="font-size: 20px; width: 450px; font-weight: bold">
                        Add SKU
                    </a>
                </div>
            </div>
            </center>
        </div>
    </div>
</section>
@endsection
@section('modal')
    <div class="modal fade text-start show" id="show_product_modal" tabindex="-1" aria-labelledby="show_product_modal33" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog ">
            <div class="modal-content" style="width: 1200px; margin-left: -60%">
                <div class="modal-header">
                    <h4 class="modal-title" id="show_product_modal33">Customer's Products</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="col-12 mb-1">
                        <div class="mb-1 row p-2">
                            <div class="col-md-2">
                                <label for="col-form-label">Products</label>
                            </div>
                            <div class="col-md-10">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%">Product</th>
                                            <th style="width: 25%" class="text-center">Purchasing Cost</th>
                                            <th style="width: 10%" class="text-center">Weight</th>
                                            <th style="width: 20%" class="text-center">Selling Cost</th>
                                            <th style="width: 10%" class="text-center">Label Status</th>
                                            {{-- <th style="width: 20px" class="text-center">Labels</th> --}}
                                            {{-- <th style="width: 200px" class="text-center">Service Charges</th> --}}
                                            <th style="width: 20%" class="">Action</th>
                                            {{-- <th style="width: 30px" class="moreHeader d-none">More</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody id="products_table">
                                        @foreach($customerProducts as $cust_prod)
                                        <tr>
                                            <td>
                                                @php
                                                    $product = App\AdminModels\Products::where('id', $cust_prod->product_id)->first();    
                                                @endphp
                                                @if(isset($product))
                                                {{$product->name}}
                                                @endisset
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-success text-center">$ @isset($product){{number_format($product->price, 2)}}@endisset</div>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-success text-center">@isset($product){{number_format($product->weight, 2)}}@endisset oz</div>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-success text-center product_selling_price"> $ {{ number_format($cust_prod->selling_price, 2) }}</div>
                                            </td>
                                            <td>
                                                <div class="col-sm-9">
                                                    <div class="form-check form-check-primary form-switch">
                                                        <input type="checkbox" @if($cust_prod->is_active == 0) checked @endif name="is_active" class="form-check-input labelSwitch" data-labelPid="{{$cust_prod->product_id}}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="{{$customer_id}}" data-product_id="{{$cust_prod->product_id}}" data-bs-toggle="dropdown">
                                                    . . .
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item edit_product" href="#" data-bs-target="#edit_product" data-customer_id="{{$customer_id}}" data-product_id="{{$cust_prod->product_id}}" data-selling_price="{{ number_format($cust_prod->selling_price, 2) }}" data-bs-toggle="modal">
                                                            <span>Edit Selling Price</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
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
                                    <table class="table table-hover table-bordered table-striped products-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%">Product</th>
                                                <th style="width: 25%" class="text-center">Purchasing Cost</th>
                                                <th style="width: 10%" class="text-center">Weight</th>
                                                <th style="width: 20%" class="text-center">Selling Cost</th>
                                                <th style="width: 10%" class="text-center">Label Status</th>
                                                <th style="width: 20%" class="">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="addProductTable">
                                            <tr>
                                                <td>
                                                    <select name="products[]" class="product form-select select2" id="product-list">
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{$product['prod_id']}}">{{$product['prod_name']}}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="product-error text-danger"></div>
                                                </td>
                                                <td>
                                                    <div class="purchasing-price font-weight-bold text-success text-center">$ 0.00 </div>
                                                </td>
                                                <td>
                                                    <div class="total-weight font-weight-bold text-success text-center"><span class="product-weight">0.00 </span><span class="unit">oz</span></div>
                                                    <input type="hidden" name="" class="p-weight" value="">
                                                </td>
                                                <td>
                                                    <div class="input-icon">
                                                        <input type="number" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="selling_price[]" class="selling_price form-control" step="0.01" placeholder="Selling Cost" value="">
                                                        <i>$</i>
                                                    </div>
                                                    <div class="sellingMsg d-none" style="color: red; font-size: 12px">Should be greater than Purchasing Cost</div>
                                                </td>
                                                <td>
                                                    <div class="col-sm-9">
                                                        <div class="form-check form-check-primary form-switch">
                                                            <input type="checkbox" name="is_active[]" class="form-check-input labelSwitch">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="m-auto btn btn-primary shadow add_prod_btn" type="button">Add</button>
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
    <div class="modal fade text-start" id="edit_product" tabindex="-1" aria-labelledby="edit_product_selling_price" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_product_selling_price">Selling Price</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <label>Selling Price: </label>
                        <div class="mb-1">
                            <input type="number" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required min="0" placeholder="Enter Selling Price" name="selling_price" class="form-control" id="modal_selling_price" data-product_id="" data-customer_id="{{ $customer_id }}">
                        </div>
                        <div class="selling_price_err text-danger d-none">Can't add negative value</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="save_product_selling_price" data-bs-dismiss="">Save</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start" id="add_brand_modal" tabindex="-1" aria-labelledby="add_brand_modal12" style="" aria-modal="true" role="dialog">
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
                                <form class="form form-horizontal" id="brand_form" enctype='multipart/form-data' action="{{route('brands.store')}}" method="post">
                                   {{@csrf_field()}}
                                    <div class="row">
                                      
                                        <div class="col-12">
                                            <div class="mb-1 row">
                                                <div class="col-sm-3">
                                                    <label class="col-form-label" for="catselect">Customer Name</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <select name="customer_id" class="form-select select2" id="basicSelect">
                                                        <option selected value="{{ $customer_id }}">{{ App\AdminModels\Customers::where('id', $customer_id)->first()->customer_name }}</option>
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
                                                    <input type="text" class="form-control" id="brand" value=""  name="brand" />
                                                    @error('brand')
                                                          <p class="text-danger">{{ $message }}</p>
                                                      @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="button" data-bs-dismiss="modal" id="submit_btn" class="btn btn-primary me-1">Submit</button>
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
    {{-- <div class="modal fade text-start" id="add_label_modal" tabindex="-1" aria-labelledby="add_label_modal" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_product_selling_price">Selling Price</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="row" id="basic-table">
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
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
    
@endsection
@section('page_js')
    <script>
    $(document).ready(function() {
        @if (session('success'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-success');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('success')}}");
            $('#toast-btn').click();
        @endif
        $(document).on('click', '#submit_btn', function() {
            $('#brand_form').submit();
        });
        $(document).on('click', '.edit_product', function() {
            var product_id = $(this).attr('data-product_id');
            $('#modal_selling_price').attr('data-product_id', product_id);
        });
        $(document).on('click', '.add_prod_btn', function(e) {
            e.preventDefault();
            var _this = $(this);
            var checkProd = 1;
            $('.product').each(function() {
                if ($(this).val() == '' || $(this).val() == null) {
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
            $('.purchasing-price').each(function() {
                var This = $(this);
                var selling_cost = This.closest('tr').find('.selling_price').val();
                var purchasing_cost = This.html();
                purchasing_cost = purchasing_cost.substring(1);
                purchasing_cost = purchasing_cost.trim(purchasing_cost);
                if (Number(selling_cost) <= Number(purchasing_cost)) {
                    This.closest('tr').find('.selling_price').css('border', '1px solid red');
                    This.closest('tr').find('.sellingMsg').removeClass('d-none');
                    count = 0;
                    // return false;
                } else {
                    This.closest('tr').find('.selling_price').css('border', '1px solid lightgray');
                    This.closest('tr').find('.sellingMsg').addClass('d-none');
                }
            });
            if (count == 1) {
                var customer_id = $('#custId').val();
                var prodIds = [];
                var labelsStatuses = [];
                var selling_costs = [];
                var labelStatus = 1;
                var html = '';
                var url = '{{ route("save_customer_product", ":id") }}';
                url = url.replace(':id', customer_id);
                $('.product').each(function() {
                    prodIds.push($(this).val());
                    if ($(this).closest('tr').find('.labelSwitch').prop('checked') == true) {
                        labelStatus = 0;
                    } else {
                        labelStatus = 1;
                    }
                    labelsStatuses.push(labelStatus);
                    selling_costs.push($(this).closest('tr').find('.selling_price').val());
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: customer_id,
                        prod_ids : prodIds,
                        labelStatus: labelsStatuses,
                        selling_cost: selling_costs
                    },
                    success:function(response) {
                        html += `
                            <tr>
                                <td>`+response.data.product_name+`</td>
                                <td>
                                    <div class="font-weight-bold text-success text-center">$ `+response.data.purchasing_cost+` </div>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-success text-center">`+response.data.weight+` oz</div>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-success text-center"> $ `+response.data.selling_price+`</td></div>
                                <td>
                                    <div class="col-sm-9">
                                        <div class="form-check form-check-primary form-switch">
                                            <input type="checkbox"`;
                                            if(response.data.is_active == 0) {
                                                html += ` checked `;
                                            }
                                            html += `name="is_active" class="form-check-input labelSwitch" data-labelPid="`+prodIds[0]+`">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="`+customer_id+`" data-product_id="`+prodIds[0]+`" data-bs-toggle="dropdown">
                                        . . .
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item edit_product" href="#" data-bs-target="#edit_product" data-customer_id="`+customer_id+`" data-product_id="`+prodIds[0]+`" data-selling_price="`+response.data.selling_price+`" data-bs-toggle="modal">
                                                <span>Edit Selling Price</span>
                                            </a>
                                            
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                        $('#products_table').append(html);
                        $('.product').each(function(){
                            $(this).find('option:selected').remove();
                            $(this).closest('tr').find('.purchasing-price').html('$ 0.00');
                            $(this).closest('tr').find('.product-weight').html('0.00');
                            $(this).closest('tr').find('.selling_price').val('');
                            $(this).closest('tr').find('.labelSwitch').prop('checked', false);
                        });
                    }
                });
            }
        });
        $(document).on('change', '.product', function() {
            var id = $(this).val();
            var context = $(this);
            var customer_id = $('#custId').val();
            var url = '{{ route("get_product_labels", ":id") }}';
            var url2 = '{{ route("getProductstoCustomer") }}';
            url2 = url2.replace(':id', id);
            url = url.replace(':id', id);
            if(id=="") {
                
            } else {
                $.ajax({
                    url: url2,
                    type: 'GET',
                    data: {
                        _token: '{{csrf_token()}}',
                        'product_id': id
                    },
                    success:function(res) {
                        if(res) {
                            context.closest('tr').find('.purchasing-price').html('$ '+Number(res.price).toFixed(2));
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
        $(document).on('keyup', '.selling_price', function() {
            var purchasing_cost = $(this).closest('tr').find('.purchasing-price').html();
            purchasing_cost = purchasing_cost.substring(1);
            purchasing_cost = purchasing_cost.trim(purchasing_cost);
            if (Number($(this).val()) <= Number(purchasing_cost)) {
                $(this).css('border', '1px solid red');
                $(this).closest('tr').find('.sellingMsg').removeClass('d-none');
            } else {
                $(this).css('border', '1px solid lightgray');
                $(this).closest('tr').find('.sellingMsg').addClass('d-none');
            }
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
                    url: '{{ route("update_customer_prod_selling_price") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        selling_price: selling_price,
                        customer_id: customer_id,
                        product_id: product_id
                    },
                    success:function(response) {
                        location.reload();
                    }
                });
            }
        });
        $(document).on('click', '.more_btn', function() {
            var customer_id = $('#custId').val();
            var brand_id = $(this).closest('tr').find('.brandId').val();
            var product_id = $(this).closest('tr').find('.productId').val();
            $(this).data('product-id', product_id);
            $('.add-labels').data('product-id', product_id);
            $('.reduce-labels').data('product-id', product_id);
            $('.add-label-cost').data('product-id', product_id);
            $('.history_more_btn').data('product-id', product_id);
            $('.history_more_btn').attr('href', '/customer/'+customer_id+'/brand/'+brand_id+'/product/'+product_id+'/labels');
            $('.forecast_more_btn').data('product-id', product_id);
            $('.forecast_more_btn').attr('href', '/customer/'+customer_id+'/brand/'+brand_id+'/product/'+product_id+'/labelforecast');
        });
        $(document).on('change', '.labelSwitch', function() {
            var custId = $('#custId').val();
            // var brandId = $(this).data('brand_id');
            var prodId = $(this).data('labelpid');
            var status = 1;
            if ($(this).prop('checked') == true) {
                status = 0;
                $.ajax({
                    url: '{{ route("set_label_status") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: custId,
                        // brand_id: brandId,
                        product_id: prodId,
                        status: status
                    },
                    success:function(response) {

                    }
                });
            }
            else {
                status = 1;
                $.ajax({
                    url: '{{ route("set_label_status") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: custId,
                        // brand_id: brandId,
                        product_id: prodId,
                        status: status
                    },
                    success:function(response) {

                    }
                });
            }
        });
        $(document).on('click', '.add-labels', function() {
            var product_id = $(this).data('product-id');
            alert(product_id);
            var brand_id = $(this).data('brand-id');
            $('#add-labels').data('product-id', product_id);
            $('#add-labels').data('brand-id', brand_id);
        });
        $(document).on('click', '.reduce-labels', function() {
            var product_id = $(this).data('product-id');
            var brand_id = $(this).data('brand-id');
            $('#reduce-labels').data('product-id', product_id);
            $('#reduce-labels').data('brand-id', brand_id);
        });
        $(document).on('click', '#reduce-labels', function() {
            var customer_id = $('#custId').val();
            var brand_id = $(this).data('brand-id');
            var product_id = $(this).data('product-id');
            var qty = $('#reduceqtyPrice').val();
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
                    window.location.reload();
                }
            });
        });
        $(document).on('click', '#add-labels', function() {
            var customer_id = $('#custId').val();
            var brand_id = $(this).data('brand-id');
            var product_id = $(this).data('product-id');
            var qty = $('#qtyPrice').val();
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
                    window.location.reload();
                }
            });
        });
        $(document).on('click', '.add-label-cost', function() {
            var product_id = $(this).data('product-id');
            var brand_id = $(this).data('brand-id');
            $('#add-label-cost').data('product-id', product_id);
            $('#add-label-cost').data('brand-id', brand_id);
        });
        $(document).on('click', '#add-label-cost', function() {
            var customer_id = $('#custId').val();
            var brand_id = $(this).data('brand-id');
            var product_id = $(this).data('product-id');
            var label_cost = $('#labelCost').val();
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
                    window.location.reload();
                }
            });
        });
        $('#show_product_modal').on('hidden.bs.modal', function (e) {
            window.view_product_datatable.destroy();
        });
    });
    </script>
@endsection