<div class="modal fade text-start show" id="invoice_detail_modal" tabindex="-1" aria-labelledby="myModalLabel33"
    style="" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-xl ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Invoice Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="clear-fix"></div>
                <br>
                <br>
                <br>
                <section class="invoice-preview-wrapper">
                    <div class="row invoice-preview">
                        <!-- /Invoice -->
                        <div class="col-12">
                            <div class="card invoice-preview-card">
                                <div class="card-header invoice-padding pb-0">
                                    <!-- Address and Contact starts -->
                                    <div class="col-md-12 p-0">
                                        <div class="row invoice-spacing">
                                            <div class="col-md-10 p-0">
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
                                            @if (Auth::user()->hasRole('admin'))
                                                <div class="col-md-1 offset-md-1">
                                                    <a href="/orders/{{ $orderId }}/edit" class="btn btn-primary">Edit</a>

                                                </div>
                                            @endif
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
                                                                @isset($val->sku_detail)
                                                                    {{ $val->sku_detail->sku_id }}
                                                                @endisset
                                                            </p>
                                                        </td>
                                                        <td class="py-1">
                                                            <p class="card-text">
                                                                @isset($val->sku_detail)
                                                                    {{ $val->sku_detail->name }}
                                                                @endisset
                                                            </p>
                                                        </td>
                                                        <td class="py-1">
                                                            <p class="card-text text-nowrap qty">
                                                                @isset($val->sku_detail)
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
                                                                                    class="summaryprodQty">0</span>: </p>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark waves-effect waves-float waves-light"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>