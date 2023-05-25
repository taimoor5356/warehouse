@extends('admin.layout.app')
@section('title', 'Edit Customer Brand')
@section('datepickercss')
{{-- <link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}"> --}}
@stop

@section('content')
<style type="text/css">
    .bootstrap-touchspin.input-group-lg {
    width: 13.375rem !important;
}
</style>
<section id="basic-horizontal-layouts">
    <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Edit Customer Brand</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Customers
                                    </li>
                                    <li class="breadcrumb-item"><a href="/customers">Manage Customers</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Customer Brand
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Brand</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" enctype='multipart/form-data' action="/customer/{{request()->route()->id}}/brand/{{$brand->id}}/update" method="post">
                                       {{@csrf_field()}}
                                        <div class="row">
                                          
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="catselect">Select Customer</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" value="{{$brand->customer->customer_name}}" class="form-control" readonly>
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
                                                        <input type="text" class="form-control" id="brand" value="{{$brand->brand}}"  name="brand" maxlength="50" required/>
                                                        @error('brand')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="catselect">Enter Mailer Charges</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <div class="input-icon">
                                                            <input type="number" min="0.0" step="0.01" id="mailer_cost" class="form-control" value="{{number_format($brand->mailer_cost, 2)}}" name="mailer_cost"/>
                                                            <i>$</i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary me-1">Submit</button>
                                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                            </div>
                                            {{--  <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Initial labels</label>
                                                    </div>
                                                    <div class="input-group input-group-lg">
                                                        <input type="number" id="qty" value="{{$dataSet['qty']}}" class="touchspin2" name="qty" />
                                                        @error('qty')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>  --}}
                                        </div>
                                        {{-- <hr> --}}
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </section>
                @section('modal')
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
                                            <input type="number" maxlength="4" onKeyDown="if(this.value.length==4 && event.keyCode!=8) return false;" required min="0" placeholder="Enter Selling Price" name="selling_price" class="form-control" id="modal_selling_price" data-product_id="" data-customer_id="">
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
                                            <input type="number" maxlength="4" onKeyDown="if(this.value.length==4 && event.keyCode!=8) return false;" required min="1" placeholder="" name="label_qty" class="form-control" id="qtyPrice">
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
                                            <input type="number" maxlength="4" onKeyDown="if(this.value.length==4 && event.keyCode!=8) return false;" required min="1" placeholder="" name="label_qty" class="form-control" id="reduceqtyPrice">
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
                                            <input type="number" maxlength="4" onKeyDown="if(this.value.length==4 && event.keyCode!=8) return false;" required min="1" placeholder="" name="label_cost" class="form-control" id="labelCost">
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
                        $('.touchspin2').TouchSpin({
                            min: 0,
                            max: 100000000,
                            step: 1,
                        });
                        $(document).on('change', '.product', function() {
                            var id = $(this).val();
                            var context = $(this);
                            var customer_id = $('#customer_id').val();
                            var brand_id = $('#brand_id').val();
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
                                            context.closest('tr').find('.selling_price').val(Number(res.price).toFixed(2));
                                            context.closest('tr').find('.product-weight').html(Number(res.weight).toFixed(2));
                                            context.closest('tr').find('.p-weight').val(Number(res.weight).toFixed(2));
                                        } else {
                                            $('.product-error').html(res.message);
                                        }
                                    }
                                });
                                $.ajax({
                                    url: url,
                                    type: 'GET',
                                    data: {
                                        _token: '{{csrf_token()}}',
                                        'customer_id': customer_id,
                                        'brand_id': brand_id,
                                    },
                                    success:function(response) {
                                        if (response > 0) {
                                            context.closest('tr').find('.label_qty').html(response);
                                            context.closest('tr').find('.label-qty-val').val(response);
                                        } else{
                                            context.closest('tr').find('.label_qty').html(response);
                                            context.closest('tr').find('.label-qty-val').val(0);
                                        }
                                    }
                                });
                            }
                        });
                        $(document).on('click', '.add_prod_btn', function(e) {
                            e.preventDefault();
                            var checkProd = 1;
                            var _this = $(this);
                            $('.product').each(function() {
                                if ($(this).val() == '' || $(this).val() == null) {
                                    $(this).closest('tr').find('.product-error').html('Required');
                                    checkProd = 0;
                                    return false;
                                } else {
                                    $(this).closest('tr').find('.product-error').html('');
                                    _this.attr('disabled', true);
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
                                var customer_id = $('#customer_id').val();
                                var brand_id = $('#brand_id').val();
                                var prodIds = [];
                                // var labelsStatuses = [];
                                // var selling_costs = [];
                                // var labelStatus = 1;
                                var html = '';
                                var url = '{{ route("save_customer_brand_product") }}';
                                url = url.replace(':id', customer_id);
                                $('.product').each(function() {
                                    prodIds.push($(this).val());
                                    // if ($(this).closest('tr').find('.labelSwitch').prop('checked') == true) {
                                    //     labelStatus = 0;
                                    // } else {
                                    //     labelStatus = 1;
                                    // }
                                    // labelsStatuses.push(labelStatus);
                                    // selling_costs.push($(this).closest('tr').find('.selling_price').val());
                                });
                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        customer_id: customer_id,
                                        brand_id: brand_id,
                                        prod_ids : prodIds,
                                        // labelStatus: labelsStatuses,
                                        // selling_cost: selling_costs
                                    },
                                    success:function(response) {
                                        html += `
                                            <tr>
                                                <td>`+response.data.product_name+`</td>
                                                <td>
                                                    <div class="font-weight-bold text-success text-center">$ 0.00</div>
                                                </td>
                                                <td>
                                                    <div class="font-weight-bold text-success text-center">0</div>
                                                </td>
                                                <td>
                                                    <div class="font-weight-bold text-success text-center"><span class="badge rounded-pill badge-light-danger me-1">0d</span></div>
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
                                                            {{-- <a class="dropdown-item history_more_btn" href="" data-product-id="">
                                                                <span>Show History</span>
                                                            </a> --}}
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
                                        });
                                        _this.attr('disabled', false);
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
                            var url = '{{ route("add_label_to_product") }}';
                            $('#labelsModal').hide();
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
                            $('#labelCostModal').hide();
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
                    });
                </script>
@endsection
@section('datepickerjs')
    {{-- <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script> --}}
    
@stop

