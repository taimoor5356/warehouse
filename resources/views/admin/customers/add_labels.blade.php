@extends('admin.layout.app')
@section('title', 'Manage Labels')
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
    .merge_product:hover{
        text-decoration: underline;
    }
</style>
    <!-- BEGIN: Content-->
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="row">
                            <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">Manage Labels</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Customers
                                    </li>
                                    <li class="breadcrumb-item">Manage Labels
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
                                            {{-- @foreach($brands as $brand)
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
                                        @foreach($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->customer_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <br>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-2 offset-md-10">
                                    <label for="customer">Search:</label>
                                    <input type="text" class="form-control" id="myInputField">
                                </div>
                            </div> --}}
                            
                        </div>
                        <div class="table-responsive" style="max-height: 1000px">
                            <table class="table data-table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 12%"><center><img class="show_all_btn" src="https://datatables.net/examples/resources/details_open.png" alt=""></center></th>
                                        <th>Brand</th>
                                        <th>Customer</th>
                                        {{-- <th>Product</th> --}}
                                        {{-- <th>Labels </th> --}}
                                        <th>Date</th>
                                        {{-- <th>Actions</th> --}}
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
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-labels" data-brand-id="" data-customer_id="" data-bs-dismiss="modal" data-product-id="">Add</button>
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
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="reduce-labels" data-brand-id="" data-customer_id="" data-bs-dismiss="modal" data-product-id="">Submit</button>
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
                    <h4 class="modal-title" id="myModalLabel34">Update Label Charges</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                         {{--  --}}
                        <div class="form-check form-switch form-check-primary mb-1">
                            <input type="checkbox" name="label_cost_type"  class="form-check-input " id="label_cost_type" value="1" style="font-size: 50px; width:60px;">
                            <label class="form-check-label">
                                <span class="switch-icon-left" data-on="default" style="font-size: 9px; margin-top: 6px; ">Default</span>
                                <span class="switch-icon-right" data-off="custom" style="font-size: 9px; margin-top: 6px; margin-left: -6px; color: rgb(88, 4, 4); ">Custom</span>
                            </label>
                        </div>
                        {{--  --}}
                        
                        <label>Labels Cost: </label>
                        <div class="mb-1">
                            <input type="number" min="1" placeholder="" name="label_cost" class="form-control" id="labelCost">
                        </div>
                        <small class="d-none negative_err text-danger">Can't Add Negative Value</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-label-cost" data-brand-id="" data-customer_id="" data-bs-dismiss="modal" data-product-id="">Update</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-start show" id="mergeModal" tabindex="-1" aria-labelledby="myModalLabel31" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel31">Link with</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body mergeItems">
                        <table class="table table-bordered">
                            <span id="productData"></span>
                            {{-- <a href="#" target="_blank" class="btn btn-primary" id="view_ledger" style="float: right">View List</a> --}}
                            <br>
                            <br>
                            <br>
                            <thead>
                                <th><input type="checkbox" class="selectAll"></th>
                                <th>Customer</th>
                                <th>Brand</th>
                                <th>Product</th>
                                <th>Label Qty</th>
                            </thead>
                            <tbody id="mergedProductsTable">
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light submit_merge" data-brand-id="" data-customer_id="" data-bs-dismiss="" data-product-id="" data-label-qty="">Merge</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
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
            // get customer brand
            $(document).on('change', '#customer', function() {
                $('.customer-error').html('');
                var id = $(this).val();
                if(id=="all") {
                    $.get('/getAllBrands', function(result) {
                        if(result.status == "success") {
                            var options = "<option value=''>All</option>";
                            result.data.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                            });
                            
                            $('#brand-error').html('');
                            $('#brand').html(options);
                        } else {
                            $('#brand').html("<option value=''>All</option>");
                            $('#brand-error').html(result.message);
                        }
                    });
                } else {
                    $.get('/customer/'+id+'/brand', function(result) {
                        if(result.status == "success") {
                            var options = "<option value=''>All</option>";
                            result.data.forEach(element => {
                                options += "<option value='"+element.id+"'>"+element.brand+"</option>"
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
            $(document).on('click', '.merge_product', function(){
                var productId = $(this).data('product-id');
                var customerId = $(this).data('customer-id');
                var brandId = $(this).data('brand-id');
                var labelQty = $(this).data('label-qty');
                var url = '{{ route("get_products_customers", ":id") }}';
                url = url.replace(':id', productId);
                // if (alreadyLinked != '') {
                //     $('#productData').html('');
                //     $('.selectAll').attr('disabled', true);
                //     $('#productData').html('<span class="text-danger">Already Linked</span>');
                // } else {
                //     $('.selectAll').attr('disabled', false);
                // }
                $('.submit_merge').data('product-id', productId);
                $('.submit_merge').data('customer-id', customerId);
                $('.submit_merge').data('brand-id', brandId);
                $('.submit_merge').data('label-qty', labelQty);
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        customer_id: customerId,
                        brand_id: brandId,
                    },
                    success:function(res)
                    {
                        var label_Qty = res.label_qty;
                        $('#productData').html('Set label quantity <b>'+ label_Qty +'</b> for below selected products.');
                        var html = '';
                        $('#mergedProductsTable').empty();
                        for (let index = 0; index < res.products.length; index++) {
                            var checked = '';
                            if (res.products[index].status == 'true') {
                                checked = 'checked';
                            }
                            html += `
                                <tr>
                                    <td><input type="checkbox" `+checked+` name="merge_item[]" class="checkedItems" data-customer-id="`+res.products[index].customer_id+`" data-brand-id="`+res.products[index].brand_id+`" data-product-id="`+res.products[index].product_id+`"></td></td>
                                    <td>`+res.products[index].customer_name+`</td>
                                    <td>`+res.products[index].brand_name+`</td>
                                    <td>`+res.products[index].product_name+`</td>
                                    <td>`+res.products[index].label_qty+`</td>
                                </tr>
                            `;
                        }
                        $('#mergedProductsTable').append(html);
                    }
                });
            });
            $(document).on('click', '.selectAll', function(){
                if ($(this).is(':checked')) {
                    $('.checkedItems').each(function(){
                        if (!$(this).is(':checked')) {
                            $(this).prop('checked', true);
                        }
                    });
                } else {
                    $('.checkedItems').each(function(){
                        if ($(this).is(':checked')) {
                            $(this).prop('checked', false);
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
                            $('.toast .toast-header').removeClass('bg-danger');
                            $('#reducelabelsModal').modal('hide');
                            // window.location.reload();
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                            $('.newdatatable'+brand_id).DataTable().draw();
                        } else if (response.error == true) {
                            $('.toast .toast-header').removeClass('bg-success');
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
                // window.myhtmldatatable.clear();
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
                            $('.toast .toast-header').removeClass('bg-danger');
                            $('#labelsModal').modal('hide');
                            // window.location.reload();
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                            $('.newdatatable'+brand_id).DataTable().draw();
                        } else if (response.error == true) {
                            $('.toast .toast-header').removeClass('bg-success');
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
                            $('.toast .toast-header').removeClass('bg-danger');
                            $('#labelCostModal').modal('hide');
                            // window.location.reload();
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                            $('.newdatatable'+brand_id).DataTable().draw();
                        } else if (response.error == true) {
                            $('.toast .toast-header').removeClass('bg-success');
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
            $(document).on('click', '.submit_merge', function(){
                var productId = $(this).data('product-id');
                var customerId = $(this).data('customer-id');
                var brandId = $(this).data('brand-id');
                var labelQty = $(this).data('label-qty');
                var arrProductIds = [];
                var arrCustomerIds = [];
                var arrBrandIds = [];
                var arrBrandStatus = [];
                $('.checkedItems').each(function(){
                    var productIds = $(this).data('product-id');
                    var customerIds = $(this).data('customer-id');
                    var brandIds = $(this).data('brand-id');
                    if ($(this).is(':checked')) {
                        arrProductIds.push(productIds);
                        arrCustomerIds.push(customerIds);
                        arrBrandIds.push(brandIds);
                        arrBrandStatus.push('checked');
                    } else {
                        arrProductIds.push(productIds);
                        arrCustomerIds.push(customerIds);
                        arrBrandIds.push(brandIds);
                        arrBrandStatus.push('unchecked');
                    }
                });
                $.ajax({
                    url: "{{ route('save_merged_items') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_ids: arrProductIds,
                        customer_ids: arrCustomerIds,
                        brand_ids: arrBrandIds,
                        status: arrBrandStatus,
                        merged_customer_id: customerId,
                        merged_brand_id: brandId,
                        merged_qty: labelQty,
                        product_id: productId
                    },
                    success:function(response){
                        if (response.success == true) {
                            $('.toast .toast-header').removeClass('bg-danger');
                            $('#mergeModal').modal('hide');
                            // window.location.reload();
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                            $('.newdatatable'+brand_id).DataTable().draw();
                        } else if (response.error == true) {
                            $('.toast .toast-header').removeClass('bg-success');
                            $('#mergeModal').modal('hide');
                            $('.toast .me-auto').html('Error');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                        }
                    }
                });
            });
        });
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                // ordering: true,
                // paging: false,
                // searching: false,
                // stateSave: true,
                // bFilter: false,
                // ajax: "{{ route('label/admin') }}",
                ajax: {
                    url: "{{ route('manage_labels') }}",
                    data: function (d) {
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
                    {'className': 'details-control', 'orderable': false, 'data': null, 'defaultContent': '', searchable: false, orderable: false},
                    {data: 'brand', name: 'brand', orderable: true},
                    {data: 'customer_name', name: 'customer_name', orderable: false},
                    {data: 'date', name: 'date', searchable: false},
                    // {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "drawCallback": function( settings ) {
                    feather.replace();
                },
                'order': [1, 'asc'],
            });
            $("#myInputField").keyup(function () {
                var value = this.value.toLowerCase().trim();
                $("table tr").each(function (index) {
                    if (!index) return;
                    $(this).find("td").each(function () {
                        var id = $(this).text().toLowerCase().trim();
                        var not_found = (id.indexOf(value) == -1);
                        $(this).closest('tr').toggle(!not_found);
                        return not_found;
                    });
                });
            });
            // Add event listener for opening and closing details
            $('.data-table tbody').on('click', 'td.details-control', function () {
                var context = $(this);
                var tr = $(this).closest('tr');
                var row = table.row( tr );
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    row.child( format(row.data()) ).show();
                    tr.addClass('shown');
                    var customer_id = row.data().customer.id;
                    var brand_id = row.data().id;
                    // return new Promise((resolve, reject) => {
                        var mydatatable = $('.newdatatable'+brand_id).DataTable({
                            // processing: true,
                            "searching": false,
                            "paging": false,
                            serverSide: true,
                            async: false,
                            ordering: true,
                            bDestroy: true,
                            cache: false,
                            stateSave: true,
                            ajax: {
                                url: '{{ route("get_labels_data") }}',
                                data: {
                                    customer_id: customer_id,
                                    brand_id: brand_id
                                },
                                success:function(response) {
                                    var html = '';
                                    // for(var i = 0; i < response.length; i++) {
                                    $('.mydatatable'+brand_id).empty();
                                    response.brands.forEach(element => {
                                        html += `
                                            <tr>`;
                                                if (element.products.length > 0)
                                                    element.products.forEach(product => {
                                                        html +=`
                                                        <tr>
                                                            <td>
                                                                <a href="#" title="Link with" class="merge_product" data-bs-toggle="modal" data-bs-target="#mergeModal" data-customer-id="`+customer_id+`" data-brand-id="`+brand_id+`" data-product-id="`+product.prod_id+`" data-label-qty="`+product.labels_qty+`">`+product.prod_name+`</a>
                                                            </td>
                                                            <td>
                                                                $ `+product.label_cost+`
                                                            </td>
                                                            <td>
                                                                `+product.labels_qty+`
                                                            </td>
                                                            <td>`;
                                                            if(product.status == 0) {
                                                                html += '<center><span class="badge rounded-pill me-1" style="background-color: lightgreen; color: darkgreen">On</span></center>';
                                                            } else {
                                                                html += '<center><span class="badge rounded-pill me-1" style="background-color: pink; color: red">Off</span></center>'
                                                            }
                                                            html +=`
                                                            </td>
                                                            <td>
                                                                `+product.forecast_labels+`
                                                            </td>
                                                            <td>
                                                                <input type="hidden" class="productId" value="`+product.prod_id+`" name="prodId[]">
                                                                <input type="hidden" class="brandId" value="`+element.brand_id+`">
                                                                <div class="dropdown">
                                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="`+customer_id+`" data-product-id="" data-bs-toggle="dropdown">
                                                                    . . .
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item add-labels" href="#" data-brand-id="`+element.brand_id+`" data-bs-target="#labelsModal" data-customer_id="" data-product-id="" `;
                                                                        if(product.status != 0) {
                                                                            html +=`style="cursor: not-allowed" onclick="alert('Please turn on the label status from customer info page')"`;
                                                                        } else {
                                                                            html +=`data-bs-toggle="modal"`;
                                                                        }
                                                                        html+=`>
                                                                            <span>Add Labels</span>
                                                                        </a>
                                                                        <a class="dropdown-item reduce-labels" href="#" data-brand-id="`+element.brand_id+`" data-customer_id="" data-bs-target="#reducelabelsModal" data-product-id=""  `;
                                                                        if(product.status != 0) {
                                                                            html +=`style="cursor: not-allowed" onclick="alert('Please turn on the label status from customer info page')"`;
                                                                        } else {
                                                                            html +=`data-bs-toggle="modal"`;
                                                                        }
                                                                        html+=`>
                                                                            <span>Reduce Labels</span>
                                                                        </a>
                                                                        <a class="dropdown-item add-label-cost" href="#"  data-brand-id="`+element.brand_id+`" data-bs-target="#labelCostModal" data-customer_id="" data-product-id="" `;
                                                                        if(product.status != 0) {
                                                                            html +=`style="cursor: not-allowed" onclick="alert('Please turn on the label status from customer info page')"`;
                                                                        } else {
                                                                            html +=`data-bs-toggle="modal"`;
                                                                        }
                                                                        html+=`>
                                                                            <span>Edit Label Charges</span>
                                                                        </a>
                                                                        <a href="#" title="Link with" class="merge_product dropdown-item" data-bs-toggle="modal" data-bs-target="#mergeModal" data-customer-id="`+customer_id+`" data-brand-id="`+brand_id+`" data-product-id="`+product.prod_id+`" data-label-qty="`+product.labels_qty+`">Link with
                                                                        </a>
                                                                        <a href="#"`;
                                                                        if (product.status != 0) {
                                                                            html += `style="cursor: not-allowed" onclick="alert('Please turn on the label status from customer info page')"`;
                                                                        } else {
                                                                            html += `class="dropdown-item resetToZeroLabel" data-customer-id="`+customer_id+`" data-brand-id="`+brand_id+`" data-product-id="`+product.prod_id+`"`;
                                                                        }
                                                                        html += `>
                                                                            <span>Reset to 0</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>`;
                                                    });
                                                html +=`            
                                            </tr>
                                        `;
                                });
                                // }
                                // context.closest('tr').after('tr').find('.mydatatable').append(html);
                                $('.mydatatable'+brand_id).append(html);
                                }
                            },
                        });
                    // });
                    window.myhtmldatatable = mydatatable;
                }
            });
            $(document).on('click', '.resetToZeroLabel', function() {
                if (confirm("Want to RESET Label quantity?") == true) {
                    var _this = $(this);
                    var brandId = _this.attr('data-brand-id');
                    $.ajax({
                        url: '{{ route("reset-label-to-zero") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_id: _this.attr('data-customer-id'),
                            brand_id: _this.attr('data-brand-id'),
                            product_id: _this.attr('data-product-id'),
                            qty: 0,
                        },
                        success:function(response) {
                            if (response.success == true) {
                                $('.toast .toast-header').removeClass('bg-danger');
                                $('#reducelabelsModal').modal('hide');
                                $('.toast .me-auto').html('Success');
                                $('.toast .toast-header').addClass('bg-success');
                                $('.toast .text-muted').html('Now');
                                $('.toast .toast-body').html(response.message);
                                $('#toast-btn').click();
                                $('.newdatatable'+brandId).DataTable().draw();
                            } else if (response.error == true) {
                                $('.toast .toast-header').removeClass('bg-success');
                                $('#reducelabelsModal').modal('hide');
                                $('.toast .me-auto').html('Add/Update');
                                $('.toast .toast-header').addClass('bg-danger');
                                $('.toast .text-muted').html('Now');
                                $('.toast .toast-body').html(response.message);
                                $('#toast-btn').click();
                            }
                        }
                    });
                } 
                else {
                
                }
            });
            $(document).on('click', '.show_all_btn', function() {
                var _this = $(this);
                $('.data-table tbody td.details-control').each(function() {
                    // $(this).click();
                    var tr = $(this).closest('tr');
                    var row = table.row( tr );
            
                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                        _this.attr('src', 'https://datatables.net/examples/resources/details_open.png');
                        // _this.addClass('btn-success');
                        // _this.removeClass('btn-danger');
                        // _this.html('+');
                    }
                    else {
                        // Open this row
                        row.child( format(row.data()) ).show();
                        tr.addClass('shown');
                        _this.attr('src', 'https://datatables.net/examples/resources/details_close.png');
                        var customer_id = row.data().customer.id;
                        var brand_id = row.data().id;
                    // return new Promise((resolve, reject) => {
                        var mydatatable = $('.newdatatable'+brand_id).DataTable({
                            // processing: true,
                            "searching": false,
                            "paging": false,
                            serverSide: true,
                            async: false,
                            ordering: true,
                            bDestroy: true,
                            cache: false,
                            stateSave: true,
                            ajax: {
                                url: '{{ route("get_labels_data") }}',
                                data: {
                                    customer_id: customer_id,
                                    brand_id: brand_id
                                },
                                success:function(response) {
                                var html = '';
                                    // for(var i = 0; i < response.length; i++) {
                                    $('.mydatatable'+brand_id).empty();
                                    response.brands.forEach(element => {
                                        html += `
                                            <tr>`;
                                                if (element.products.length > 0)
                                                    element.products.forEach(product => {
                                                        html +=`
                                                        <tr>
                                                            <td>
                                                                <a href="#" title="Link with" class="merge_product" data-bs-toggle="modal" data-bs-target="#mergeModal" data-customer-id="`+customer_id+`" data-brand-id="`+brand_id+`" data-product-id="`+product.prod_id+`" data-label-qty="`+product.labels_qty+`">`+product.prod_name+`</a>
                                                            </td>
                                                            <td>
                                                                $ `+product.label_cost+`
                                                            </td>
                                                            <td>
                                                                `+product.labels_qty+`
                                                            </td>
                                                            <td>`;
                                                            if(product.status == 0) {
                                                                html += '<center><span class="badge rounded-pill me-1" style="background-color: lightgreen; color: darkgreen">On</span></center>';
                                                            } else {
                                                                html += '<center><span class="badge rounded-pill me-1" style="background-color: pink; color: red">Off</span></center>'
                                                            }
                                                            html +=`
                                                            </td>
                                                            <td>
                                                                `+product.forecast_labels+`
                                                            </td>
                                                            <td>
                                                                <input type="hidden" class="productId" value="`+product.prod_id+`" name="prodId[]">
                                                                <input type="hidden" class="brandId" value="`+element.brand_id+`">
                                                                <div class="dropdown">
                                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="`+customer_id+`" data-product-id="" data-bs-toggle="dropdown">
                                                                    . . .
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item add-labels" href="#" data-brand-id="`+element.brand_id+`" data-bs-target="#labelsModal" data-customer_id="" data-product-id="" `;
                                                                        if(product.status != 0) {
                                                                            html +=`style="cursor: not-allowed" onclick="alert('Please turn on the label status from customer info page')"`;
                                                                        } else {
                                                                            html +=`data-bs-toggle="modal"`;
                                                                        }
                                                                        html+=`>
                                                                            <span>Add Labels</span>
                                                                        </a>
                                                                        <a class="dropdown-item reduce-labels" href="#" data-brand-id="`+element.brand_id+`" data-customer_id="" data-bs-target="#reducelabelsModal" data-product-id=""  `;
                                                                        if(product.status != 0) {
                                                                            html +=`style="cursor: not-allowed" onclick="alert('Please turn on the label status from customer info page')"`;
                                                                        } else {
                                                                            html +=`data-bs-toggle="modal"`;
                                                                        }
                                                                        html+=`>
                                                                            <span>Reduce Labels</span>
                                                                        </a>
                                                                        <a class="dropdown-item add-label-cost" href="#" data-brand-id="`+element.brand_id+`" data-bs-target="#labelCostModal" data-customer_id="" data-product-id="" `;
                                                                        if(product.status != 0) {
                                                                            html +=`style="cursor: not-allowed" onclick="alert('Please turn on the label status from customer info page')"`;
                                                                        } else {
                                                                            html +=`data-bs-toggle="modal"`;
                                                                        }
                                                                        html+=`>
                                                                            <span>Add Label Charges</span>
                                                                        </a>
                                                                        <a href="#" title="Link with" class="dropdown-item merge_product" data-bs-toggle="modal" data-bs-target="#mergeModal" data-customer-id="`+customer_id+`" data-product-id="`+product.prod_id+`" data-label-qty="`+product.labels_qty+`">
                                                                            <span>Link with</span>
                                                                        </a>
                                                                        <a class="dropdown-item history_more_btn" href="" data-product-id="">
                                                                            <span>Show History</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>`;
                                                    });
                                                html +=`            
                                            </tr>
                                        `;
                                });
                                $('.mydatatable'+brand_id).append(html);
                                }
                            },
                        });
                    }
                });
            });
            $('#customer, #brand').on('change', function () {
                table.draw();
            });
            // Refilter the table
            $('#search').on('click', function () {
                table.draw();
            });

            $("#label_cost_type").on('change', function(){  
              var labelCostType=$("#label_cost_type").val();
            // custom 0
            // default 1
             if (labelCostType==1) {
                var url = '{{ route("toggleLabelCost") }}';
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success:function(response) {
                    $("#labelCost").val(response.lable_cost);
                    $("#labelCost").attr("readonly",true);                }
            });
             $("#label_cost_type").val(0);
             } else {
                $("#labelCost").attr("readonly",false);
                $("#labelCost").val('');
             $("#label_cost_type").val(1);
             }
          });
        });
        /* Formatting function for row details - modify as you need */
        function format (data) {
            var html = '';
            html += `
                <div class="col-12">
                    <div class="mb-1 row mt-1">
                        <center>
                        <div class="col-md-11">
                            <table class="table table-bordered d_Table table-striped table-hover newdatatable`+data.id+`">
                                <thead>
                                    <th style="width: 250px">Product</th>
                                    <th style="width: 150px">Charges Per Label</th>
                                    <th style="width: 80px">Label Qty</th>
                                    <th style="width: 80px">Label Status</th>
                                    <th style="width: 50px">Forecast Days</th>
                                    <th style="width: 50px">More</th>
                                </thead>
                                <tbody class="mydatatable`+data.id+`">
                                </tbody>
                            </table>
                        </div>
                        </center>
                    </div>
                </div>
            `;
            return html;
        }
    </script>
@endsection
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@stop

