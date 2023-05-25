@extends('admin.layout.app')
@section('title', 'Create Return Order')
@section('datatablecss')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.2/css/fixedHeader.bootstrap4.min.css">

@stop
@section('datepickercss')
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
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
            /* background: url('{{ asset('images/details_open.png') }}') no-repeat center center; */
            /* z-index: 50000 !important; */
            cursor: pointer;
        }

        tr:hover {
            background: rgb(223, 226, 255);
        }

        tr.shown td.details-control {
            background: url('{{ asset('images/details_open.png') }}') no-repeat center center;
            z-index: 50000 !important;
        }

        .dbRowColor {
            background-color: rgb(255, 232, 236);
        }

        /**! ColResize 2.6.0
                         * ©2017 Steven Masala
                         */

        .dt-colresizable-table-wrapper {
            overflow: auto;
            /* width: 100%; */
            position: relative;
        }

        .word-wrap {
            word-wrap: break-word;
        }

        .dt-colresizable {
            height: 0;
            position: relative;
            top: 0;
            z-index: 999;
        }

        .dt-colresizable .dt-colresizable-col {
            display: block;
            position: absolute;

            width: 5px;
            cursor: ew-resize;
            z-index: 1000;
        }

        .dt-colresizable-table-wrapper.dt-colresizable-with-scroller {
            overflow-x: auto;
            overflow-y: hidden;
        }

        .dt-colresizable-scroller-wrapper {
            position: absolute;
            overflow-y: hidden;
            overflow-x: hidden;
            /** FF **/
            /* width: 100%; */
            right: 0;
        }

        .dt-colresizable-scroller-content-wrapper {
            /* width: 100%; */
        }

        .dt-colresizable-scroller-content {
            /* width: 100%; */
        }

        .dt-colresizable-with-scroller table thead,
        .dt-colresizable-with-scroller table tbody tr {
            table-layout: fixed;
            /* width: 100%;     */
        }

        .dt-colresizable-with-scroller table tbody {
            overflow-y: hidden;
        }

        table.data-table {
            table-layout: fixed;
            margin: 0;
        }

        table.data-table,
        table.data-table th,
        table.data-table td {}

        table.data-table thead th,
        table.data-table tbody td,
        table.data-table tfoot td {
            /* overflow: hidden; */
        }

        td.tbl_clr {
            background-color: rgb(253, 253, 253);
        }

        /* table.data-table td:nth-child(1)
            {
                z-index: 10;
            }

            table.data-table td:nth-child(2)
            {
                z-index: 10;
            } */
        /* Hide scrollbar for Chrome, Safari and Opera */
        .table-height::-webkit-scrollbar {
            display: none;
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
        <div class="row">
            <div class="col-md-12">
                <form id="orderForm" enctype="multipart/form-data">
                    <div class="row invoice-add">
                        <!-- Invoice Add Left starts -->
                        <div class="col-xl-12 col-md-12 col-12">
                            <div class="card invoice-preview-card p-2">
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
                                        {{-- <div class="col-md-3 mb-lg-1 col-bill-to ps-0">
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
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                            </div>
                                            <div class="table-height" style="overflow-y: auto; overflow-x: hidden;">
                                                <table class="table data-table table-bordered order_table" id="order_table" style="padding-bottom: 190px; margin-bottom: 200px">
                                                    {{-- <table class="table table-bordered order_table" id="order_table"
                                                    style="width: 100%"> --}}
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 15%">Brand</th>
                                                            <th style="width: 10%">Status</th>
                                                            <th style="width: 10%">Order No</th>
                                                            <th style="width: 10%">Name</th>
                                                            <th style="width: 10%">State</th>
                                                            <th style="width: 10%">Product</th>
                                                            <th style="width: 5%">Qty</th>
                                                            <th style="width: 25%">Notes</th>
                                                            <th style="width: 10%">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <select name="brand[]" class="form-control brand select2" data-order-no="0">
                                                                    <option value="">Select Brand</option>
                                                                </select>
                                                                <div id="brand-error" class="text-danger font-weight-bold"></div>
                                                                <small class="text-danger d-none" id="brand_required">Required</small>
                                                            </td>
                                                            <td>
                                                                <select name="item_status[]" class="form-control item_returned_satus">
                                                                    <option value="0">-Item Status-</option>
                                                                    <option value="1">Return</option>
                                                                    <option value="2">Damaged</option>
                                                                    <option value="3">Opened</option>
                                                                    <option value="4">Invalid Address</option>
                                                                </select>
                                                                <span class="item_status_not_selected_error d-none text-danger">Required</span>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control order_number"
                                                                    name="order_number[]" placeholder="Enter Order Number">
                                                                <span class="order_number_not_selected_error d-none text-danger">Required</span>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control name" name="name[]"
                                                                    placeholder="Enter Name">
                                                                <span class="name_not_selected_error d-none text-danger">Required</span>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control state" name="state[]"
                                                                    placeholder="Enter Enter State">
                                                                <span class="state_not_selected_error d-none text-danger">Required</span>
                                                            </td>
                                                            <td>
                                                                <select name="product_id0[]" class="form-control product_id select2" multiple="multiple" data-order-no="0">
                                                                    <option value="">-Select Product-</option>
                                                                </select>
                                                                <div class="product-error text-danger d-none">No Product Found.</div>
                                                                <span class="product_required_error d-none text-danger">Required</span>
                                                            </td>
                                                            <td class="products_qty">
                                                                <div class="input_fields">
                                                                    <input type="number"
                                                                        class="form-control deduction_qty returnQuantity removeable"
                                                                        data-product-no="0" data-price="" data-qty=""
                                                                        name="return_qty0[]" placeholder="0" value="">
                                                                    <input type="hidden"
                                                                        class="form-control total_amount_field removeable"
                                                                        data-product-no="`+index+`" value="0"
                                                                        name="deducted_price0[]">
                                                                    <input type="hidden"
                                                                        class="form-control remaining_qty removeable"
                                                                        data-product-no="`+index+`" name="remaining_qty0[]"
                                                                        value="0">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <textarea name="description[]" class="form-control description" id="" cols="5" rows="1"
                                                                    placeholder="Enter Special Notes"></textarea>
                                                                <span class="description_not_selected_error d-none text-danger">Required</span>
                                                            </td>
                                                            <td>
                                                                <button class="m-auto btn-primary add-row border-0 rounded-circle shadow" type="button"><i data-feather="plus"></i></button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
                                            <button type="reset" class="btn btn-outline-secondary" id="reset" style="float: right">Reset</button>
                                            <button type="button" class="btn btn-primary me-1 saveOrder" style="float: right">Submit</button>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-9">
                                        </div>
                                        <div class="col-sm-3">
                                            <h5 class="text-danger d-none atleast_required_error" style="float: right"> Alteast 1 item required</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="totalRes" value="0">
                        <input type="hidden" id="orderId" value="">
                        <!-- Invoice Add Left ends -->
                    </div>
                </form>
            </div>
        </div>
    </section>
@section('modal')
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
    const dt_state = {
        "time": 1641591290095,
        "start": 0,
        "length": 50,
        "order": [
            [1, "asc"]
        ],
        "search": {
            "search": "",
            "smart": true,
            "regex": false,
            "caseInsensitive": true
        },
        "columns": [{
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }, {
            "visible": true,
            "search": {
                "search": "",
                "smart": true,
                "regex": false,
                "caseInsensitive": true
            }
        }]
    };
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
        $('.product_id').select2({
            placeholder: 'Select Product',
            templateResult: formatState
        });
        $('.brand').select2({
            placeholder: 'Select Brand',
        });
        $('#order_table').DataTable({
            searching: false,
            ordering: false,
            paging: false,
            scrollCollapse: true,
            autoWidth: true,
            fixedHeader: true,
            stateSave: true,
            stateDuration: -1,
            colResize: {
                resizeTable: true
            },
            stateLoadCallback: function(settings) {
                var o;
                $.ajax({
                    url: '{{ route("get_create_return_order_state") }}',
                    async: false,
                    dataType: 'json',
                    success: function(json) {
                        o = json;
                    }
                });
                if (o.length !== undefined)
                    return JSON.parse(o);
                return dt_state;
            },
            stateSaveCallback: function(settings, stateData) {
                $.ajax({
                    url: '{{ route("save_create_return_order_state") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        create_return_order_state: stateData
                    },
                    dataType: 'json'
                })
            },
        });
        $(document).on('change', '#customer', function() {
            $('.customer-error').html('');
            $('#new_customer').val($(this).val());
            var id = $(this).val();
            // $('.order_number').each(function() {
            //     $(this).val('');
            // });
            // $('.name').each(function() {
            //     $(this).val('');
            // });
            // $('.state').each(function() {
            //     $(this).val('');
            // });
            $('.product_id').each(function() {
                $(this).html('');
            });
            $('.returnQuantity').each(function() {
                $(this).val('0');
            });
            $('.total_amount_field').each(function() {
                $(this).val('0');
            });
            $('.remaining_qty').each(function() {
                $(this).val('0');
            });
            // $('.description').each(function() {
            //     $(this).val('');
            // });

            if (id == "") {
                $('.brand').html('<option value="">Select Brand</option>');
            } else {
                $.get('/customer/' + id + '/brand', function(result) {
                    if (result.status == "success") {
                        var options = "<option value=''>Select Brand</option>";
                        result.data.forEach(element => {
                            options += "<option value='" + element.id + "'>" + element
                                .brand + "</option>"
                        });
                        $('.brand-error').html('');
                        $('.brand').html(options);
                    } else {
                        $('.brand').html("<option value=''>Select Brand</option>");
                        $('.brand-error').html(result.message);
                    }
                });
            }
        });
        $(document).on('change', '.brand', function() {
            var _thisBrand = $(this);
            var id = $(this).val();
            var customer_id = $('#customer').val();
            var brandId = $('.brand').val();
            getBrandProducts(_thisBrand);
        });
        $(document).on('click', '.add-row', function() {
            var customer_id = $('#customer').val();
            var url = '/customer/:id/brand';
            url = url.replace(':id', customer_id);
            $.ajax({
                async: false,
                url: url,
                success:function(result) {
                    if (result.status == "success") {
                        brandOptions = '<option value="">Select Brand</option>';
                        result.data.forEach(element => {
                            brandOptions += "<option value='" + element.id + "'>" + element
                                .brand + "</option>"
                        });
                        $('.brand-error').html('');
                        // $('.brand').html(options);
                    } else {
                        $('.brand').html("<option value=''>Select Brand</option>");
                        $('.brand-error').html(result.message);
                    }
                    return brandOptions;
                }
            });
            newRowsData(brandOptions);
        });
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
        $(document).on('change', '.product_id', function() {
            var _this = $(this);
            var products = _this.val();
            var orderNo = _this.data('order-no');
            var html = '';
            _this.closest('tr').find('td.products_qty').append(html);
            for (let index = 0; index < products.length; index++) {
                getProductDetail(products[index], _this, index);
                html +=
                    `<input type="number" class="form-control deduction_qty returnQuantity appended_classes" data-product-no="` +
                    index + `" data-price` + index + `="" data-qty` + index + `="" name="return_qty` +
                    orderNo + `[]" placeholder="0" value="">
                        <input type="hidden" class="form-control total_amount_field` + index +
                    ` appended_classes" data-product-no="` + index +
                    `" value="0" name="deducted_price` + orderNo + `[]">
                        <input type="hidden" class="form-control remaining_qty` + index +
                    ` appended_classes" data-product-no="` + index + `" name="remaining_qty` + orderNo + `[]" value="0">
                `;
            }
            if (products.length > 1) {
                _this.closest('tr').find('.input_fields').empty();
                _this.closest('tr').find('.removeable').addClass('d-none');
                _this.closest('tr').find('.input_fields').append(html);
            } else if (products.length == 1) {
                _this.closest('tr').find('.removeable').removeClass('d-none');
                _this.closest('tr').find('.appended_classes').remove();
                _this.closest('tr').find('.input_fields').empty();
                _this.closest('tr').find('.input_fields').append(html);
            } else {
                _this.closest('tr').find('.removeable').removeClass('d-none');
            }
        });
        $(document).on('keyup', '.deduction_qty', function() {
            var _this = $(this);
            var qty = _this.val();
            var productNo = _this.attr('data-product-no');
            var totalAmount = Number(qty) * _this.data('price' + productNo);
            var remainingQty = _this.data('qty' + productNo) - Number(qty);
            var amountField = _this.closest('tr').find('input.total_amount_field' + productNo);
            var qtyField = _this.closest('tr').find('input.remaining_qty' + productNo);
            if (qty > _this.data('qty')) {} else {
                amountField.val(totalAmount.toFixed(2));
                qtyField.val(remainingQty);
            }
        });
        $(document).on('click', '.saveOrder', function() {
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
            var checkCount = 1;
            var atleastOneSelected = 0;
            $('.brand').each(function() {
                let _this = $(this);
                let brandOrderStatus = _this.closest('tr').find('.item_returned_satus');
                let brandOrderNo = _this.closest('tr').find('.order_number');
                let brandOrderName = _this.closest('tr').find('.name');
                let brandOrderState = _this.closest('tr').find('.state');
                let brandOrderProduct = _this.closest('tr').find('.product_id');
                let brandOrderQty = _this.closest('tr').find('.returnQuantity');
                let brandOrderDescription = _this.closest('tr').find('.description');
                if (_this.val() != '') {
                    atleastOneSelected = 1;
                    checkCount = 1;
                    if (brandOrderStatus.val() != '0') {
                        brandOrderStatus.closest('tr').find('.item_status_not_selected_error').addClass('d-none');
                        if (brandOrderProduct.val() != '') {
                            brandOrderProduct.closest('tr').find('.product_required_error').addClass('d-none');
                        } else {
                            brandOrderProduct.closest('tr').find('.product_required_error').removeClass('d-none');
                            checkCount = 0;
                            return false;
                        }
                        if (brandOrderQty.length > 0) {
                            brandOrderQty.each(function () {
                                let _this = $(this);
                                if (_this.val() != '') {
                                    _this.css('border', '1px solid lightgray');
                                } else {
                                    _this.css('border', '1px solid red');
                                    checkCount = 0;
                                    return false;
                                }
                                if (_this.val() != 0) {
                                    _this.css('border', '1px solid lightgray');
                                } else {
                                    _this.css('border', '1px solid red');
                                    checkCount = 0;
                                    return false;
                                }
                                if (_this.val() != null) {
                                    _this.css('border', '1px solid lightgray');
                                } else {
                                    _this.css('border', '1px solid red');
                                    checkCount = 0;
                                    return false;
                                }
                            });
                        } else {
                            checkCount = 0;
                            return false;
                        }
                    } else {
                        brandOrderStatus.closest('tr').find('.item_status_not_selected_error').removeClass('d-none');
                        checkCount = 0;
                        return false;
                    }
                }
                if (checkCount == 0) {
                    return false;
                }
            });
            if (atleastOneSelected == 1) {
                if (checkCount == 1) {
                    // $('#orderForm').submit();
                    submitForm();
                }
            } else {
                alert('Atleast 1 required');
                return false;
            }
        });
    });

    function submitForm() {
        var form = $('#orderForm')[0];
        var formData = new FormData(form);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "{{ route('save_return_order') }}",
            data: formData,
            contentType: false,
            processData: false,
            success:function(response) {
                if (response.status == true) {
                    alert(response.msg);
                    location.reload();
                } else {
                    alert(response.msg);
                }
            }
        });
    }

    function getBrandProducts(_thisBrand) {
        var customer_id = $('#customer').val();
        var brandId = _thisBrand.val();
        $.ajax({
            url: '{{ route('sku.create') }}',
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                customer_id: customer_id,
                brand_id: brandId
            },
            success: function(response) {
                _thisBrand.closest('tr').find('.product_id').html('');
                if (response.length > 0) {
                    var options = '';
                    response.forEach(element => {
                        options += "<option image='"+element.products.image+"' value='" + element.product_id + "'>" + element.products.name + "</option>";
                    });
                    _thisBrand.closest('tr').find('.product-error').addClass('d-none');
                    _thisBrand.closest('tr').find('.product_id').html(options);
                } else {
                    _thisBrand.closest('tr').find('.product-error').removeClass('d-none');
                }
            }
        });
    }

    function newRowsData(brandOptions) {
        var customer_id = $('#customer').val();
        var brandId = $('.brand').val();
        var indexLength = $('tr').length - 1;
        $.ajax({
            url: '{{ route('sku.create') }}',
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                customer_id: customer_id,
                brand_id: brandId
            },
            success: function(response) {
                if (response.length > 0) {
                    var options = '';
                    response.forEach(element => {
                        options += `<option image="`+element.products.image+`" value='` + element.product_id + `'>` + element.products.name + `</option>`;
                    });
                    $('.product-error').addClass('d-none');
                } else {
                    $('.product-error').removeClass('d-none');
                }
                var html = '';
                for (let index = indexLength; index <= (indexLength + 9); index++) {
                    html += `
                            <tr>
                                <td>
                                    <select name="brand[]" class="form-control brand select2" data-order-no="`+ index +`">
                                        <option value="">Select Brand</option>`;
                    html += brandOptions;
                                    html += `</select>
                                    <div id="brand-error" class="text-danger font-weight-bold"></div>
                                    <small class="text-danger d-none" id="brand_required">Required</small>
                                </td>
                                <td>
                                    <select name="item_status[]" class="form-control item_returned_satus" value="0">
                                        <option value="0">-Item Status-</option>
                                        <option value="1">Return</option>
                                        <option value="2">Damaged</option>
                                        <option value="3">Opened</option>
                                        <option value="4">Invalid Address</option>
                                    </select>
                                    <span class="item_status_not_selected_error d-none text-danger">Required</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control order_number" name="order_number[]" placeholder="Enter Order Number">
                                </td>
                                <td>
                                    <input type="text" class="form-control name" name="name[]" placeholder="Enter Name">
                                </td>
                                <td>
                                    <input type="text" class="form-control state" name="state[]" placeholder="Enter Enter State">
                                </td>
                                <td>
                                    <select name="product_id` + index + `[]" class="form-control product_id select2" multiple="multiple" value="" data-order-no="` + index + `">
                                         ` + options + ` 
                                    </select>
                                    <div class="product-error text-danger d-none">No Product Found.</div>
                                    <span class="product_required_error d-none text-danger">Required</span>
                                </td>
                                <td class="products_qty">
                                    <div class="input_fields">
                                        <input type="number" class="form-control deduction_qty returnQuantity removeable" data-price="" data-product-no="` +
                        index + `" data-qty="" name="return_qty` + index +
                        `[]" placeholder="0">
                                        <input type="hidden" class="form-control total_amount_field removeable" value="0" data-product-no="` +
                        index +
                        `" name="deducted_price` + index +
                        `[]">
                                        <input type="hidden" class="form-control remaining_qty removeable" data-product-no="` +
                        index +
                        `" name="remaining_qty` + index + `[]" value="0">
                                    </div>
                                </td>
                                <td>
                                    <textarea name="description[]" class="form-control" id="" cols="5" rows="1"
                                        placeholder="Enter Special Notes"></textarea>
                                </td>
                                <td>
                                    <button class="m-auto btn-danger remove-row border-0 rounded-circle shadow" type="button"><i data-feather="minus">-</i></button>
                                </td>
                            </tr>
                        `;
                }
                $('#order_table tbody').append(html);
                $('.product_id').select2({
                    placeholder: 'Select Product',
                    templateResult: formatState
                });
                $('.brand').select2({
                    placeholder: 'Select Brand',
                });
            }
        });
    }

    function getProductDetail(productId, _this, index) {
        var product_id = productId;
        var This = _this;
        var url = '{{ route('get_product_detail', ':id') }}';
        url = url.replace(':id', product_id);
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: product_id,
            },
            success: function(response) {
                This.closest('tr').find('input.returnQuantity').attr('data-price' + index + '', response
                    .price);
                This.closest('tr').find('input.returnQuantity').attr('data-qty' + index + '', response
                    .inventory.qty);
                This.closest('tr').find('input.remaining_qty' + index + '').val(response.inventory.qty);
            }
        });
    }

    function formatState (state) {
        if (!state.id) {
            return state.text;
        }
        var image = state.element.getAttribute("image");
        var baseUrl = "{{ $storagePath }}";
        var $state = $(
            '<span><img src="' + baseUrl + image + '" class="img-flag" width="35" height="45" /> ' + state.text + '</span>'
        );
        return $state;
    };
</script>
@endsection

@section('datatablejs')

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.datatables.net/fixedheader/3.2.2/js/dataTables.fixedHeader.min.js"></script>

{{-- <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.min.js') }}"></script> --}}

{{-- <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

<script src="https://datatables.net/release-datatables/media/js/dataTables.bootstrap4.js"></script>

<script src="https://datatables.net/release-datatables/extensions/FixedHeader/js/dataTables.fixedHeader.js"></script> --}}

{{-- <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script> --}}

{{-- <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script> --}}

<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>

<script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/resize.js') }}"></script>

@stop
@section('datepickerjs')
<script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
@stop
