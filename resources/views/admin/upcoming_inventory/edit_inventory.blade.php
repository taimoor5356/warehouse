@extends('admin.layout.app')
@section('title', 'Edit Purchased Inventory')
@section('datepickercss')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@stop

@section('content')

<style type="text/css">
    .bootstrap-touchspin.input-group-lg {
    width: 13.375rem !important;
}
</style>
<section id="basic-horizontal-layouts">
    <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Inventory</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Inventory
                                    </li>
                                    <li class="breadcrumb-item"><a href="/upcoming_inventory">Purchased Inventory</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Purchased Inventory
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Purchased Inventory</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" enctype='multipart/form-data' action="{{route('upcoming_inventory.update',$dataSet['id'])}}" method="post">
                                       {{@csrf_field()}}
                                       @method('PUT')
                                        <div class="row">
                                          
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="catselect">Select Product</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <select name="product_id" class="form-select" id="basicSelect">
                                                            <option value="" >--Select Product--</option>
                                                            @foreach($products as $product)
                                                            <option @if($product->id == $dataSet['product_id']) selected @endif value="{{$product->id}}">{{$product->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('product_id')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Quantity</label>
                                                    </div>
                                                    <div class="input-group input-group-lg">
                                                        <input type="number" id="qty" value="{{$dataSet['qty']}}" class="touchspin2" name="qty" />
                                                        @error('qty')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Purchase Date</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        
                                                        <input type="text" id="fp-default" class="form-control flatpickr-basic flatpickr-input" name="purchase_date" value="{{\Carbon\Carbon::parse($dataSet['date'])->format('m/d/Y')}}" placeholder="{{\Carbon\Carbon::parse($dataSet['date'])->format('m/d/Y')}}" />
                                                        @error('purchase_date')
                                                            <p class="text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="description">Supplier/Notes</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <textarea name="description" id="description" cols="10" rows="2" class="form-control">{{$dataSet['description']}}</textarea>
                                                        @error('description')
                                                              <p class="text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary me-1">Submit</button>
                                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </section>
                <script type="text/javascript">
                    $(document).ready(function(){
                        $('.touchspin2').TouchSpin({
                            min: 0,
                            max: 100000000,
                            step: 1,
                        });
                        $(".flatpickr-input").flatpickr({
                            dateFormat: 'm/d/Y'
                        });
                    });
                </script>
@endsection
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    
@stop

