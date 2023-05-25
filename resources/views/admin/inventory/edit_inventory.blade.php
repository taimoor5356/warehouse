@extends('admin.layout.app')
@section('title', 'Edit Inventory')
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
                                    <li class="breadcrumb-item"><a href="/inventory">Inventory</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Inventory
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
                                    <h4 class="card-title">Edit Inventory</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" enctype='multipart/form-data' action="{{route('inventory.update',$dataSet['id'])}}" method="post" id="update_inventory_form">
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
                                                            <option value="" disabled>--Select Product--</option>
                                                            @foreach($products as $product)
                                                                @if($product->id == $dataSet['product_id'])
                                                                    <option selected value="{{$product->id}}">{{$product->name}}</option>
                                                                @endif
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
                                                        <label class="col-form-label" for="first-name">Date</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="fp-default" class="form-control flatpickr-basic" name="date" value="{{$dataSet['date']}}" placeholder="YYYY-MM-DD" />
                                                        @error('date')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 offset-sm-3">
                                                <a class="btn btn-primary me-1 enter-pincode" data-prod-id="{{$dataSet['product_id']}}" href="/inventory/{{$dataSet['product_id']}}/edit" data-type="edit" data-bs-toggle="modal" data-bs-target="#enter_pin_Modal">
                                                    Submit
                                                </a>
                                                {{-- <button type="submit" class="btn btn-primary me-1" id="update_inventory" data-type="edit">Submit</button> --}}
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
                        $(document).on('click', '#update_inventory', function(e){
                            e.preventDefault();
                            $('#enter_pin_Modal').modal('toggle');
                        });
                        $(document).on('click', '.enter-pincode', function(){
                            $('#enter_pin_Modal form input[type="password"]').focus();
                            var href = $(this).attr('href');
                            var type = $(this).data('type');
                            $('#enter-pin-code').attr('href', href);
                            $('#enter-pin-code').data('type', type);
                        });
                        $(document).on('click', '#enter-pin-code', function(e){
                            e.preventDefault();
                            var pin_code = $('#inputPinCode').val();
                            var type = $(this).data('type');
                            if (pin_code != '') {
                                $.ajax({
                                    url: '{{route("pin_code.check_pin")}}',
                                    type: 'POST',
                                    data: {
                                        _token: '{{csrf_token()}}',
                                        'pin_code': pin_code
                                    },
                                    success:function(reponse) {
                                        if (type == 'add') {
                                            if(reponse.status == 'success') {
                                                // confirmAdd(e);
                                                $('#enter_pin_Modal').modal('toggle');
                                                $('#inventoryModal').modal('toggle');
                                            } else {
                                                $('#pin_error').html(reponse.msg);
                                            }
                                        } else if (type == 'edit') {
                                            if(reponse.status == 'success') {
                                                $('#update_inventory_form').submit();
                                            } else {
                                                $('#pin_error').html(reponse.msg);
                                            }
                                        } else if(type == 'delete') {
                                            if(reponse.status == 'success') {
                                                confirmDelete(e);
                                            } else {
                                                $('#pin_error').html(reponse.msg);
                                            }
                                        }
                                    }
                                });
                            } else {
                                $('#pin_error').html('Pin Required');
                            }
                        });
                        $('.touchspin2').TouchSpin({
                        min: 0,
                        max: 100000000,
                        step: 1,
                    });
                    });
                    
        function confirmEdit(e) {
            var url = e.currentTarget.getAttribute('href');
            window.location.replace(url);
          }
                </script>
@endsection

@section('modal')
@include('modals.modal')
@endsection

@section('datepickerjs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>

@stop

