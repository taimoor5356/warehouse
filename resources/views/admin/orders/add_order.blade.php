@extends('admin.layout.app')
@section('title', 'Add Batches')
@section('datatablecss')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/pages/app-invoice.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@stop

@section('content')
<style type="text/css">
    .hide {
        display: none;
    }
    .sku-table thead th {
        font-size: 0.7vw;
    }/* Popup container - can be anything you want */
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
    from {opacity: 0;} 
    to {opacity: 1;}
    }

    @keyframes fadeIn {
    from {opacity: 0;}
    to {opacity:1 ;}
    }
    /**! ColResize 2.6.0
 * ©2017 Steven Masala
 */

.dt-colresizable-table-wrapper {
    overflow: auto;
    /* width: 100%; */
    position: relative;
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
    overflow-x: hidden; /** FF **/
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

table.sku-table {
    table-layout: fixed;
    margin: 0;
}

table.sku-table,
table.sku-table th,
table.sku-table td {
    
}

table.sku-table thead th,
table.sku-table tbody td,
table.sku-table tfoot td {
    /* overflow: hidden; */
}
.loader {
  border: 5px solid #f3f3f3;
  border-radius: 50%;
  border-top: 5px solid #3498db;
  width: 40px;
  height: 40px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<section id="basic-horizontal-layouts">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Add Batches</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item">Batches
                            </li>
                            <li class="breadcrumb-item active">Add Batches
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{route('orders.store')}}" id="orderForm" enctype="multipart/form-data" method="post">
        {{@csrf_field()}}
        <div class="row invoice-add">
            <!-- Invoice Add Left starts -->
            <div class="col-xl-12 col-md-12 col-12">
                <div class="card invoice-preview-card">
                    <!-- Address and Contact starts -->
                    <div class="card-body invoice-padding pt-0">
                        <div class="row row-bill-to invoice-spacing">
                            <div class="col-md-3 mb-lg-1 col-bill-to ps-0">
                                <label for="customer">Select Customer</label>
                                <div class="invoice-customer">
                                    <select name="customer" id="customer" class="form-select select2" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->customer_name}}</option>
                                        @endforeach
                                    </select>
                                    <span id="customer_required" class="text-danger d-none">Required</span>
                                    @error('customer')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                    <div class="text-danger customer-error"></div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-lg-1 col-bill-to ps-0">
                                <label for="brand">Select Brand</label>
                                <div class="invoice-customer">
                                    <select name="brand" id="brand" class="form-select select2" required>
                                        <option value="">Select Brand</option>
                                    </select>
                                    <span id="brand_required" class="text-danger d-none">Required</span>
                                    @error('brand')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                    <div id="brand-error" class="text-danger font-weight-bold"></div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-lg-1 col-bill-to ps-0">
                                <label for="">Enter Short Note</label>
                                <input type="text" class="form-control" placeholder="Enter Short Note" name="notes" id="notes">
                            </div>
                            <div class="col-md-3 mb-lg-1 col-bill-to ps-0">
                                <label for="batch" class="">Enable Custom Postage Cost</label>
                                <div class="invoice-customer">
                                    <div class="form-check form-check-primary form-switch text-center">
                                        <input type="checkbox" {{ old('is_active') == 1 ? 'checked' : '' }} name="is_active" value="1" class="form-check-input" id="pc_default_cb" style="margin-top: 10px">
                                        <input type="text" value="0" min="0" name="newPostageCost" id="newPostageCost" class="form-control postageCostInput invisible">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        </div>
                    </div>
                    <!-- Address and Contact ends -->
                    <div class="row m-1 loaderWrapper">
                        {{-- <h3>SKU</h3> --}}
                        <div class="loader d-none" style="position: relative; top: 150px; left: 50%;"></div>
                        <table class="table table-bordered sku-table" id="skutable">
                            <thead>
                                <tr>
                                    <th style="width: 10%" class="align-middle">SKU ID</th>
                                    <th style="width: 15%" class="align-middle">SKU</th>
                                    <th style="width: 10%" class="align-middle">Quantity</th>
                                    <th style="width: 12%" class="align-middle text-center">SKU Price</th>
                                    <th style="width: 12%" class="align-middle text-center">SKU Weight</th>
                                    {{-- <th style="width: 200px" class="align-middle">Service Charges</th> --}}
                                    <th style="width: 12%" class="align-middle text-center">Total</th>
                                    <th style="width: 5%" class="align-middle text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="skuTablebody">
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="row m-1">
                        <div class="row invoice-sales-total-wrapper">
                            <div class="col-md-5 order-md-1 order-2 mt-md-0 mt-3">
                                <input type="hidden" name="pc_lt5" id="pc_lt5">
                                <input type="hidden" name="pc_lt9" id="pc_lt9">
                                <input type="hidden" name="pc_lt13" id="pc_lt13">
                                <input type="hidden" name="pc_gte13" id="pc_gte13">
                                <!-- lbs -->
                                
                                <input type="hidden" name="lbs1_1_99" id="lbs1_1_99">
                                <input type="hidden" name="lbs1_1_2" id="lbs1_1_2">
                                <input type="hidden" name="lbs2_1_3" id="lbs2_1_3">
                                <input type="hidden" name="lbs3_1_4" id="lbs3_1_4">

                                <!-- lbs -->
                                <!-- discounted -->
                                
                                <input type="hidden" name="discounted_pc_lt5" id="discounted_pc_lt5">
                                <input type="hidden" name="discounted_pc_lt9" id="discounted_pc_lt9">
                                <input type="hidden" name="discounted_pc_lt13" id="discounted_pc_lt13">
                                <input type="hidden" name="discounted_pc_gte13" id="discounted_pc_gte13">
                                <!-- lbs -->
                                
                                <input type="hidden" name="discounted_lbs1_1_99" id="discounted_lbs1_1_99">
                                <input type="hidden" name="discounted_lbs1_1_2" id="discounted_lbs1_1_2">
                                <input type="hidden" name="discounted_lbs2_1_3" id="discounted_lbs2_1_3">
                                <input type="hidden" name="discounted_lbs3_1_4" id="discounted_lbs3_1_4">
                                <!-- discounted -->

                                <input type="hidden" name="pc_default" id="pc_default">
                                <input type="hidden" name="pick_cost" class="pick_cost" value="0">
                                <input type="hidden" name="pack_cost" class="pack_cost" value="0">
                                <input type="hidden" name="pick_pack_flat_cost" class="pick_pack_flat_cost" value="0">
                                <input type="hidden" name="labels_cost" class="labels_cost" value="0">
                                <input type="hidden" name="discounted_postage_status" id="discounted_postage_charges_status" value="">
                            </div>
                            <div class="col-md-7 d-flex justify-content-end order-md-2 order-1">
                                <div class="invoice-total-wrapper" style="min-width: 30rem">
                                    <div class="summary"><h4>Summary</h4><hr>
                                        <div class="summaryData">
                                        </div>
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Label x <span id="labelX"></span>:</p>
                                        <p id="total_label_cost" class="">$ <span class="price">0.00</span></p>
                                        <input type="hidden" id="grandTotalLabelPrice" name="grandTotalLabelPrice">
                                        <input type="hidden" id="labelqty" name="labelqty">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Pick x <span id="pickX"></span>:</p>
                                        <p id="total_pick_cost" class="">$ <span class="price">0.00</span></p>
                                        <input type="hidden" id="grandTotalPickPrice" name="grandTotalPickPrice">
                                        <input type="hidden" id="pickqty" name="pickqty">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Pack x <span id="packX"></span>:</p>
                                        <p id="total_pack_cost" class="">$ <span class="price">0.00</span></p>
                                        <input type="hidden" id="grandTotalPackPrice" name="grandTotalPackPrice">
                                        <input type="hidden" id="packqty" name="packqty">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Pick / Pack Flat x <span id="pickPackFlatX"></span>:</p>
                                        <p id="total_pick_pack_flat_cost" class="">$ <span class="price">0.00</span></p>
                                        <input type="hidden" id="grandTotalPickPackFlatPrice" name="grandTotalPickPackFlatPrice">
                                        <input type="hidden" id="pickpackflatqty" name="pickpackflatqty">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Mailer x <span id="mailerX"></span>:</p>
                                        <p id="total_mailer_cost" class="">$ <span class="price">0.00</span></p>
                                        <input type="hidden" name="mailer_cost" id="mailer_cost" />
                                        <input type="hidden" name="brand_mailer" id="brand_mailer" />
                                        <input type="hidden" name="mailer_costNew" id="mailer_costNew">
                                        <input type="hidden" id="mailerqty" name="mailerqty">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Postage x <span id="postageX"></span>:</p>
                                        <p id="total_postage_cost" class="">$ <span class="price">0.00</span></p>
                                        <input type="hidden" name="postage_cost" id="postage_cost" />
                                        <input type="hidden" name="total_postage_price" id="total_postage_price" value="">
                                        <input type="hidden" id="postageqty" name="postageqty">
                                            <!-- individual weight cost -->
                                            <input type="hidden" name="one_to_four_ounces" id="one_to_four_ounces" value="0">
                                            <input type="hidden" name="five_to_eight_ounces" id="five_to_eight_ounces" value="0">
                                            <input type="hidden" name="nine_to_twelve_ounces" id="nine_to_twelve_ounces" value="0">
                                            <input type="hidden" name="thirteen_to_fifteen_ounces" id="thirteen_to_fifteen_ounces" value="0">
                                            <input type="hidden" name="one_lbs" id="one_lbs" value="0">
                                            <input type="hidden" name="one_to_two_lbs" id="one_to_two_lbs" value="0">
                                            <input type="hidden" name="two_to_three_lbs" id="two_to_three_lbs" value="0">
                                            <input type="hidden" name="three_to_four_lbs" id="three_to_four_lbs" value="0">
                                            <!-- individual dicounted weight cost -->
                                            <input type="hidden" name="discounted_one_to_four_ounces" id="discounted_one_to_four_ounces" value="0">
                                            <input type="hidden" name="discounted_five_to_eight_ounces" id="discounted_five_to_eight_ounces" value="0">
                                            <input type="hidden" name="discounted_nine_to_twelve_ounces" id="discounted_nine_to_twelve_ounces" value="0">
                                            <input type="hidden" name="discounted_thirteen_to_fifteen_ounces" id="discounted_thirteen_to_fifteen_ounces" value="0">
                                            <input type="hidden" name="discounted_one_lbs" id="discounted_one_lbs" value="0">
                                            <input type="hidden" name="discounted_one_to_two_lbs" id="discounted_one_to_two_lbs" value="0">
                                            <input type="hidden" name="discounted_two_to_three_lbs" id="discounted_two_to_three_lbs" value="0">
                                            <input type="hidden" name="discounted_three_to_four_lbs" id="discounted_three_to_four_lbs" value="0">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Grand Total:</p>
                                        <p id="grand_total_price" class="invoice-total-amount">$ 0.00</p>
                                        <input type="hidden" name="grand_total_price" id="grand_total_price_input" />
                                        <input type="hidden" id="new_customer">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Product Details ends -->
                    <!-- Invoice Total starts -->
                    <div class="card-body invoice-padding">
                        <!-- Invoice Total ends -->
                        <div class="row">
                            <div class="col-md-9"></div>
                            <div class="col-sm-3">
                                <button type="reset" class="btn btn-outline-secondary" id="reset" style="float: right">Reset</button>
                                <button type="button" class="btn btn-primary me-1 saveOrder" style="float: right">Submit</button>
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
            <!-- Invoice Add Left ends -->
    </form>
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
        var skus_data = [];
        var products_data = [];
        $(document).on('click', '.saveOrder', function() {
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
            var qtySum = 0;
            $('.qty').each(function(){
                qtySum += Number($(this).val());
            });
            if (qtySum == 0) {
                $('.atleast_required_error').removeClass('d-none');
                return false;
            } else {
                $('.atleast_required_error').addClass('d-none');
            }
            if ($('#customer').val() != '' && $('#brand').val() != '') {
                if ($('#grand_total_price_input').val() > 0) {
                    $('#orderForm').submit();
                } else {
                    $('.atleast_required_error').removeClass('d-none');
                }
            }
        });
        // load batch no
        // $.get('{{route("get-batch-number")}}', function(result) {
        //     if(result.status == "success") {
        //         var opt = '';
        //         result.data.forEach(element => {
        //             opt = opt + "<option>"+element+"</option>";
        //         });
        //         $("#batch_no").html(opt);
        //     }
        // });
        // get customer brand
        $(document).on('change', '#customer', function() {
            $('.atleast_required_error').addClass('d-none');
            $('#customer_required').addClass('d-none');
            $('.customer-error').html('');
            $('#new_customer').val($(this).val());
            var id = $(this).val();
            if(id=="") {
                $('#brand').html('<option value="">Select Brand</option>');
            } else {
                $.get("{{route('customer-service-charges', ['id' => "+id+"])}}", function(result) {
                    if(result.status == "success") {
                        $('#discounted_postage_charges_status').val(result.data.discounted_default_postage_charges);
                        $('#mailer_cost').val(result.data.mailer);
                        $('#postage_cost').val(result.data.postage_cost);
                        $('.pick_cost').val(result.data.pick);
                        $('.pack_cost').val(result.data.pack);
                        $('.pick_pack_flat_cost').val(result.data.pick_pack_flat);
                        $('.labels_cost').val(result.data.labels);
                        $('#pc_lt5').val(result.data.postage_cost_lt5);
                        $('#pc_lt9').val(result.data.postage_cost_lt9);
                        $('#pc_lt13').val(result.data.postage_cost_lt13);
                        $('#pc_gte13').val(result.data.postage_cost_gte13);
                        // lbs
                        $('#lbs1_1_99').val(result.data.lbs1_1_99);
                        $('#lbs1_1_2').val(result.data.lbs1_1_2);
                        $('#lbs2_1_3').val(result.data.lbs2_1_3);
                        $('#lbs3_1_4').val(result.data.lbs3_1_4);
                        // lbs

                        // discounted
                        $('#discounted_pc_lt5').val(result.data.discounted_default_postage_charges == 1 ? result.data.discounted_postage_cost_lt5 : 0);
                        $('#discounted_pc_lt9').val(result.data.discounted_default_postage_charges == 1 ? result.data.discounted_postage_cost_lt9 : 0);
                        $('#discounted_pc_lt13').val(result.data.discounted_default_postage_charges == 1 ? result.data.discounted_postage_cost_lt13 : 0);
                        $('#discounted_pc_gte13').val(result.data.discounted_default_postage_charges == 1 ? result.data.discounted_postage_cost_gte13 : 0);
                        // lbs
                        $('#discounted_lbs1_1_99').val(result.data.discounted_default_postage_charges == 1 ? result.data.discounted_lbs1_1_99 : 0);
                        $('#discounted_lbs1_1_2').val(result.data.discounted_default_postage_charges == 1 ? result.data.discounted_lbs1_1_2 : 0);
                        $('#discounted_lbs2_1_3').val(result.data.discounted_default_postage_charges == 1 ? result.data.discounted_lbs2_1_3 : 0);
                        $('#discounted_lbs3_1_4').val(result.data.discounted_default_postage_charges == 1 ? result.data.discounted_lbs3_1_4 : 0);
                        //
                        // $('#one_to_four_ounces').val(result.data.postage_cost_lt5);
                        // $('#five_to_eight_ounces').val(result.data.postage_cost_lt9);
                        // $('#nine_to_twelve_ounces').val(result.data.postage_cost_lt13);
                        // $('#thirteen_to_fifteen_ounces').val(result.data.postage_cost_gte13);
                        // $('#one_lbs').val(result.data.lbs1_1_99);
                        // $('#one_to_two_lbs').val(result.data.lbs1_1_2);
                        // $('#two_to_three_lbs').val(result.data.lbs2_1_3);
                        // $('#three_to_four_lbs').val(result.data.lbs3_1_4);
                        
                        // $('#discounted_one_to_four_ounces').val(result.data.discounted_postage_cost_lt5);
                        // $('#discounted_five_to_eight_ounces').val(result.data.discounted_postage_cost_lt9);
                        // $('#discounted_nine_to_twelve_ounces').val(result.data.discounted_postage_cost_lt13);
                        // $('#discounted_thirteen_to_fifteen_ounces').val(result.data.discounted_postage_cost_gte13);
                        // $('#discounted_one_lbs').val(result.data.discounted_lbs1_1_99);
                        // $('#discounted_one_to_two_lbs').val(result.data.discounted_lbs1_1_2);
                        // $('#discounted_two_to_three_lbs').val(result.data.discounted_lbs2_1_3);
                        // $('#discounted_three_to_four_lbs').val(result.data.discounted_lbs3_1_4);
                        //
                        
                        $('#pc_default').val(result.data.postage_cost);
                        setChargesRate('pick_price', result.data.pick);
                        setChargesRate('pack_price', result.data.pack);
                        setChargesRate('pick_pack_flat_price', result.data.pick_pack_flat);
                        setChargesRate('labels_price', result.data.labels);   
                    } else {
                        $('#charges-error').html(result.message);
                    }
                });
                var getCustomerBrandUrl = "{{route('customer-brand', ['id' => ':id'])}}";
                getCustomerBrandUrl = getCustomerBrandUrl.replace(':id', id);
                $.get(getCustomerBrandUrl, function(result) {
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
        });
        // get sku of brand
        $(document).on('change', '#brand', function() {
            $('.atleast_required_error').addClass('d-none');
            $('.loader').removeClass('d-none');
            var id = $(this).val();
            var customer_id = $('#new_customer').val();
            var brandId = $('#brand').val();
            $('#total_mailer_cost .price').html('0.00');
            $('#total_label_cost .price').html('0.00');
            $('#total_pick_cost .price').html('0.00');
            $('#total_pack_cost .price').html('0.00');
            $('#total_pick_pack_flat_cost .price').html('0.00');
            $('#total_postage_cost .price').html('0.00');
            $('#grand_total_price').html('$ 0.00');
            $('#grand_total_price_input').val('0.00');
            $('#total_postage_price').val('0.00');
            $('#mailer_costNew').val('0.00');
            $('#labelX').html('0');
            $('#pickX').html('0');
            $('#packX').html('0');
            $('#pickPackFlatX').html('0');
            $('#mailerX').html('0');
            $('#postageX').html('0');
            $('#mailerX').html('0');
            $('#total_label_cost .price').html('0.00');
            $('#total_pick_cost .price').html('0.00');
            $('#total_pack_cost .price').html('0.00');
            $('#total_pick_pack_flat_cost .price').html('0.00');
            $('#total_mailer_cost .price').html('0.00');
            $('#total_postage_cost .price').html('0.00');
            $('#grand_total_price').html('$ 0.00');
            if(id == "") {
                $('#skutable').css('opacity', '1');
                $('.loader').addClass('d-none');
                $('.sku').html('<option value="">Select SKU</option>');
            } else {
                $('#brand_required').addClass('d-none');
                $('#skutable').css('opacity', '0.3');
                var brandUrl = '{{ route("get_brand_mailer") }}';
                $.ajax({
                    url: brandUrl,
                    data: {
                        _token: '{{ csrf_token() }}',
                        brandId: id,
                    },
                    success:function(data) {
                        if(data != null || data != 0) {
                            $('#brand_mailer').val(data);
                        } else if(data == null || data == 0) {
                            $('#brand_mailer').val('0.00');
                        }
                    }
                });
                $('#skutable').DataTable().destroy();
                $('#skuTablebody').empty();  
                var url = '{{route("get-brand-sku", ":id")}}';
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        _token: '{{csrf_token()}}',
                        'customer_id': customer_id,
                    },
                    success:function(response) {
                        var list = '';
                        if (response.length == 0) {
                            $('.subt_qty').prop('disabled', false);
                            $('.add_qty').prop('disabled', false);
                            $('.qty').prop('disabled', false);
                            $('.loader').addClass('d-none');
                            $('#skutable').css('opacity', '1');
                        }
                        response.forEach(element => {
                            // var objJson = JSON.parse(element.service_charges_detail);
                            list += `
                                    <tr id="sku__`+element.id+`" class="item">
                                        <td>`+element.sku_id+`</td>
                                        <td>
                                            <h5 class="sku_name">`+element.name+`</h5>
                                            <div class="sku-error text-danger"></div>
                                            <input type="hidden" name="sku[]" value="`+element.id+`" class="sku" id="sku-list">
                                            <input type="hidden" value="0" class="total_sku_service_charges" name="_total_service_charges[]">
                                            <input type="hidden" value="0" class="total_product_label_cost">
                                            <input type="hidden" value="0" class="total_of_label" name="_total_label_charges[]">
                                            <input type="hidden" value="0" class="total_product_pick_cost">
                                            <input type="hidden" value="0" class="total_of_pick" name="_total_pick_charges[]">
                                            <input type="hidden" value="0" class="total_product_pack_cost">
                                            <input type="hidden" value="0" class="total_of_pack" name="_total_pack_charges[]">
                                            <input type="hidden" value="0" class="total_product_pick_pack_flat_cost">
                                            <input type="hidden" value="0" class="total_of_pick_pack_flat" name="_total_pick_pack_flat_charges[]">
                                            <input type="hidden" value="`+element.pick_pack_flat_status+`" class="pick_pack_flat_status">
                                            <input type="hidden" value="0" class="total_products" name="_total_products[]">
                                            <input type="hidden" value="0" class="_label_cost" name="_total_label_cost[]">
                                            <input type="hidden" value="0" class="_label_qty" name="_total_label_qty[]">
                                            <div class="getProductLabels">
                                            </div>
                                        </td>
                                        <td>    
                                        <div class="d-none hiddenQty">0</div>
                                        <center>
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-primary subt_qty" disabled>-</button>
                                                    <input type="number" class="form-control text-center qty touchspin2" max="" value="0" name="qty[]" disabled/>
                                                <button type="button" class="btn btn-primary add_qty" disabled>+</button> 
                                            </div>
                                            <span class="errMsg text-danger d-none">Label Quantity Exceeded</span>
                                        </center>
                                        `;
                            list +=`    </td>
                                        <td>
                                            <div class="selling_price font-weight-bold text-success text-center">$ <span class="price">`+parseFloat(element.selling_cost).toFixed(2)+`</span> </div>
                                        </td>
                                        <td>
                                            <div class="sku_weight font-weight-bold text-success text-center"><span class="total">`+element.weight+`</span> oz</div>
                                            <input type="hidden" class="total_weight_price">
                                        </td>
                                        <td>
                                            <div class="sku-total-price font-weight-bold text-success text-center">$ <span class="price">0.00</span> </div>
                                            <input type="hidden" name="sku_selling_cost[]" class="sku_selling_cost" value="0.00">
                                            <input type="hidden" name="getservice_charges[]" value="0" class="getservice_charges">
                                        </td>
                                        <td>
                                            <center>
                                                <div class="popup"><i class="fas fa-minus-circle text-danger shadow remove-sku-row"></i>
                                                    <span class="popuptext d-none" ><span class="yes_btn">Yes</span> | <span class="bg no_btn">No</span></span>
                                                </div>
                                                <br>
                                            </center>
                                        </td>
                                    </tr>
                            `;
                            skus_data[element.id] = element;
                        });
                        $('#skuTablebody').append(list);
                        $('#skutable').DataTable({
                            autoWidth: true,
                            stateSave: true,
                            "lengthChange": false,
                            paging: false,
                            colResize: {
                                // scrollY: 200,
                                resizeTable: true
                            },
                            "columns": [
                                { "width": "10%" },
                                { "width": "40%" },
                                { "width": "15%" },
                                { "width": "10%" },
                                { "width": "10%" },
                                { "width": "35%" },
                                { "width": "10%" },
                            ],
                            'order': [
                                [2, 'desc']
                            ],
                        });
                        var count = 0;
                        var totalSkus = $('.sku').length;
                        $('.sku').each(function(indexOfSku) {
                            var _this = $(this);
                            var sku_id = $(this).val();
                            var customer_id = $('#new_customer').val();
                            var url = '{{route("skuProductDetails", ":id")}}';
                            url = url.replace(':id', sku_id);
                            count = count;
                            $.ajax({
                                url: url,
                                type: 'GET',
                                data: {
                                    _token: '{{csrf_token()}}',
                                    'customer_id': customer_id,
                                },
                                success:function(response) {
                                    // return false;
                                    var label_cost = 0.00;
                                    var pick_cost = 0.00;
                                    var pack_cost = 0.00;
                                    var pick_pack_flat_cost = 0.00;
                                    var label_qty = 0;
                                    var label_q = 0;
                                    var total = 0.00;
                                    var getstatus = 1;
                                    var html = '';
                                    var sellingPrice = 0;
                                    if (response.length > 0) {
                                        for (var index = 0; index < response.length; index++) {
                                            if (response[index].seller_cost_status == 1) {
                                                label_cost += Number(response[index].labels_price) * Number(response[index].prodCounts);
                                                pick_cost += Number(response[index].pick_price);
                                                pack_cost += Number(response[index].pack_price);
                                                pick_pack_flat_cost = Number(response[index].prod_pick_pack_flat_price);
                                                label_qty = (label_qty) + (response[index].label_qty);
                                                label_q = response[index].label_qty;
                                                getstatus = response[index].status;
                                                sellingPrice = response[index].selling_price;
                                                if (label_q == null || label_q == '') {
                                                    label_q = 0;
                                                }
                                                html += `
                                                    <input type="hidden"
                                                    data-prod_label_count`+response[index].prod_id+`="`+response[index].prod_label_count+`"
                                                    data-prod_pick_count`+response[index].prod_id+`="`+response[index].prod_pick_count+`"
                                                    data-prod_pack_count`+response[index].prod_id+`="`+response[index].prod_pack_count+`"
                                                    data-prod_pick_pack_flat_count`+response[index].prod_id+`="`+response[index].prod_pick_pack_flat_count+`"
                                                    data-pickCost`+response[index].prod_id+`="`+response[index].pick_price+`"
                                                    data-packCost`+response[index].prod_id+`="`+response[index].pack_price+`" 
                                                    data-pickPackFlatCost`+response[index].prod_id+`="`+response[index].prod_pick_pack_flat_price+`" 
                                                    data-labelCost`+response[index].prod_id+`="`+response[index].labels_price+`" 
                                                    data-prod_count`+response[index].prod_id+`="`+response[index].prodCounts+`" 
                                                    data-last_entered`+sku_id+`="0" 
                                                    data-label_remaining`+response[index].prod_id+`="`+response[index].label_qty+`" 
                                                    data-qty="0" data-label_qty`+response[index].prod_id+`="`+response[index].label_qty+`" 
                                                    data-temp_label_qty`+response[index].prod_id+`="`+response[index].label_qty+`" 
                                                    data-selling_price="`+sellingPrice+`" data-skuid="`+sku_id+`" class="ProductWithLabel labelClass`+response[index].prod_id+`" data-prodId="`+response[index].prod_id+`" data-status`+response[index].prod_id+`="`+getstatus+`" value="`+response[index].label_qty+`">
                                                    <input type="hidden" class="indvProdId" value="`+response[index].prod_id+`">
                                                    <input type="hidden" class="indvProdStatus" value="`+getstatus+`">
                                                    <input type="hidden" class="productQuantity`+response[index].prod_id+`" value="0">
                                                    <input type="hidden" class="productTotalCost`+response[index].prod_id+`" value="0">
                                                `;
                                            } else {
                                                label_cost += 0;
                                                pick_cost += Number(response[index].pick_price);
                                                pack_cost += Number(response[index].pack_price);
                                                pick_pack_flat_cost += 0;
                                                label_qty = 0;
                                                label_q = 0;
                                                getstatus = 1;
                                                sellingPrice = 0;
                                                if (label_q == null || label_q == '') {
                                                    label_q = 0;
                                                }
                                                // html += `
                                                //     <input type="hidden"
                                                //     data-prod_label_count`+response[index].prod_id+`="0"
                                                //     data-prod_pick_count`+response[index].prod_id+`="`+response[index].prod_pick_count+`"
                                                //     data-prod_pack_count`+response[index].prod_id+`="`+response[index].prod_pick_count+`"
                                                //     data-prod_pick_pack_flat_count`+response[index].prod_id+`="0"
                                                //     data-pickCost`+response[index].prod_id+`="`+response[index].pick_price+`" 
                                                //     data-packCost`+response[index].prod_id+`="`+response[index].pack_price+`" 
                                                //     data-pickPackFlatCost`+response[index].prod_id+`="0" 
                                                //     data-labelCost`+response[index].prod_id+`="0" 
                                                //     data-prod_count`+response[index].prod_id+`="`+response[index].prodCounts+`" 
                                                //     data-last_entered`+sku_id+`="0" 
                                                //     data-label_remaining`+response[index].prod_id+`="0" 
                                                //     data-qty="0" 
                                                //     data-label_qty`+response[index].prod_id+`="0" 
                                                //     data-temp_label_qty`+response[index].prod_id+`="0" 
                                                //     data-selling_price="0" 
                                                //     data-skuid="`+sku_id+`" class="ProductWithLabel labelClass`+response[index].prod_id+`" 
                                                //     data-prodId="`+response[index].prod_id+`" 
                                                //     data-status`+response[index].prod_id+`="1" value="0">
                                                //     <input type="hidden" class="indvProdId" value="`+response[index].prod_id+`">
                                                //     <input type="hidden" class="indvProdStatus" value="1">
                                                //     <input type="hidden" class="productQuantity`+response[index].prod_id+`" value="0">
                                                //     <input type="hidden" class="productTotalCost`+response[index].prod_id+`" value="0">
                                                // `;
                                                html += `
                                                    <input type="hidden"
                                                    data-prod_label_count`+response[index].prod_id+`="0"
                                                    data-prod_pick_count`+response[index].prod_id+`="`+response[index].prod_pick_count+`"
                                                    data-prod_pack_count`+response[index].prod_id+`="`+response[index].prod_pack_count+`"
                                                    data-prod_pick_pack_flat_count`+response[index].prod_id+`="0"
                                                    data-pickCost`+response[index].prod_id+`="`+response[index].pick_price+`"
                                                    data-packCost`+response[index].prod_id+`="`+response[index].pack_price+`" 
                                                    data-pickPackFlatCost`+response[index].prod_id+`="0" 
                                                    data-labelCost`+response[index].prod_id+`="0" 
                                                    data-prod_count`+response[index].prod_id+`="`+response[index].prodCounts+`" 
                                                    data-last_entered`+sku_id+`="0" 
                                                    data-label_remaining`+response[index].prod_id+`="0" 
                                                    data-qty="0" 
                                                    data-label_qty`+response[index].prod_id+`="0" 
                                                    data-temp_label_qty`+response[index].prod_id+`="0" 
                                                    data-selling_price="0" 
                                                    data-skuid="`+sku_id+`" class="ProductWithLabel labelClass`+response[index].prod_id+`" 
                                                    data-prodId="`+response[index].prod_id+`" 
                                                    data-status`+response[index].prod_id+`="1" value="0">
                                                    <input type="hidden" class="indvProdId" value="`+response[index].prod_id+`">
                                                    <input type="hidden" class="indvProdStatus" value="1">
                                                    <input type="hidden" class="productQuantity`+response[index].prod_id+`" value="0">
                                                    <input type="hidden" class="productTotalCost`+response[index].prod_id+`" value="0">
                                                `;
                                            }
                                        }
                                    } else {
                                        html += `
                                                <input type="hidden" class="ProductWithLabel" value="0">
                                                <input type="hidden" class="indvProdId" value="0">
                                                <input type="hidden" class="indvProdStatus" value="0">
                                            `;
                                    }
                                    total = total + ((label_cost) + (pick_cost) + (pack_cost) + (pick_pack_flat_cost));
                                    _this.closest('tr').find('input.total_sku_service_charges').val(parseFloat(total).toFixed(2));
                                    _this.closest('tr').find('input.total_product_label_cost').val(parseFloat(label_cost).toFixed(2));
                                    _this.closest('tr').find('input.total_product_pick_cost').val(parseFloat(pick_cost).toFixed(2));
                                    _this.closest('tr').find('input.total_product_pack_cost').val(parseFloat(pack_cost).toFixed(2));
                                    _this.closest('tr').find('input.total_product_pick_pack_flat_cost').val(parseFloat(pick_pack_flat_cost).toFixed(2));
                                    _this.closest('tr').find('input.total_products').val(response.length);
                                    _this.closest('tr').find('input._label_qty').val(label_qty);
                                    _this.closest('tr').find('.getProductLabels').append(html);
                                    // _this.closest('tr').find('.ProductWithLabel').attr('name', 'indv_prod_label'+count+'[]');
                                    // _this.closest('tr').find('.indvProdId').attr('name', 'indv_prod_Id'+count+'[]');
                                    // _this.closest('tr').find('.indvProdStatus').attr('name', 'indv_prod_Status'+count+'[]');
                                    count = count+1;
                                    if (indexOfSku === totalSkus - 1) {
                                        $('.subt_qty').prop('disabled', false);
                                        $('.add_qty').prop('disabled', false);
                                        $('.qty').prop('disabled', false);
                                        $('.loader').addClass('d-none');
                                        $('#skutable').css('opacity', '1');
                                    }
                                }
                            });
                        });
                        var html = '';
                        $.ajax({
                            url: '{{ route("getCountsProducts") }}',
                            type: 'GET',
                            data: {
                                _token: '{{csrf_token()}}',
                                'customer_id': customer_id,
                                'brand_id': brandId,
                            }, 
                            success:function(response) {
                                $('.summaryData').empty();
                                for (var i = 0; i < response.length; i++) {
                                    if (response[i].seller_cost_status == 1) {
                                        html += `<div class="invoice-total-item invoiceTotalItem`+response[i].prod_id+`">
                                                    <p class="summary`+response[i].prod_id+` d-none invoice-total-title">`+response[i].prod_name+` x <span class="summaryprodQty`+response[i].prod_id+`"></span>: </p>
                                                    <p id="" class="summary-prod-qty`+response[i].prod_id+` d-none">$ <span class="calcrestotal totalres`+response[i].prod_id+`"></span></p>
                                                    <input type="hidden" id="" name="summary-grandTotalLabelPrice">
                                                </div>
                                        `;
                                    } else {
                                        html += `<div class="invoice-total-item invoiceTotalItem`+response[i].prod_id+`">
                                                    <p class="summary`+response[i].prod_id+` d-none invoice-total-title">`+response[i].prod_name+` x <span class="summaryprodQty`+response[i].prod_id+`">0</span>: </p>
                                                    <p id="" class="summary-prod-qty`+response[i].prod_id+` d-none">$ <span class="calcrestotal totalres`+response[i].prod_id+`">0</span></p>
                                                    <input type="hidden" id="" name="summary-grandTotalLabelPrice" value="0">
                                                </div>
                                        `;
                                    }
                                }
                                html += `<hr>`;
                                $('.summaryData').append(html);
                            }
                        });
                    
                    makeProductArray(skus_data);
                    }
                });
            }
        });
        // $('#skutable').DataTable();
        $(document).on('click', '.add_qty', function() {
            var _this = $(this);
            increaseQty(_this, 'add_qty');
        });
        var one = 0;
        var two = 0;
        var three = 0;
        var four = 0;
        var five = 0;
        var six = 0;
        var seven = 0;
        var eight = 0;

        var discounted_one = 0;
        var discounted_two = 0;
        var discounted_three = 0;
        var discounted_four = 0;
        var discounted_five = 0;
        var discounted_six = 0;
        var discounted_seven = 0;
        var discounted_eight = 0;
        function increaseQty(context, type) {
            $('.saveOrder').addClass('disabled');
            var _this = context;
            var arr2 = [];
            var _qty = _this.closest('tr div.input-group').find('input.qty').val();
            if (type == 'add_qty') {
                _this.closest('tr div.input-group').find('input.qty').val(Number(_qty) + Number(1));
            } else if (type == 'sub_qty') {
                _this.closest('tr div.input-group').find('input.qty').val(Number(_qty) - Number(1));
            }
            else if (type == 'custom') {
                _this.closest('tr div.input-group').find('input.qty').val(Number(_qty));
            }
            else if (type == 'delete') {
                _this.closest('tr div.input-group').find('input.qty').val(0);
            }
            var qty = _this.closest('tr div.input-group').find('input.qty').val();
            _this.closest('tr').find('.hiddenQty').html(qty);
            var skuId = _this.closest('tr').find('input.sku').val();
            var getLabelCost = _this.closest('tr').find('.total_product_label_cost').val();
            var res1 = getLabelCost * (Number(qty));
            _this.closest('tr').find('.total_of_label').val(parseFloat(res1).toFixed(2));
            var getPickCost = _this.closest('tr').find('.total_product_pick_cost').val();
            var res2 = getPickCost * (Number(qty));
            _this.closest('tr').find('.total_of_pick').val(parseFloat(res2).toFixed(2));
            var getPackCost = _this.closest('tr').find('.total_product_pack_cost').val();
            var res3 = getPackCost * (Number(qty));
            _this.closest('tr').find('.total_of_pack').val(parseFloat(res3).toFixed(2));
            var getPickPackFlatCost = _this.closest('tr').find('.total_product_pick_pack_flat_cost').val();
            var res4 = getPickPackFlatCost;
            _this.closest('tr').find('.total_of_pick_pack_flat').val(parseFloat(res4).toFixed(2));
            var wt = 0;
            var res = 0;
            var totalRes = $('#totalRes').val();
            res = _this.closest('tr').find('.sku_weight .total').html();

            if(res > 0) {
                if ($('#pc_default_cb').is(':checked')) {
                    totalRes = totalRes + parseFloat($('#newPostageCost').val());
                } else {
                    if (res < 5) {
                        totalRes = Number(totalRes) + $('#pc_lt5').val();
                        if (type == 'add_qty') {
                            one = Number(one) + Number($('#pc_lt5').val());
                            discounted_one = Number(discounted_one) + Number($('#discounted_pc_lt5').val());
                            if (discounted_one <= 0) {
                                one = 0;
                            }
                        }
                        if (type == 'sub_qty') {
                            one = Number(one) - Number($('#pc_lt5').val());
                            discounted_one = Number(discounted_one) - Number($('#discounted_pc_lt5').val());
                            if (discounted_one <= 0) {
                                one = 0;
                            }
                        }
                        // if (qty > 0) {
                            $('#one_to_four_ounces').val(one);
                            $('#discounted_one_to_four_ounces').val(discounted_one);
                        // }
                    } else if(res >= 5 && res < 9) {
                        totalRes = Number(totalRes)+ $('#pc_lt9').val();
                        if (type == 'add_qty') {
                            two = Number(two) + Number($('#pc_lt9').val());
                            discounted_two = Number(discounted_two) + Number($('#discounted_pc_lt9').val());
                            if (discounted_two <= 0) {
                                two = 0;
                            }
                        }
                        if (type == 'sub_qty') {
                            two = Number(two) - Number($('#pc_lt9').val());
                            discounted_two = Number(discounted_two) - Number($('#discounted_pc_lt9').val());
                            if (discounted_two <= 0) {
                                two = 0;
                            }
                        }
                        // if (qty > 0) {
                            $('#five_to_eight_ounces').val(two);
                            $('#discounted_five_to_eight_ounces').val(discounted_two);
                        // }
                    } else if(res >= 9 && res < 13) {
                        totalRes = Number(totalRes) + $('#pc_lt13').val();
                        if (type == 'add_qty') {
                            three = Number(three) + Number($('#pc_lt13').val());
                            discounted_three = Number(discounted_three) + Number($('#discounted_pc_lt13').val());
                            if (discounted_three <= 0) {
                                three = 0;
                            }
                        }
                        if (type == 'sub_qty') {
                            three = Number(three) - Number($('#pc_lt13').val());
                            discounted_three = Number(discounted_three) - Number($('#discounted_pc_lt13').val());
                            if (discounted_three <= 0) {
                                three = 0;
                            }
                        }
                        // if (qty > 0) {
                            $('#nine_to_twelve_ounces').val(three);
                            $('#discounted_nine_to_twelve_ounces').val(discounted_three);
                        // }
                    } else if(res >= 13 && res < 16) {
                        totalRes = Number(totalRes) + $('#pc_gte13').val();
                        if (type == 'add_qty') {
                            four = Number(four) + Number($('#pc_gte13').val());
                            discounted_four = Number(discounted_four) + Number($('#discounted_pc_gte13').val());
                            if (discounted_four <= 0) {
                                four = 0;
                            }
                            if (discounted_four == 0) {
                                four = 0;
                            }
                        }
                        if (type == 'sub_qty') {
                            four = Number(four) - Number($('#pc_gte13').val());
                            discounted_four = Number(discounted_four) - Number($('#discounted_pc_gte13').val());
                            if (discounted_four <= 0) {
                                four = 0;
                            }
                        }
                        // if (qty > 0) {
                            $('#thirteen_to_fifteen_ounces').val(four);
                            $('#discounted_thirteen_to_fifteen_ounces').val(discounted_four);
                        // }
                    } else if(res >= 16 && res < 16.16) { // LBS rates
                        totalRes = Number(totalRes) + $('#lbs1_1_99').val();
                        if (type == 'add_qty') {
                            five = Number(five) + Number($('#lbs1_1_99').val());
                            discounted_five = Number(discounted_five) + Number($('#discounted_lbs1_1_99').val());
                            if (discounted_five <= 0) {
                                five = 0;
                            }
                        }
                        if (type == 'sub_qty') {
                            five = Number(five) - Number($('#lbs1_1_99').val());
                            discounted_five = Number(discounted_five) - Number($('#discounted_lbs1_1_99').val());
                            if (discounted_five <= 0) {
                                five = 0;
                            }
                        }
                        // if (qty > 0) {
                            $('#one_lbs').val(five);
                            $('#discounted_one_lbs').val(discounted_five);
                        // }
                    } else if(res >= 16.16 && res < 32) {
                        totalRes = Number(totalRes) + $('#lbs1_1_2').val();
                        if (type == 'add_qty') {
                            six = Number(six) + Number($('#lbs1_1_2').val());
                            discounted_six = Number(discounted_six) + Number($('#discounted_lbs1_1_2').val());
                            if (discounted_six <= 0) {
                                six = 0;
                            }
                        }
                        if (type == 'sub_qty') {
                            six = Number(six) - Number($('#lbs1_1_2').val());
                            discounted_six = Number(discounted_six) - Number($('#discounted_lbs1_1_2').val());
                            if (discounted_six <= 0) {
                                six = 0;
                            }
                        }
                        // if (qty > 0) {
                            $('#one_to_two_lbs').val(six);
                            $('#discounted_one_to_two_lbs').val(discounted_six);
                        // }
                    } else if(res >= 32.16 && res < 48) {
                        totalRes = Number(totalRes) + $('#lbs2_1_3').val();
                        if (type == 'add_qty') {
                            seven = Number(seven) + Number($('#lbs2_1_3').val());
                            discounted_seven = Number(discounted_seven) + Number($('#discounted_lbs2_1_3').val());
                            if (discounted_seven <= 0) {
                                seven = 0;
                            }
                        }
                        if (type == 'sub_qty') {
                            seven = Number(seven) - Number($('#lbs2_1_3').val());
                            discounted_seven = Number(discounted_seven) - Number($('#discounted_lbs2_1_3').val());
                            if (discounted_seven <= 0) {
                                seven = 0;
                            }
                        }
                        // if (qty > 0) {
                            $('#two_to_three_lbs').val(seven);
                            $('#discounted_two_to_three_lbs').val(discounted_seven);
                        // }
                    } else if(res >= 48.16) {
                        totalRes = Number(totalRes) + $('#lbs3_1_4').val();
                        if (type == 'add_qty') {
                            eight = Number(eight) + Number($('#lbs3_1_4').val());
                            discounted_eight = Number(discounted_eight) + Number($('#discounted_lbs3_1_4').val());
                            if (discounted_eight <= 0) {
                                eight = 0;
                            }
                        }
                        if (type == 'sub_qty') {
                            eight = Number(eight) - Number($('#lbs3_1_4').val());
                            discounted_eight = Number(discounted_eight) - Number($('#discounted_lbs3_1_4').val());
                            if (discounted_eight <= 0) {
                                eight = 0;
                            }
                        }
                        // if (qty > 0) {
                            $('#three_to_four_lbs').val(eight);
                            $('#discounted_three_to_four_lbs').val(discounted_eight);
                        // }
                    }
                }
            }
            _this.closest('tr').find('.total_weight_price').val(totalRes * qty);
            var total_label_cost = 0;
            var total_pick_cost = 0;
            var total_pack_cost = 0;
            var total_pick_pack_flat_cost = 0;
            var prods_charges = _this.closest('tr').find('input.total_sku_service_charges').val();
            total = (qty * prods_charges);
            var _selling_price = _this.closest('tr').find('.selling_price .price').html();
            // total_sku_cost = ((_selling_price * qty) + total);
            total_sku_cost = qty * _this.closest('tr').find('.selling_price .price').html();
            _this.closest('tr').find('.sku-total-price .price').html(parseFloat(total_sku_cost).toFixed(2));
            _this.closest('tr').find('.sku_selling_cost').val(parseFloat(total_sku_cost).toFixed(2));
            var total_qty = 0;
            $('.qty').each(function() {
                var labels = $(this).closest('tr').find('input.ProductWithLabel');
                total_qty = total_qty + $(this).val();
                var totalLabelCounts = 0;
                if(Number($(this).val() > 0)) {
                    labels.each(function() {
                        var prodId = $(this).attr('data-prodid');
                        var prodCounts = $(this).attr('data-labelcost'+prodId);
                        totalLabelCounts += Number(prodCounts);
                    });
                }
                var labelCharges = $(this).closest('tr').find('input.total_product_label_cost').val();
                total_label_cost = total_label_cost + ($(this).val() * labelCharges);
                var pickCharges = $(this).closest('tr').find('input.total_product_pick_cost').val();
                total_pick_cost = total_pick_cost + ($(this).val() * pickCharges);
                var packCharges = $(this).closest('tr').find('input.total_product_pack_cost').val();
                total_pack_cost = total_pack_cost + ($(this).val() * packCharges);
                if ($(this).val() > 0) {
                    var pickPackFlatCharges = $(this).closest('tr').find('input.total_product_pick_pack_flat_cost').val();
                    total_pick_pack_flat_cost += (Number($(this).val()) * pickPackFlatCharges);
                }
                // console.log(total_pick_pack_flat_cost);
            });
            ////////////////////////////////////////////////////////////////////////////////////////
            if (total_qty >= 0) {
                $('#total_label_cost .price').html(parseFloat(total_label_cost).toFixed(2));
                $('#grandTotalLabelPrice').val(parseFloat(total_label_cost).toFixed(2));
                $('#_label_cost').val(parseFloat(total_label_cost).toFixed(2));
                $('#total_pick_cost .price').html(parseFloat(total_pick_cost).toFixed(2));
                $('#grandTotalPickPrice').val(parseFloat(total_pick_cost).toFixed(2));
                $('#_pick_cost').html(parseFloat(total_pick_cost).toFixed(2));
                $('#total_pack_cost .price').html(parseFloat(total_pack_cost).toFixed(2));
                $('#grandTotalPackPrice').val(parseFloat(total_pack_cost).toFixed(2));
                $('#_pack_cost').html(parseFloat(total_pack_cost).toFixed(2));
                $('#total_pick_pack_flat_cost .price').html(parseFloat(total_pick_pack_flat_cost).toFixed(2));
                $('#grandTotalPickPackFlatPrice').val(parseFloat(total_pick_pack_flat_cost).toFixed(2));
                $('#_pick_pack_flat_cost').html(parseFloat(total_pick_pack_flat_cost).toFixed(2));
            }
            var arr = [];
            var getlabels = _this.closest('tr').find('input.ProductWithLabel');
            var res = 0;
            var err = 1;
            var labelX = 0;
            var pickX = 0;
            var packX = 0;
            var pickPackFlatX = 0;
            var mailerX = 0;
            var postageX = 0;
            var qqty = 0;
            var flatQqty = 0;
            $('.sku').each(function() {
                var _sku = $(this);
                var prod_label = _sku.closest('tr').find('input.ProductWithLabel');
                count = 0;
                var selling_cost = 0;
                var res = 0;
                var total = 0;
                var sku_qty = Number(_sku.closest('tr').find('input.qty').val());
                qqty = Number(qqty) + Number(sku_qty);
                prod_label.each(function() {
                    var prodLabel = $(this);
                    var tempLabelQty = prodLabel.attr('data-temp_label_qty');
                    var sellingCost = prodLabel.attr('data-selling_price');
                    var prodId = prodLabel.attr('data-prodId');
                    var labelClass = $('.labelClass'+prodId);
                    var skuid = prodLabel.attr('data-skuid');
                    var totalres = 0;
                    var status = prodLabel.attr('data-status'+prodId);
                    var orgQty = prodLabel.attr('data-label_qty'+prodId);
                    var prodCounts = prodLabel.attr('data-prod_count'+prodId);
                    var grandtotal = 0;
                    var labelCost = prodLabel.attr('data-labelCost'+prodId);
                    var pickCost = prodLabel.attr('data-pickCost'+prodId);
                    var packCost = prodLabel.attr('data-packCost'+prodId);
                    var pickPackFlatCost = prodLabel.attr('data-pickPackFlatCost'+prodId);
                    var prod_label_count = prodLabel.attr('data-prod_label_count'+prodId);
                    var prod_pick_count = prodLabel.attr('data-prod_pick_count'+prodId);
                    var prod_pack_count = prodLabel.attr('data-prod_pack_count'+prodId);
                    var prod_pick_pack_flat_count = prodLabel.attr('data-prod_pick_pack_flat_count'+prodId);
                    $('.invoiceTotalItem'+prodId).addClass('d-none');
                    $('.productQuantity'+prodId).val('');
                    $('.summaryprodQty'+prodId).html('');
                    $('.totalres'+prodId).html('');

                    if (arr2.hasOwnProperty(prodId)) {
                        if (sku_qty != 0) {
                            arr2[prodId]['qty'] += Number(prodCounts) * Number(sku_qty);
                            arr2[prodId]['amount'] += (Number(prodCounts) * Number(sku_qty)) * sellingCost; 
                            if (status == 0) {
                                if (labelCost == 0) {
                                    labelX = 0 + labelX;
                                } else {
                                    labelX = Number(labelX) + Number(Number(prodCounts) * Number(sku_qty));
                                }
                            } else {
                                labelX = 0 + labelX;
                            }
                            pickX = Number(pickX) + Number(Number(prod_pick_count) * Number(sku_qty));
                            packX = Number(packX) + Number(Number(prod_pack_count) * Number(sku_qty));
                            pickPackFlatX = Number(pickPackFlatX) + Number(sku_qty);
                            mailerX = Number(sku_qty) + mailerX;
                            postageX = Number(sku_qty) + postageX;
                        }
                    } else {
                        if (sku_qty != 0) {
                            arr2[prodId] = [];
                            arr2[prodId]['qty'] = Number(prodCounts) * Number(sku_qty);
                            arr2[prodId]['amount'] = (Number(prodCounts) * Number(sku_qty)) * sellingCost;
                            if (status == 0) {
                                if (labelCost == 0) {
                                    labelX = 0 + labelX;
                                } else {
                                    labelX = Number(labelX) + Number(Number(prodCounts) * Number(sku_qty));
                                }
                            } else {
                                labelX = 0 + labelX;
                            }
                            pickX = Number(pickX) + Number(Number(prod_pick_count) * Number(sku_qty));
                            packX = Number(packX) + Number(Number(prod_pack_count) * Number(sku_qty));
                            pickPackFlatX = Number(pickPackFlatX) + Number(sku_qty);
                            mailerX = Number(sku_qty) + mailerX;
                            postageX = Number(sku_qty) + postageX;
                        }
                    }
                    var summary_html = '';
                    if (arr2.length > 0) {
                        arr2.forEach((value, key) => {
                            $('.invoiceTotalItem'+key).removeClass('d-none');
                            $('.productQuantity'+key).val(value['qty']);
                            $('.summaryprodQty'+key).html(value['qty']);
                            $('.totalres'+key).html(parseFloat(value['amount']).toFixed(2)); 
                            if ($('.totalres'+key).html() != 0 || $('.totalres'+key).html() != '') {
                                $('.invoiceTotalItem'+key).removeClass('d-none');
                                $('.summary'+key).removeClass('d-none');
                                $('.summary-prod-qty'+key).removeClass('d-none');
                            }
                        });
                        
                        $(".summaryData").removeClass('d-none');
                    } else {
                        $(".summaryData").addClass('d-none');
                    }
                });
                if (_sku.closest('tr').find('input.pick_pack_flat_status').val() == 1) {
                    flatQqty = Number(flatQqty) + Number(sku_qty);
                }
            });
            $('#labelX').html(labelX);
            $('#pickX').html(pickX);
            $('#packX').html(packX);
            $('#pickPackFlatX').html(flatQqty);
            $('#mailerX').html(qqty);
            $('#postageX').html(qqty);
            $('#labelqty').val(labelX);
            $('#pickqty').val(pickX);
            $('#packqty').val(packX);
            $('#pickpackflatqty').val(flatQqty);
            $('#mailerqty').val(qqty);
            $('#postageqty').val(qqty);
            getlabels.each(function() {
                var getlabelsThis = $(this);
                var prodId = getlabelsThis.attr('data-prodId');
                var labelsqty = getlabelsThis.val();
                var tempLabelQty = getlabelsThis.attr('data-temp_label_qty'+prodId);
                var orgQty = getlabelsThis.attr('data-label_qty'+prodId);
                var prodCounts = getlabelsThis.attr('data-prod_count'+prodId);
                if (getlabelsThis.attr('data-status'+prodId) == 0) {
                    if (arr2.hasOwnProperty(prodId)) {
                        if (orgQty < arr2[prodId]['qty']) {
                            $('.saveOrder').attr('disabled', true);
                            getlabelsThis.closest('tr').find('.errMsg').removeClass('d-none');
                        }
                    } else {
                        $('.saveOrder').attr('disabled', false);
                        getlabelsThis.closest('tr').find('.errMsg').addClass('d-none');
                    }
                }
                var _qty_ = $('.labelClass'+prodId).attr('data-qty');
                var selling_price = getlabelsThis.attr('data-selling_price');
                var getprodqty = _qty_;
                arr.push({'product_id' : prodId, 'qty': getprodqty, 'selling_price': selling_price, 'skuid': _this.closest('tr').find('.sku').val()});
            });
            ////////////////////////////////////////////////////////////////////////////////////////
            calculateGrandTotal();
            setTimeout(() => {
                $('.saveOrder').removeClass('disabled');
            }, 1000);
        }
        $(document).on('click', '.subt_qty', function() {
            var _qty = $(this).closest('tr div.input-group').find('input.qty').val();
            if (_qty <= 0) {
                $(this).closest('tr div.input-group').find('input.qty').val(0);
            } else {
                var qty_ = Number($(this).closest('tr div.input-group').find('input.qty').val()) - Number(1);
                var _this = $(this);
                increaseQty(_this, 'sub_qty');
            }
        });
        // add new sku row
        $(document).on('click', '.add-sku-row', function() {
            var products = $('#product-list').html();
            var tr = $(".sku-row table tr")
                        .clone()
                        .appendTo('.sku-table tbody');
            $('.cloned-sku').each(function(){
                return $(this).addClass('select2');
            });
        });
        // remove sku row
        $(document).on('click', '.remove-sku-row', function(){
            $('td center div span.popuptext').addClass('d-none');
            $(this).closest('tr').find('td center div span.popuptext').removeClass('d-none');
        });
        $(document).on('click', '.yes_btn', function() {
            var _this = $(this);
            var This = _this.closest('tr').find('input.qty');
            increaseQty(This, 'delete');
            $(this).closest('tr').remove();
            ////////////////////////////////////////////////////////////////////////////////////////
            // calculateGrandTotal();
        });
        $(document).on('click', '.no_btn', function() {
            $(this).closest('tr').find('td center div span.popuptext').addClass('d-none');
        });
        $(document).on("keyup", ".qty", function () {
            var _this = $(this);
            increaseQty(_this, 'custom');
        });
        $(document).on("click", ".charges", function(){
            var context = $(this);
            var name = $(this).attr('name');
            var value = $(this).val();
            var qty = context.closest('tr').find('.qty').val();
            var sku_total = calculateSkuTotal(context);
            if(context.is(":checked")) {
                if (name == "labels_price") {
                    context.closest('tr').find('.sku_labels_price').val(value * qty);
                } else if (name == "pick_price") {
                    context.closest('tr').find('.sku_pick_price').val(value);
                } else if (name == "pack_price") {
                    context.closest('tr').find('.sku_pack_price').val(value);
                }
            } else {
                if (name == "labels_price") {
                    context.closest('tr').find('.sku_labels_price').val(0);
                } else if (name == "pick_price") {
                    context.closest('tr').find('.sku_pick_price').val(0);
                } else if (name == "pack_price") {
                    context.closest('tr').find('.sku_pack_price').val(0);
                }
            }
            context.closest('tr').find('.sku-total-price .price').html(Number(sku_total).toFixed(2));
            context.closest('tr').find('.sku_selling_cost').val(Number(sku_total).toFixed(2));
            var grand_total = calculateGrandTotal();
        });
        $(document).on("change", "#pc_default_cb", function(){
            var postage_val = 0;
            if ($(this).is(':checked')) {
                $('.postageCostInput').removeClass('invisible');
                var postage_val = $('#newPostageCost').val();
            } else {
                $('.postageCostInput').addClass('invisible');
            }
            updatePostageCost(postage_val);
            calculateGrandTotal();
        });
        $(document).on('keyup', '#newPostageCost', function() {
            updatePostageCost();
            calculateGrandTotal();
        });
        // reset form
        $(document).on('click', '#reset', function() {
            // $('.sku-total-price .price, .selling_price .price').each(function () {
            //     $(this).html('0.00');
            // });
            // $('.qty').each(function () {
            //     $(this).html('1');
            // });
        });
        function calculateSkuTotal()
        {
            var qty = context.closest('tr').find('.qty').val();
            var selling_cost = parseFloat(context.closest('tr').find('.selling_price').find('.price').html());
            var labels_price = context.closest('tr').find('.labels_price').is(":checked") ? parseFloat($('.labels_price').val()) * qty : 0;
            var pick_price = context.closest('tr').find('.pick_price').is(":checked") ? parseFloat($('.pick_price').val()) : 0;
            var pack_price = context.closest('tr').find('.pack_price').is(":checked") ? parseFloat($('.pack_price').val()) : 0;
            var pickPackFlat_price = context.closest('tr').find('.pick_pack_flat_price').is(":checked") ? parseFloat($('.pick_pack_flat_price').val()) : 0;
            var total = qty * selling_cost;
            (labels_price > 0) ? total += labels_price :total = total ;
            (pick_price > 0) ? total += pick_price : total = total;
            (pack_price > 0) ? total += pack_price : total = total;
            (pick_pack_flat_price > 0) ? total += pick_pack_flat_price : total = total;
            total = Number(total).toFixed(2);
            if (context.closest('tr').find('.sku').val() == '' || context.closest('tr').find('.sku').val() === undefined) {
                return 0.00;
            } else {
                return total;
            }
        }
        function calculateGrandTotal()
        {
            updatePostageCost();
            var total = 0;
            var row_total_price = 0;
            var qty = 0;
            $('.qty').each(function() {
                qty += $(this).closest('tr').find('input.total_products').val() * $(this).val();
                // sku_items += $(this).closest('tr').find('input.total_products').val() * $(this).val();
            });
            $('.sku-total-price .price').each(function() {
                row_total_price = row_total_price + parseFloat($(this).html());
            });
            // total = total + row_total_price;
            if (qty > 0) {
                // adding postage cost
                var labelCost = $('#total_label_cost .price').html();
                var pickCost = $('#total_pick_cost .price').html();
                var packCost = $('#total_pack_cost .price').html();
                var pickPackFlatCost = $('#total_pick_pack_flat_cost .price').html();
                var mailerCost = $('#total_mailer_cost .price').html();
                var postage_cost = $("#total_postage_cost .price").html();
                var totalprodamount = 0;
                $('.calcrestotal').each(function() {
                    totalprodamount = Number(totalprodamount) + Number($(this).html());
                });
                total = Number(total) + Number(totalprodamount) + (Number(mailerCost)) + (Number(postage_cost)) + (Number(labelCost)) + (Number(pickCost)) + (Number(packCost)) + (Number(pickPackFlatCost));
            } else {
                $('#total_mailer_cost .price').html('0.00');
                total = total + 0;
            }
            $("#grand_total_price").text("$ " + parseFloat(total).toFixed(2));
            $("#grand_total_price_input").val(parseFloat(total).toFixed(2));
        }
        function updatePostageCost()
        {
            var totalqty = 0;
            var mailer = 0;
            var postageTotal = 0;
            var brand_mailer = $('#brand_mailer').val();
            var mailer_cost = 0;
            $('.qty').each(function() {
                totalqty = Number(totalqty) + Number($(this).val());
                postageTotal = Number(postageTotal) + Number($(this).closest('tr').find('.total_weight_price').val());
            });
            if ($('#pc_default_cb').is(':checked')) {
                if (Number($('#newPostageCost').val()) != '' || Number($('#newPostageCost').val()) != null || Number($('#newPostageCost').val()) != 0) {
                    postageTotal = Number(Number($('#newPostageCost').val())) * Number(totalqty);
                }
            }
            if(brand_mailer != 0) {
                mailer_cost = brand_mailer;
            } else {
                mailer_cost = $('#mailer_cost').val();
            }
            mailer = totalqty * mailer_cost;
            $('#total_mailer_cost .price').html(parseFloat(mailer).toFixed(2));
            $('#mailer_costNew').val(parseFloat(mailer).toFixed(2));
            var _qty = 0;
            $('.qty').each(function() {
                _qty = _qty + $(this).val();
            });
            if (_qty == 0) {
                totalRes = 0;
            }
            $("#total_postage_cost .price").html(parseFloat(postageTotal).toFixed(2));
            $("#total_postage_price").val(parseFloat(postageTotal).toFixed(2));
        }
        function setChargesRate(selector, value){
            $('.' + selector).each(function(){
                $(this).val(value);
            });
        }
        function makeProductArray(skus) {
            skus.forEach(element => {
                var products = element.products;
                products.forEach(product => {
                    if(!products_data.hasOwnProperty(product.product_id)) {
                        var temp_data = [];
                        temp_data['labels'] = product.labelqty[0].label_qty;
                        temp_data['utilized_labels'] = 0;
                        temp_data['remaining_labels'] = 0;
                        products_data[product.product_id] = temp_data;
                    }
                });
            });
        }
    });
    $("body").on("change", "#country_id", function () {
        var countryId = $(this).val();
        $.ajax({
            type: 'POST',
            url: '/getStates',
            data: {
                "country_id": countryId,
                _token: "{{ csrf_token() }}",
                dataType: "HTML",
            },
            success: function (res) {

                $("#state_id").html(res)

            }
        });
    });
    $("body").on("change", "#state_id", function () {
        var stateId = $(this).val();
        $.ajax({
            type: 'POST',
            url: '/getCities',
            data: {
                "state_id": stateId,
                _token: "{{ csrf_token() }}",
                dataType: "HTML",
            },
            success: function (res) {
                $("#city_id").html(res)

            }
        });
    });
    $('body').on("click", ".deleteItem", function () {
        var productCost = $(this).parent().parent().find('.single_product_price_input').val();
        var grandTotal = $("#grand_total_price_input").val();
        var newGrandTotal = grandTotal - productCost;
        $("#grand_total_price").text(newGrandTotal);
        $("#grand_total_price_input").val(newGrandTotal);
        $(this).parents(".repeater-wrapper").remove();
    });           
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
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/resize.js') }}"></script>
@stop