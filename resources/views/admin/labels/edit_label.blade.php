@extends('admin.layout.app')
@section('title', 'Edit Brand')
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
                    <h2 class="content-header-title float-start mb-0">Edit Brand</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item">Customers
                            </li>
                            <li class="breadcrumb-item"><a href="/brands">Manage Brands</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Brand
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
                    <h4 class="card-title">Edit Brand</h4>
                </div>
                <div class="card-body">
                    <form class="form form-horizontal" enctype='multipart/form-data' action="{{route('brands.update',$dataSet['id'])}}" method="post">
                        {{@csrf_field()}}
                        @method('PUT')
                        <div class="row">
                            
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="catselect">Select Customer</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select name="customer_id" class="form-select" id="basicSelect">
                                            <option value="" >--Select Customer--</option>
                                            @foreach($customers as $customer)
                                            <option @if($customer->id == $dataSet['customer_id']) selected @endif value="{{$customer->id}}">{{$customer->customer_name}}</option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="catselect">Enter Brand Name</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="brand" value="{{$dataSet['brand']}}"  name="brand" />
                                        @error('brand')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="catselect">Enter Mailer Charges</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="input-icon">
                                            <input type="number" min="0.0" step="0.01" id="mailer_cost" class="form-control" value="{{number_format($dataSet['mailer_cost'], 2)}}" name="mailer_cost"/>
                                            <i>$</i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--  <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label" for="first-name">Initial labels</label>
                                    </div>
                                    <div class="input-group input-group-lg">
                                        <input type="number" id="qty" value="{{$dataSet['qty']}}" class="touchspin2" name="qty" />
                                        @error('qty')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>  --}}
                            
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
@section('modal')
<!-- Basic toast -->
<button class="btn btn-outline-primary toast-basic-toggler mt-2" id="toast-btn"></button>
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
    $(document).ready(function(){
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
        $('.touchspin2').TouchSpin({
        min: 0,
        max: 100000000,
        step: 1,
    });
    });
</script>
@endsection
@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    
@stop

