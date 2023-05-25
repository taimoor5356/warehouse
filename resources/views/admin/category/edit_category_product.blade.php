@extends('admin.layout.app')
@section('title', 'Edit Category Product')
@section('content')

<section id="basic-horizontal-layouts">
    <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Edit Category Product</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Inventory
                                    </li>
                                    <li class="breadcrumb-item"><a href="/category">Categories</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="/categorywise/products/{{ $cat_id }}">Category Products</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Category Product
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
                                    <h4 class="card-title">Edit Product</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal editproductform" enctype='multipart/form-data' action="@isset($dataSet['id']){{route('update_category_product',$dataSet['id'])}} @endisset" method="POST">
                                       {{@csrf_field()}}
                                        <div class="row">
                                          <input type="hidden" name="type" value="category_type">
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="catselect" style="font-weight: bold">Select Category Name</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <select name="category_name" class="form-select" id="basicSelect">
                                                            <option value="" >--Select Category--</option>
                                                            @foreach($cats as $cat)
                                                            <option @isset($dataSet['category_id'])@if($cat->id == $dataSet['category_id']) selected @endif @endisset value="{{$cat->id}}">{{$cat->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_name')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="first-name" style="font-weight: bold">Product Name</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" id="name" class="form-control" value="@isset($dataSet['name']){{$dataSet['name']}}@endisset" name="name" />
                                                        @error('name')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="catselect" style="font-weight: bold">Select Unit</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <select name="product_unit_id" class="form-select" id="basicSelect">
                                                            <option value="">--Select Unit--</option>
                                                            
                                                            @foreach($units as $unit)
                                                            <option @isset($dataSet['product_unit_id']) @if($unit->id == $dataSet['product_unit_id']) selected @endif @endisset value="{{$unit->id}}">{{$unit->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('product_unit_id')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="col-form-label" for="first-name" style="font-weight: bold">Weight (oz)</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="number" min="0.0" step="0.01" id="weight" class="form-control" value="@isset($dataSet['weight']){{$dataSet['weight']}}@endisset" name="weight" required />
                                                        @error('price')
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
                                                            <input type="number" min="0.0" step="0.01" id="cog" class="form-control" value="@isset($dataSet['cog']){{$dataSet['cog']}}@endisset" name="cog" required/>
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
                                                            <input type="number" min="0.0" step="0.01" id="shipping_cost" class="form-control" value="@isset($dataSet['shipping_cost']){{ $dataSet['shipping_cost'] }}@endisset" name="shipping_cost" required/>
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
                                                            <input type="number" step="0.01" id="price" class="form-control" name="price" value="{{ $dataSet['price'] }}" readonly required/>
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
                                                        <input type="file" name="image" value="{{ $dataSet['image'] }}" onchange="document.getElementById('product_thumnail').src = window.URL.createObjectURL(this.files[0])" class="form-control" >
                                                        <div class="row img-preview">
                                                            <div class="col-md-6 p-1">
                                                                @php
                                                                    $url= asset('images/products/'.$dataSet["image"]);
                                                                @endphp
                                                                <img src="{{$url}}" alt="Product Image" id="product_thumnail" class="img-fluid">
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
                                                                    <input type="checkbox" {{ old('forecast_status') == 1 ? 'checked' : '' }} @if($dataSet['forecast_status'] == 1) checked @endif name="forecast_status" value="1" class="form-check-input" id="forecast_status" style="margin-top: 10px">
                                                                </div>
                                                            </div>
                                                            <div class="col-12 alert_forecast">
                                                                <div class="mb-1 row">
                                                                    <div class="col-sm-5">
                                                                        <label class="col-form-label" for="manual_threshold" style="font-weight: bold">Manual Forecast Calculation</label>
                                                                    </div>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" name="manual_threshold" @if($dataSet['forecast_status'] == '' || $dataSet['forecast_status'] == '0') disabled @endif id="manual_threshold" class="form-control manual_threshold" placeholder="Set Manual Forecast Calculation" value="{{ old('manual_threshold') }}@if($dataSet['forecast_status'] == 1)@if($dataSet['manual_threshold'] != ''){{ $dataSet['manual_threshold'] }}@endif @endif" required>
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
                                                                        <input type="checkbox" {{ old('automated_forecast_checkbox') == 1 ? 'checked' : '' }} @if($dataSet['forecast_status'] == 0) @if($dataSet['automated_status'] != '' || $dataSet['automated_status'] != 0) checked @endif @else disabled @endif class="form-check-input" id="automated_forecast_checkbox" name="automated_forecast_checkbox" value="1" style="margin-top: 10px">
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
                                                                    <input type="text" @if($dataSet['forecast_status'] == 0) @if($dataSet['automated_status'] == 0) @if($dataSet['forecast_days'] != '') @endif @else disabled @endif @else disabled @endif class="form-control forecast_days" id="forecast_days" name="forecast_days" @if($dataSet['forecast_status'] == 0)@if($dataSet['automated_status'] == 1) value="" @else value="@if($dataSet['forecast_days'] != ''){{ $dataSet['forecast_days'] }}@endif" @endif @endif  placeholder="Enter forecast days" required>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-1">
                                                                <div class="col-sm-5">
                                                                    <label class="col-form-label" for="threshold_val" style="font-weight: bold">Alert Days</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <input type="text" class="form-control threshold_val" id="threshold_val" name="threshold_val" placeholder="Enter Alert days" @if($dataSet['forecast_status'] == 0) @if($dataSet['automated_status'] == 1) disabled @endif @else disabled @endif @if($dataSet['forecast_status'] == 0)@if($dataSet['automated_status'] == 1) value="" @else value="@if($dataSet['threshold_val'] != ''){{ $dataSet['threshold_val'] }}@else 0 @endif" @endif @endif required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="col-sm-12 offset-sm-2">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                {{-- <a href="#" class="btn btn-primary me-1 enter-pincode"  data-prod-id='{{$dataSet['id']}}' data-type="edit"  data-bs-toggle="modal" data-bs-target="#enter_pin_Modal">Submit</a> --}}
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
        <!-- Basic toast -->
        <button class="btn btn-outline-primary toast-basic-toggler mt-2" id="toast-btn">ab</button>
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

<script type="text/javascript">-
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
                      if (type == 'edit') {
                          if(reponse.status == 'success') {
                            //   confirmEdit(e);
                            $('.editproductform').submit();
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
      $(document).on('keypress', '#enter-pin-code', function(e){
          if (e.which == 13) {
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
          }
      });
    });
    function confirmEdit(e) {
      var url = e.currentTarget.getAttribute('href');
      window.location.replace(url);
    }
  </script>
@endsection
