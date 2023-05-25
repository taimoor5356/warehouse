@extends('admin.layout.app')
@section('title', 'Add SKU')
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
                            <h2 class="content-header-title float-start mb-0">Add SKU</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Customers
                                    </li>
                                    <li class="breadcrumb-item"><a href="/sku">Manage SKUs</a>
                                    </li>
                                    <li class="breadcrumb-item">Add SKU</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-3 addBtnClass">
                            <button style="margin-left:auto;" onclick="return window.history.back()" class="btn btn-primary waves-effect waves-float waves-light"><i data-feather="arrow-left"></i> Back</button>
                            <a href="/sku" target="blank" class="btn btn-primary waves-effect waves-float waves-light">View SKUs</a>
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
            <form class="skuForm" id="skuForm" action="{{route('sku.store')}}" method="post">
                @csrf
                <div class="row">
                    
                    <div class="col-md-3">
                        <div class="mb-1">
                            @php
                                $customer_id = '';
                                $name = '';
                                if(isset(request()->customer_id)){
                                    $customer_id = request()->customer_id;
                                    $cust = App\AdminModels\Customers::where('id', $customer_id)->first();
                                    if (isset($cust)){
                                        $name = $cust->customer_name;
                                    }
                                }
                            @endphp
                            <input type="hidden" id="url_customer_id" value="{{ $customer_id }}">
                            <label class="form-label" for="customer">Customer</label>
                            <select name="customer" id="customer" class="form-select select2" required>
                                @if ($customer_id != '')
                                    <option selected value="{{ $customer_id }}">{{$name}}</option>
                                @else
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option {{ old('customer') == $customer->id ? "selected" : "" }} value="{{$customer->id}}">{{$customer->customer_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label" for="Brand">Brand</label>
                            <select name="brand" id="brand" class="form-select select2" required>
                                <option value="">Select Brand</option>
                            </select>
                            <div id="brand-error" class="text-danger font-weight-bold"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label" for="first-name-column">SKU ID</label>
                            <input type="text" id="sku_name_id" class="form-control" placeholder="S1, S2 etc" name="sku_id" maxlength="25" required>
                            <small class="text-danger d-none sku_name_id_err">Required</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label class="form-label" for="first-name-column">SKU Name</label>
                            <input type="text" id="sku_name" class="form-control" placeholder="" name="sku_name" maxlength="100000" onKeyDown="if(this.value.length==100000 && event.keyCode!=8) return false;">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    {{-- <p>Products</p> --}}
                    <div class="col-12">
                        <table class="table table-hover products-table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 200px">Product</th>
                                    <th style="width: 10px" class="text-center">Qty</th>
                                    <th style="width: 20px" class="text-center">Labels</th>
                                    <th style="width: 180px" class="text-center">Purchasing Cost</th>
                                    <th style="width: 50px" class="text-center">Weight</th>
                                    <th style="width: 200px" class="text-center">
                                        <span>Service Charges</span>
                                        <br>
                                        <center>
                                            <div class="col-8">
                                                <div class="form-check form-switch form-check-primary">
                                                    <input type="checkbox" disabled name="default_pick_pack_flat_status" class="form-check-input" id="default_pick_pack_flat_status" style="font-size: 30px; width: 110px">
                                                    <label class="form-check-label">
                                                        <span class="switch-icon-left" style="font-size: 9px; margin-top: 6px;">Flat Rate ON</span>
                                                        <span class="switch-icon-right" style="font-size: 9px; margin-top: 6px; color: black">Flat Rate OFF</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </center>
                                        <div class="flatRateRequired d-none" style="color: red; margin-top: 8px">Required</div>
                                    </th>
                                    <th style="width: 150px" class="text-center">Selling Cost</th>
                                    <th style="width: 10px" class="">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="products[]" class="product form-select select2" id="product-list" required>
                                            <option value="">Select Product</option>
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
                                            <input type="checkbox" class="form-check-input pick label_pick_pack" data-name="pick" id="pick_name" name="pick_name0[]" value="0" data-product-id="">
                                            <label class="form-label pick_name" for="pick_name">Pick <span style="font-weight: bold"></span></label>
                                            <input type="hidden" name="serv_charges[]">
                                        </div>
                                        <div>
                                            <input type="checkbox" class="form-check-input pack label_pick_pack" data-name="pack" id="pack_name" name="pack_name0[]" value="0" data-product-id="">
                                            <label class="form-label pack_name" for="pack_name">Pack <span style="font-weight: bold"></span></label>
                                            <input type="hidden" name="serv_charges[]">
                                        </div>
                                        <div>
                                            <input type="checkbox" style="visibility: hidden" class="form-check-input flat label_pick_pack flat_label_pick_pack_status" onclick="return false;" data-name="flat" id="pick_pack_flat" name="pick_pack_flat0[]" value="0" data-pick-pack-flat-rate="" data-product-id="">
                                            <label class="form-label pick_pack_flat" for="pick_pack_flat"><span style="font-weight: bold"></span></label>
                                            <input type="hidden" name="serv_charges[]">
                                        </div>
                                        <div class="chargesSum d-none"></div>
                                        <div class="sumOfCharges" style="color: red"></div>
                                    </td>
                                    <td>
                                        <div class="input-icon">
                                            <input type="number" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" readonly name="selling_price[]" class="selling_price form-control" step="0.01" placeholder="Selling Cost" value="0.00" min="0.01">
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
                                <input type="number" min="1" placeholder="" name="qty" class="form-control" id="qty">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="add-labels" data-bs-dismiss="modal" data-product-id="" data-brand-id="">Add</button>
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
            $('.toast .toast-body').html("Something went wrong");
            $('#toast-btn').click();
        @endif
        $(document).on('change', '#default_pick_pack_flat_status', function() {
            var customerId = $('#customer').val();
            var _this = $(this);
            $.get('/get_customer_charges/'+customerId+'', function(result) {
                if(result.status == 'success') {
                    if(_this.is(':checked')) {
                        if (result.data.pick_pack_flat == 0) {
                            alert('Set Flat rate first.');
                            $('.submit_btn').attr('disabled', true);
                            $('.flat').each(function(){
                                $(this).closest('tr').find('input.pick').prop('checked', false);
                                $(this).closest('tr').find('input.pack').prop('checked', false);
                                $(this).closest('tr').find('input.pick').val('0');
                                $(this).closest('tr').find('input.pack').val('0');
                                $(this).closest('tr').find('input.pick').attr('disabled', true);
                                $(this).closest('tr').find('input.pack').attr('disabled', true);
                            });
                            return false;
                        } else {
                            $('.submit_btn').attr('disabled', false);
                            $('.flat').each(function(){
                                $(this).prop('checked', true);
                                $(this).attr('disabled', false);
                                $(this).val(result.data.pick_pack_flat);
                                $(this).closest('tr').find('input.pick').prop('checked', false);
                                $(this).closest('tr').find('input.pack').prop('checked', false);
                                $(this).closest('tr').find('input.pick').val('0');
                                $(this).closest('tr').find('input.pack').val('0');
                                $(this).closest('tr').find('input.pick').attr('disabled', true);
                                $(this).closest('tr').find('input.pack').attr('disabled', true);
                            });
                        }
                    } else {
                        $('.submit_btn').attr('disabled', false);
                        // if (result.data.pick_pack_flat == 0) {
                        //     alert('Set Flat rate first');
                        //     return false;
                        // }
                        $('.flat').each(function(){
                            $(this).prop('checked', false);
                            $(this).attr('disabled', true);
                            $(this).val('0');
                            // $(this).closest('tr').find('input.pick').prop('checked', true);
                            // $(this).closest('tr').find('input.pack').prop('checked', true);
                            $(this).closest('tr').find('input.pick').attr('disabled', false);
                            $(this).closest('tr').find('input.pack').attr('disabled', false);
                        });
                    }
                } else {
                    if (result.data.pick_pack_flat == 0) {
                        alert('Set Flat rate first');
                        return false;
                    }
                }
            });
            $('.flat_label_pick_pack_status').each(function(){
                var _this = $(this);
                _this.prop('checked', true);
                _this.val(1);
            });
        });
        if($('#url_customer_id').val() != '') {
            var id = $('#url_customer_id').val();
            var url = '';
            if(id=="") {
                $('#brand').html('<option value="">Select Brand</option>');
                $('.product').each(function() {
                    $(this).html('<option value="">Select Product</option>');
                });
            } else {
                $.get('/customer/'+id+'/brand', function(result) {
                    if(result.status == "success") {
                        var options = "<option value=''>Select Brand</option>";
                        result.data.forEach(element => {
                            options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                        });
                        $('#brand-error').html('');
                        $('#brand').html(options);
                    } else {
                        $('#brand').html("<option value=''>Select Brand</option>");
                        $('#brand-error').html(result.message);
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
            $(this).closest('tr').find('.purchasing-price').html('$ 0.00');
            $(this).closest('tr').find('.total-weight .product-weight').html('0.00');
            $(this).closest('tr').find('.selling_price').val('0.00');
            });
        }
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
                    $('.flatRateRequired').removeClass('d-none');
                    count2 = 0;
                } else {
                    This.closest('tr').find('.sumOfCharges').html('');
                    $('.flatRateRequired').addClass('d-none');
                }
            });
            var checkProducts = 1;
            $('.product').each(function() {
                if ($(this).val() == '') {
                    $(this).closest('tr').find('.prodReq').removeClass('d-none');
                    checkProducts = 0;
                } else {
                    $(this).closest('tr').find('.prodReq').addClass('d-none');
                }
            });
            if ($('#sku_name_id').val() == '') {
                $('.sku_name_id_err').removeClass('d-none');
                return false;
            } else {
                $('.sku_name_id_err').addClass('d-none');
            }
            if (checkProducts == 1 && count2 == 1) {
                var formData = new FormData($('#skuForm')[0]);
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: "{{route('sku.store')}}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    dataType: 'json',
                    encode: true,
                    success:function(response) {
                        if (response.status == true) {
                            $('.toast .toast-header').removeClass('bg-danger');
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.msg);
                            $('#toast-btn').click();
                        } else {
                            $('.toast .toast-header').removeClass('bg-success');
                            $('.toast .me-auto').html('Error');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.msg);
                            $('#toast-btn').click();
                        }
                    }
                })
                // $('.skuForm').submit();
            }
        });
        $(document).on('keyup', '.selling_price', function() {
            var purchasing_cost = $(this).closest('tr').find('.purchasing-price').html();
            purchasing_cost = purchasing_cost.substring(1);
            purchasing_cost = purchasing_cost.trim(purchasing_cost);
            // if (Number($(this).val()) <= Number(purchasing_cost)) {
            //     $(this).css('border', '1px solid red');
            //     $(this).closest('tr').find('.sellingMsg').removeClass('d-none');
            // } else {
            //     $(this).css('border', '1px solid lightgray');
            //     $(this).closest('tr').find('.sellingMsg').addClass('d-none');
            // }
        });
        $(document).on('click', '.add_qty', function() {
            qty = $(this).closest('div.input-group').find('input.qty').val();
            // $(this).closest('div.input-group').find('input.qty').val(Number(Number(qty)+Number(1)));
        });
        $(document).on('click', '.subt_qty', function() {
            qty = $(this).closest('div.input-group').find('input.qty').val();
            if (qty <= 0) {
                $(this).closest('div.input-group').find('input.qty').val(0);
            } else {
                // $(this).closest('div.input-group').find('input.qty').val(Number(Number(qty)-Number(1)));
            }
        });
        $(document).on('change', '#customer', function() {
            var id = $(this).val();
            var url = '';
            if(id=="") {
                $('#brand').html('<option value="">Select Brand</option>');
                $('.product').each(function() {
                    $(this).html('<option value="">Select Product</option>');
                });
            } else {
                $.get('/customer/'+id+'/brand', function(result) {
                    if(result.status == "success") {
                        var options = "<option value=''>Select Brand</option>";
                        result.data.forEach(element => {
                            options += "<option value='"+element.id+"'>"+element.brand+"</option>"
                        });
                        $('#brand-error').html('');
                        $('#brand').html(options);
                    } else {
                        $('#brand').html("<option value=''>Select Brand</option>");
                        $('#brand-error').html(result.message);
                    }
                });
            }
            $('label.label_name span').empty();
            $('label.pick_name span').empty();
            $('label.pack_name span').empty();
            // $('.label_pick_pack').prop('checked', false);
            $('.label_qty').each(function() {
            $(this).html('0');
            $(this).closest('tr').find('.label-qty-val').val(0);
            $(this).closest('tr').find('.purchasing-price').html('$ 0.00');
            $(this).closest('tr').find('.total-weight .product-weight').html('0.00');
            $(this).closest('tr').find('.selling_price').val('0.00');
            });
        });
        $(document).on('change', '#brand', function() {
            $('#default_pick_pack_flat_status').attr('disabled', false);
            var id = $(this).val();
            var context = $(this);
            var customer_id = $('#customer').val();
            var brand_id = $('#brand').val();
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
                        if (result.data.pick == 0) {
                            alert('Customer Charges not set');
                            _this.prop('checked', false);
                            return false;
                        }
                        if (_this.is(':checked')) {
                            _this.closest('tr').find('input.pick').val(result.data.pick);
                            _this.closest('tr').find('input.flat').prop('checked', false);
                            _this.closest('tr').find('input.flat').val(0);
                            _this.closest('tr').find('input.flat').attr('disabled', true);
                            $('.pick').each(function() {
                                var This = $(this);
                                if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                    // This.closest('tr').find('input.pick').val(result.data.pick);
                                    // This.closest('tr').find('input.pick').prop('checked', true);
                                    // This.closest('tr').find('input.flat').prop('checked', false);
                                    // This.closest('tr').find('input.flat').val(0);
                                    // This.closest('tr').find('input.flat').attr('disabled', true);
                                }
                            });
                            // _this.closest('tr').find('label.pick_name span').html('- $' + result.data.pick);
                        } else {
                            $('.pick').each(function() {
                                var This = $(this);
                                if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                    // This.closest('tr').find('input.pick').prop('checked', false);
                                }
                            });
                            if (_this.closest('tr').find('input.pack').is(':checked')) {
                                $('.pack').each(function() {
                                    var This = $(this);
                                    if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                        // This.closest('tr').find('input.pack').val(result.data.pack);
                                        // This.closest('tr').find('input.pack').prop('checked', true);
                                        // This.closest('tr').find('input.flat').prop('checked', false);
                                        // This.closest('tr').find('input.flat').attr('disabled', true);
                                    }
                                });
                            } else {
                                $('.pack').each(function() {
                                    var This = $(this);
                                    if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                        // This.closest('tr').find('input.pack').prop('checked', false);
                                        // This.closest('tr').find('input.flat').prop('checked', true);
                                        // This.closest('tr').find('input.flat').val(result.data.pick_pack_flat);
                                        // This.closest('tr').find('input.flat').attr('disabled', false);
                                    }
                                });
                                // _this.closest('tr').find('input.flat').prop('checked', true);
                                // _this.closest('tr').find('input.flat').val(result.data.pick_pack_flat);
                                // _this.closest('tr').find('input.flat').attr('disabled', false);
                            }
                            _this.closest('tr').find('input.pick').val('');
                            _this.closest('tr').find('label.pick_name span').html('');
                        }
                    }
                    if (_this.data('name') == 'pack') {
                        if (result.data.pack == 0) {
                            alert('Customer Charges not set');
                            _this.prop('checked', false);
                            return false;
                        }
                        if (_this.is(':checked')) {
                            _this.closest('tr').find('input.pack').val(result.data.pack);
                            // _this.closest('tr').find('input.flat').prop('checked', false);
                            // _this.closest('tr').find('input.flat').attr('disabled', true);
                            $('.pack').each(function() {
                                var This = $(this);
                                if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                    // This.closest('tr').find('input.pack').val(result.data.pack);
                                    // This.closest('tr').find('input.pack').prop('checked', true);
                                    // This.closest('tr').find('input.flat').prop('checked', false);
                                    // This.closest('tr').find('input.flat').attr('disabled', true);
                                }
                            });
                            // _this.closest('tr').find('label.pack_name span').html('- $' + result.data.pack);
                        } else {
                            $('.pack').each(function() {
                                var This = $(this);
                                if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                    // This.closest('tr').find('input.pack').prop('checked', false);
                                    // This.closest('tr').find('input.flat').prop('checked', true);
                                    // This.closest('tr').find('input.flat').val(result.data.pick_pack_flat);
                                    // This.closest('tr').find('input.flat').attr('disabled', false);
                                }
                            });
                            if (_this.closest('tr').find('input.pick').is(':checked')) {
                                $('.pick').each(function() {
                                    var This = $(this);
                                    if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                        // This.closest('tr').find('input.pick').val(result.data.pick);
                                        // This.closest('tr').find('input.pick').prop('checked', true);
                                        // This.closest('tr').find('input.flat').prop('checked', false);
                                        // This.closest('tr').find('input.flat').attr('disabled', true);
                                    }
                                });
                            } else {
                                // _this.closest('tr').find('input.flat').prop('checked', true);
                                // _this.closest('tr').find('input.flat').val(result.data.pick_pack_flat);
                                // _this.closest('tr').find('input.flat').attr('disabled', false);
                                $('.pick').each(function() {
                                    var This = $(this);
                                    if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                        // This.closest('tr').find('input.pick').prop('checked', false);
                                        // This.closest('tr').find('input.flat').prop('checked', true);
                                        // This.closest('tr').find('input.flat').val(result.data.pick_pack_flat);
                                        // This.closest('tr').find('input.flat').attr('disabled', false);
                                    }
                                });
                            }
                            _this.closest('tr').find('input.pack').val('');
                            _this.closest('tr').find('label.pack_name span').html('');
                        }
                    }
                    if (_this.data('name') == 'flat') {
                        if (result.data.pick_pack_flat == 0 || result.data.pick_pack_flat == '') {
                            alert('Customer Charges not set');
                            _this.prop('checked', false);
                            return false;
                        }
                        if (_this.is(':checked')) {
                            _this.closest('tr').find('input.flat').val(result.data.pick_pack_flat);
                            $('.flat').each(function() {
                                var This = $(this);
                                if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                    // This.closest('tr').find('input.flat').val(result.data.pick_pack_flat);
                                }
                            });
                            // _this.closest('tr').find('label.pick_pack_flat span').html('- $' + result.data.pick_pack_flat);
                        } else {
                            $('.flat').each(function() {
                                var This = $(this);
                                if(This.attr('data-product-id') == _this.attr('data-product-id')) {
                                }
                            });
                            _this.closest('tr').find('input.flat').val('');
                            _this.closest('tr').find('label.pick_pack_flat span').html('');
                        }
                    }
                }
            });
        });
        $(document).on('change', '.product', function() {
            var id = $(this).val();
            var context = $(this);
            context.closest('tr').find('.label_pick_pack').attr('data-product-id', id);
            // context.closest('tr').find('input#pick_pack_flat').prop('checked', true);
            var customer_id = $('#customer').val();
            var brand_id = $('#brand').val();
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
                            if (res.data.seller_cost_status == 0) {
                                selling_price = '0.00';
                            }
                            context.closest('tr').find('.purchasing-price').html('$ '+Number(res.data.products.price).toFixed(2));
                            context.closest('tr').find('.selling_price').val(Number(selling_price).toFixed(2));
                            if (res.data.products.product_unit.unit_type == 1) {
                                context.closest('tr').find('.product-weight').html(Number((res.data.products.weight) / (Number(16))).toFixed(2));
                                context.closest('tr').find('.p-weight').val(Number((res.data.products.weight) / (Number(16))).toFixed(2));
                            } else {
                                context.closest('tr').find('.product-weight').html(Number(res.data.products.weight).toFixed(2));
                                context.closest('tr').find('.p-weight').val(Number(res.data.products.weight).toFixed(2));
                            }
                            // context.closest('tr').find('.unit').html(res.data.products.product_unit.name);
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
        $(document).on('click', '.add-product-row', function() {
            var count = ($('.new-rows').length) + 1;
            var id = $('#customer').val();
            var brand_id = $('#brand').val();
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
                        <tr class="new-rows">
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
                                    <input type="checkbox" class="form-check-input pick label_pick_pack" data-name="pick" id="pick_name" name="pick_name`+count+`[]" value="0" data-product-id=""`;
                                    if($('#default_pick_pack_flat_status').is(':checked')) {
                                        html += `unchecked disabled`;
                                    }
                                    html +=`>
                                    <label class="form-label pick_name" for="pick_name">Pick <span style="font-weight: bold"></span></label>
                                    <input type="hidden" name="serv_charges[]">
                                </div>
                                <div>
                                    <input type="checkbox" class="form-check-input pack label_pick_pack" data-name="pack" id="pack_name" name="pack_name`+count+`[]" value="0" data-product-id=""`;
                                    if($('#default_pick_pack_flat_status').is(':checked')) {
                                        html += `unchecked disabled`;
                                    }
                                    html +=`>
                                    <label class="form-label pack_name" for="pack_name">Pack <span style="font-weight: bold"></span></label>
                                    <input type="hidden" name="serv_charges[]">
                                </div>
                                <div>
                                    <input type="checkbox" style="visibility: hidden" class="form-check-input flat label_pick_pack flat_label_pick_pack_status" onclick="return false;" data-name="flat" id="pick_pack_flat" name="pick_pack_flat`+count+`[]" value="0" data-pick-pack-flat-rate="" data-product-id=""`;
                                    if($('#default_pick_pack_flat_status').is(':checked')) {
                                        html += `checked`;
                                    } else {
                                        html += `unchecked`;
                                    }
                                    html +=`>
                                    <label class="form-label pick_pack_flat" for="pick_pack_flat"><span style="font-weight: bold"></span></label>
                                    <input type="hidden" name="serv_charges[]">
                                </div>
                                <div class="chargesSum d-none"></div>
                                <div class="sumOfCharges" style="color: red"></div>
                            </td>
                            <td>
                                <div class="input-icon">
                                    <input type="number" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" readonly name="selling_price[]" class="selling_price form-control" step="0.01" placeholder="Selling Cost" value="0.00" min="0.01">
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
                    $('.remove-product-row i').replaceWith(feather.icons['minus'].toSvg());
                }
            });
        });
        $(document).on('click', '.remove-product-row', function(){
            $(this).closest('tr').remove();
            let countNum = 1;
            $('tr.new-rows').each(function() {
                $(this).find('td .pick').attr('name', 'pick_name'+countNum+'[]');
                $(this).find('td .pack').attr('name', 'pack_name'+countNum+'[]');
                $(this).find('td .flat').attr('name', 'pick_pack_flat_name'+countNum+'[]');
                countNum++;
            });
        });
        $(document).on('keyup', '.qty', function(){
            var qty = $(this).val();
            if (qty != 1) {
                $(this).val(1);
            }
            if(qty != "") {
                var weight_element = $(this).closest('tr').find('.product-weight');
                var weight = parseFloat($(this).closest('tr').find('.p-weight').val());
                var new_weight = qty * weight;
                weight_element.html(Number(new_weight).toFixed(2));
            }
        });
        $(document).on('click', '.more_btn', function() {
            var product_id = $(this).closest('tr').find('.products').val();
            $(this).data('product-id', product_id);
            $('.add-labels').data('product-id', product_id);
            $('.history_more_btn').data('product-id', product_id);
            $('.history_more_btn').attr('href', '/product_labels_history/'+product_id);
        });
        $(document).on('click', '.add-labels', function() {
            var product_id = $(this).data('product-id');
            var brand_id = $(this).data('brand-id');
            $('#add-labels').data('product-id', product_id);
            $('#add-labels').data('brand-id', brand_id);
        });
        $(document).on('click', '#add-labels', function() {
            var customer_id = $('#customer').val();
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
                    getLables();
                }
            });
        });
        function getLables() {
            var customer_id = $('#customer').val();
            var brand_id = $('#brand').val();
            $('.product').each(function() {
                var _this = $(this);
                var product_id = $(this).val();
                var url = '{{ route("get_product_labels", ":id") }}';
                url = url.replace(':id', product_id);
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
                            _this.closest('tr').find('.label_qty').html(response);
                            _this.closest('tr').find('.label-qty-val').val(response);
                        } else{
                            _this.closest('tr').find('.label_qty').html(response);
                            _this.closest('tr').find('.label-qty-val').val(0);
                        }
                    }
                });
            });
        }
        });
        </script>
@endsection



