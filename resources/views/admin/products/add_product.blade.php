@extends('admin.layout.app')
@section('title', 'Add Product')
@section('content')

<section id="basic-horizontal-layouts">
    <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Add Product</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Inventory
                                    </li>
                                    <li class="breadcrumb-item"><a href="/products">Products</a>
                                    </li>
                                    <li class="breadcrumb-item active">Add Product
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
                                    <h4 class="card-title">Add Product</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" enctype='multipart/form-data' action="{{route('products.store')}}" method="post" id="add_product_id">
                                       {{@csrf_field()}}
                                        <div class="row">
                                          
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="category_name" style="font-weight: bold">Select Category Name</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <select name="category_name" class="form-select select2" id="" required>
                                                            <option value="" >--Select Category--</option>
                                                            @foreach($cats as $cat)
                                                            <option @if(isset($cat_id) && $cat_id == $cat->id) selected @endif value="{{$cat->id}}">{{$cat->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_name')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="name" style="font-weight: bold">Product Name</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" value="{{old('name')}}" id="name" class="form-control" name="name" placeholder="Enter Product Name" required/>
                                                        @error('name')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="product_unit_id" style="font-weight: bold">Select Unit</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <select name="product_unit_id" class="form-select" id="" required>
                                                            <option value="">--Select Unit--</option>
                                                            
                                                            @foreach($units as $unit)
                                                            <option {{ old('product_unit_id') == $unit->id ? 'selected' : '' }} value="{{$unit->id}}">{{$unit->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('product_unit_id')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="weight" style="font-weight: bold">Weight</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="number" step="0.01" id="weight" class="form-control" name="weight" placeholder="Enter weight" required/>
                                                        @error('weight')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="cog" style="font-weight: bold">Cost of Good</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="input-icon">
                                                            <input type="number" step="0.01" id="cog" class="form-control" placeholder="Enter Cost of Good" name="cog" required/>
                                                            <i>$</i>
                                                        </div>
                                                        @error('cog')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="shipping_cost" style="font-weight: bold">Shipping Cost</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="input-icon">
                                                            <input type="number" step="0.01" id="shipping_cost" class="form-control" placeholder="Enter Shipping Cost" name="shipping_cost"/>
                                                            <i>$</i>
                                                        </div>
                                                        @error('shipping_cost')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="price" style="font-weight: bold">Total Cost</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="input-icon">
                                                            <input type="number" step="0.01" id="price" class="form-control" name="price" placeholder="0.00" readonly required/>
                                                            <i>$</i>
                                                        </div>
                                                        @error('price')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="image" style="font-weight: bold">Select Image</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="file" name="image" onchange="document.getElementById('product_thumnail').src = window.URL.createObjectURL(this.files[0])" class="form-control" >
                                                        <div class="row img-preview">
                                                            <div class="col-md-6 p-1">
                                                                <img src="{{asset('images/products/placeholder.jpg')}}" alt="Product Image" id="product_thumnail" class="img-fluid">
                                                            </div>
                                                        </div>
                                                        @error('image')
                                                            <p class="text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-12">
                                                <div class="row mb-1">
                                                    <div class="col-md-5">
                                                        <div class="row mb-1">
                                                            <div class="col-5 alert_forecast">
                                                                <label class="col-form-label" style="font-weight: bold; text-decoration: underline"><h4>Manual Forecast</h4></label>
                                                            </div>
                                                            <div class="col-sm-7">
                                                                <div class="form-check form-check-primary form-switch">
                                                                    <input type="checkbox" {{ old('forecast_status') == 1 ? 'checked' : '' }} name="forecast_status" value="1" class="form-check-input" id="forecast_status" style="margin-top: 10px">
                                                                </div>
                                                            </div>
                                                            <div class="col-12 alert_forecast">
                                                                <div class="mb-1 row">
                                                                    <div class="col-sm-5">
                                                                        <label class="col-form-label" for="manual_threshold" style="font-weight: bold">Manual Forecast Calculation</label>
                                                                    </div>
                                                                    <div class="col-sm-7">
                                                                            <input type="number" name="manual_threshold" disabled id="manual_threshold" class="form-control manual_threshold" placeholder="Set Manual Forecast Calculation" value="{{ old('manual_threshold') }}" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-5">
                                                        <div class="col-12 automated">
                                                            <div class="mb-1 row">
                                                                <div class="col-sm-5">
                                                                    <label class="col-form-label" for="" style="font-weight: bold; text-decoration: underline"><h4>Automated Forecast</h4></label>
                                                                </div>
                                                                <div class="col-sm-7">
                                                                    <div class="form-check form-check-primary form-switch">
                                                                        <input type="checkbox" {{ old('automated_forecast_checkbox') == 1 ? 'checked' : '' }} value="1" checked class="form-check-input" id="automated_forecast_checkbox" name="automated_forecast_checkbox" value="1" style="margin-top: 10px">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 forecast_orders_alert_days">
                                                            <div class="row mb-1">
                                                                <div class="col-sm-5">
                                                                    <label class="col-form-label" for="forecast_days" style="font-weight: bold">Forecast Orders (Days)</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <input type="text" class="form-control forecast_days" disabled id="forecast_days" name="forecast_days" placeholder="Enter forecast days" required>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-1">
                                                                <div class="col-sm-5">
                                                                    <label class="col-form-label" for="threshold_val" style="font-weight: bold">Alert Days</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <input type="text" class="form-control threshold_val" disabled id="threshold_val" name="threshold_val" placeholder="Enter alert days" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            {{-- <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="is_active" style="font-weight: bold">Status</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-check form-check-primary form-switch">
                                                            <input type="checkbox" {{ old('is_active') == 1 ? 'checked' : '' }} name="is_active" value="1" checked class="form-check-input" id="customSwitch3">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <div class="col-sm-12 offset-sm-2">
                                                <button type="submit" class="btn btn-primary">Submit</button>
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
@include('modals.modal')
@endsection
<script>
    
    $(document).on('ready', function(){
        @if (session('success'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-success');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("{{session('success')}}");
            $('#toast-btn').click();
        @elseif (session('error'))
            $('.toast .me-auto').html('Success');
            $('.toast .toast-header').addClass('bg-danger');
            $('.toast .text-muted').html('Now');
            $('.toast .toast-body').html("Something went wrong");
            $('#toast-btn').click();
        @endif
        $(document).on('click', '#automated_forecast_checkbox', function()
        {
            if($(this).is(':checked')) {
                // $('.forecast_orders_alert_days').addClass('invisible');
                $('#forecast_days').attr('disabled', true);
                $('#threshold_val').attr('disabled', true);
            }
            else
            {
                // $('.forecast_orders_alert_days').removeClass('invisible');
                $('#forecast_days').attr('disabled', false);
                $('#threshold_val').attr('disabled', false);
            }
        });
        $(document).on("click", "#forecast_status", function()
        {
            if ($(this).is(':checked'))
            {
                if($('#automated_forecast_checkbox').is(':checked'))
                {
                    $('#automated_forecast_checkbox').prop('checked', 0);
                }
                else
                {
                    $('#automated_forecast_checkbox').prop('checked', 0);
                }
                $('#manual_threshold').attr('disabled', false);
                // $('#automated_forecast_checkbox').attr('checked', false);
                $('#automated_forecast_checkbox').attr('disabled', true);
                $('#forecast_days').attr('disabled', true);
                $('#threshold_val').attr('disabled', true);
            }
            else
            {
                $('#automated_forecast_checkbox').attr('disabled', false);
                $('#automated_forecast_checkbox').prop('checked', 1);
                $('#forecast_days').attr('disabled', false);
                $('#threshold_val').attr('disabled', false);
                if ($('#automated_forecast_checkbox').is(':checked'))
                {
                    $('#forecast_days').attr('disabled', true);
                    $('#threshold_val').attr('disabled', true);
                }
                else
                {
                    $('#forecast_days').attr('disabled', false);
                    $('#threshold_val').attr('disabled', false);
                }
                $('#manual_threshold').attr('disabled', true);
                $('.automated').removeClass('invisible');
            }
        });
        $(document).on('keyup', '#cog, #shipping_cost', function() {
            var cog = $('#cog').val();
            var shipping_cost = $('#shipping_cost').val();
            if(cog == '') {
                cog = 0;
            } else if(shipping_cost == '') {
                shipping_cost = 0;
            }
            var sum = Number(cog) + Number(shipping_cost);
            var price = sum;
            $('#price').val(price.toFixed(2));
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
                                // alert('here');
                                $('#add_product_id').submit();
                            } else {
                                $('#pin_error').html(reponse.msg);
                            }
                        } else if (type == 'edit') {
                            if(reponse.status == 'success') {
                                confirmEdit(e);
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
    });
    function confirmDelete(e) {
        var url = e.currentTarget.getAttribute('href');
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this product!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-outline-secondary ms-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                window.location.replace(url);
            }
        });
    }
    function confirmEdit(e) {
        var url = e.currentTarget.getAttribute('href');
        window.location.replace(url);
    }
</script>
@endsection