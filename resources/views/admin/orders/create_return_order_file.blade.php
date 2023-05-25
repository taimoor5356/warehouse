@extends('admin.layout.app')
@section('title', 'Create Return Order')
@section('datatablecss')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/pages/app-invoice.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"
        integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@stop
@section('content')
    <style type="text/css">
        .hide {
            display: none;
        }

        .sku-table thead th {
            font-size: 0.7vw;
        }

        /* Popup container - can be anything you want */
        .popup {
            position: relative;
            display: inline-block;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* The actual popup */
        .popup .popuptext {
            visibility: visible;
            width: 100px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 70%;
            margin-left: -55px;
        }

        /* Popup arrow */
        .popup .popuptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        .btn_yes:hover {
            color: rgb(74, 202, 74);
        }

        .btn_no:hover {
            color: rgb(253, 59, 59);
        }

        /* Toggle this class - hide and show the popup */
        /* .popup .show {
                visibility: visible;
                -webkit-animation: fadeIn 1s;
                animation: fadeIn 1s;
                } */
        /* Add animation (fade in the popup) */
        @-webkit-keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

    </style>
    <section id="basic-horizontal-layouts">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Create Return Order</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">Home</a>
                                </li>
                                <li class="breadcrumb-item"> Customers
                                </li>
                                <li class="breadcrumb-item"><a href="/order_return">Returns List</a>
                                </li>
                                <li class="breadcrumb-item active">Create Return Order
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="{{ route('save_return_order') }}" id="orderForm" enctype="multipart/form-data" method="post">
            {{ @csrf_field() }}
            <div class="row invoice-add">
                <!-- Invoice Add Left starts -->
                <div class="col-xl-12 col-md-12 col-12">
                    <div class="card invoice-preview-card">
                        <div class="card-body invoice-padding pt-0">
                            <div class="row row-bill-to invoice-spacing">
                                <div class="col-md-3 mb-lg-1 col-bill-to ps-0">
                                    <label for="customer">Select Customer / PO Number </label>
                                    <div class="invoice-customer">
                                        <select name="customer" id="customer" class="form-select select2" required>
                                            <option value="">Select Customer</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">
                                                    @if ($customer->po_box_number)
                                                        <span style="font-weight: bold;">POBox:
                                                            {{ $customer->po_box_number }} - </span>
                                                        {{ $customer->customer_name }}@else{{ $customer->customer_name }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('customer')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        <div class="text-danger customer-error"></div>
                                        <small class="text-danger d-none" id="customer_required">Required</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-lg-1 col-bill-to ps-0">
                                    <label for="brand">Select Brand</label>
                                    <div class="invoice-customer">
                                        <select name="brand" id="brand" class="form-select select2" required>
                                            <option value="">Select Brand</option>
                                        </select>
                                        @error('brand')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                        <div id="brand-error" class="text-danger font-weight-bold"></div>
                                        <small class="text-danger d-none" id="brand_required">Required</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row m-1">
                            <div class="table-responsive">
                                <table class="table table-bordered order_table table-responsive" id="order_table"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 13%" class="">Status</th>
                                            <th style="width: 10%" class="">Order No</th>
                                            <th style="width: 15%" class="">Name</th>
                                            <th style="width: 10%" class="">State</th>
                                            <th style="width: 15%" class="">Product</th>
                                            <th style="width: 10%" class="">Qty</th>
                                            <th style="width: 28%" class="">Notes</th>
                                            <th style="width: 10%" class="">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="">
                                                <select name="item_status[]" class="form-control item_returned_satus">
                                                    <option value="0">-Item Status-</option>
                                                    <option value="1">Return</option>
                                                    <option value="2">Damaged</option>
                                                    <option value="3">Opened</option>
                                                </select>
                                                <span class="item_status_not_selected_error d-none text-danger">Required</span>
                                            </td>
                                            <td class="">
                                                <input type="text" class="form-control order_number" name="order_number[]" placeholder="Enter Order Number">
                                            </td>
                                            <td class="">
                                                <input type="text" class="form-control name" name="name[]" placeholder="Enter Name">
                                            </td>
                                            <td class="">
                                                <input type="text" class="form-control state" name="state[]" placeholder="Enter Enter State">
                                            </td>
                                            <td class="">
                                                <select name="product_id[]" class="form-control product_id" value="0">
                                                    <option value="0">-Select Product-</option>
                                                </select>
                                                <div class="product-error text-danger d-none">No Product Found.</div>
                                                <span class="product_required_error d-none text-danger">Required</span>
                                            </td>
                                            <td class="">
                                                <input type="number" class="form-control deduction_qty returnQuantity" data-price="" data-qty="" name="return_qty[]" placeholder="Enter Quantity">
                                                <input type="hidden" class="form-control total_amount_field" value="0" name="deducted_price[]">
                                                <input type="hidden" class="form-control remaining_qty" name="remaining_qty[]" value="">
                                                <p class="text-danger d-none return_qty_error">Must be greater than 0</p>
                                            </td>
                                            <td class="">
                                                <textarea name="description[]" class="form-control" id="" cols="5" rows="1"
                                                    placeholder="Enter Special Notes"></textarea>
                                            </td>
                                            <td class="">
                                                <button class="m-auto btn-primary add-row border-0 rounded-circle shadow"
                                                    type="button"><i data-feather="plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <!-- Product Details ends -->
                        <!-- Invoice Total starts -->
                        <div class="card-body invoice-padding">
                            <div class="row">
                                <div class="col-md-8">
                                </div>
                                <div class="col-sm-4">
                                    <button type="reset" class="btn btn-outline-secondary" id="reset"
                                        style="float: right">Reset</button>
                                    <button type="button" class="btn btn-primary me-1 saveOrder"
                                        style="float: right">Submit</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-9">
                                </div>
                                <div class="col-sm-3">
                                    <h5 class="text-danger d-none atleast_required_error" style="float: right">Alteast 1
                                        item required</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="totalRes" value="0">
                <input type="hidden" id="orderId" value="">
                <!-- Invoice Add Left ends -->
        </form>
    @section('modal')

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
    @endsection
</section>
<script type="text/javascript">
    $(document).ready(function() {
        @if (session('success'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-success');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{ session('success') }}");
            $('#toast-btn').click();
        @elseif (session('error'))
            $('.toast .me-auto').html('Error');
            $('.toast .toast-header').addClass('bg-danger');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{ session('error') }}");
            $('#toast-btn').click();
        @endif
        // $(document).on('keyup', '#order_number', function() {
        //     var _this = $(this);
        //     $.ajax({
        //         url: '{{ route('check_order_number') }}',
        //         data: {
        //             order_number: _this.val(),
        //         },
        //         success: function(data) {
        //             if (data.msg == 'error') {
        //                 _this.css('border', '1px solid red');
        //                 $('.saveOrder').attr('disabled', true);
        //                 $('.order_number_error').removeClass('d-none');
        //             } else if (data.msg == 'success') {
        //                 _this.css('border', '1px solid lightgrey');
        //                 $('.saveOrder').attr('disabled', false);
        //                 $('.order_number_error').addClass('d-none');
        //             }
        //         }
        //     });
        // });
        $(document).on('change', '#customer', function() {
            $('.customer-error').html('');
            $('#new_customer').val($(this).val());
            var id = $(this).val();
            if (id == "") {
                $('#brand').html('<option value="">Select Brand</option>');
            } else {
                // $.get('/customer/' + id + '/service-charges', function(result) {
                //     if (result.status == "success") {
                //         $('#mailer_cost').val(result.data.mailer);
                //         $('#postage_cost').val(result.data.postage_cost);
                //         $('.pick_cost').val(result.data.pick);
                //         $('.pack_cost').val(result.data.pack);
                //         $('.labels_cost').val(result.data.labels);
                //         $('#pc_lt5').val(result.data.postage_cost_lt5);
                //         $('#pc_lt9').val(result.data.postage_cost_lt9);
                //         $('#pc_lt13').val(result.data.postage_cost_lt13);
                //         $('#pc_gte13').val(result.data.postage_cost_gte13);
                //         $('#pc_default').val(result.data.postage_cost);
                //         setChargesRate('pick_price', result.data.pick);
                //         setChargesRate('pack_price', result.data.pack);
                //         setChargesRate('labels_price', result.data.labels);
                //     } else {
                //         $('#charges-error').html(result.message);
                //     }
                // });
                $.get('/customer/' + id + '/brand', function(result) {
                    if (result.status == "success") {
                        var options = "<option value=''>Select Brand</option>";
                        result.data.forEach(element => {
                            options += "<option value='" + element.id + "'>" + element
                                .brand + "</option>"
                        });
                        $('#brand-error').html('');
                        $('#brand').html(options);
                    } else {
                        $('#brand').html("<option value=''>Select Brand</option>");
                        $('#brand-error').html(result.message);
                    }
                });
            }
        });
        $(document).on('change', '#brand', function() {
            var id = $(this).val();
            var customer_id = $('#customer').val();
            var brandId = $('#brand').val();
            $('.item_returned_satus').each(function(){
                var _this = $(this);
                _this.prop('selectedIndex',0);
                // $(_this+' option:first').prop('selected',true);
            });
            $('.product_id').each(function() {
                var _this = $(this);
                _this.prop('selectedIndex',0);
            });
            if (id != "") {
                getBrandProducts();
            } else {
                $('.product_id').each(function() {
                    var _this = $(this);
                    _this.html("");
                    _this.html("<option value='0'>-Select Product-</option>");
                });
            }
        });
        $(document).on('keyup', '.deduction_qty', function() {
            var _this = $(this);
            var qty = _this.val();
            var totalAmount = Number(qty) * _this.data('price');
            var remainingQty = _this.data('qty') - Number(qty);
            var amountField = _this.closest('tr').find('input.total_amount_field');
            var qtyField = _this.closest('tr').find('input.remaining_qty');
            // if (qty > 0) {
            //     $(this).closest('tr').find('input.order_status_required').attr('required', true);
            // } else {
            //     $(this).closest('tr').find('input.order_status_required').attr('required', false);
            // }
            if (qty > _this.data('qty')) {
                // _this.css('border', '1px solid red');
                // $('.saveOrder').attr('disabled', true);
            } else {
                // _this.css('border', '1px solid lightgrey');
                // $('.saveOrder').attr('disabled', false);
                amountField.val(totalAmount.toFixed(2));
                qtyField.val(remainingQty);
            }
            $('.deduction_qty').each(function() {
                var avaiable = $(this).data('qty');
                if ($(this).val() != '') {} else {
                    // $(this).closest('tr').find('input.order_status_required').attr('required', false);
                }
                if ($(this).val() < 0) {
                    // $(this).css('border', '1px solid red');
                    // $('.saveOrder').attr('disabled', true);
                    // return false;
                }
                if ($(this).val() > avaiable) {
                    // $('.saveOrder').attr('disabled', true);
                    // return false;
                } else {
                    // $('.saveOrder').attr('disabled', false);
                }
            });
        });
        $(document).on('click', '.saveOrder', function(e) {
            var count_0 = 0;
            var count_1 = 0;
            var count_2 = 0;
            var count_3 = 0;
            var count_4 = 0;
            var count_5 = 0;
            var count_6 = 0;
            if ($('#customer').val() == '') {
                $('#customer_required').removeClass('d-none');
                return false;
            } else {
                $('#customer_required').addClass('d-none');

            }
            if ($('#brand').val() == '') {
                $('#brand_required').removeClass('d-none');
                return false;
            } else {
                $('#brand_required').addClass('d-none');

            }
            if ($('#status').val() == '') {
                // $('#status_required').removeClass('d-none');
                // return false;
            } else {
                $('#status_required').addClass('d-none');

            }
            // if ($('#order_number').val() == '') {
            //     $('#order_number_required').removeClass('d-none');
            //     return false;
            // } else {
            //     $('#order_number_required').addClass('d-none');

            // }
            var sumOfQty = 0;
            $('.item_returned_satus').each(function() {
                var _this = $(this);
                var status = _this.val();
                var _Qty = _this.closest('tr').find('input.returnQuantity').val();
                var _product = _this.closest('tr').find('.product_id option:selected').val();
                if (status == 0 && _Qty == 0 && _product == 0) {
                    _this.closest('tr').find('.item_status_not_selected_error').addClass(
                        'd-none');
                    _this.closest('tr').find('.return_qty_error').addClass('d-none');
                    _this.closest('tr').find('.product_required_error').addClass('d-none');
                    count_0 = 1;
                    // return false;
                }
                if (status != 0 && _Qty != 0 && _product != 0) {
                    _this.closest('tr').find('.item_status_not_selected_error').addClass(
                        'd-none');
                    _this.closest('tr').find('.return_qty_error').addClass('d-none');
                    _this.closest('tr').find('.product_required_error').addClass('d-none');
                    count_1 = 1;
                }
                if (status == 0 && _Qty != 0 && _product != 0) {
                    _this.closest('tr').find('.item_status_not_selected_error').removeClass(
                        'd-none');
                    _this.closest('tr').find('.return_qty_error').addClass('d-none');
                    _this.closest('tr').find('.product_required_error').addClass('d-none');
                    count_2 = 1;
                    // return false;
                }
                if (status != 0 && _Qty == 0 && _product == 0) {
                    _this.closest('tr').find('.item_status_not_selected_error').addClass(
                        'd-none');
                    _this.closest('tr').find('.return_qty_error').removeClass('d-none');
                    _this.closest('tr').find('.product_required_error').removeClass('d-none');
                    count_3 = 1;
                    // return false;
                }
                if (status != 0 && _Qty == 0 && _product != 0) {
                    _this.closest('tr').find('.item_status_not_selected_error').addClass(
                        'd-none');
                    _this.closest('tr').find('.return_qty_error').removeClass('d-none');
                    _this.closest('tr').find('.product_required_error').addClass('d-none');
                    count_4 = 1;
                    // return false;
                }
                if (status == 0 && _Qty == 0 && _product != 0) {
                    _this.closest('tr').find('.item_status_not_selected_error').removeClass(
                        'd-none');
                    _this.closest('tr').find('.return_qty_error').removeClass('d-none');
                    _this.closest('tr').find('.product_required_error').addClass('d-none');
                    count_5 = 1;
                    // return false;
                }
                if (status != 0 && _Qty != 0 && _product == 0) {
                    _this.closest('tr').find('.item_status_not_selected_error').addClass(
                        'd-none');
                    _this.closest('tr').find('.return_qty_error').addClass('d-none');
                    _this.closest('tr').find('.product_required_error').removeClass('d-none');
                    count_6 = 1;
                    // return false;
                }
                if (status == 0 && _Qty != 0 && _product == 0) {
                    _this.closest('tr').find('.item_status_not_selected_error').removeClass(
                        'd-none');
                    _this.closest('tr').find('.return_qty_error').addClass('d-none');
                    _this.closest('tr').find('.product_required_error').removeClass('d-none');
                    count_6 = 1;
                    // return false;
                }
                sumOfQty = sumOfQty + _Qty;
            });
            if (sumOfQty == 0) {
                $('.atleast_required_error').removeClass('d-none');
            } else {
                $('.atleast_required_error').addClass('d-none');
                if (count_1 == 1) {
                    if (count_2 == 1) {

                    } else if (count_3 == 1) {

                    } else if (count_4 == 1) {

                    } else if (count_5 == 1) {

                    } else if (count_6 == 1) {

                    } else {
                        $('#orderForm').submit();
                    }
                }
            }
        });
        $(document).on('click', '.add-row', function() {
            getSingleRowProducts();
        });
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
        $(document).on('change', '.product_id', function() {
            getProductDetail($(this).val(), $(this));
        });
    });
    function getBrandProducts() {
        var customer_id = $('#customer').val();
        var brandId = $('#brand').val();
        $.ajax({
            url: '{{ route("sku.create") }}',
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                customer_id: customer_id,
                brand_id: brandId
            },
            success:function(response) {
                $('.product_id').each(function() {
                    if (response.length > 0) {
                        var options = "<option value='0'>-Select Product-</option>";
                        response.forEach(element => {
                            options += "<option value='"+element.product_id+"'>"+element.products.name+"</option>"
                        });
                        $(this).closest('tr').find('.product-error').addClass('d-none');
                        $(this).html(options);
                    } else {
                        $(this).html("<option value='0'>-Select Product-</option>");
                        $(this).closest('tr').find('.product-error').removeClass('d-none');
                    }
                });
            }
        });
    }
    function getSingleRowProducts() {
        var customer_id = $('#customer').val();
        var brandId = $('#brand').val();
        $.ajax({
            url: '{{ route("sku.create") }}',
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                customer_id: customer_id,
                brand_id: brandId
            },
            success:function(response) {
                // $('.product_id').each(function() {
                    if (response.length > 0) {
                        var options = '';
                        options += `<option value="0">-Select Product-</option>`;
                        response.forEach(element => {
                            options += `<option value='`+element.product_id+`'>`+element.products.name+`</option>`;
                        });
                        $('.product-error').addClass('d-none');
                    } else {
                        $('.product_id').html("<option value=''>Select Product</option>");
                        $('.product-error').removeClass('d-none');
                    }
                    var html = '';
                    html += `
                        <tr>
                            <td class="">
                                <select name="item_status[]" class="form-control item_returned_satus" value="0">
                                    <option value="0">-Item Status-</option>
                                    <option value="1">Return</option>
                                    <option value="2">Damaged</option>
                                    <option value="3">Opened</option>
                                </select>
                                <span class="item_status_not_selected_error d-none text-danger">Required</span>
                            </td>
                            <td class="">
                                <input type="text" class="form-control order_number" name="order_number[]" placeholder="Enter Order Number">
                            </td>
                            <td class="">
                                <input type="text" class="form-control name" name="name[]" placeholder="Enter Name">
                            </td>
                            <td class="">
                                <input type="text" class="form-control state" name="state[]" placeholder="Enter Enter State">
                            </td>
                            <td class="">
                                <select name="product_id[]" class="form-control product_id">
                                    `+options+`
                                </select>
                                <div class="product-error text-danger d-none">No Product Found.</div>
                                <span class="product_required_error d-none text-danger">Required</span>
                            </td>
                            <td class="">
                                <input type="number" class="form-control deduction_qty returnQuantity" data-price="" data-qty="" name="return_qty[]" placeholder="Enter Quantity">
                                <input type="hidden" class="form-control total_amount_field" value="0" name="deducted_price[]">
                                <input type="hidden" class="form-control remaining_qty" name="remaining_qty[]" value="">
                                <p class="text-danger d-none return_qty_error">Must be greater than 0</p>
                            </td>
                            <td class="">
                                <textarea name="description[]" class="form-control" id="" cols="5" rows="1"
                                    placeholder="Enter Special Notes"></textarea>
                            </td>
                            <td class="">
                                <button class="m-auto btn-danger remove-row border-0 rounded-circle shadow"
                                    type="button"><i data-feather="minus">-</i></button>
                            </td>
                        </tr>
                    `;
                    for (let index = 1; index <= 10; index++) {
                        $('#order_table tbody').append(html);
                    }
                // });
            }
        });
    }
    function getProductDetail(productId, _this) {
        var product_id = productId;
        var This = _this;
        var url = '{{ route("get_product_detail", ":id") }}';
        url = url.replace(':id', product_id);
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: product_id,
            },
            success:function(response) {
                This.closest('tr').find('input.returnQuantity').attr('data-price', response.price);
                This.closest('tr').find('input.returnQuantity').attr('data-qty', response.inventory.qty);
                This.closest('tr').find('input.remaining_qty').val(response.inventory.qty);
            }
        });
    }
</script>
@endsection
@section('datatablejs')
<script src="{{ URL::asset('admin/app-assets/vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.min.js') }}"></script>
{{-- <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script> --}}
<script src="{{ URL::asset('admin/app-assets/grabbedFile/sorttable.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>

<script src="{{ URL::asset('admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/js/scripts/pages/app-invoice.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/js/scripts/forms/form-select2.js') }}"></script>
@stop
