<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>warehousesystem</title>
    <style type="text/css">
        .hide {
            display: none;
        }
        .billed_to p{
            text-align: left;
        }
        .payment p, a, strong, h1{
            text-align: right;
        }
        .clearfix::after{
            content: "";
            display: table;
            clear: both;
            margin: 0px;
        }
        section{
            font-family: Arial, Helvetica, sans-serif
        }
        th{
            padding: 1px 0px 15px 0px !important;
        }
        tr, td{
            padding: 5px 0px 10px 0px !important;
        }
        td p{
            margin-bottom: 0px !important;
        }
        .header_description p{
            margin-bottom: 0px !important;
        }
        table, th, td {
            padding: 5px 5px !important;
            border: 1px solid lightgrey;
            border-collapse: collapse;
        }
        .header {
            display: inline-block;
            width: 100%;
        }
        .header2 {
            display: inline-block;
        }
        .header_description1{
            display: inline-block;
            width: 30%;
            vertical-align: top;
        }
        .header_description2{
            display: inline-block;
            width: 28%;
            vertical-align: top;
        }
        .logo {
            display: inline-block;
            width: 40%;
            vertical-align: top;
        }
        .billed_to1{
            margin: 0px auto;
            display: inline-block;
            vertical-align: top;
            width: 30%;
            /* border-right: 1px solid lightgrey; */
            min-height: 100px !important;
        }
        .billed_to2{
            margin: 0px auto;
            display: inline-block;
            vertical-align: top;
            width: 14.25%;
            padding-left: 5px !important;
            min-height: 100px !important;
        }
        .billed_to3{
            margin: 0px auto;
            display: inline-block;
            vertical-align: top;
            width: 26.25%;
            min-height: 100px !important;
        }

        .payment {
            margin: 0px auto;
            display: inline-block;
            vertical-align: top;
            width: 24.25%;
            text-align: right !important;
            min-height: 100px !important;
            padding: 0px 5px;
        }
    </style>
</head>
<body>
    <section class="invoice-preview-wrapper" id="printable">
        @php
            $setting = \App\Models\Setting::first();
        @endphp
        <div class="bg-white" style="padding: 20px 20px 20px 20px;">
            <div class="header">
                <div class="logo">
                    <img src="{{ asset('images/Warehousesystem.jpg') }}" height="155" width="210" alt="">
                </div>
                <div class="header_description1">
                    <p style="text-align: right">@isset($setting){{ $setting->company_name }}@endisset</p>
                    <p style="text-align: right">@isset($setting){{ $setting->company_number }}@endisset</p>
                </div>
                <div class="header_description2">
                    <p style="text-align: right; width: 65%; float: right">@isset($setting){{ $setting->company_address }}@endisset</p>
                </div>
            </div>
            <br>
            <br>
            <div class="header2">
                <div class="details billed_to1" style="color: black;">
                    @php
                    $customerName = 'Customer Name';
                    $customerAddress = 'Customer Address';
                        $invDetail = $detail->first();
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

                    <p><a href="#"><small>Billed To</small></a><br>{{ $customerName }}<br>{{ $customerAddress }}</p>
                </div>
                <div class="details billed_to2" style="color: black;">
                    <p><a href="#"><small>Date Issued</small></a><br>@isset($mergedInv) {{ $mergedInv->created_at->format('m/d/Y') }} @endisset</p>
                    <p><a href="#"><small>Due Date</small></a><br>@isset($mergedInv) {{ $mergedInv->created_at->addDays(5)->format('m/d/Y') }} @endisset</p>
                </div>
                <div class="details billed_to3" style="color: black;">
                    <p><a href="#"><small>Invoice Number</small></a><br>@isset($invDetail) {{ $customerName }}-invoice-no-{{ $invDetail->inv_no }} @endisset</p>
                </div>
                <div class="details payment" style="color: black;">
                    <p><a href="#"><small>Amount Due (USD)</small></a><b><h1>@isset($invDetail) @isset($invDetail->merged_invoice) ${{ number_format($invDetail->merged_invoice->total_cost + ($invDetail->merged_invoice->return_charges * $detail->sum('product_return_qty')) - $detail->sum('product_return_cost'), 2) }} @endisset @endisset</h1></b></p>
                </div>
            </div>
            <hr class="text-primary" style="">
            <table class="table table-bordered" style="width: 100%">
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
                                    {{ number_format(\App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('product_id', $d->product_id)->sum('product_qty')) }}
                                @endisset
                            </td>
                            <td style="text-align: right">
                                @isset($d)
                                    ${{ number_format(\App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('product_id', $d->product_id)->where('product_qty', '>', '0')->sum('product_price'),2) }}
                                @endisset
                                @php
                                    $productsSum += \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('product_id', $d->product_id)->where('product_qty', '>', '0')->sum('product_price');
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
                                $pickQty = $mergeInvoice->pick_qty;
                                $packQty = $mergeInvoice->pack_qty;
                                $mailerQty = $mergeInvoice->mailer_qty;
                                $pickPackFlatQty = $mergeInvoice->flat_pick_pack_qty;
                                $postageQty = $mergeInvoice->postage_qty;
                                $totalCost = $mergeInvoice->total_cost;
                        
                                $labelCost = $mergeInvoice->label_charges;
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
                        $mailerRate = array();
                        $mailerRateQty = array();
                        $mailerRate_Qty = 0;
                        $newMailerRate_Qty = 0;
                        foreach ($diffMailerCosts as $diffMailerKey => $diffMailerValue) {
                            $mailerRate_Qty = $diffMailerValue['qty'];
                            $newMailerRate_Qty += $mailerRate_Qty;
                            if (in_array(number_format($diffMailerValue['rate'], 2), $mailerRate)) {
                                unset($mailerRate[$diffMailerKey]);
                                $mailerRateQty = [];
                                array_push($mailerRateQty, $newMailerRate_Qty);
                            } else {
                                array_push($mailerRate, number_format($diffMailerValue['rate'], 2));
                                array_push($mailerRateQty, $mailerRate_Qty);
                            }
                        }
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
                            {{-- ${{ $detail->sum('product_return_cost') }} --}}
                            {{ $detail->sum('product_return_qty') }}
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
            $('#sum_of_amounts').html($('#invoice_total_amount').html());
            $(document).on('click', '#printInvoice', function() {
                alert();
                return false;
                $.print("#printable");
            });
        });
    </script>
</body>
</html>