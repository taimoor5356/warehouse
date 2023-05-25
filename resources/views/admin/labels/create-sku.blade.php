@extends('admin.layout.app')
@section('title', 'Add Brand SKU')
@section('datatablecss')

@stop

@section('content')

<!-- BEGIN: Content-->
            <div class="content-header row">
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-9">
                                    <h2 class="content-header-title float-start mb-0">Add Brand SKU</h2>
                                    <div class="breadcrumb-wrapper">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/">Home</a>
                                            </li>
                                            <li class="breadcrumb-item">Customers
                                            </li>
                                            <li class="breadcrumb-item"><a href="/brands">Manage Brands</a>
                                            </li>
                                            <li class="breadcrumb-item"><a href="/brand/{{request()->route()->id}}/sku">Brand SKUs</a>
                                            </li>
                                            <li class="breadcrumb-item">Add Brand SKU</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="col-3 addBtnClass">
                                    @can('view',\App\Models\Labels::class)
                                    <button style="margin-left:auto;" onclick="return window.history.back()" class="btn btn-primary waves-effect waves-float waves-light"><i data-feather="arrow-left"></i> Back</button>
                                    @endcan
                            
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                
            </div>
            <div class="card">
                <div class="card-header">

                </div>
                <div class="card-body">
                    <form action="{{route('sku.store')}}" class="skuForm" method="post">
                        @csrf
                        <input type="hidden" id="brand_id" name="brand_id" value="{{$brand->id}}">
                        <input type="hidden" id="customer_id" name="customer_id" value="{{$brand->customer->id}}">
                        <div class="row">
                            
                            <div class="col-md-3">
                                <div class="mb-1">
                                    <label class="form-label" for="customer">Customer</label>
                                    <input type="text" name="customer" class="form-control" id="customer" value="{{$brand->customer->customer_name}}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-1">
                                    <label class="form-label" for="brand">Brand</label>
                                    <input type="text" name="brand" class="form-control" id="brand" value="{{$brand->brand}}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-1">
                                    <label class="form-label" for="first-name-column">SKU ID</label>
                                    <input type="text" id="" class="form-control" placeholder="S1, S2 etc" name="sku_id" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-1">
                                    <label class="form-label" for="first-name-column">SKU Name</label>
                                    <input type="text" id="sku_name" class="form-control" placeholder="" name="sku_name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <p>Products</p>
                            <table class="table table-hover products-table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 200px">Product</th>
                                        <th style="width: 20px" class="text-center">Quantity</th>
                                        <th style="width: 20px" class="text-center">Labels</th>
                                        <th style="width: 80px" class="text-center">Purchasing Cost</th>
                                        <th style="width: 50px" class="text-center">Weight</th>
                                        <th style="width: 200px" class="text-center">Service Charges</th>
                                        <th style="width: 200px" class="text-center">Selling Cost</th>
                                        <th style="width: 10px" class="">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="products[]" class="product form-select select2" id="product-list" required>
                                                <option value="">Select Product</option>
                                                {{-- @foreach ($products as $product)
                                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                                @endforeach --}}
                                            </select>
                                            <div class="product-error text-danger"></div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-success text-center" data-toggle="" title="">1</div>
                                            <input type="hidden" name="qty[]" class="qty" value="1">
                                        </td>
                                        <td>
                                            <div class="label_qty font-weight-bold text-success text-center" data-toggle="" title="">0</div>
                                            <input type="hidden" value="0" class="label-qty-val">
                                        </td>
                                        <td>
                                            <div class="purchasing-price font-weight-bold text-success text-center">$0.00 </div>
                                        </td>
                                        <td>
                                            <div class="total-weight font-weight-bold text-success text-center"><span class="product-weight">0.00</span><span class="unit">oz</span></div>
                                            <input type="hidden" name="" class="p-weight" value="">
                                        </td>
                                        <td class="service_charges">
                                            {{-- <div>
                                                <input type="checkbox" class="form-check-input label label_pick_pack" data-name="label" id="label_name" name="label_name0[]" value="0">
                                                <label class="form-label label_name" for="label_name">Label <span style="font-weight: bold"></span></label>
                                                <input type="hidden" name="serv_charges[]">
                                            </div> --}}
                                            <div>
                                                <input type="checkbox" class="form-check-input pick label_pick_pack" data-name="pick" id="pick_name" name="pick_name0[]" value="0">
                                                <label class="form-label pick_name" for="pick_name">Pick <span style="font-weight: bold"></span></label>
                                                <input type="hidden" name="serv_charges[]">
                                            </div>
                                            <div>
                                                <input type="checkbox" class="form-check-input pack label_pick_pack" data-name="pack" id="pack_name" name="pack_name0[]" value="0">
                                                <label class="form-label pack_name" for="pack_name">Pack <span style="font-weight: bold"></span></label>
                                                <input type="hidden" name="serv_charges[]">
                                            </div>
                                            <div class="chargesSum d-none"></div>
                                            <div class="sumOfCharges" style="color: red"></div>
                                        </td>
                                        <td>
                                            <div class="input-icon">
                                                <input type="number" name="selling_price[]" class="selling_price form-control" step="0.01" placeholder="Selling Cost" value="0.00" min="0.01">
                                                <i>$</i>
                                            </div>
                                            <div class="sellingMsg d-none" style="color: red; font-size: 12px">Should be greater than Purchasing Cost</div>
                                        </td>
                                        <td>
                                            <button class="m-auto btn-primary add-product-row border-0 rounded-circle shadow" type="button"><i data-feather="plus"></i></button>
                                        </td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="text-right w-100">
                            <button type="button" class="btn btn-primary ml-auto submit_btn"> Save</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 col-12">
                    
                </div>
                    
            </div>
       {{-- <div class="product-row " style="display: none">
        <table>
            <tbody>
                <tr>
                    <td>
                        <select name="products[]" class="product form-select :select2 cloned-products" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                        <div class="product-error text-danger"></div>
                    </td>
                    <td>
                        <input type="number" name="qty[]" class="form-control text-center qty" placeholder="Quantity" value="1" min="1">
                    </td>
                    <td>
                        <div class="purchasing-price font-weight-bold text-success text-center">$0.00 </div>
                    </td>
                    <td>
                        <div class="total-weight font-weight-bold text-success text-center"><span class="product-weight">0.00</span><span class="unit">oz</span></div>
                        <input type="hidden" name="" class="p-weight" value="">
                    </td>
                    <td>
                        <div class="input-icon">
                            <input type="number" name="selling_price[]" class="selling_price form-control text-center" step="0.01" placeholder="Selling Cost" value="0.00" min="1">
                            <i>$</i>
                        </div>
                    </td>
                    <td>
                        <button class="border-0 rounded-circle shadow btn-danger remove-product-row" type="button"><i data-feather="minus"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
       </div> --}}
    <!-- END: Content-->
    @section('modal')
        {{-- Add Inventory Modal --}}
        <div class="modal fade text-start show" id="inventoryModal" tabindex="-1" aria-labelledby="myModalLabel33" style="" aria-modal="true" role="dialog">
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-inventory" data-bs-dismiss="modal">Add</button>
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
        $(document).on('ready', function(){
            var context = $(this);
            var customer_id = $('#customer_id').val();
            var brand_id = $('#brand_id').val();
            if(customer_id=="") {
            
            } else {
                $.ajax({
                    url: '{{ route("sku.create") }}',
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: customer_id,
                        brand_id: brand_id
                    },
                    success:function(response) {
                        $('.product').each(function() {
                            if (response.length > 0) {
                                var options = "<option value=''>Select Product</option>";
                                response.forEach(element => {
                                    options += "<option value='"+element.product_id+"'>"+element.products.name+"</option>"
                                });
                                $(this).closest('tr').find('.product-error').addClass('d-none');
                                $(this).html(options);
                            } else {
                                $(this).html("<option value=''>Select Product</option>");
                                $(this).closest('tr').find('.product-error').removeClass('d-none');
                            }
                        });
                    }
                });
            }
            $('label.label_name span').empty();
            $('label.pick_name span').empty();
            $('label.pack_name span').empty();
            $('.label_pick_pack').prop('checked', false);
            $('.label_qty').each(function() {
            $(this).html('0');
            $(this).closest('tr').find('.label-qty-val').val(0);
            });
            $(document).on('click', '.submit_btn', function(e) {
                e.preventDefault();
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
                    var serviceCharges = This.closest('tr').find('.label_pick_pack');
                    var s_count = 0;
                    serviceCharges.each(function() {
                        var _this = $(this);
                        s_count = Number(s_count) + Number(_this.val());
                    });
                    This.closest('tr').find('.chargesSum').html(s_count);
                    var sCount = This.closest('tr').find('.chargesSum').html();
                    if (sCount <= 0) {
                        This.closest('tr').find('.sumOfCharges').html('Atleast 1 Required');
                        count2 = 0;
                        // return false;
                    } else {
                        This.closest('tr').find('.sumOfCharges').html('');
                    }
                });
                if (count == 1 && count2 == 1) {
                    // alert('Form Submitted');
                    $('.skuForm').submit();
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
            $(document).on('click', '.label_pick_pack', function() {
                var customerId = $('#customer').val();
                var _this = $(this);
                $.get('/get_customer_charges/'+customerId+'', function(result) {
                    if (result.status == 'success') {
                        if (_this.data('name') == 'label') {
                            if (_this.is(':checked')) {
                                _this.closest('tr').find('input.label').val(result.data.labels);
                                // _this.closest('tr').find('label.label_name span').html('- $' + result.data.labels);
                            } else {
                                _this.closest('tr').find('input.label').val('');
                                _this.closest('tr').find('label.label_name span').html('');
                            }
                        }
                        if (_this.data('name') == 'pick') {
                            if (_this.is(':checked')) {
                                _this.closest('tr').find('input.pick').val(result.data.pick);
                                // _this.closest('tr').find('label.pick_name span').html('- $' + result.data.pick);
                            } else {
                                _this.closest('tr').find('input.pick').val('');
                                _this.closest('tr').find('label.pick_name span').html('');
                            }
                        }
                        if (_this.data('name') == 'pack') {
                            if (_this.is(':checked')) {
                                _this.closest('tr').find('input.pack').val(result.data.pack);
                                // _this.closest('tr').find('label.pack_name span').html('- $' + result.data.pack);
                            } else {
                                _this.closest('tr').find('input.pack').val('');
                                _this.closest('tr').find('label.pack_name span').html('');
                            }
                        }
                    }
                });
            });
            $(document).on('change', '.product', function() {
                var id = $(this).val();
                var context = $(this);
                var customer_id = $('#customer_id').val();
                var brand_id = $('#brand_id').val();
                var url = '{{ route("get_product_labels", ":id") }}';
                var url2 = '{{ route("products.show", ":id") }}';
                url2 = url2.replace(':id', id);
                url = url.replace(':id', id);
                if(id=="") {
                
                } else {
                    $.ajax({
                        url: url2,
                        type: 'GET',
                        data: {
                            _token: '{{csrf_token()}}',
                            'customer_id': customer_id,
                            'product_id': id,
                            'brand_id': brand_id,
                        },
                        success:function(res) {
                            if(res.status == "success") {
                                selling_price = res.data.selling_price;
                                if (selling_price == 0 || selling_price == '' || selling_price == null) {
                                    selling_price = res.data.products.price;
                                }
                                context.closest('tr').find('.purchasing-price').html('$ '+Number(res.data.products.price).toFixed(2));
                                context.closest('tr').find('.selling_price').val(Number(selling_price).toFixed(2));
                                context.closest('tr').find('.product-weight').html(Number(res.data.products.weight).toFixed(2));
                                context.closest('tr').find('.p-weight').val(Number(res.data.products.weight).toFixed(2));
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
            var count = 1;
            $(document).on('click', '.add-product-row', function() {
                var id = $('#customer_id').val();
                var brand_id = $('#brand_id').val();
                $.ajax({
                    url: '{{ route("sku.create") }}',
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: id,
                        brand_id: brand_id
                    },
                    success:function(response) {
                        if (response.length > 0) {
                            var options = "";
                            response.forEach(element => {
                                options += "<option value='"+element.product_id+"'>"+element.products.name+"</option>"
                            });
                            $('.product-error').addClass('d-none');
                        } else {
                            $('.product').html("<option value=''>Select Product</option>");
                            $('.product-error').removeClass('d-none');
                        }
                        var html = `
                            <tr>
                                <td>
                                    <select name="products[]" class="product form-select select2 getProducts" id="product-list" required>
                                        <option value="">Select Product</option>
                                        `+options+`
                                    </select>
                                    <div class="product-error text-danger d-none">No Product Found. Please add Brand first.</div>
                                    <div class="prodReq text-danger d-none">Required</div>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-success text-center" data-toggle="" title="">1</div>
                                    <input type="hidden" name="qty[]" class="qty" value="1">
                                </td>
                                <td>
                                    <div class="label_qty font-weight-bold text-success text-center" data-toggle="" title="">0</div>
                                    <input type="hidden" value="0" class="label-qty-val">
                                </td>
                                <td>
                                    <div class="purchasing-price font-weight-bold text-success text-center">$0.00 </div>
                                </td>
                                <td>
                                    <div class="total-weight font-weight-bold text-success text-center"><span class="product-weight">0.00</span><span class="unit">oz</span></div>
                                    <input type="hidden" name="" class="p-weight" value="">
                                </td>
                                <td>
                                    <div>
                                        <input type="checkbox" class="form-check-input pick label_pick_pack" data-name="pick" id="pick_name" name="pick_name`+count+`[]" value="0">
                                        <label class="form-label pick_name" for="pick_name">Pick <span style="font-weight: bold"></span></label>
                                        <input type="hidden" name="serv_charges[]">
                                    </div>
                                    <div>
                                        <input type="checkbox" class="form-check-input pack label_pick_pack" data-name="pack" id="pack_name" name="pack_name`+count+`[]" value="0">
                                        <label class="form-label pack_name" for="pack_name">Pack <span style="font-weight: bold"></span></label>
                                        <input type="hidden" name="serv_charges[]">
                                    </div>
                                    <div class="chargesSum d-none"></div>
                                    <div class="sumOfCharges" style="color: red"></div>
                                </td>
                                <td>
                                    <div class="input-icon">
                                        <input type="number" name="selling_price[]" class="selling_price form-control" step="0.01" placeholder="Selling Cost" value="0.00" min="0.01">
                                        <i>$</i>
                                    </div>
                                    <div class="sellingMsg d-none" style="color: red; font-size: 12px">Should be greater than Purchasing Cost</div>
                                </td>
                                <td>
                                    <button class="border-0 rounded-circle shadow btn-danger remove-product-row" type="button"><i data-feather="minus"></i></button>
                                </td>
                                
                            </tr>

                        `;
                        count++;
                        $('table tbody').append(html);
                    }
                });
            });
            $(document).on('click', '.remove-product-row', function(){
                $(this).closest('tr').remove();
            });
            $(document).on('keyup', '.qty', function(){
                var qty = $(this).val();
                if(qty != "") {
                    var weight_element = $(this).closest('tr').find('.product-weight');
                    var weight = parseFloat($(this).closest('tr').find('.p-weight').val());
                    var new_weight = qty * weight;
                    weight_element.html(Number(new_weight).toFixed(2));
                }
            });
        });

          
        </script>
@endsection



