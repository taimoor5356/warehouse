@extends('admin.layout.app')
@section('title', 'Invoice Details')
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
    </style>
    <section class="invoice-preview-wrapper">
        <div class="row invoice-preview">
            <!-- /Invoice -->
            <div class="col-12">
                <div class="card invoice-preview-card">
                    <div class="card-header invoice-padding pb-0">
                        <!-- Address and Contact starts -->
                        <div class="col-md-12 p-0">
                            <div class="row invoice-spacing">
                                <div class="col-md-8 p-0">
                                    {{-- <h6 class="mb-2">Invoice To:</h6> --}}
                                    {{-- <small>Customer Name:</small> --}}
                                    <h3 class="mb-25"><a href='/customer/{{ $customerData->id }}/brands'
                                            class="text-dark"
                                            style="text-decoration: none">{{ $customerData->customer_name }}</a></h3>
                                    {{-- <small>Customer Address:</small> --}}
                                    <p class="card-text mb-25">{{ $customerData->address }}</p>
                                    <p class="card-text mb-25">{{ $customerData->phone }}</p>
                                    <p class="card-text mb-0">{{ $customerData->email }}</p>
                                    <input type="hidden" id="orderid" value="{{ Request::route()->id }}">
                                </div>
                                <div class="d-flex col-md-4" style="justify-content: flex-end">
                                    @if (Auth::user()->hasRole('admin'))
                                        <div class="bor">
                                            @php 
                                            $batchData = \App\AdminModels\Orders::where('id', $orderId)->first();
                                            if (isset($batchData)) {
                                                if ($batchData->status == 4 || $batchData->merged == 1) {
                                                    $disabled = 'true';
                                                } else {
                                                    $disabled = 'false';
                                                }
                                            } else {
                                                $disabled = 'true';
                                            }
                                            @endphp
                                            <a @if($disabled == 'false') href="/orders/{{ $orderId }}/edit" @else style="cursor: pointer" @endif class="btn btn-primary">Edit</a>
                                            <button style="margin-left:auto;" onclick="history.back(-1)" class="btn btn-primary waves-effect waves-float waves-light"><i data-feather="arrow-left"></i> Back</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <hr class="invoice-spacing">
                            <small>Brand:</small>
                            <div class="col-md-12 mb-2">
                                <h3>{{ ucwords($brandName) }}</h3>
                            </div>
                            <input type="hidden" value="{{ ucwords($labelsData->id) }}" id="brand_id">
                            <input type="hidden" value="{{ ucwords($labelsData->customer_id) }}" id="new_customer">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive p-1">
                            <table class="table data-table table-bordered" style="table-layout: fixed; width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">SKU ID</th>
                                        <th style="width: 35%">SKU</th>
                                        <th style="width: 10%">Quantity</th>
                                        <th class="py-1 pull-right" style="width: 15%; text-align: right">Sku Price</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                    @foreach ($orderDetailData as $val)
                                        <tr>
                                            <td class="py-1">
                                                <p class="card-text text-nowrap">
                                                    @isset($val->sku_order)
                                                        {{ $val->sku_order->sku_id_name }}
                                                    @endisset
                                                </p>
                                            </td>
                                            <td class="py-1">
                                                <p class="card-text">
                                                    @isset($val->sku_order)
                                                        {{ $val->sku_order->name }}
                                                    @endisset
                                                </p>
                                            </td>
                                            <td class="py-1">
                                                <p class="card-text text-nowrap qty">
                                                    @isset($val->sku_order)
                                                        {{ $val->qty }}
                                                    @endisset
                                                </p>
                                            </td>
                                            <td class="py-1 pull-right" style="text-align: right">
                                                @php
                                                    $charges = json_decode($val->service_charges_detail);
                                                    foreach ($charges as $charge) {
                                                        if ($charge->slug == 'labels_price') {
                                                            $tLabels = $tLabels + $charge->price;
                                                            if ($charge->price > 0) {
                                                                $labelQty = $labelQty + 1;
                                                            }
                                                        }
                                                        if ($charge->slug == 'pick_price') {
                                                            $tpick = $tpick + $charge->price;
                                                            if ($charge->price > 0) {
                                                                $pickQty = $pickQty + 1;
                                                            }
                                                        }
                                                        if ($charge->slug == 'pack_price') {
                                                            $tPack = $tPack + $charge->price;
                                                            if ($charge->price > 0) {
                                                                $packQty = $packQty + 1;
                                                            }
                                                        }
                                                        if ($charge->slug == 'mailer_price') {
                                                            $tMailer = $charge->price;
                                                            if ($charge->price > 0) {
                                                                $mailerQty = $mailerQty + 1;
                                                            }
                                                        }
                                                        if ($charge->slug == 'postage_price') {
                                                            $tPostage = $charge->price;
                                                            if ($charge->price > 0) {
                                                                $postageQty = $postageQty + 1;
                                                            }
                                                        }
                                                    }
                                                    $cost = $val->sku_selling_cost;
                                                    $servicesCost = $servicesCost + $val->sku_selling_cost;
                                                @endphp
                                                <span class="fw-bold pull-right"
                                                    style="text-align: right">${{ number_format($cost, 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <hr>
                        <br>
                        <br>
                        <div class="invoice-padding pb-0">
                            <div class="row invoice-sales-total-wrapper">
                                <div class="col-md-5 order-md-1 order-2 mt-md-0 mt-3">
                                    <p class="card-text mb-0">
                                    </p>
                                </div>
                                <div class="col-md-7 d-flex justify-content-end order-md-2 order-1">
                                    <div class="invoice-total-wrapper" style="min-width: 30rem">
                                        <div class="summary">
                                            <h4>Summary</h4>
                                            <hr>
                                            <div class="summaryData">
                                                @foreach ($products as $product)
                                                    <div class="invoice-total-item invoiceTotalItem">
                                                        @php
                                                            $status = \App\Models\ProductOrderDetail::where('product_id', $product['prod_id'])
                                                                ->where('order_id', $orderId)
                                                                ->first();
                                                        @endphp
                                                        @isset($status)
                                                            @if ($status->seller_cost_status == 1)
                                                                <p class="summary invoice-total-title">
                                                                    {{ $product['product_name'] }} x <span
                                                                        class="summaryprodQty">{{ $product['product_occ'][0] }}</span>:
                                                                </p>
                                                                <p id="" class="summary-prod-qty">$
                                                                    {{ number_format($product['product_occ'][1], 2) }}<span
                                                                        class="calcrestotal totalres"></span></p>
                                                                <input type="hidden" id="" name="summary-grandTotalLabelPrice">
                                                            @else
                                                                <p class="summary invoice-total-title">
                                                                    {{ $product['product_name'] }} x <span
                                                                        class="summaryprodQty">{{ $product['product_occ'][0] }}</span>: </p>
                                                                <p id="" class="summary-prod-qty">$ 0.00<span
                                                                        class="calcrestotal totalres"></span></p>
                                                                <input type="hidden" id="" name="summary-grandTotalLabelPrice"
                                                                    value="0">
                                                            @endif
                                                        @else
                                                            <p class="summary invoice-total-title">
                                                                {{ $product['product_name'] }} x <span
                                                                    class="summaryprodQty">{{ $product['product_occ'][0] }}</span>:
                                                            </p>
                                                            <p id="" class="summary-prod-qty">$
                                                                {{ number_format($product['product_occ'][1], 2) }}<span
                                                                    class="calcrestotal totalres"></span></p>
                                                            <input type="hidden" id="" name="summary-grandTotalLabelPrice">
                                                        @endisset
                                                    </div>
                                                @endforeach
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Label x @if ($orderMainData != null)
                                                {{ $orderMainData->labelqty }} @endisset:</p>
                                            <p class="invoice-total-amount">${{ number_format($tLabels, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Pick x @if ($orderMainData != null)
                                                {{ $orderMainData->pickqty }} @endisset:</p>
                                            <p class="invoice-total-amount">${{ number_format($tpick, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Pack x @if ($orderMainData != null)
                                                {{ $orderMainData->packqty }} @endisset:</p>
                                            <p class="invoice-total-amount">${{ number_format($tPack, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Pick / Pack Flat x @if ($orderMainData != null)
                                                {{ $orderMainData->pick_pack_flat_qty }} @endisset:</p>
                                            <p class="invoice-total-amount">
                                                ${{ number_format($orderMainData->pick_pack_flat_price, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Mailer x @if ($orderMainData != null)
                                                {{ $orderMainData->mailerqty }} @endisset:</p>
                                            <p class="invoice-total-amount">${{ number_format($tMailer, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Postage x @if ($orderMainData != null)
                                                {{ $orderMainData->postageqty }} @endisset:</p>
                                            <p class="invoice-total-amount">${{ number_format($tPostage, 2) }}</p>
                                        </div>
                                        {{-- {{dd($sum_of_amounts)}} --}}
                                        <hr class="my-50">
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Total:</p>
                                            <p class="invoice-total-amount" id="invoice_total_amount">
                                                ${{ number_format($orderMainData->total_cost, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
            </div>
        </div>
    </section>
    <script>
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
            $('#sum_of_amounts').html($('#invoice_total_amount').html());
            // $('.data-table').DataTable();
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
