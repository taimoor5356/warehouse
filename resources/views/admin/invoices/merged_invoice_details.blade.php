@extends('admin.layout.app')
@section('title', 'Merged Invoice Details')
@section('datatablecss')

    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
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

        .details {
            margin: 0px auto;
        }

        .billed_to p {
            text-align: left;
        }

        .payment p,
        a,
        strong,
        h1 {
            text-align: right;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
            margin: 0px;
        }

        section {
            font-family: Arial, Helvetica, sans-serif
        }

        th {
            padding: 1px 0px 15px 0px !important;
        }

        tr,
        td {
            padding: 5px 0px 10px 0px !important;
        }

        td p {
            margin-bottom: 0px !important;
        }

        .header_description p {
            margin-bottom: 0px !important;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            width: 30px;
            height: 30px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
            float: right;
        }

        @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
        }
    </style>
    <div class="content-header row">
        <div class="content-header-left col-md-12 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="content-header-title float-start mb-0">Merged Invoice Details</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Invoices
                                    </li>
                                    <li class="breadcrumb-item">Merged Invoice Details
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-6 addBtnClass">
                            @php 
                                $invDetail = $detail->first();
                                $email = '';
                                if (isset($invDetail)) {
                                    $mI = $invDetail->merged_invoice;
                                    if (isset($mI)) {
                                        $customer = $mI->customer;
                                        if (isset($customer)) {
                                            $email = $customer->email;
                                        }
                                    }
                                }
                                $setting = \App\Models\Setting::first();
                            @endphp
                            <input type="text" title="Use COMMA to separate multiple emails" id="user-email" class="email-address" style="margin-top: 7px; visibility: hidden; width: 300px; border-radius: 5px !important; border: 1px solid lightgray; padding: 5px" value="{{ $email }}" placeholder="Use COMMA to separate multiple emails">
                            <span style="border-left: 2px solid; height: 35px; margin: 0px 5px 0px 5px; visibility: hidden;" class="email-address"></span>
                            <input type="checkbox" class="form-check-input" id="confirm-send" style="margin-top: 15px">
                            <label class="form-label text-dark" for="" style="font-weight: bold">Send Mail</label>
                            <span style="border-left: 2px solid; height: 35px; margin: 0px 5px 0px 5px"></span>
                            <button href="#" style="margin-left:auto; border: 1px solid; padding: 10px"
                                class="btn btn-primary waves-effect waves-float waves-light" id="printInvoice">
                                <div class="spinner-border d-none" style="height: 15px; width: 15px"></div>
                                <i data-feather="printer"></i>
                                <span class="print-text">Print</span>
                            </button>
                            <input type="hidden" id="detailedData" value="{{ $detail }}">
                            <input type="hidden" id="id" value="{{ $id }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="invoice-preview-wrapper" id="printable">
        <div class="bg-white" style="padding: 20px 20px 20px 20px;">
            <div class="header" style="width: 100%; display:flex">
                <div class="logo" style="width: 40%;">
                    <img src="{{ asset('images/Warehousesystem.jpg') }}" height="160" width="210" alt="">
                </div>
                <div class="header_description" style="width: 30%; color: black; margin-left: auto; margin-right: 0;">
                    <p style="text-align: right">@isset($setting){{ $setting->company_name }}@endisset</p>
                    <p style="text-align: right">@isset($setting){{ $setting->company_number }}@endisset</p>
                </div>
                <div class="header_description" style="width: 20%; margin-left: auto; margin-right: 0; color: black">
                    <p style="text-align: right; width: 80%; float: right">@isset($setting){{ $setting->company_address }}@endisset</p>
                </div>
            </div>
            <br>
            <br>
            <div class="header2" style="width: 100%; display: flex">
                <div class="details billed_to" style="width: 35%; color: black;">
                    @php
                        $invDetail = $detail->first();
                        $customerName = '';
                        $customerAddress = '';
                        if (isset($invDetail)) {
                            $mergedInv = $invDetail->merged_invoice;
                            if (isset($mergedInv)) {
                                $customer = $mergedInv->customer;
                                if (isset($customer)) {
                                    $customerName = $customer->customer_name;
                                    $customerAddress = $customer->address;
                                    // echo $customer->customer_name;
                                } else {
                                    // echo 'Customer Name';
                                }
                            } else {
                                // echo 'Customer Name';
                            }
                        } else {
                            // echo 'Customer Name';
                        }
                    @endphp

                    <p><a href="#"><small>Billed To</small></a><br>{{ $customerName }}<br>{{ $customerAddress }}
                    </p>
                </div>
                <div class="details billed_to" style="width: 20%; color: black;">
                    <p><a href="#"><small>Date Issued</small></a><br>
                        @isset($mergedInv)
                            {{ $mergedInv->created_at->format('m/d/Y') }}
                        @endisset
                    </p>
                    <p><a href="#"><small>Due Date</small></a><br>
                        @isset($mergedInv)
                            {{ $mergedInv->created_at->addDays(5)->format('m/d/Y') }}
                        @endisset
                    </p>
                </div>
                <div class="details billed_to" style="width: 19%; color: black;">
                    <p><a href="#"><small>Invoice Number</small></a><br>
                        @isset($invDetail)
                            {{ $customerName }}-invoice-no-{{ $invDetail->inv_no }}
                        @endisset
                    </p>
                </div>
                <div class="details payment" style="width: 24%; color: black;">
                    <p><a href="#"><small>Amount Due (USD)</small></a><b>
                            <h1 id="amount-due">
                                <div class="loader"></div>
                            </h1>
                        </b></p>
                </div>
            </div>
            <hr class="text-primary" style="">
            <table class="table">
                <thead>
                    <th style="background: white; width: 50%; text-align: left;"><a href="#"> Description</a></th>
                    <th style="background: white; width: 15%; text-align: right;"><a href="#"> Rate</a></th>
                    <th style="background: white; width: 10%; text-align: right;"><a href="#"> Qty</a></th>
                    <th style="background: white; width: 25%; text-align: right;"><a href="#"> Line Total</a></th>
                </thead>
                <tbody>
                    @php $productsSum = 0; @endphp
                    @foreach ($detail as $d)
                        <tr style="color: black">
                            <td>
                                @isset($d->product)
                                    {{ $d->product->name }}
                                @endisset
                                <p><small>
                                        @isset($d->product)
                                            @isset($d->product->product_unit)
                                                {{ $d->product->product_unit->name }}
                                            @endisset
                                        @endisset
                                    </small></p>
                            </td>
                            <td style="text-align: right">
                                $@isset($d){{number_format($d->selling_cost, 2)}}@endisset
                            </td>
                            <td style="text-align: right">
                                @isset($d)
                                    {{ number_format(\App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('product_id', $d->product_id)->where('product_price', '>', 0)->sum('product_qty')) }}
                                @endisset
                            </td>
                            <td style="text-align: right">
                                @isset($d)
                                    ${{ number_format(\App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('product_id', $d->product_id)->where('product_qty', '>', '0')->where('product_price', '>', 0)->sum('product_price'),2) }}
                                @endisset
                                @php
                                    $productsSum += \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('product_id', $d->product_id)->where('product_qty', '>', '0')->where('product_price', '>', 0)->sum('product_price');
                                @endphp
                            </td>
                        </tr>
                    @endforeach
                    @php
                        $labelQty = 0;
                        $pickQty = 0;
                        $packQty = 0;
                        $mailerQty = 0;
                        $pickPackFlatQty = 0;
                        $postageQty = 0;
                        
                        $labelCost = 0;
                        $pickCost = 0;
                        $packCost = 0;
                        $mailerCost = 0;
                        $pickPackFlatCost = 0;
                        $postageCost = 0;
                        
                        $totalCost = 0;
                        $getDetail = \App\Models\InvoicesMerged::with('merged_invoice')
                            ->where('merged_invoice_id', $id)
                            ->first();
                        if (isset($getDetail)) {
                            $mergeInvoice = $getDetail->merged_invoice;
                            if (isset($mergeInvoice)) {
                                $labelQty = $mergeInvoice->label_qty;
                                if ($getDetail->selling_cost <= 0) {
                                    $labelQty = 0;
                                }
                                $pickQty = $mergeInvoice->pick_qty;
                                $packQty = $mergeInvoice->pack_qty;
                                $mailerQty = $mergeInvoice->mailer_qty;
                                $pickPackFlatQty = $mergeInvoice->flat_pick_pack_qty;
                                $postageQty = $mergeInvoice->postage_qty;
                                $totalCost = $mergeInvoice->total_cost;
                        
                                $labelCost = $mergeInvoice->label_charges;
                                if ($getDetail->selling_cost <= 0) {
                                    $labelCost = 0;
                                }
                                $pickCost = $mergeInvoice->pick_charges;
                                $packCost = $mergeInvoice->pack_charges;
                                $mailerCost = $mergeInvoice->mailer_charges;
                                $pickPackFlatCost = $mergeInvoice->pick_pack_flat_charges;
                                $postageCost = $mergeInvoice->postage_charges;
                            }
                        }
                    @endphp
                    @php 
                        $order = \App\AdminModels\Orders::where('id', $invDetail->order_id)->first();
                        $serviceCharges = json_decode($order->customer_service_charges);
                        $invoicesMergeds = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->groupBy('order_id')->get();
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $diffLabelCosts = array();
                        $diffLabelCostsKeys = array('rate', 'qty');
                        $checkLabelRateArray = array();
                        foreach ($invoicesMergeds as $imkey => $invoice_merged) {
                            $labelUnitCost = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('order_id', $invoice_merged->order_id)->where('label_unit_cost', '>', 0)->get();
                            $unitCostOfLabel = 0;
                            $productQty = 0;
                            $totalLabelQty = 0;
                            foreach ($labelUnitCost as $label_unit_cost) {
                                if (isset($label_unit_cost)) {
                                    $unitCostOfLabel = $label_unit_cost->label_unit_cost;
                                    $productQty = $label_unit_cost->product_qty;
                                } else {
                                    $unitCostOfLabel = 0;
                                    $productQty = 1;
                                }
                                $unitLabelCost = $unitCostOfLabel / $productQty;
                                $totalLabelQty = $productQty;
                                array_push($diffLabelCosts, array_combine($diffLabelCostsKeys, [number_format($unitLabelCost, 2), $totalLabelQty]));
                            }
                        }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $labelRate = array();
                        $labelRateQty = array();
                        $labelRate_Qty = 0;
                        $newLabelRate_Qty = 0;
                        foreach ($diffLabelCosts as $diffLabelKey => $diffLabelValue) {
                            $labelRate_Qty = $diffLabelValue['qty'];
                            $newLabelRate_Qty += $labelRate_Qty;
                            if (in_array(number_format($diffLabelValue['rate'], 2), $labelRate)) {
                                unset($labelRate[$diffLabelKey]);
                                $labelRateQty = [];
                                array_push($labelRateQty, $newLabelRate_Qty);
                            } else {
                                array_push($labelRate, number_format($diffLabelValue['rate'], 2));
                                array_push($labelRateQty, $labelRate_Qty);
                            }
                        }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $diffPickCosts = array();
                        $diffPickCostsKeys = array('rate', 'qty');
                        foreach ($invoicesMergeds as $imkey => $invoice_merged) {
                            $pickUnitCost = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('order_id', $invoice_merged->order_id)->where('pick_unit_cost', '>', 0)->get();
                            $unitCostOfPick = 0;
                            $productQty = 0;
                            $totalPickQty = 0;
                            foreach ($pickUnitCost as $pick_unit_cost) {
                                if (isset($pick_unit_cost)) {
                                    $unitCostOfPick = $pick_unit_cost->pick_unit_cost;
                                    $productQty = $pick_unit_cost->product_qty;
                                } else {
                                    $unitCostOfPick = 0;
                                    $productQty = 1;
                                }
                                $unitPickCost = $unitCostOfPick / $productQty;
                                $totalPickQty = $productQty;
                                array_push($diffPickCosts, array_combine($diffPickCostsKeys, [number_format($unitPickCost, 2), $totalPickQty]));
                            }
                        }
                        $pickRate = array();
                        $pickRateQty = array();
                        $pickRate_Qty = 0;
                        $newPickRate_Qty = 0;
                        foreach ($diffPickCosts as $diffPickKey => $diffPickValue) {
                            $pickRate_Qty = $diffPickValue['qty'];
                            $newPickRate_Qty += $pickRate_Qty;
                            if (in_array(number_format($diffPickValue['rate'], 2), $pickRate)) {
                                unset($pickRate[$diffPickKey]);
                                $pickRateQty = [];
                                array_push($pickRateQty, $newPickRate_Qty);
                            } else {
                                array_push($pickRate, number_format($diffPickValue['rate'], 2));
                                array_push($pickRateQty, $pickRate_Qty);
                            }
                        }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $diffPackCosts = array();
                        $diffPackCostsKeys = array('rate', 'qty');
                        foreach ($invoicesMergeds as $imkey => $invoice_merged) {
                            $packUnitCost = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('order_id', $invoice_merged->order_id)->where('pack_unit_cost', '>', 0)->get();
                            $unitCostOfPack = 0;
                            $productQty = 0;
                            $totalPackQty = 0;
                            foreach ($packUnitCost as $pack_unit_cost) {
                                if (isset($pack_unit_cost)) {
                                    $unitCostOfPack = $pack_unit_cost->pack_unit_cost;
                                    $productQty = $pack_unit_cost->product_qty;
                                } else {
                                    $unitCostOfPack = 0;
                                    $productQty = 1;
                                }
                                $unitPackCost = $unitCostOfPack / $productQty;
                                $totalPackQty = $productQty;
                                array_push($diffPackCosts, array_combine($diffPackCostsKeys, [number_format($unitPackCost, 2), $totalPackQty]));
                            }
                        }
                        $packRate = array();
                        $packRateQty = array();
                        $packRate_Qty = 0;
                        $newPackRate_Qty = 0;
                        foreach ($diffPackCosts as $diffPackKey => $diffPackValue) {
                            $packRate_Qty = $diffPackValue['qty'];
                            $newPackRate_Qty += $packRate_Qty;
                            if (in_array(number_format($diffPackValue['rate'], 2), $packRate)) {
                                unset($packRate[$diffPackKey]);
                                $packRateQty = [];
                                array_push($packRateQty, $newPackRate_Qty);
                            } else {
                                array_push($packRate, number_format($diffPackValue['rate'], 2));
                                array_push($packRateQty, $packRate_Qty);
                            }
                        }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $diffPickPackFlatCosts = array();
                        $diffPickPackFlatCostsKeys = array('rate', 'qty');
                        foreach ($invoicesMergeds as $imkey => $invoice_merged) {
                            $pickPackFlatUnitCost = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('order_id', $invoice_merged->order_id)->where('pick_pack_flat_unit_cost', '>', 0)->groupBy('order_id')->get();
                            $unitCostOfPickPackFlat = 0;
                            $productQty = 0;
                            $totalPickPackFlatQty = 0;
                            foreach ($pickPackFlatUnitCost as $pick_pack_flat_unit_cost) {
                                if (isset($pick_pack_flat_unit_cost)) {
                                    $unitCostOfPickPackFlat = $pick_pack_flat_unit_cost->pick_pack_flat_unit_cost;
                                    $productQty = $pick_pack_flat_unit_cost->product_qty;
                                } else {
                                    $unitCostOfPickPackFlat = 0;
                                    $productQty = 1;
                                }
                                $unitPickPackFlatCost = $unitCostOfPickPackFlat / $productQty;
                                $totalPickPackFlatQty = $productQty;
                                array_push($diffPickPackFlatCosts, array_combine($diffPickPackFlatCostsKeys, [number_format($unitPickPackFlatCost, 2), $totalPickPackFlatQty]));
                            }
                        }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $pickPackFlatRate = array();
                        $pickPackFlatRateQty = array();
                        $pickPackFlatRate_Qty = 0;
                        $newPickPackFlatRate_Qty = 0;
                        foreach ($diffPickPackFlatCosts as $diffPickPackFlatKey => $diffPickPackFlatValue) {
                            $pickPackFlatRate_Qty = $diffPickPackFlatValue['qty'];
                            $newPickPackFlatRate_Qty += $pickPackFlatRate_Qty;
                            if (in_array(number_format($diffPickPackFlatValue['rate'], 2), $pickPackFlatRate)) {
                                unset($pickPackFlatRate[$diffPickPackFlatKey]);
                                $pickPackFlatRateQty = [];
                                array_push($pickPackFlatRateQty, $newPickPackFlatRate_Qty);
                            } else {
                                array_push($pickPackFlatRate, number_format($diffPickPackFlatValue['rate'], 2));
                                array_push($pickPackFlatRateQty, $pickPackFlatRate_Qty);
                            }
                        }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $diffMailerCosts = array();
                        $diffMailerCostsKeys = array('rate', 'qty');
                        $mailerInvoicesMergeds = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->groupBy('order_id')->get();
                        foreach ($mailerInvoicesMergeds as $imkey => $invoice_merged) {
                            $totalMailerQty = 0;
                            $mailerUnitCost = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('order_id', $invoice_merged->order_id)->where('mailer_unit_cost', '>', 0)->first();
                            if (isset($mailerUnitCost)) {
                                $unitCostOfMailer = $mailerUnitCost->mailer_unit_cost;
                                $productQty = $mailerUnitCost->product_qty;
                            } else {
                                $unitCostOfMailer = 0;
                                $productQty = 1;
                            }
                            $unitMailerCost = $unitCostOfMailer;
                            $totalMailerQty = $invoice_merged->mailer_unit_qty;
                            array_push($diffMailerCosts, array_combine($diffMailerCostsKeys, [$unitMailerCost, $totalMailerQty]));
                        }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $mailerRate = array();
                        $mailerRateQty = array();
                        $individualMailerRateQty = array();
                        $mailerRate_Qty = 0;
                        $newMailerRate_Qty = 0;
                        foreach ($diffMailerCosts as $diffMailerKey => $diffMailerValue) {
                            $mailerRate_Qty = $diffMailerValue['qty'];
                            $newMailerRate_Qty += $mailerRate_Qty;
                            if (in_array(number_format($diffMailerValue['rate'], 2), $mailerRate)) {
                                unset($mailerRate[$diffMailerKey]);
                                $mailerRateQty = [];
                                array_push($mailerRateQty, $newMailerRate_Qty);
                                array_push($individualMailerRateQty, $mailerRate_Qty);
                            } else {
                                array_push($mailerRate, number_format($diffMailerValue['rate'], 2));
                                array_push($mailerRateQty, $mailerRate_Qty);
                                array_push($individualMailerRateQty, $mailerRate_Qty);
                            }
                        }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    @endphp
                    <tr style="color: black">
                        <td style="text-align: left">
                            Labels
                        </td>
                        <td style="text-align: right">
                            @foreach ($labelRate as $labelCost)
                                @if($labelCost != 0)
                                    ${{ number_format($labelCost, 2) }}<br>
                                @endif
                            @endforeach
                        </td>
                        <td style="text-align: right">
                            @foreach ($labelRate as $labelCost)
                                @if($labelCost != 0)
                                    @php
                                        $labelInvoicesMergeds = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->groupBy('order_id')->get();
                                        $labelqtty = 0;
                                        foreach ($labelInvoicesMergeds as $imIndvPickkey => $indvPick) {
                                            $getlabelUnitCosts = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)
                                                                ->where('order_id', $indvPick->order_id)
                                                                ->where('label_unit_cost', '>', 0)
                                                                ->get();
                                            foreach ($getlabelUnitCosts as $getlabelUnitCost) {
                                                if (isset($getlabelUnitCost)) {
                                                    if (number_format($getlabelUnitCost->label_unit_cost/$getlabelUnitCost->product_qty, 2) == number_format($labelCost, 2)) {
                                                        $labelqtty += $getlabelUnitCost->product_qty;
                                                    }
                                                } else {
                                                    // $labelqtty += 1;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $labelqtty }}<br>
                                @endif
                            @endforeach
                            {{-- @foreach ($labelRateQty as $labelRateqty)
                                {{ $labelRateqty }}<br>
                            @endforeach --}}
                            {{-- {{ number_format($labelQty) }} --}}
                        </td>
                        <td style="background: white; text-align: right">
                            @php $totalOfLabel = 0; @endphp
                            @foreach ($diffLabelCosts as $labelCost)
                                {{-- ${{ number_format($labelCost['qty'] * $labelCost['rate'], 2) }}<br> --}}
                                @php $totalOfLabel += $labelCost['qty'] * $labelCost['rate']; @endphp
                            @endforeach
                            ${{ number_format($totalOfLabel, 2) }}
                        </td>
                    </tr>
                    <tr style="color: black">
                        <td style="text-align: left">
                            Pick
                        </td>
                        <td style="text-align: right">
                            @foreach ($pickRate as $pickCost)
                                @if($pickCost != 0)
                                    ${{ number_format($pickCost, 2) }}<br>
                                @endif
                            @endforeach
                        </td>
                        <td style="text-align: right">
                            @foreach ($pickRate as $pickCost)
                                @if($pickCost != 0)
                                    @php
                                        $pickInvoicesMergeds = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->groupBy('order_id')->get();
                                        $pickqtty = 0;
                                        foreach ($pickInvoicesMergeds as $imIndvPickkey => $indvPick) {
                                            $getpickUnitCosts = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)
                                                                ->where('order_id', $indvPick->order_id)
                                                                ->where('pick_unit_cost', '>', 0)
                                                                ->get();
                                            foreach ($getpickUnitCosts as $getpickUnitCost) {
                                                if (isset($getpickUnitCost)) {
                                                    if (number_format($getpickUnitCost->pick_unit_cost/$getpickUnitCost->product_qty, 2) == number_format($pickCost, 2)) {
                                                        $pickqtty += $getpickUnitCost->product_qty;
                                                    }
                                                } else {
                                                    // $pickqtty += 1;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $pickqtty }}<br>
                                @endif
                            @endforeach
                            {{-- @foreach ($pickRateQty as $pickCostqty)
                                {{ $pickCostqty }}<br>
                            @endforeach --}}
                        </td>
                        <td style="background: white; text-align: right">
                            @php $totalOfPick = 0; @endphp
                            @foreach ($diffPickCosts as $pickCost)
                                {{-- ${{ number_format($labelCost['qty'] * $labelCost['rate'], 2) }}<br> --}}
                                @php $totalOfPick += $pickCost['qty'] * $pickCost['rate']; @endphp
                            @endforeach
                            ${{ number_format($totalOfPick, 2) }}
                        </td>
                    </tr>
                    <tr style="color: black">
                        <td style="text-align: left">
                            Pack
                        </td>
                        <td style="text-align: right">
                            @foreach ($packRate as $packCost)
                                @if ($packCost != 0)
                                    ${{ number_format($packCost, 2) }}<br>
                                @endif
                            @endforeach
                        </td>
                        <td style="text-align: right">
                            @foreach ($packRate as $packCost)
                                @if($packCost != 0)
                                    @php
                                        $packInvoicesMergeds = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->groupBy('order_id')->get();
                                        $packqtty = 0;
                                        foreach ($packInvoicesMergeds as $imIndvPackkey => $indvPack) {
                                            $getpackUnitCosts = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)
                                                                ->where('order_id', $indvPack->order_id)
                                                                ->where('pack_unit_cost', '>', 0)
                                                                ->get();
                                            foreach ($getpackUnitCosts as $getpackUnitCost) {
                                                if (isset($getpackUnitCost)) {
                                                    if (number_format($getpackUnitCost->pack_unit_cost/$getpackUnitCost->product_qty, 2) == number_format($packCost, 2)) {
                                                        $packqtty += $getpackUnitCost->product_qty;
                                                    }
                                                } else {
                                                    // $packqtty += 1;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $packqtty }}<br>
                                @endif
                            @endforeach
                            
                            {{-- @foreach ($packRateQty as $packCostqty) --}}
                                {{-- {{ $packCostqty }}<br> --}}
                            {{-- @endforeach --}}
                        </td>
                        <td style="background: white; text-align: right">
                            @php $totalOfPack = 0; @endphp
                            @foreach ($diffPackCosts as $packCost)
                                {{-- ${{ number_format($labelCost['qty'] * $labelCost['rate'], 2) }}<br> --}}
                                @php $totalOfPack += $packCost['qty'] * $packCost['rate']; @endphp
                            @endforeach
                            ${{ number_format($totalOfPack, 2) }}
                        </td>
                    </tr>
                    <tr style="color: black">
                        <td style="text-align: left">
                            Pick Pack Flat
                        </td>
                        <td style="text-align: right">
                            @foreach ($pickPackFlatRate as $pickPackFlatCost)
                                @if ($pickPackFlatCost != 0)
                                    ${{ number_format($pickPackFlatCost, 2) }}<br>
                                @endif
                            @endforeach
                        </td>
                        <td style="text-align: right">
                            @foreach ($pickPackFlatRate as $pick_pack_flatCost)
                                @if($pick_pack_flatCost != 0)
                                    @php
                                        $pick_pack_flatInvoicesMergeds = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->groupBy('order_id')->get();
                                        $pick_pack_flatqtty = 0;
                                        foreach ($pick_pack_flatInvoicesMergeds as $imIndvPickkey => $indvPick) {
                                            $getpick_pack_flatUnitCosts = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)
                                                                ->where('order_id', $indvPick->order_id)
                                                                ->where('pick_pack_flat_unit_cost', '>', 0)
                                                                ->get();
                                            foreach ($getpick_pack_flatUnitCosts as $getpick_pack_flatUnitCost) {
                                                if (isset($getpick_pack_flatUnitCost)) {
                                                    if (number_format($getpick_pack_flatUnitCost->pick_pack_flat_unit_cost/$getpick_pack_flatUnitCost->product_qty, 2) == number_format($pick_pack_flatCost, 2)) {
                                                        $pick_pack_flatqtty += $getpick_pack_flatUnitCost->product_qty;
                                                    }
                                                } else {
                                                    // $pick_pack_flatqtty += 1;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $pick_pack_flatqtty }}<br>
                                @endif
                            @endforeach
                            {{-- @foreach ($pickPackFlatRateQty as $pickPackFlatCostqty)
                                {{ $pickPackFlatCostqty }}<br>
                            @endforeach --}}
                        </td>
                        <td style="background: white; text-align: right">
                            @php $totalOfPickPackFlat = 0; @endphp
                            @foreach ($diffPickPackFlatCosts as $pickPackFlatCost)
                                {{-- ${{ number_format($labelCost['qty'] * $labelCost['rate'], 2) }}<br> --}}
                                @php $totalOfPickPackFlat += $pickPackFlatCost['qty'] * $pickPackFlatCost['rate']; @endphp
                            @endforeach
                            ${{ number_format($totalOfPickPackFlat, 2) }}
                        </td>
                    </tr>
                    <tr style="color: black">
                        <td style="text-align: left">
                            Mailer
                        </td>
                        <td style="text-align: right">
                            @foreach ($mailerRate as $mailerCost)
                                @if($mailerCost != 0)
                                    ${{ number_format($mailerCost, 2) }}<br>
                                @endif
                            @endforeach
                        </td>
                        <td style="text-align: right">
                            @foreach ($mailerRate as $mailerCost)
                                @if($mailerCost != 0)
                                    @php
                                        $mailerInvoicesMergeds = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->groupBy('order_id')->get();
                                        $qtty = 0;
                                        foreach ($mailerInvoicesMergeds as $imIndvMailerkey => $indvM) {
                                            $mailerUnitCost = \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('order_id', $indvM->order_id)->where('mailer_unit_cost', '=', $mailerCost)->first();
                                            if (isset($mailerUnitCost)) {
                                                $qtty += $mailerUnitCost->mailer_unit_qty;
                                            }
                                        }
                                    @endphp
                                    {{ $qtty }}<br>
                                @endif
                            @endforeach
                            {{-- @foreach ($mailerRateQty as $mailerCostqty)
                                {{ number_format($mailerCostqty) }}<br>
                            @endforeach --}}
                        </td>
                        <td style="background: white; text-align: right">
                            @php $totalOfMailer = 0; @endphp
                            @foreach ($diffMailerCosts as $mailerCost)
                                {{-- ${{ number_format($labelCost['qty'] * $labelCost['rate'], 2) }}<br> --}}
                                @php $totalOfMailer += $mailerCost['rate'] * $mailerCost['qty']; @endphp
                            @endforeach
                            ${{ number_format($totalOfMailer, 2) }}
                        </td>
                    </tr>
                    <tr style="color: black">
                        <td style="text-align: left">
                            Postage
                        </td>
                        <td style="text-align: right">
                            -
                        </td>
                        <td style="text-align: right">
                            {{ number_format($postageQty) }}
                        </td>
                        <td style="background: white; text-align: right">
                            ${{ number_format($postageCost, 2) }}
                            @php $totalOfPostage = $postageCost; @endphp
                        </td>
                    </tr>
                    <tr style="color: black">
                        <td style="text-align: left">
                            Return Service Charges
                        </td>
                        <td style="text-align: right">
                            @php 
                                $charges = 0;
                                if(isset($mergeInvoice)) {
                                    $charges = $mergeInvoice->return_charges;
                                }
                            @endphp
                            ${{ number_format($charges, 2) }}
                        </td>
                        <td style="text-align: right">
                            @php 
                                $totalReturnQty = 0;
                                if(isset($mergeInvoice)) {
                                    $totalReturnQty = $detail->sum('product_return_qty');
                                }
                            @endphp
                            {{ $detail->sum('product_return_qty') }}
                        </td>
                        <td style="background: white; text-align: right">
                            ${{ number_format(($charges * $totalReturnQty), 2) }}
                        </td>
                    </tr>
                    <tr style="color: black">
                        <td style="text-align: left">
                            COG Credit
                        </td>
                        <td style="text-align: right">
                        </td>
                        <td style="text-align: right">
                            {{-- ${{ $invDetail->sum('product_return_cost') }} --}}
                            {{ $totalReturnQty }}
                        </td>
                        <td style="background: white; text-align: right">
                            @php 
                                $returnedProductTotal = $detail->sum('product_return_cost');
                            @endphp
                            -${{ number_format($detail->sum('product_return_cost'), 2) }}
                        </td>
                    </tr>
                    <tr style="color: black">
                        <td style="text-align: left">
                            Discount
                        </td>
                        <td style="text-align: right">
                            $0.00
                        </td>
                        <td style="text-align: right">
                            0
                        </td>
                        <td style="background: white; text-align: right">
                            $0.00
                        </td>
                    </tr>
                    <tr style="color: black">
                        <td colspan="2" style="text-align: right;">
                            Subtotal
                            <p>Tax</p>
                            <hr style="width: 100px; float: right">
                            <div class="clearfix"></div>
                            Total
                            <p>Amount Paid</p>
                            <hr style="width: 100px; float: right">
                            <div class="clearfix"></div>
                            Amount Due (USD)
                        </td>
                        <td colspan="2" style="text-align: right;">
                            <div class="clearfix"></div>
                            @php $totalBill = $productsSum + $totalOfPick + $totalOfPack + $totalOfPickPackFlat + $totalOfLabel + $totalOfMailer + $totalOfPostage; @endphp
                            @isset($invDetail) @isset($invDetail->merged_invoice)
                            ${{ number_format($totalBill, 2) }}
                            @endisset @endisset
                            <p>$0.00</p>
                            <hr style="width: 100px; float: right">
                            <div class="clearfix"></div>
                            @isset($invDetail) @isset($invDetail->merged_invoice)
                            ${{ number_format($totalBill, 2) }}
                            @endisset @endisset
                            <p>
                                @php
                                    $invoicesMergeds = \App\Models\InvoicesMerged::groupBy('invoice_id')->where('merged_invoice_id', $id)->get();
                                    $paidAmount = 0;
                                    foreach ($invoicesMergeds as $key => $inv) {
                                        $invoice = \App\AdminModels\Invoices::where('id', $inv->invoice_id)->first();
                                        if (isset($invoice)) {
                                            $paidAmount += $invoice->paid;
                                        }
                                    }
                                @endphp
                                ${{ number_format($paidAmount, 2) }}
                            </p>
                            <hr style="width: 100%; float: right">
                            <div class="clearfix"></div>
                            @isset($invDetail) @isset($invDetail->merged_invoice)
                            <input type="hidden" value="${{number_format($totalBill, 2)}}" id="total-bill-amount">
                            <span id="total-due-amount">${{ number_format($totalBill + ($charges * $totalReturnQty) - $paidAmount - $returnedProductTotal, 2) }}</span>
                            @endisset @endisset
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <div class="notes" style="width: 100%">
                <div class="leftside" style="width: 50%; color: black">
                    <p><a href="#">Notes</a></p>
                    Pay by ACH/Wire: JPMorgan Chase Bank, N.A<br>
                    Account Name: Warehousesystem Corp<br>
                    Account # 705016308<br>
                    ABA Routing # 322271627<br><br>
                    To pay by Check: Make checks payable to:<br>
                    Warehousesystem Corp<br>
                    1150 King Rd #40<br>
                    San Jose, CA 95122<br>
                    Box 318<br><br>
                    To pay by Credit Card:<br>
                    Contact Our Customer Services Representative<br>
                    925-918-2281<br>
                    VISA/MC 3% AMEX 3.5% fee applies<br><br><br>
                    <p><a href="#">Terms</a></p>
                    Billing invoices from 10/11 - 10/17<br>
                    NET 5 From billing date

                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            $('.loader').removeClass('d-none');
            var totalDueAmount = $('#total-due-amount').html();
            setTimeout(() => {
                $('#amount-due').html(totalDueAmount);
                $('.loader').addClass('d-none');
            }, 1000);
            $('#sum_of_amounts').html($('#invoice_total_amount').html());
            $(document).on('change', '#confirm-send', function() {
                let _this = $(this);
                if (_this.is(':checked')) {
                    $('#printInvoice').find('.print-text').html('Send Mail & Print');
                    $('.email-address').css('visibility', 'visible');
                } else {
                    $('#printInvoice').find('.print-text').html('Print');
                    $('.email-address').css('visibility', 'hidden');
                }
            });
            $(document).on('click', '#printInvoice', function() {
                $(this).attr('disabled', true);
                $('.spinner-border').removeClass('d-none');
                let send = $('#confirm-send').is(':checked') ? 'true' : 'false';
                let userMail = $('#user-email').val();
                if (send == 'false') {
                    userMail = 'Warehousesystem@gmail.com'
                }
                $.ajax({
                    url: "{{ route('send_email') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: $('#id').val(),
                        send_mail: send,
                        email: userMail
                    },
                    success: function(response) {
                        $('.spinner-border').addClass('d-none');
                        $('#printInvoice').attr('disabled', false);
                        if (response.status == true) {
                            $.print("#printable");
                        } else {
                            alert('Something went wrong');
                        }
                    }
                });
            });
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.2/jQuery.print.min.js"
    integrity="sha512-t3XNbzH2GEXeT9juLjifw/5ejswnjWWMMDxsdCg4+MmvrM+MwqGhxlWeFJ53xN/SBHPDnW0gXYvBx/afZZfGMQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@stop
