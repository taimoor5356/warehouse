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
                                    @if(isset($error))
                                    @error('customer')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                    @endif
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
                                    @if(isset($error))
                                    @error('brand')
                                    <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                    @endif
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
                                            <input type="hidden" name="sku[]" data-sku-pick-pack-flat-status="@isset($val->sku_order){{$skuOrder->pick_pack_flat_status}}@endisset" data-sku-pick-pack-flat-cost="@isset($val){{ $val->sku_pick_pack_flat_cost }}@endisset" value="@isset($val){{$val->sku_id}}@endisset" class="sku" id="sku-list">
                                            <div class="sku-product-details" data-sku-id="@isset($val){{$val->sku_id}}@endisset">
                                                @foreach ($val->sku_order_detail as $skuproduct)
                                                    @if ($skuproduct->sku_id == $val->sku_id)
                                                    <div class="sku-products" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-id" value="{{$skuproduct->product_id}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-name" value="{{$skuproduct->product->name}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-selling-cost" value="{{$skuproduct->selling_cost}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-seller-cost-status" value="{{$skuproduct->seller_cost_status}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-label-status" value="{{$skuproduct->is_active}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-label-qty" value="{{\App\Models\CustomerHasProduct::where('customer_id', $skuproduct->customer_id)->where('brand_id', $skuproduct->brand_id)->where('product_id', $skuproduct->product_id)->first()->label_qty}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-label-cost" value="{{$skuproduct->label}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-pick-cost" value="{{$skuproduct->pick}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-pack-cost" value="{{$skuproduct->pack}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-pick-pack-flat-status" value="{{$val->sku_pick_pack_flat_status}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-pick-pack-flat-cost" value="{{$val->sku_pick_pack_flat_cost}}">
                                                        <input type="hidden" data-sku-id="@isset($val){{$val->sku_id}}@endisset" data-sku-product-id="{{$skuproduct->product_id}}" class="sku-product-counts" value="0">
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-1">    
                                            <div class="d-none hiddenQty">0</div>
                                            <center>
                                                <div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-primary subt_qty" data-sku-id="{{$val->sku_id}}">-</button>
                                                        <input type="number" class="form-control text-center qty runFuntionQty touchspin2" max="" value="@isset($val->sku_order){{$val->qty}}@endisset" name="qty[]" data-sku-id="{{$val->sku_id}}"/>
                                                    <button type="button" class="btn btn-primary add_qty" data-sku-id="{{$val->sku_id}}">+</button> 
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
                                                @endphp
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
                                            @endforeach
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Label x <span id="labelX"> @if($orderMainData != null) {{ $orderMainData->labelqty }} @endisset</span>:</p>
                                        <p id="total_label_cost" class="">$ <span class="price">{{number_format(($tLabels), 2)}}</span></p>
                                        <input type="hidden" id="grandTotalLabelPrice" name="grandTotalLabelPrice" value="{{ $tLabels }}">
                                        <input type="hidden" id="labelqty" name="labelqty" value="{{ $orderMainData->labelqty }}">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Pick x <span id="pickX">@if($orderMainData != null) {{ $orderMainData->pickqty }} @endisset</span>:</p>
                                        <p id="total_pick_cost" class="">$ <span class="price">{{number_format(($tpick), 2)}}</span></p>
                                        <input type="hidden" id="grandTotalPickPrice" name="grandTotalPickPrice" value="{{ $tpick }}">
                                        <input type="hidden" id="pickqty" name="pickqty" value="{{ $orderMainData->pickqty }}">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Pack x <span id="packX">@if($orderMainData != null) {{ $orderMainData->packqty }} @endisset</span>:</p>
                                        <p id="total_pack_cost" class="">$ <span class="price">{{number_format(($tPack), 2)}}</span></p>
                                        <input type="hidden" id="grandTotalPackPrice" name="grandTotalPackPrice" value="{{ $tPack }}">
                                        <input type="hidden" id="packqty" name="packqty" value="{{ $orderMainData->packqty }}">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Pick / Pack Flat x <span id="pickPackFlatX">@if($orderMainData != null) {{ $orderMainData->pick_pack_flat_qty }} @endisset</span>:</p>
                                        <p id="total_pick_pack_flat_cost" class="">$ <span class="price">{{number_format(($orderMainData->pick_pack_flat_price), 2)}}</span></p>
                                        <input type="hidden" id="grandTotalPickPackFlatPrice" name="grandTotalPickPackFlatPrice" value="{{ $orderMainData->pick_pack_flat_price }}">
                                        <input type="hidden" id="pickpackflatqty" name="pickpackflatqty" value="{{ $orderMainData->pick_pack_flat_qty }}">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Mailer x <span id="mailerX">@if($orderMainData != null) {{ $orderMainData->mailerqty }} @endisset</span>:</p>
                                        <p id="total_mailer_cost" class="">$ <span class="price">{{number_format(($tMailer), 2)}}</span></p>
                                        <input type="hidden" name="mailer_cost" id="mailer_cost" />
                                        <input type="hidden" name="brand_mailer" id="brand_mailer" />
                                        <input type="hidden" name="mailer_costNew" id="mailer_costNew" value="{{ $tMailer }}">
                                        <input type="hidden" id="mailerqty" name="mailerqty" value="{{ $orderMainData->mailerqty }}">
                                    </div>
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">Postage x <span id="postageX">@if($orderMainData != null) {{ $orderMainData->postageqty }} @endisset</span>:</p>
                                        <p id="total_postage_cost" class="">$ <span class="price">{{number_format(($tPostage), 2)}}</span></p>
                                        <input type="hidden" name="postage_cost" id="postage_cost" value="{{number_format(($tPostage), 2)}}"/>
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
        var orderId = $('#orderId').val();
        var brandId = $('#brand').val();
        $.get('/customer/'+customer_id+'/'+orderId+'/'+brandId+'/edit-order-service-charges', function(result) {
            if(result.status == "success") {
                if (result.data.mailer > 0) {
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
        var customerId = $('#customer').val();
        var brandId = $('#brand').val();
        let url = "{{ route('get-brand-sku', ':id') }}";
        url = url.replace(":id", brandId);
        var list = '';
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                _token: '{{csrf_token()}}',
                'customer_id': customerId,
            },
            success:function(response) {
                var mailerCharges = $('#mailer_cost').val();
                if (response.mailer_cost > 0) {
                    $('#mailer_cost').val(response.mailer_cost);
                    $('#brand_mailer').val(response.mailer_cost);
                    $('#mailer_costNew').val(response.mailer_cost);
                }
            }
        });
        $(document).on('click', '.add_qty', function() {
            let _this = $(this);
            let skuId = _this.data('sku-id');
            let _qty = _this.closest('tr div.input-group').find('input.qty').val();
            _this.closest('tr div.input-group').find('input.qty').val(Number(_qty) + Number(1));
            let qty_ = _this.closest('tr div.input-group').find('input.qty').val();
            changeQty(_this, skuId, qty_);
            $('.saveOrder').prop('disabled', false);
        });
        $(document).on("keyup", ".qty", function () {
            let _this = $(this);
            let skuId = _this.data('sku-id');
            let _qty = _this.val();
            changeQty(_this, skuId, _qty);
        });
        $(document).on('click', '.subt_qty', function() {
            let _this = $(this);
            let _qty = _this.closest('tr div.input-group').find('input.qty').val();
            if (_qty <= 0) {
                _this.closest('tr div.input-group').find('input.qty').val(0);
            } else {
                _this.closest('tr div.input-group').find('input.qty').val(Number(_qty) - Number(1));
                let skuId = _this.data('sku-id');
                let qty_ = _this.closest('tr div.input-group').find('input.qty').val();
                changeQty(_this, skuId, qty_);
            }
        });
        function changeQty(_this, skuId, newQty)
        {
            _this.closest('tr').find('input.qty').attr('data-sku-qty', Number(newQty));
            _this.closest('tr').find('input.qty').attr('value', newQty);
            var skuPrice = _this.closest('tr').find('.selling_price .price').html();
            _this.closest('tr').find('.sku-total-price .price').html(parseFloat(skuPrice * newQty).toFixed(2));
            _this.closest('tr').find('.sku_selling_cost').val(parseFloat(skuPrice * newQty).toFixed(2));
            var customerMailerCharges = $('#mailer_cost').val();
            var labelQty = 0;
            var pickQty = 0;
            var packQty = 0;
            var pickPackFlatQty = 0;
            var mailerQty = 0;
            var postageQty = 0;
            var totalLabelCharges = 0;
            var totalPickCharges = 0;
            var totalPackCharges = 0;
            var totalPickPackFlatCharges = 0;
            var totalMailerCharges = 0;
            var totalPostageCharges = 0;
            var skuWeight = 0;
            var postageCost = 0;
            var summaryData = '';
            var grandTotalPrice = 0;
            var arr = [];
            $('.summaryData').empty();
            // Iterate each SKU
            $('.sku').each(function () {
                var _thisSku = $(this);
                var newQtty = _thisSku.closest('tr').find('td input.qty').val();
                var skuPickPackFlatStatus = _thisSku.attr('data-sku-pick-pack-flat-status');
                var skuPickPackFlatCost = _thisSku.attr('data-sku-pick-pack-flat-cost');
                if (newQtty > 0) {
                    var html = 0;
                    var skuProducts = _thisSku.closest('tr').find('td div.sku-product-details div.sku-products');
                    var totalProductSellingCost = 0;
                    // Sku products iteration
                    skuProducts.each(function() {
                        var _thisSkuProduct = $(this);
                        var productSellingCost = _thisSkuProduct.find('input.sku-product-selling-cost').val();
                        var productSellerCostStatus = _thisSkuProduct.find('input.sku-product-seller-cost-status').val();
                        var productLabelStatus = _thisSkuProduct.find('input.sku-product-label-status').val();
                        var productId = _thisSkuProduct.find('input.sku-product-id').val();
                        var productName = _thisSkuProduct.find('input.sku-product-name').val();
                        var productLabelCharges = _thisSkuProduct.find('input.sku-product-label-cost').val();
                        var productPickCharges = _thisSkuProduct.find('input.sku-product-pick-cost').val();
                        var productPackCharges = _thisSkuProduct.find('input.sku-product-pack-cost').val();
                        if (productSellerCostStatus == 1) {
                            if (productLabelStatus == 0) {
                                if (productLabelCharges > 0) {
                                    labelQty += Number(newQtty);
                                    totalLabelCharges += Number(productLabelCharges * newQtty);
                                }
                            }
                        }
                        if (productPickCharges > 0) {
                            pickQty += Number(newQtty);
                            totalPickCharges += Number(productPickCharges * newQtty);
                        }
                        if (productPackCharges > 0) {
                            packQty += Number(newQtty);
                            totalPackCharges += Number(productPackCharges * newQtty);
                        }
                        // Show products summary
                        if (productSellerCostStatus == 1) {
                            totalProductSellingCost = parseFloat(productSellingCost * newQtty).toFixed(2);
                            var newProductQty = 0;
                            var newProductCost = 0;
                            if (arr.hasOwnProperty(productId)) {
                                arr[productId]['qty'] += Number(newQtty);
                                arr[productId]['amount'] += Number(totalProductSellingCost);
                                arr[productId]['name'] = productName;
                                newProductQty = arr[productId]['qty'];
                                newProductCost = arr[productId]['amount'];
                            } else {
                                arr[productId] = [];
                                arr[productId]['qty'] = Number(newQtty);
                                arr[productId]['amount'] = Number(totalProductSellingCost);
                                arr[productId]['name'] = productName;
                                newProductQty = arr[productId]['qty'];
                                newProductCost = arr[productId]['amount'];
                            }
                        } else {
                            totalProductSellingCost = 0;
                            var newProductQty = 0;
                            var newProductCost = 0;
                            if (arr.hasOwnProperty(productId)) {
                                arr[productId]['qty'] += Number(newQtty);
                                arr[productId]['amount'] += Number(totalProductSellingCost);
                                arr[productId]['name'] = productName;
                                newProductQty = arr[productId]['qty'];
                                newProductCost = arr[productId]['amount'];
                            } else {
                                arr[productId] = [];
                                arr[productId]['qty'] = Number(newQtty);
                                arr[productId]['amount'] = Number(totalProductSellingCost);
                                arr[productId]['name'] = productName;
                                newProductQty = arr[productId]['qty'];
                                newProductCost = arr[productId]['amount'];
                            }
                        }
                    });
                    mailerQty += Number(newQtty);
                    if (skuPickPackFlatStatus > 0) {
                        pickPackFlatQty += Number(newQtty);
                        totalPickPackFlatCharges = Number(skuPickPackFlatCost * pickPackFlatQty);
                    }
                    totalMailerCharges += customerMailerCharges * Number(newQtty);
                    postageQty += Number(newQtty);
                    // Weight calculation for postage
                    skuWeight = _thisSku.closest('tr').find('.sku_weight .total').html();
                    totalPostageCharges += calculatePostageCost(skuWeight, newQtty);
                }
                $('#labelX').html(labelQty);
                $('#total_label_cost .price').html(parseFloat(totalLabelCharges).toFixed(2));
                $('#pickX').html(pickQty);
                $('#total_pick_cost .price').html(parseFloat(totalPickCharges).toFixed(2));
                $('#packX').html(packQty);
                $('#total_pack_cost .price').html(parseFloat(totalPackCharges).toFixed(2));
                $('#pickPackFlatX').html(pickPackFlatQty);
                $('#total_pick_pack_flat_cost .price').html(parseFloat(totalPickPackFlatCharges).toFixed(2));
                $('#mailerX').html(mailerQty);
                $('#total_mailer_cost .price').html(parseFloat(totalMailerCharges).toFixed(2));
                $('#postageX').html(postageQty);
                $('#total_postage_cost .price').html(parseFloat(totalPostageCharges).toFixed(2));
                $('#postage_cost').val(Number(totalPostageCharges));
                $("#total_postage_price").val(parseFloat(totalPostageCharges).toFixed(2));
                grandTotalPrice = (Number(totalLabelCharges) + Number(totalPickCharges) + Number(totalPackCharges) + Number(totalPickPackFlatCharges) + Number(totalMailerCharges) + Number(totalPostageCharges));
                
            });
            $('#labelqty').val($('#labelX').html());
            $('#pickqty').val($('#pickX').html());
            $('#packqty').val($('#packX').html());
            $('#pickpackflatqty').val($('#pickPackFlatX').html());
            $('#mailerqty').val($('#mailerX').html());
            $('#postageqty').val($('#postageX').html());
            arr.forEach((value, key) => {
                summaryData += `<div class="invoice-total-item invoiceTotalItem`+key+`">
                    <p class="summary`+key+` invoice-total-title">`+value['name']+` x <span class="summaryprodQty`+key+`">`+value['qty']+`</span>: </p>
                    <p id="" class="summary-prod-qty`+key+`">$ <span class="calcrestotal totalres`+key+`">`+parseFloat(value['amount']).toFixed(2)+`</span></p>
                    <input type="hidden" id="" name="summary-grandTotalLabelPrice">
                </div>
                `;
            });
            summaryData += `<hr>`;
            $('.summaryData').append(summaryData);
            var grandTotalProductSellingCost = 0;
            $('.invoice-total-item .calcrestotal').each(function () {
                grandTotalProductSellingCost += Number($(this).html());
            });
            $('#grandTotalLabelPrice').val(parseFloat(totalLabelCharges).toFixed(2));
            $('#grandTotalPickPrice').val(parseFloat(totalPickCharges).toFixed(2));
            $('#grandTotalPackPrice').val(parseFloat(totalPackCharges).toFixed(2));
            $('#grandTotalPickPackFlatPrice').val(parseFloat(totalPickPackFlatCharges).toFixed(2));
            $('#grand_total_price').html('$ '+ parseFloat(Number(grandTotalPrice) + Number(grandTotalProductSellingCost)).toFixed(2));
            $('#grand_total_price_input').val(parseFloat(Number(grandTotalPrice) + Number(grandTotalProductSellingCost)).toFixed(2));
        }
        function calculatePostageCost(skuWeight, newQtty)
        {
            var postageCost = 0;
            if(skuWeight > 0) {
                if ($('#pc_default_cb').is(':checked')) {
                    postageCost = postageCost + Number($('#newPostageCost').val());
                } else {
                    if (skuWeight < 5) {
                        postageCost = Number(postageCost) + Number($('#pc_lt5').val());
                    } else if(skuWeight >= 5 && skuWeight < 9) {
                        postageCost = Number(postageCost)+ Number($('#pc_lt9').val());
                    } else if(skuWeight >= 9 && skuWeight < 13) {
                        postageCost = Number(postageCost) + Number($('#pc_lt13').val());
                    } else if(skuWeight >= 13 && skuWeight < 16) {
                        postageCost = Number(postageCost) + Number($('#pc_gte13').val());
                    } else if(skuWeight >= 16 && skuWeight < 16.16) { // LBS rates
                        postageCost = Number(postageCost) + Number($('#lbs1_1_99').val());
                    } else if(skuWeight >= 16.16 && skuWeight < 32) {
                        postageCost = Number(postageCost) + Number($('#lbs1_1_2').val());
                    } else if(skuWeight >= 32.16 && skuWeight < 48) {
                        postageCost = Number(postageCost) + Number($('#lbs2_1_3').val());
                    } else if(skuWeight >= 48.16) {
                        postageCost = Number(postageCost) + Number($('#lbs3_1_4').val());
                    }
                }
            }
            var totalPostageCost = postageCost * newQtty;
            return totalPostageCost;
        }
        
        function setChargesRate(selector, value){
            $('.' + selector).each(function(){
                $(this).val(value);
            });
        }
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