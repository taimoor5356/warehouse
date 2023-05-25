@extends('admin.layout.app')
@section('title', 'Edit Batch')
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
                    <h2 class="content-header-title float-start mb-0">Edit Batch</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item">Batch History
                            </li>
                            <li class="breadcrumb-item active">Edit Batch
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{route('update_order', $orderId)}}" id="orderForm" enctype="multipart/form-data" method="post">
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
                                        <option value="{{ $customer->id }}" selected>{{ $customer->customer_name }}</option>
                                        {{-- <option value="">Select Customer</option> --}}
                                        {{-- @foreach($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->customer_name}}</option>
                                        @endforeach --}}
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
                                        <option selected value="@isset($brand){{ $brand->id }}@endisset">@isset($brand){{ $brand->brand }}@endisset</option>
                                        {{-- <option value="">Select Brand</option> --}}
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
                                <input type="text" class="form-control" placeholder="Enter Short Note" name="notes" id="notes" value="{{ $orderMainData->notes }}">
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
                    </div>
                    <!-- Address and Contact ends -->
                    <div class="row m-1">
                        <div class="loader d-none" style="position: relative; top: 150px; left: 50%;"></div>
                        <table class="table table-bordered sku-table" id="skutable">
                            <thead>
                                <tr>
                                    <th style="width: 10%" class="align-middle">SKU ID</th>
                                    <th style="width: 15%" class="align-middle">SKU</th>
                                    <th style="width: 10%" class="align-middle">Quantity</th>
                                    <th style="width: 12%" class="align-middle text-center">Selling Price</th>
                                    <th style="width: 12%" class="align-middle text-center">SKU Weight</th>
                                    <th style="width: 12%" class="align-middle text-center">Total</th>
                                    {{-- <th style="width: 5%" class="align-middle text-center">Action</th> --}}
                                </tr>
                            </thead>
                            <tbody id="skuTablebody">
                                @php
                                    $sum_of_amounts = 0;
                                    $service_charges = 0;
                                    $mailer = 0;
                                    $pick = 0;
                                    $pack = 0;
                                    $labels = 0;
                                    $tpick = 0;
                                    $tPack = 0;
                                    $tLabels = 0;
                                    $servicesCost = 0;
                                    $tMailer = 0;
                                    $tPostage = 0;
                                    $labelQty = 0;
                                    $pickQty = 0;
                                    $packQty = 0;
                                    $mailerQty = 0;
                                    $postageQty = 0;
                                @endphp
                                @foreach($orderDetailData as $val)
                                @php
                                    $skuOrder = \App\Models\SkuOrder::where('sku_id', $val->sku_id)->where('order_id', $val->order_id)->first();
                                    // dd($skuOrder);
                                @endphp
                                <tr id="sku__{{ $skuOrder->sku_id }}" class="item">
                                    <td class="py-1">
                                        <p class="card-text text-nowrap">
                                            @isset($val->sku_order){{$skuOrder->sku_id_name}}@endisset
                                        </p>
                                    </td>
                                    <td class="py-1">
                                        <h5 class="sku_name">@isset($val->sku_order){{$skuOrder->name}}@endisset</h5>
                                        <div class="sku-error text-danger"></div>
                                        <input type="hidden" name="sku[]" value="{{ $skuOrder->sku_id }}" class="sku">
                                        <input type="hidden" class="total_sku_service_charges" name="_total_service_charges[]" value="{{$skuOrder->service_charges}}">
                                        <input type="hidden" class="total_product_label_cost">
                                        <input type="hidden" class="total_of_label" name="_total_label_charges[]" value="{{json_decode($val->service_charges_detail)[0]->price}}">
                                        <input type="hidden" class="total_product_pick_cost" value="">
                                        <input type="hidden" class="total_of_pick" name="_total_pick_charges[]" value="{{json_decode($val->service_charges_detail)[1]->price}}">
                                        <input type="hidden" class="total_product_pack_cost">
                                        <input type="hidden" class="total_of_pack" name="_total_pack_charges[]" value="{{json_decode($val->service_charges_detail)[2]->price}}">
                                            
                                        <input type="hidden" class="total_product_pick_pack_flat_cost">
                                        <input type="hidden" class="total_of_pick_pack_flat" name="_total_pick_pack_flat_charges[]" value="0.00">
                                        <input type="hidden" value="{{$skuOrder->pick_pack_flat_status}}" class="pick_pack_flat_status">

                                        <input type="hidden" class="total_products" name="_total_products[]">
                                        <input type="hidden" class="_label_cost" name="_total_label_cost[]">
                                        <input type="hidden" class="_label_qty" name="_total_label_qty[]">
                                        <div class="getProductLabels">
                                        </div>
                                    </td>
                                    <td class="py-1">    
                                        <div class="d-none hiddenQty">0</div>
                                        <center>
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-primary subt_qty">-</button>
                                                    <input type="number" class="form-control text-center qty runFuntionQty touchspin2" max="" value="@isset($val->sku_order){{$val->qty}}@endisset" name="qty[]" />
                                                <button type="button" class="btn btn-primary add_qty">+</button> 
                                            </div>
                                            <span class="errMsg text-danger d-none">Label Quantity Exceeded</span>
                                        </center>
                                    </td>
                                    <td class="py-1 pull-right" style="text-align: right">
                                        @php
                                            $getPostageValues = json_decode($orderMainData->customer_service_charges);
                                            $charges = json_decode($val->service_charges_detail);
                                            foreach ($charges as $charge) {
                                                if ($charge->slug == 'labels_price') {
                                                    if ($orderMainData->labelqty > 0) {
                                                        $tLabels = ($tLabels + $charge->price);
                                                        if ($charge->price > 0) {
                                                            $labelQty = $labelQty + 1;
                                                        }
                                                    }
                                                }
                                                if ($charge->slug == 'pick_price') {
                                                    if ($orderMainData->pickqty > 0) {
                                                        $tpick = $tpick + $charge->price;
                                                        if ($charge->price > 0) {
                                                            $pickQty = $pickQty + 1;
                                                        }
                                                    }
                                                }
                                                if ($charge->slug == 'pack_price') {
                                                    if ($orderMainData->packqty > 0) {
                                                        $tPack = $tPack + $charge->price;
                                                        if ($charge->price > 0) {
                                                            $packQty = $packQty + 1;
                                                        }
                                                    }
                                                }
                                                if ($charge->slug == 'mailer_price') {
                                                    if ($orderMainData->mailerqty > 0) {
                                                        $tMailer = $charge->price;
                                                        if ($charge->price > 0) {
                                                            $mailerQty = $mailerQty + 1;
                                                        }
                                                    }
                                                }
                                                if ($charge->slug == 'postage_price') {
                                                    if ($orderMainData->postageqty > 0) {
                                                        $tPostage = $charge->price;
                                                        if ($charge->price > 0) {
                                                            $postageQty = $postageQty + 1;
                                                        }
                                                    }
                                                }
                                            }
                                            $cost = $val->sku_selling_cost;
                                            $servicesCost = $servicesCost + $val->sku_selling_cost;
                                                    $weight = $skuOrder->weight;
                                                    if ($weight < 5 && $weight > 0) {
                                                        $weightValue = $getPostageValues->postage_cost_lt5;
                                                        if ($val->qty == 0) {
                                                            $weightValue = 0;
                                                        }
                                                    } else if($weight >= 5 && $weight < 9) {
                                                        $weightValue = $getPostageValues->postage_cost_lt9;
                                                        if ($val->qty == 0) {
                                                            $weightValue = 0;
                                                        }
                                                    } else if($weight >= 9 && $weight < 13) {
                                                        $weightValue = $getPostageValues->postage_cost_lt13;
                                                        if ($val->qty == 0) {
                                                            $weightValue = 0;
                                                        }
                                                    } else if($weight >= 13 && $weight < 16) {
                                                        $weightValue = $getPostageValues->postage_cost_gte13;
                                                        if ($val->qty == 0) {
                                                            $weightValue = 0;
                                                        }
                                                    } else if($weight >= 16 && $weight < 16.16) { // LBS rates
                                                        $weightValue = $getPostageValues->lbs1_1_99;
                                                        if ($val->qty == 0) {
                                                            $weightValue = 0;
                                                        }
                                                    } else if($weight >= 16.16 && $weight < 32) {
                                                        $weightValue = $getPostageValues->lbs1_1_2;
                                                        if ($val->qty == 0) {
                                                            $weightValue = 0;
                                                        }
                                                    } else if($weight >= 32.16 && $weight < 48) {
                                                        $weightValue = $getPostageValues->lbs2_1_3;
                                                        if ($val->qty == 0) {
                                                            $weightValue = 0;
                                                        }
                                                    } else if($weight >= 48.16) {
                                                        $weightValue = $getPostageValues->lbs3_1_4;
                                                        if ($val->qty == 0) {
                                                            $weightValue = 0;
                                                        }
                                                    } else {
                                                        $weightValue = 0;
                                                    }
                                        @endphp
                                        <div class="selling_price font-weight-bold text-success text-center">$ <span class="price">@isset($val->sku_order){{number_format($skuOrder->selling_cost, 2)}}@endisset</span> </div>
                                    </td>
                                    <td>
                                        <div class="sku_weight font-weight-bold text-success text-center"><span class="total">@isset($val->sku_order){{number_format($skuOrder->weight, 2)}}@endisset</span> oz</div>
                                        <input type="hidden" class="total_weight_price" value="{{ $weightValue * $val->qty }}">
                                        @php 
                                        $weightValue = 0; @endphp
                                    </td>
                                    <td>
                                        <div class="sku-total-price font-weight-bold text-success text-center">$ <span class="price">{{number_format(($cost), 2)}}</span> </div>
                                        <input type="hidden" name="sku_selling_cost[]" class="sku_selling_cost" value="{{ $val->sku_selling_cost }}">
                                        <input type="hidden" name="getservice_charges[]" value="0" class="getservice_charges">
                                    </td>
                                </tr>
                                @endforeach

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
                                <input type="hidden" name="pc_default" id="pc_default">
                                <input type="hidden" name="pick_cost" class="pick_cost" value="0">
                                <input type="hidden" name="pack_cost" class="pack_cost" value="0">
                                <input type="hidden" name="pick_pack_flat_cost" class="pick_pack_flat_cost" value="0">
                                <input type="hidden" name="labels_cost" class="labels_cost" value="0">
                            </div>
                            <div class="col-md-7 d-flex justify-content-end order-md-2 order-1">
                                <div class="invoice-total-wrapper" style="min-width: 30rem">
                                    <div class="summary"><h4>Summary</h4><hr>
                                        <div class="summaryData">
                                            <input type="hidden" class="checkValue" value="1">
                                            @foreach($products as $product)
                                                @php
                                                    $status = \App\Models\ProductOrderDetail::where('product_id', $product['prod_id'])->where('order_id', $orderId)->first();
                                                @endphp
                                                {{-- @isset($product['product_occ'][2])
                                                    @if ($product['product_occ'][2] == 1) --}}
                                                        @if ( $product['product_occ'][0] > 0)
                                                            <div class="invoice-total-item invoiceTotalItem{{ $product['prod_id'] }}">
                                                                <p class="summary{{ $product['prod_id'] }} invoice-total-title">{{ $product['product_name'] }} x <span class="summaryprodQty{{ $product['prod_id'] }}">{{ $product['product_occ'][0] }}</span>: </p>
                                                                <p id="" class="summary-prod-qty{{ $product['prod_id'] }}">$ <span class="calcrestotal totalres{{ $product['prod_id'] }}">{{number_format(($product['product_occ'][1]), 2)}}</span></p>
                                                                <input type="hidden" id="" name="summary-grandTotalLabelPrice" value="0">
                                                            </div>
                                                        @else
                                                            <div class="invoice-total-item invoiceTotalItem{{ $product['prod_id'] }} d-none">
                                                                <p class="summary{{ $product['prod_id'] }} invoice-total-title">{{ $product['product_name'] }} x <span class="summaryprodQty{{ $product['prod_id'] }}">{{ $product['product_occ'][0] }}</span>: </p>
                                                                <p id="" class="summary-prod-qty{{ $product['prod_id'] }}">$ <span class="calcrestotal totalres{{ $product['prod_id'] }}">{{number_format(($product['product_occ'][1]), 2)}}</span></p>
                                                                <input type="hidden" id="" name="summary-grandTotalLabelPrice" value="0">
                                                            </div>
                                                        @endif
                                                    {{-- @else
                                                        <div class="invoice-total-item invoiceTotalItem{{ $product['prod_id'] }}">
                                                            <p class="summary{{ $product['prod_id'] }} invoice-total-title">{{ $product['product_name'] }} x <span class="summaryprodQty{{ $product['prod_id'] }}">{{ $product['product_occ'][0] }}</span>: </p>
                                                            <p id="" class="summary-prod-qty{{ $product['prod_id'] }}">$ <span class="calcrestotal totalres{{ $product['prod_id'] }}">0.00</span></p>
                                                            <input type="hidden" id="" name="summary-grandTotalLabelPrice" value="0">
                                                        </div>
                                                    @endif --}}
                                                {{-- @else --}}
                                                    {{-- @if ( $product['product_occ'][0] > 0)
                                                        <div class="invoice-total-item invoiceTotalItem{{ $product['prod_id'] }}">
                                                            <p class="summary{{ $product['prod_id'] }} invoice-total-title">{{ $product['product_name'] }} x <span class="summaryprodQty{{ $product['prod_id'] }}">{{ $product['product_occ'][0] }}</span>: </p>
                                                            <p id="" class="summary-prod-qty{{ $product['prod_id'] }}">$ <span class="calcrestotal totalres{{ $product['prod_id'] }}">{{number_format(($product['product_occ'][1]), 2)}}</span></p>
                                                            <input type="hidden" id="" name="summary-grandTotalLabelPrice">
                                                        </div>
                                                    @endif --}}
                                                {{-- @endisset --}}
                                            @endforeach
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Label x <span id="labelX"> @if($orderMainData != null) {{ $orderMainData->labelqty }} @endisset</span>:</p>
                                        <p id="total_label_cost" class="">$ <span class="price">{{number_format(($tLabels), 2)}}</span></p>
                                        <input type="hidden" id="grandTotalLabelPrice" name="grandTotalLabelPrice" value="{{ $tLabels }}">
                                        <input type="hidden" id="labelqty" name="labelqty" value="{{ $orderMainData->labelqty }}">
                                        {{-- <p class="invoice-total-title">Label x  @if($orderMainData != null) {{ $orderMainData->labelqty }} @endisset:</p>
                                        <p class="invoice-total-amount">${{number_format(($tLabels), 2)}}</p> --}}
                                    </div>
                                    <div class="invoice-total-item">

                                        <p class="invoice-total-title">Pick x <span id="pickX">@if($orderMainData != null) {{ $orderMainData->pickqty }} @endisset</span>:</p>
                                        <p id="total_pick_cost" class="">$ <span class="price">{{number_format(($tpick), 2)}}</span></p>
                                        <input type="hidden" id="grandTotalPickPrice" name="grandTotalPickPrice" value="{{ $tpick }}">
                                        <input type="hidden" id="pickqty" name="pickqty" value="{{ $orderMainData->pickqty }}">

                                        {{-- <p class="invoice-total-title">Pick x  @if($orderMainData != null) {{ $orderMainData->pickqty }} @endisset:</p>
                                        <p class="invoice-total-amount">${{number_format(($tpick), 2)}}</p> --}}
                                    </div>
                                    <div class="invoice-total-item">

                                        <p class="invoice-total-title">Pack x <span id="packX">@if($orderMainData != null) {{ $orderMainData->packqty }} @endisset</span>:</p>
                                        <p id="total_pack_cost" class="">$ <span class="price">{{number_format(($tPack), 2)}}</span></p>
                                        <input type="hidden" id="grandTotalPackPrice" name="grandTotalPackPrice" value="{{ $tPack }}">
                                        <input type="hidden" id="packqty" name="packqty" value="{{ $orderMainData->packqty }}">

                                        {{-- <p class="invoice-total-title">Pack x  @if($orderMainData != null) {{ $orderMainData->packqty }} @endisset:</p>
                                        <p class="invoice-total-amount">${{number_format(($tPack), 2)}}</p> --}}
                                    </div>
                                    <div class="invoice-total-item">

                                        <p class="invoice-total-title">Pick / Pack Flat x <span id="pickPackFlatX">@if($orderMainData != null) {{ $orderMainData->pick_pack_flat_qty }} @endisset</span>:</p>
                                        <p id="total_pick_pack_flat_cost" class="">$ <span class="price">{{number_format(($orderMainData->pick_pack_flat_price), 2)}}</span></p>
                                        <input type="hidden" id="grandTotalPickPackFlatPrice" name="grandTotalPickPackFlatPrice" value="{{ $orderMainData->pick_pack_flat_price }}">
                                        <input type="hidden" id="pickpackflatqty" name="pickpackflatqty" value="{{ $orderMainData->pick_pack_flat_qty }}">

                                        {{-- <p class="invoice-total-title">Pack x  @if($orderMainData != null) {{ $orderMainData->packqty }} @endisset:</p>
                                        <p class="invoice-total-amount">${{number_format(($tPack), 2)}}</p> --}}
                                    </div>
                                    <div class="invoice-total-item">

                                        <p class="invoice-total-title">Mailer x <span id="mailerX">@if($orderMainData != null) {{ $orderMainData->mailerqty }} @endisset</span>:</p>
                                        <p id="total_mailer_cost" class="">$ <span class="price">{{number_format(($tMailer), 2)}}</span></p>

                                        <input type="hidden" name="mailer_cost" id="mailer_cost" />
                                        <input type="hidden" name="brand_mailer" id="brand_mailer" />
                                        <input type="hidden" name="mailer_costNew" id="mailer_costNew" value="{{ $tMailer }}">
                                        <input type="hidden" id="mailerqty" name="mailerqty" value="{{ $orderMainData->mailerqty }}">

                                        {{-- <p class="invoice-total-title">Mailer x  @if($orderMainData != null) {{ $orderMainData->mailerqty }} @endisset:</p>
                                        <p class="invoice-total-amount">${{number_format(($tMailer), 2)}}</p> --}}
                                    </div>
                                    <div class="invoice-total-item">

                                        <p class="invoice-total-title">Postage x <span id="postageX">@if($orderMainData != null) {{ $orderMainData->postageqty }} @endisset</span>:</p>
                                        <p id="total_postage_cost" class="">$ <span class="price">{{number_format(($tPostage), 2)}}</span></p>
                                        <input type="hidden" name="postage_cost" id="postage_cost" value=""/>
                                        <input type="hidden" name="total_postage_price" id="total_postage_price" value="{{ $tPostage }}">
                                        <input type="hidden" id="postageqty" name="postageqty" value="{{ $orderMainData->postageqty }}">
                                    </div>
                                    <hr class="my-50">
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Grand Total:</p>
                                        <p id="grand_total_price" class="invoice-total-amount">$ {{number_format(($orderMainData->total_cost), 2)}}</p>
                                        <input type="hidden" name="grand_total_price" id="grand_total_price_input" value="{{ $orderMainData->total_cost }}"/>
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
            <input type="hidden" id="orderId" value="{{ $orderId }}">
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
            $('.toast .me-auto').html('Error');
            $('.toast .toast-header').addClass('bg-danger');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('error')}}");
            $('#toast-btn').click();
        @endif
        $('.sku-table').DataTable({
            // searhing: false,
            paging:false,
            stateSave: true,
            ordering: false,
        });
        $('.subt_qty').prop('disabled', true);
        $('.add_qty').prop('disabled', true);
        $('.qty').prop('disabled', true);
        $('.loader').removeClass('d-none');
        $('#skutable').css('opacity', '0.3');
        var skusData = [];
        var productsData = [];
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
        var customer_id = $('#customer').val();
        var brandId = $('#brand').val();
        var orderId = $('#orderId').val();
        $.get('/customer/'+customer_id+'/'+orderId+'/'+brandId+'/edit-order-service-charges', function(result) {
            if(result.status == "success") {
                if (result.mailer_cost > 0) {
                    $('#mailer_cost').val(result.data.mailer);
                } else {
                    $('#mailer_cost').val(0.00);
                }
                // $('#postage_cost').val(result.data.postage_cost);
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
                $('#pc_default').val(result.data.postage_cost);
                setChargesRate('pick_price', result.data.pick);
                setChargesRate('pack_price', result.data.pack);
                setChargesRate('pick_pack_flat_price', result.data.pick_pack_flat);
                setChargesRate('labels_price', result.data.labels);   
            } else {
                $('#charges-error').html(result.message);
            }
        });
        var brandUrl = '{{ route("get_edit_brand_mailer") }}';
        $.ajax({
            url: brandUrl,
            data: {
                _token: '{{ csrf_token() }}',
                brand_id: brandId,
                customer_id: customer_id,
                order_id: orderId,
            },
            success:function(data) {
                if(data != null || data != 0) {
                    $('#brand_mailer').val(data);
                } else if(data == null || data == 0) {
                    $('#brand_mailer').val('0.00');
                }
            }
        });
        $.ajax({
            url: '{{route("edit_order_details")}}',
            type: 'GET',
            data: {
                _token: '{{csrf_token()}}',
                order_id: orderId,
            },
            success:function(response) {
                response.forEach(element => {
                    skusData[element.sku_id] = element;
                });
                var count = 0;
                $('.sku').each(function() {
                    var _this = $(this);
                    var sku_id = $(this).val();
                    var url = '{{route("edit_order_sku_product_details", ":id")}}';
                    url = url.replace(':id', sku_id);
                    count = count;
                    var customer_id = $('#customer').val();
                    var brandId = $('#brand').val();
                    var orderId = $('#orderId').val();
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: {
                            _token: '{{csrf_token()}}',
                            'customer_id': customer_id,
                            'order_id': orderId,
                            'brand_id': brandId,
                            'sku_id': sku_id
                        },
                        success:function(response) {
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
                                            data-qty="0" 
                                            data-label_qty`+response[index].prod_id+`="`+response[index].label_qty+`" 
                                            data-temp_label_qty`+response[index].prod_id+`="`+response[index].label_qty+`" 
                                            data-selling_price="`+sellingPrice+`" 
                                            data-skuid="`+sku_id+`" class="ProductWithLabel labelClass`+response[index].prod_id+`" 
                                            data-prodId="`+response[index].prod_id+`" 
                                            data-status`+response[index].prod_id+`="`+getstatus+`" value="`+response[index].label_qty+`">
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
                        }
                    });
                });
                var html = '';
                makeProductArray(skusData);
                $('.subt_qty').prop('disabled', false);
                $('.add_qty').prop('disabled', false);
                $('.qty').prop('disabled', false);
                $('.loader').addClass('d-none');
                $('#skutable').css('opacity', '1');
            }
        });
        $(document).on('click', '.add_qty', function() {
            var _this = $(this);
            increaseQty(_this, 'add_qty');
        });
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
                 _this.closest('tr div.input-group').find('qty').val();
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
                    if (res < 5 && res > 0) {
                        totalRes = Number(totalRes) + $('#pc_lt5').val();
                    } else if(res >= 5 && res < 9) {
                        totalRes = Number(totalRes)+ $('#pc_lt9').val();
                    } else if(res >= 9 && res < 13) {
                        totalRes = Number(totalRes) + $('#pc_lt13').val();
                    } else if(res >= 13 && res < 16) {
                        totalRes = Number(totalRes) + $('#pc_gte13').val();
                    } else if(res >= 16 && res < 16.16) { // LBS rates
                        totalRes = Number(totalRes) + $('#lbs1_1_99').val();
                    } else if(res >= 16.16 && res < 32) {
                        totalRes = Number(totalRes) + $('#lbs1_1_2').val();
                    } else if(res >= 32.16 && res < 48) {
                        totalRes = Number(totalRes) + $('#lbs2_1_3').val();
                    } else if(res >= 48.16) {
                        totalRes = Number(totalRes) + $('#lbs3_1_4').val();
                    }
                }
            }
            _this.closest('tr').find('.total_weight_price').val(totalRes * Number(qty));

            var total_label_cost = 0;
            var total_pick_cost = 0;
            var total_pack_cost = 0;
            var total_pick_pack_flat_cost = 0;
            var prods_charges = _this.closest('tr').find('input.total_sku_service_charges').val();
            total = (qty * prods_charges);
            var _selling_price = _this.closest('tr').find('.selling_price .price').html();
            total_sku_cost = qty * _this.closest('tr').find('.selling_price .price').html();
            _this.closest('tr').find('.sku-total-price .price').html(parseFloat(total_sku_cost).toFixed(2));
            _this.closest('tr').find('.sku_selling_cost').val(parseFloat(total_sku_cost).toFixed(2));
            var total_qty = 0;
            $('.qty').each(function() {
                var labels = $(this).closest('tr').find('input.ProductWithLabel');
                total_qty = Number(total_qty) + Number($(this).val());

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
                            pickPackFlatX = Number(pickPackFlatX) + Number(Number(prod_pick_pack_flat_count) * Number(sku_qty));
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
                            pickPackFlatX = Number(pickPackFlatX) + Number(Number(prod_pick_pack_flat_count) * Number(sku_qty));
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
            $(this).closest('tr').remove();
            increaseQty(_this, 'custom');
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
            // if ($(this).val() == '' || $(this).val() == null) {
            //     $(this).val(0);
            // }
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
                var products = element.sku_order_detail;
                products.forEach(product => {
                    if(!productsData.hasOwnProperty(product.product_id)) {
                        var temp_data = [];
                        temp_data['labels'] = product.labelqty[0].label_qty;
                        temp_data['utilized_labels'] = 0;
                        temp_data['remaining_labels'] = 0;
                        productsData[product.product_id] = temp_data;
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
    <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/pages/app-invoice.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/form-select2.js') }}"></script>
@stop