@extends('admin.layout.app')
@section('title', 'Merged Invoice Details')
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
                                <div class="col-md-9 p-0">
                                    {{-- <h6 class="mb-2">Invoice To:</h6> --}}
                                    {{-- <small>Customer Name:</small> --}}
                                    <h3 class="mb-25"><a href='#'
                                        class="text-dark"
                                        style="text-decoration: none">
                                    @php
                                        $invDetail = $detail->first();
                                        if (isset($invDetail)) {
                                            $mergedInv = $invDetail->merged_invoice->first();
                                            if (isset($mergedInv)) {
                                                $customer = $mergedInv->customer;
                                                if (isset($customer)) {
                                                    echo $customer->customer_name;
                                                } else {
                                                    echo 'Customer Name';
                                                }
                                            } else {
                                                echo 'Customer Name';
                                            }
                                        } else {
                                            echo 'Customer Name';
                                        }
                                    @endphp
                                    </a></h3>
                                    {{-- <small>Customer Address:</small> --}}
                                    <p class="card-text mb-25">{{ $customer->address }}</p>
                                    <p class="card-text mb-25">{{ $customer->phone }}</p>
                                    <p class="card-text mb-0">{{ $customer->email }}</p>
                                    <input type="hidden" id="merged_invoice_id" value="{{ Request::route()->id }}">
                                </div>
                                @if (Auth::user()->hasRole('admin'))
                                    <div class="col-md-3">
                                        <a href="{{ route('print_merged_invoice') }}" class="btn btn-primary" style="float: right"><i data-feather="printer"></i> Print</a>
                                    </div>
                                @endif
                            </div>
                            <hr class="invoice-spacing">
                            {{-- <small>Brand:</small> --}}
                            <div class="col-md-12 mb-2">
                                <h3></h3>
                            </div>
                            <input type="hidden" value="" id="brand_id">
                            <input type="hidden" value="" id="new_customer">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive p-1">
                            <table class="table data-table table-bordered" style="table-layout: fixed; width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">Date</th>
                                        <th style="width: 35%">Product</th>
                                        <th style="width: 10%">Quantity</th>
                                        <th style="width: 15%">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail as $d)
                                        <tr>
                                            <td class="py-1">
                                                <p class="card-text text-nowrap">
                                                    @isset($d)
                                                        {{ $d->created_at->format('M, d-Y') }}
                                                    @endisset
                                                </p>
                                            </td>
                                            <td class="py-1">
                                                <p class="card-text">
                                                    @isset($d->product)
                                                        {{ $d->product->name }}
                                                    @endisset
                                                </p>
                                            </td>
                                            <td class="py-1">
                                                <p class="card-text text-nowrap">
                                                    @isset($d)
                                                        {{ \App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('product_id', $d->product_id)->sum('product_qty') }}
                                                    @endisset
                                                </p>
                                            </td>
                                            <td class="py-1">
                                                <p class="card-text text-nowrap">
                                                    @isset($d)
                                                        ${{ number_format(\App\Models\InvoicesMerged::where('merged_invoice_id', $id)->where('product_id', $d->product_id)->where('product_qty', '>', '0')->sum('product_price'), 2) }}
                                                    @endisset
                                                </p>
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
                                            {{-- <div class="summaryData">
                                                    <div class="invoice-total-item invoiceTotalItem">
                                                        <p class="summary invoice-total-title">
                                                            x <span
                                                                class="summaryprodQty"></span>:
                                                        </p>
                                                        <p id="" class="summary-prod-qty">$<span
                                                                class="calcrestotal totalres"></span></p>
                                                        <input type="hidden" id="" name="summary-grandTotalLabelPrice">
                                                        <p class="summary invoice-total-title">
                                                            x <span
                                                                class="summaryprodQty">0</span>: </p>
                                                        <p id="" class="summary-prod-qty">$ 0.00<span
                                                                class="calcrestotal totalres"></span></p>
                                                        <input type="hidden" id="" name="summary-grandTotalLabelPrice"
                                                            value="0">
                                                        <p class="summary invoice-total-title">
                                                            $ x <span
                                                                class="summaryprodQty"></span>:
                                                        </p>
                                                        <p id="" class="summary-prod-qty">$<span
                                                                class="calcrestotal totalres"></span></p>
                                                        <input type="hidden" id="" name="summary-grandTotalLabelPrice">
                                                    </div>
                                                <hr>
                                            </div> --}}
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">
                                                Label x
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
                                                    $getDetail = \App\Models\InvoicesMerged::with('merged_invoice')->where('merged_invoice_id', $id)->first();
                                                    if (isset($getDetail)) {
                                                        $mergeInvoice = $getDetail->merged_invoice;
                                                        if (isset($mergeInvoice)) {
                                                            $labelQty = $mergeInvoice->label_qty;
                                                            $pickQty = $mergeInvoice->pick_qty;
                                                            $packQty = $mergeInvoice->pack_qty;
                                                            $mailerQty = $mergeInvoice->mailer_qty;
                                                            $pickPackFlatQty = $mergeInvoice->pick_pack_flat_qty;
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
                                                {{ $labelQty }}:</p>
                                            <p class="invoice-total-amount">${{ number_format($labelCost, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Pick x {{ $pickQty }}:</p>
                                            <p class="invoice-total-amount">${{ number_format($pickCost, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Pack x {{ $packQty }}:</p>
                                            <p class="invoice-total-amount">${{ number_format($packCost, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Pick / Pack Flat x {{ $pickPackFlatQty }}:</p>
                                            <p class="invoice-total-amount">${{ number_format($pickPackFlatCost, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Mailer x {{ $mailerQty }}:</p>
                                            <p class="invoice-total-amount">${{ number_format($mailerCost, 2) }}</p>
                                        </div>
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Postage x {{ $postageQty }}:</p>
                                            <p class="invoice-total-amount">${{ number_format($postageCost, 2) }}</p>
                                        </div>
                                        <hr class="my-50">
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Total:</p>
                                            <p class="invoice-total-amount" id="invoice_total_amount"> {{ number_format($totalCost, 2) }}</p>
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
