@extends('admin.layout.app')
@section('title', 'Batches History')
@section('datatablecss')

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/pages/app-invoice.css') }}">
@stop

@section('content')
<style type="text/css">
    .hide{display: none;}
</style>
<section class="invoice-preview-wrapper">
                    <div class="row invoice-preview">
                        <!-- Invoice -->
                        <div class="col-xl-12 col-md-12 col-12">
                            <div class="card invoice-preview-card">
                                <div class="card-body invoice-padding pb-0">
                                    <!-- Header starts -->
                                    <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                                        
                                        <div class="mt-md-0 mt-2">
                                            <h4 class="invoice-title">
                                                Batch
                                                <span class="invoice-number">#{{$orderMainData->id}}</span>
                                            </h4>
                                           
                                        </div>
                                    </div>
                                    <!-- Header ends -->
                                </div>

                                <hr class="invoice-spacing" />

                                <!-- Address and Contact starts -->
                                <div class="card-body invoice-padding pt-0">
                                    <div class="row invoice-spacing">
                                        <div class="col-xl-4 p-0 mt-xl-0 mt-2">
                                            <h6 class="mb-2">Payment Details:</h6>
                                            <table>
                                                <tbody>
                                                    
                                                    <tr>
                                                        <td class="pe-1">Total:</td>
                                                        <td><span class="fw-bold">${{number_format($orderMainData->total_cost,2)}}</span></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-xl-4 p-0 mt-xl-0 mt-2">
                                            <h6 class="mb-2">Payment Details:</h6>
                                            <table>
                                                <tbody>
                                                    
                                                    <tr>
                                                        <td class="pe-1">Total:</td>
                                                        <td><span class="fw-bold">${{number_format($orderMainData->total_cost,2)}}</span></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Address and Contact ends -->

                                <!-- Invoice Description starts -->
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="py-1">Brand</th>
                                                <th class="py-1">SKU ID</th>
                                                <th class="py-1">SKU Name</th>
                                                <th class="py-1">Qty</th>
                                                <th class="py-1">Price</th>
                                                <th class="py-1">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orderDetailData as $val)
                                            <tr>
                                                <td class="py-1">
                                                    <p class="card-text text-nowrap">
                                                        {{$labelsData->brand}}
                                                    </p>
                                                </td>
                                                <td class="py-1">
                                                    <p class="card-text text-nowrap">
                                                        {{$val->sku_detail->sku_id}}
                                                    </p>
                                                </td>
                                                <td class="py-1">
                                                    <p class="card-text text-nowrap">
                                                        {{$val->sku_detail->name}}
                                                    </p>
                                                </td>
                                                <td class="py-1">
                                                    <span class="fw-bold">{{$val->qty}}</span>
                                                </td>
                                                <td class="py-1">
                                                    <span class="fw-bold">${{number_format($val->cost_of_good,2)}}</span>
                                                </td>
                                                <td class="py-1">
                                                    <span class="fw-bold">${{number_format($val->qty*$val->cost_of_good,2)}}</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        
                                        </tbody>
                                    </table>
                                </div>

                                <div class="card-body invoice-padding pb-0">
                                    <div class="row invoice-sales-total-wrapper">
                                        <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
                                            <p class="card-text mb-0">
                                               
                                            </p>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
                                            <div class="invoice-total-wrapper">
                                                <hr class="my-50" />
                                                <div class="invoice-total-item">
                                                    <p class="invoice-total-title">Subtotal:</p>
                                                    <p class="invoice-total-amount">${{number_format($orderMainData->total_cost,2)}}</p>
                                                </div>
                                                <div class="invoice-total-item">
                                                    <p class="invoice-total-title">Duty Fee:</p>
                                                    <p class="invoice-total-amount">${{number_format($orderMainData->duty_fee,2)}}</p>
                                                </div>
                                                <div class="invoice-total-item">
                                                    <p class="invoice-total-title">Freight Cost:</p>
                                                    <p class="invoice-total-amount">${{number_format($orderMainData->freight_cost,2)}}</p>
                                                </div>
                                                <div class="invoice-total-item">
                                                    <p class="invoice-total-title">Service Charges:</p>
                                                    <p class="invoice-total-amount">${{number_format($orderMainData->service_charges,2)}}</p>
                                                </div>
                                                <hr>
                                                <div class="invoice-total-item">
                                                    <p class="invoice-total-title">Total:</p>
                                                    <p class="invoice-total-amount">${{number_format($orderMainData->total_cost,2)}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Invoice Description ends -->
                                <form action="{{route('orders.update',$orderMainData->id)}}"  enctype="multipart/form-data" method="post">
                                    {{@csrf_field()}}
                                       @method('PUT')
                                <hr class="invoice-spacing" />
                                
                            </div>
                        </div>
                        <!-- /Invoice -->
                        <!-- Invoice Add Right starts -->
                        <div class="col-xl-3 col-md-4 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-50">Status</p>
                                    <select name="status_id" data-order-id="'.$row->id.'" id="order_status_id" class="form-select" >
                                                            <option @if($orderMainData->status == 0) selected @endif value="0">New Batch</option>
                                                            <option @if($orderMainData->status == 1) selected @endif value="1">In Process</option>
                                                            <option @if($orderMainData->status == 2) selected @endif value="2">Shipped</option>
                                                            <option @if($orderMainData->status == 3) selected @endif value="3">Delivered</option>
                                                            <option @if($orderMainData->status == 4) selected @endif value="4">Cancelled</option>
                                                            
                                                        </select>
                                    <p></p>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label for="note" class="form-label fw-bold">Note:</label>
                                                <textarea name="order_notes" class="form-control" placeholder="Add Notes" rows="2" id="note">{{$orderMainData->notes}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @if($orderMainData->status == 4)
                                    <button type="submit" disabled="disabled"  class="btn btn-primary w-100 mb-75">Submit</button>
                                    @else
                                        <button type="submit"  class="btn btn-primary w-100 mb-75">Submit</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Invoice Add Right ends -->
                        </form>
                    </div>
                </section>
                
@endsection
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>

    <script src="{{ URL::asset('admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/pages/app-invoice.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/form-select2.js') }}"></script>
@stop