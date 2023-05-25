@extends('admin.layout.app')
@section('title', 'Edit Customer Info')
@section('content')
<style type="text/css">
    /* ._input_fields{
        border: 0px;
        outline: 0;
    } */
    ._input_fields:focus{
        outline: 0 none;
        /* border: 0px; */
        /* border-bottom: 1px solid; */
    }
</style>
<section id="basic-horizontal-layouts">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-start mb-0">Edit Customer Info</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Customers
                            </li>
                            <li class="breadcrumb-item"><a href="/customers">Manage Customers</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Customer Info
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
                    <h4 class="card-title">Edit Customer Information</h4>
                </div>
                <div class="card-body">
                    <form class="form form-horizontal" enctype='multipart/form-data' action="{{route('customers.update',$dataSet['id'])}}" method="post">
                        {{@csrf_field()}}
                        @method('PUT')
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Customer Name</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" id="name" class="form-control" placeholder="Enter Customer Name" value="{{$dataSet['customer_name']}}" name="customer_name" maxlength="50" required/>
                                        @error('customer_name')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Phone</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="number" id="phone" class="form-control" placeholder="Enter Phone Number" value="{{$dataSet['phone']}}" name="phone" maxlength="15" onKeyDown="if(this.value.length==15 && event.keyCode!=8) return false;" required/>
                                        @error('phone')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Email</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" id="email" class="form-control" placeholder="Enter Email Address" value="{{$dataSet['email']}}" name="email" maxlength="40" required/>
                                        @error('email')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Address</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" id="address" class="form-control" placeholder="Enter Address" value="{{$dataSet['address']}}" name="address" maxlength="80" required/>
                                        @error('address')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-2">
                                        <label class="col-form-label" for="po_box_number">Po Box Number</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text"  value="{{$dataSet['po_box_number']}}" id="po_box_number" placeholder="Enter Po Box Number" class="form-control" name="po_box_number" maxlength="100"/>
                                        @error('po_box_number')
                                              <p class="text-danger">{{ $message }}</p>
                                          @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="password">Password</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="password" minlength="8" maxlength="10" id="password" placeholder="Min 8 Characters Password" class="form-control" name="confirm_password"/>
                                        @error('password')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="confirm_password">Confirm Password</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="password" minlength="8" maxlength="10" id="confirm_password" placeholder="Min 8 Characters Password" class="form-control" name="password"/>
                                        @error('confirm_password')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{-- <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-2">
                                        <label for="col-form-label">Return Service charges</label>
                                    </div>
                                    <div class="col-md-10">
                                        $ <input type="number" value="{{$custServiceCharges->return_service_charges}}" name="return_service_charges" class="_input_fields border-0">
                                    </div>
                                </div>
                            </div>
                            <hr> --}}
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-2">
                                        <label for="col-form-label">Service Charges</label>
                                    </div>
                                    <div class="col-md-10">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th style="width: 300px">Name</th>
                                                <th>
                                                    Price
                                                </th>
                                                <th>
                                                    <div class="form-check form-check-primary form-switch" style="float: right">
                                                        <input type="checkbox" name="default_service_charges" @isset($custServiceCharges) @if($custServiceCharges->default_service_charges != NULL) checked @endif @endisset value="1" class="form-check-input" id="set_default_service_charges"> Set Default Charges
                                                    </div>
                                                </th>
                                                {{-- <th>
                                                    <div class="form-check form-check-primary form-switch" style="float: right">
                                                        <input type="checkbox" name="pick_pack_flat_status" @isset($custServiceCharges) @if($custServiceCharges->pick_pack_flat_status != 0) checked @endif @endisset class="form-check-input" value="1" id="pick_pack_flat_btn"> Set Flat Pick/Pack
                                                    </div>
                                                </th> --}}
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Label</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->labels, 2)}}@else  0.00 @endisset" data-setting-price="{{$settingValues->labels}}" id="label_default" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="labels_cost" value="@isset($custServiceCharges){{number_format($custServiceCharges->labels, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_service_charges != NULL) readonly @endif @endisset></td>
                                                    <td></td>
                                                    {{-- <td></td> --}}
                                                </tr>
                                                <tr>
                                                    <td>Pick</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->pick, 2)}}@else  0.00 @endisset" data-setting-price="{{$settingValues->pick}}" id="pick_default" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="pick_cost"
                                                        <?php
                                                        $val = 0.00;
                                                        if(isset($custServiceCharges)) {
                                                            if($custServiceCharges->pick_pack_flat_status == 0) {
                                                                $val = number_format($custServiceCharges->pick, 2);
                                                            } else if($custServiceCharges->pick_pack_flat_status != 0) {
                                                                $val = number_format($custServiceCharges->pick_pack_flat, 2);
                                                            } else {
                                                                $val = 0.00;
                                                            }
                                                        }
                                                        ?>
                                                        value="@isset($custServiceCharges){{number_format($custServiceCharges->pick, 2)}}@else 0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_service_charges != NULL) readonly @endif @endisset></td>
                                                    <td>
                                                    </td>
                                                    {{-- <td></td> --}}
                                                </tr>
                                                <tr>
                                                    <td>Pack</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->pack, 2)}}@else  0.00 @endisset" data-setting-price="{{$settingValues->pack}}" id="pack_default" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="pack_cost"
                                                        <?php
                                                        $val = 0.00;
                                                        if(isset($custServiceCharges)) {
                                                            if($custServiceCharges->pick_pack_flat_status == 0) {
                                                                $val = number_format($custServiceCharges->pack, 2);
                                                            } else if($custServiceCharges->pick_pack_flat_status == 1) {
                                                                $val = number_format($custServiceCharges->pick_pack_flat, 2);
                                                            } else {
                                                                $val = 0.00;
                                                            }
                                                        }
                                                        ?>
                                                    value="@isset($custServiceCharges){{number_format($custServiceCharges->pack, 2)}}@else 0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_service_charges != NULL) readonly @endif @endisset></td>
                                                    <td>
                                                    </td>
                                                    {{-- <td></td> --}}
                                                </tr>
                                                <tr>
                                                    <td>Mailer</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->mailer, 2)}}@else  0.00 @endisset" data-setting-price="{{$settingValues->mailer}}" id="mailer_default" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="mailer_cost" value="@isset($custServiceCharges){{number_format($custServiceCharges->mailer, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_service_charges != NULL) readonly @endif @endisset></td>
                                                    <td></td>
                                                    {{-- <td></td> --}}
                                                </tr>
                                                {{-- <div class="col-12">
                                                    <div class="mb-1 row">
                                                        <div class="col-md-2">
                                                            <label for="col-form-label">Return Service charges</label>
                                                        </div>
                                                        <div class="col-md-10">
                                                            $ <input type="number" value="{{$custServiceCharges->return_service_charges}}" name="return_service_charges" class="_input_fields border-0">
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr> --}}
                                                <tr>
                                                    <td>Return Service Charges</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->return_service_charges, 2)}}@else  0.00 @endisset" data-setting-price="{{$settingValues->return_service_charges}}" id="return_service_charges_default" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="return_service_charges" value="@isset($custServiceCharges){{number_format($custServiceCharges->return_service_charges, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_service_charges != NULL) readonly @endif @endisset></td>
                                                    <td></td>
                                                    {{-- <td></td> --}}
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-2">
                                        <label for="col-form-label">Pick / Pack Flat Rate</label>
                                    </div>
                                    <div class="col-md-10">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th style="width: 300px">Pick / Pack </th>
                                                <th>
                                                    Price
                                                    <div class="form-check form-check-primary form-switch" style="float: right">
                                                        <input type="checkbox" name="default_pick_pack_status" @isset($custServiceCharges) @if($custServiceCharges->default_pick_pack_flat_status == 1) checked @endif @endisset class="form-check-input" value="1" id="default_pick_pack_rate"> Set Default Pick/Pack Flat Rate
                                                    </div>
                                                </th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Flat Rate</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->pick_pack_flat, 2)}}@else 0.00 @endisset" data-setting-price="{{$settingValues->pick_pack_flat}}" id="pick_pack_flat_input" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="pick_pack_flat" @isset($custServiceCharges) @if($custServiceCharges->default_pick_pack_flat_status == 1) readonly @endif @endisset
                                                    <?php
                                                        $defaultValue = 0.00;
                                                        if (isset($custServiceCharges)) {
                                                            if ($custServiceCharges->default_pick_pack_status == 1) {
                                                                $defaultValue = $settingValues->pick_pack_flat;
                                                            } else {
                                                                $defaultValue = $custServiceCharges->pick_pack_flat;
                                                            }
                                                        }
                                                    ?>
                                                    value="<?php echo number_format($defaultValue, 2); ?>" class="_input_fields border-0"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-7 offset-5 mb-2">
                                        <div class="form-check form-check-primary form-switch" style="float: right">
                                            <input type="checkbox" name="default_postage_charges" value="1" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) checked @endif @endisset  class="form-check-input" id="set_default_postage"> Set Default Postage
                                        </div>
                                        <div class="form-check form-check-primary form-switch mx-1" style="float: left">
                                            <input type="checkbox" name="discounted_default_postage_charges" value="1" @isset($custServiceCharges) @if($custServiceCharges->discounted_default_postage_charges != NULL) checked @endif @endisset  class="form-check-input" id="set_discounted_default_postage"> Enable Discounted Postage
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="col-form-label">Postage Costs</label>
                                    </div>
                                    <div class="col-md-10">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th style="width: 300px">Weight (Ounces)</th>
                                                <th style="width: 310px">Discounted Postage Cost</th>
                                                <th style="">
                                                    Price
                                                </th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1 - 4 oz</td>
                                                    <td>$ <input type="number" id="discounted_postage_default4" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_postage_cost_lt5, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->discounted_postage_cost_lt5 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="discounted_pc_1_4oz" value="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_postage_cost_lt5, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset></td>
                                                    
                                                    <td>$ <input type="number" id="postage_default4" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->postage_cost_lt5, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->postage_cost_lt5 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="pc_1_4oz" value="@isset($custServiceCharges){{number_format($custServiceCharges->postage_cost_lt5, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset></td>
                                                </tr>
                                                <tr>
                                                    <td>5 - 8 oz</td>
                                                    <td>$ <input type="number" id="discounted_postage_default8" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_postage_cost_lt9, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->discounted_postage_cost_lt9 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="discounted_pc_5_8oz" value="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_postage_cost_lt9, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset></td>
                                                    
                                                    <td>$ <input type="number" id="postage_default8" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->postage_cost_lt9, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->postage_cost_lt9 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="pc_5_8oz" value="@isset($custServiceCharges){{number_format($custServiceCharges->postage_cost_lt9, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset></td>
                                                </tr>
                                                <tr>
                                                    <td>9 - 12 oz</td>
                                                    <td>$ <input type="number" id="discounted_postage_default12" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_postage_cost_lt13, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->discounted_postage_cost_lt13 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="discounted_pc_9_12oz" value="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_postage_cost_lt13, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset></td>
                                                    
                                                    <td>$ <input type="number" id="postage_default12" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->postage_cost_lt13, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->postage_cost_lt13 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="pc_9_12oz" value="@isset($custServiceCharges){{number_format($custServiceCharges->postage_cost_lt13, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset></td>
                                                </tr>
                                                <tr>
                                                    <td>13 - 15.99 oz</td>
                                                    <td>$ <input type="number" id="discounted_postage_default16" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_postage_cost_gte13, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->discounted_postage_cost_gte13 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="discounted_pc_13_15oz" value="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_postage_cost_gte13, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset></td>
                                                    
                                                    <td>$ <input type="number" id="postage_default16" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->postage_cost_gte13, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->postage_cost_gte13 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="pc_13_15oz" value="@isset($custServiceCharges){{number_format($custServiceCharges->postage_cost_gte13, 2)}}@else  0.00 @endisset" class="_input_fields border-0" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset></td>
                                                </tr>
                                                <!-- LBS weights -->
                                                <tr>
                                                    <td>1 lbs</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_lbs1_1_99, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->discounted_lbs1_1_99 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="discounted_lbs1_1_99" value="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_lbs1_1_99, 2)}}@else 0.00 @endisset" id="discounted_lbs1_1_99" class="_input_fields border-0 lbs_rates"><small class="lbs_result"></small></td>
                                                    
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->lbs1_1_99, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->lbs1_1_99 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="lbs1_1_99" value="@isset($custServiceCharges){{number_format($custServiceCharges->lbs1_1_99, 2)}}@else 0.00 @endisset" id="lbs1_1_99" class="_input_fields border-0 lbs_rates" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset><small class="lbs_result"></small></td>
                                                </tr>
                                                <tr>
                                                    <td>1.01 - 2 lbs</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_lbs1_1_2, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->discounted_lbs1_1_2 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="discounted_lbs1_1_2" value="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_lbs1_1_2, 2)}}@else 0.00 @endisset" id="discounted_lbs1_1_2" class="_input_fields border-0 lbs_rates"><small class="lbs_result"></small></td>
                                                    
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->lbs1_1_2, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->lbs1_1_2 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="lbs1_1_2" value="@isset($custServiceCharges){{number_format($custServiceCharges->lbs1_1_2, 2)}}@else 0.00 @endisset" id="lbs1_1_2" class="_input_fields border-0 lbs_rates" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset><small class="lbs_result"></small></td>
                                                </tr>
                                                <tr>
                                                    <td>2.01 - 3 lbs</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_lbs2_1_3, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->discounted_lbs2_1_3 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="discounted_lbs2_1_3" value="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_lbs2_1_3, 2)}}@else 0.00 @endisset" id="discounted_lbs2_1_3" class="_input_fields border-0 lbs_rates"><small class="lbs_result"></small></td>
                                                    
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->lbs2_1_3, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->lbs2_1_3 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="lbs2_1_3" value="@isset($custServiceCharges){{number_format($custServiceCharges->lbs2_1_3, 2)}}@else 0.00 @endisset" id="lbs2_1_3" class="_input_fields border-0 lbs_rates" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset><small class="lbs_result"></small></td>
                                                </tr>
                                                <tr>
                                                    <td>3.01 - 4 lbs</td>
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_lbs3_1_4, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->discounted_lbs3_1_4 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="discounted_lbs3_1_4" value="@isset($custServiceCharges){{number_format($custServiceCharges->discounted_lbs3_1_4, 2)}}@else 0.00 @endisset" id="discounted_lbs3_1_4" class="_input_fields border-0 lbs_rates"><small class="lbs_result"></small></td>
                                                    
                                                    <td>$ <input type="number" data-input-price="" data-saved-price="@isset($custServiceCharges){{number_format($custServiceCharges->lbs3_1_4, 2)}}@else  0.00 @endisset" data-setting-price="{{ $settingValues->lbs3_1_4 }}" step="any" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required name="lbs3_1_4" value="@isset($custServiceCharges){{number_format($custServiceCharges->lbs3_1_4, 2)}}@else 0.00 @endisset" id="lbs3_1_4" class="_input_fields border-0 lbs_rates" @isset($custServiceCharges) @if($custServiceCharges->default_postage_charges != NULL) readonly @endif @endisset><small class="lbs_result"></small></td>
                                                </tr>
                                                <!-- LBS weights -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12 mb-1">
                                <div class="mb-1 row">
                                    <div class="col-md-2">
                                        <label for="col-form-label">Products</label>
                                    </div>
                                    <div class="col-md-10">
                                        <table class="table table-bordered table-striped" id="data-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%">Product</th>
                                                    <th style="width: 25%" class="text-center">Purchasing Cost</th>
                                                    <th style="width: 10%" class="text-center">Weight</th>
                                                    <th style="width: 20%" class="text-center">Selling Cost</th>
                                                    <th style="width: 20%" class="text-center">Seller Cost</th>
                                                    <th style="width: 10%" class="text-center">Label Status</th>
                                                    {{-- <th style="width: 20px" class="text-center">Labels</th> --}}
                                                    {{-- <th style="width: 200px" class="text-center">Service Charges</th> --}}
                                                    <th style="width: 20%" class="">Action</th>
                                                    {{-- <th style="width: 30px" class="moreHeader d-none">More</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody id="products_table">
                                                @foreach($customerProducts as $cust_prod)
                                                <tr>
                                                    <td>
                                                        @php
                                                            $product = App\AdminModels\Products::where('id', $cust_prod->product_id)->first();
                                                        @endphp
                                                        @if(isset($product))
                                                            {{$product->name}}
                                                        @endisset
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-bold text-success text-center">$ @isset($product){{number_format($product->price, 2)}}@endisset</div>
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-bold text-success text-center">@isset($product){{number_format($product->weight, 2)}}@endisset</div>
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-bold text-success text-center product_selling_price">
                                                            $ @if ($cust_prod->seller_cost_status == 1) {{ number_format($cust_prod->selling_price, 2) }} @else 0.00 @endif</div>
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-9">
                                                            <div class="form-check form-switch form-check-primary">
                                                                <input type="checkbox" @if($cust_prod->seller_cost_status == 1) checked @endif name="seller_cost_status" class="form-check-input sellerCostSwitch" data-labelPid="{{$cust_prod->product_id}}" style="font-size: 30px">
                                                                <label class="form-check-label">
                                                                    <span class="switch-icon-left" style="font-size: 9px; margin-top: 6px;">ON</span>
                                                                    <span class="switch-icon-right" style="font-size: 9px; margin-top: 6px; margin-left: -6px; color: black">OFF</span>
                                                                </label>
                                                            </div>
                                                            {{-- <div class="form-check form-check-primary form-switch">
                                                                <input type="checkbox" @if($cust_prod->seller_cost_status == 1) checked @endif name="seller_cost_status" class="form-check-input sellerCostSwitch" data-labelPid="{{$cust_prod->product_id}}">
                                                            </div> --}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="col-sm-9">
                                                            <div class="form-check form-check-primary form-switch">
                                                                <input type="checkbox" @if($cust_prod->is_active == 0) checked @endif name="is_active" class="form-check-input labelSwitch" data-labelPid="{{$cust_prod->product_id}}">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="{{$id}}" data-product_id="{{$cust_prod->product_id}}" data-bs-toggle="dropdown">
                                                            . . .
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item edit_product" href="#" @if($cust_prod->seller_cost_status == 0) style="cursor: not-allowed;" @else data-bs-toggle="modal" @endisset data-bs-target="#edit_product" data-customer_id="{{$id}}" data-product_id="{{$cust_prod->product_id}}" data-selling_price="{{ number_format($cust_prod->selling_price, 2) }}">
                                                                    <span>Edit Selling Price</span>
                                                                </a>
                                                                <a href="/delete_customer_prod/{{$id}}/{{$cust_prod->product_id}}" onclick="confirmDelete(event)" class="dropdown-item delete_product" data-customer_id="{{$id}}" data-bs-target="#delete_product_modal" data-product_id="{{$cust_prod->product_id}}" data-bs-toggle="modal">
                                                                    <span>Delete</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p>Add Products</p>
                                    </div>
                                    <div class="col-md-10">
                                        <form id="form_ID">
                                            <table class="table table-bordered table-striped products-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 30%">Product</th>
                                                        <th style="width: 15%" class="text-center">Purchasing Cost</th>
                                                        <th style="width: 10%" class="text-center">Weight</th>
                                                        <th style="width: 25%" class="text-center">Selling Cost</th>
                                                        <th style="width: 10%" class="text-center">Seller Cost</th>
                                                        <th style="width: 10%" class="text-center">Label Status</th>
                                                        <th style="width: 20%" class="">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="addProductTable">
                                                    <tr>
                                                        <td>
                                                            <select name="products[]" class="product form-select select2" id="product-list">
                                                                <option value="">Select Product</option>
                                                                @foreach ($products as $product)
                                                                    <option value="{{$product['prod_id']}}">{{$product['prod_name']}}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="product-error text-danger"></div>
                                                        </td>
                                                        <td>
                                                            <div class="purchasing-price font-weight-bold text-success text-center add_purchasing_cost">$ 0.00 </div>
                                                        </td>
                                                        <td>
                                                            <div class="total-weight font-weight-bold text-success text-center"><span class="product-weight">0.00 </span><span class="unit">oz</span></div>
                                                            <input type="hidden" name="" class="p-weight" value="">
                                                        </td>
                                                        <td>
                                                            <div class="input-icon">
                                                                <input type="number" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" name="selling_price[]" class="selling_price form-control add_selling_price" step="0.01" placeholder="Selling Cost" value="">
                                                                <i>$</i>
                                                            </div>
                                                            <div class="sellingMsg d-none" style="color: red; font-size: 12px">Should be greater than Purchasing Cost</div>
                                                        </td>
                                                        <td>
                                                            <div class="form-check form-switch form-check-primary">
                                                                <input type="checkbox" checked name="seller_cost_status[]" class="form-check-input addSellerCostSwitch" style="font-size: 30px">
                                                                <label class="form-check-label">
                                                                    <span class="switch-icon-left" style="font-size: 9px; margin-top: 6px">ON</span>
                                                                    <span class="switch-icon-right" style="font-size: 9px; margin-top: 6px; margin-left: -6px; color: black">OFF</span>
                                                                </label>
                                                            </div>
                                                            {{-- <div class="form-check-#{danger} form-check-primary form-switch switch-icon-left & switch-icon-right">
                                                                <input type="checkbox" name="seller_cost_status[]" checked class="form-check-input addSellerCostSwitch">
                                                            </div> --}}
                                                        </td>
                                                        <td>
                                                            <div class="col-sm-9">
                                                                <div class="form-check form-check-primary form-switch">
                                                                    <input type="checkbox" name="is_active[]" class="form-check-input labelSwitch">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button class="m-auto btn btn-primary shadow add_prod_btn" type="button">Add</button>
                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-1">
                                <div class="mb-1 row">
                                    <div class="col-sm-2">
                                        <label class="col-form-label" for="first-name">Status</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="form-check form-check-primary form-switch">
                                            <input type="checkbox" name="is_active" value="1" @if($dataSet['is_active'] == 1) checked="checked" @endif class="form-check-input" id="customSwitch3">
                                            <input type="hidden" value="{{$id}}" id="custId">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" id="submitbtn" class="btn btn-primary me-1">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
@section('modal')
    <div class="modal fade text-start" id="edit_product" tabindex="-1" aria-labelledby="edit_product_selling_price" style="" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit_product_selling_price">Selling Price</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <label>Selling Price: </label>
                        <div class="mb-1">
                            <input type="number" maxlength="4" onKeyDown="if(this.value.length==6 && event.keyCode!=8) return false;" required min="0" placeholder="Enter Selling Price" name="selling_price" class="form-control" id="modal_selling_price" data-product_id="" data-customer_id="">
                        </div>
                        <div class="selling_price_err text-danger d-none">Can't add negative value</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light" id="save_product_selling_price" data-bs-dismiss="">Save</button>
                        <button type="button" class="btn btn-dark waves-effect waves-float waves-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Basic toast -->
    <button class="btn btn-outline-primary toast-basic-toggler mt-2" id="toast-btn">ab</button>
    <div class="toast-container">
        <div class="toast basic-toast position-fixed top-0 end-0 m-2" role="alert" aria-live="" aria-atomic="true">
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
@section('datatablejs')
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
@endsection
@section('page_js')
<script src="{{asset('admin/app-assets/js/scripts/classie.js')}}"></script>
<script>
    $(document).ready(function() {
        $('input').text().trim();
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
        // Set Dicounted Postage Cost
        $(document).on('click', '#set_discounted_default_postage', function () {
            if ($(this).is(':checked')) {
                
            }
        });
        // Password Check
        $(document).on('keyup', '#confirm_password', function(e) {
            e.preventDefault();
            if($('#password').val() != $(this).val()) {
                $(this).css('border', '1px solid red');
                $('#submitbtn').attr('disabled', true);
            } else {
                $(this).css('border', '1px solid lightgreen');
                $('#submitbtn').attr('disabled', false);
            }
        });
        $(document).on('keyup', '#password', function(e) {
            e.preventDefault();
            if($('#confirm_password').val() != $(this).val()) {
                $('#confirm_password').css('border', '1px solid red');
                $('#submitbtn').attr('disabled', true);
            } else {
                $('#confirm_password').css('border', '1px solid green');
                $('#submitbtn').attr('disabled', false);
            }
        });
        // data table
        $('#data-table').DataTable({
            processing: true,
            // serverSide: true,
            // ordering: true,
            stateSave: true,
            searching: false,
            paging: false,
            bDestroy: true,
        });
        // Charges
        $(document).on('keyup', '#label_default', function() {
            $(this).attr('data-input-price', $(this).val());
        });
        $(document).on('keyup', '#pick_default', function() {
            $(this).attr('data-input-price', $(this).val());
        });
        $(document).on('keyup', '#pack_default', function() {
            $(this).attr('data-input-price', $(this).val());
        });
        $(document).on('keyup', '#mailer_default', function() {
            $(this).attr('data-input-price', $(this).val());
        });
        $(document).on('keyup', '#return_service_charges_default', function() {
            $(this).attr('data-input-price', $(this).val());
        });
        $(document).on('click', '#set_default_service_charges', function() {
            if($(this).is(':checked')) {
                var default_service_label = $('#label_default').attr('data-setting-price');
                var default_service_pick = $('#pick_default').attr('data-setting-price');
                var default_service_pack = $('#pack_default').attr('data-setting-price');
                var default_service_mailer = $('#mailer_default').attr('data-setting-price');
                var return_service_charges_default = $('#return_service_charges_default').attr('data-setting-price');
                $('#label_default').val(default_service_label);
                $('#pick_default').val(default_service_pick);
                $('#pack_default').val(default_service_pack);
                $('#mailer_default').val(default_service_mailer);
                $('#return_service_charges_default').val(return_service_charges_default);
                // disable inputs
                $('#label_default').attr('readonly', true);
                $('#pick_default').attr('readonly', true);
                $('#pack_default').attr('readonly', true);
                $('#mailer_default').attr('readonly', true);
                $('#return_service_charges_default').attr('readonly', true);
            } else {
                $('#label_default').attr('readonly', false);
                $('#pick_default').attr('readonly', false);
                $('#pack_default').attr('readonly', false);
                $('#mailer_default').attr('readonly', false);
                $('#return_service_charges_default').attr('readonly', false);
                var default_label_input = $('#label_default').attr('data-input-price');
                var default_label_price = $('#label_default').attr('data-saved-price');

                var default_pick_input = $('#pick_default').attr('data-input-price');
                var default_pick_price = $('#pick_default').attr('data-saved-price');

                var default_pack_input = $('#pack_default').attr('data-input-price');
                var default_pack_price = $('#pack_default').attr('data-saved-price');

                var default_mailer_input = $('#mailer_default').attr('data-input-price');
                var default_mailer_price = $('#mailer_default').attr('data-saved-price');

                var default_return_charges_input = $('#return_service_charges_default').attr('data-input-price');
                var default_return_charges_price = $('#return_service_charges_default').attr('data-saved-price');

                if(default_label_input != '' || default_label_input != 0) {
                    $('#label_default').val(default_label_price);
                } else if(default_label_price != '' || default_label_price != 0) {
                    $('#label_default').val(default_label_price);
                } else {
                    $('#label_default').val('0.00');
                }

                if(default_pick_input != '' || default_pick_input != 0) {
                    $('#pick_default').val(default_pick_price);
                } else if(default_pick_price != '' || default_pick_price != 0) {
                    $('#pick_default').val(default_pick_price);
                } else {
                    $('#pick_default').val('0.00');
                }

                if(default_pack_input != '' || default_pack_input != 0) {
                    $('#pack_default').val(default_pack_price);
                } else if(default_pack_price != '' || default_pack_price != 0) {
                    $('#pack_default').val(default_pack_price);
                } else {
                    $('#pack_default').val('0.00');
                }

                if(default_mailer_input != '' || default_mailer_input != 0) {
                    $('#mailer_default').val(default_mailer_price);
                } else if(default_mailer_price != '' || default_mailer_price != 0) {
                    $('#mailer_default').val(default_mailer_price);
                } else {
                    $('#mailer_default').val('0.00');
                }

                if(default_return_charges_input != '' || default_return_charges_input != 0) {
                    $('#return_service_charges_default').val(default_return_charges_price);
                } else if(default_return_charges_price != '' || default_return_charges_price != 0) {
                    $('#return_service_charges_default').val(default_return_charges_price);
                } else {
                    $('#return_service_charges_default').val('0.00');
                }
            }
        });
        $(document).on('change', '#default_pick_pack_rate', function() {
            if($(this).is(':checked')) {
                var defaultValue = $('#pick_pack_flat_input').data('setting-price');
                $('#pick_pack_flat_input').attr('readonly', true);
                if(defaultValue == null) {
                    defaultValue = 0.00;
                }
                $('#pick_pack_flat_input').val(defaultValue.toFixed(2));
            } else {
                var savePrice = $('#pick_pack_flat_input').data('saved-price');
                $('#pick_pack_flat_input').attr('readonly', false);
                if(savePrice == null) {
                    savePrice = 0.00;
                }
                $('#pick_pack_flat_input').val(savePrice);
            }
        });
        $(document).on('change', '#pick_pack_flat_btn', function() {
            var pickPackFlatRate = $('#pick_pack_flat_input').val();
            var savePickPrice = $('#pick_default').data('saved-price');
            var savePackPrice = $('#pack_default').data('saved-price');
            if($(this).is(':checked')) {
                $('#pick_default').val(pickPackFlatRate);
                $('#pack_default').val(pickPackFlatRate);
            } else {
                $('#pick_default').val(savePickPrice);
                $('#pack_default').val(savePackPrice);
            }
        });
        $(document).on('click', '#set_default_postage', function() {
            if($(this).is(':checked')) {
                var default_postage4 = $('#postage_default4').attr('data-setting-price');
                var default_postage8 = $('#postage_default8').attr('data-setting-price');
                var default_postage12 = $('#postage_default12').attr('data-setting-price');
                var default_postage16 = $('#postage_default16').attr('data-setting-price');
                var default_custom_postage = $('#postage_default_custom').attr('data-setting-price');
                //
                var discounted_default_postage4 = $('#discounted_postage_default4').attr('data-setting-price');
                var discounted_default_postage8 = $('#discounted_postage_default8').attr('data-setting-price');
                var discounted_default_postage12 = $('#discounted_postage_default12').attr('data-setting-price');
                var discounted_default_postage16 = $('#discounted_postage_default16').attr('data-setting-price');
                var discounted_default_custom_postage = $('#discounted_postage_default_custom').attr('data-setting-price');
                //

                // LBS
                
                var lbs1_1_99 = $('#lbs1_1_99').attr('data-setting-price');
                var lbs1_1_2 = $('#lbs1_1_2').attr('data-setting-price');
                var lbs2_1_3 = $('#lbs2_1_3').attr('data-setting-price');
                var lbs3_1_4 = $('#lbs3_1_4').attr('data-setting-price');
                //
                var discounted_lbs1_1_99 = $('#discounted_lbs1_1_99').attr('data-setting-price');
                var discounted_lbs1_1_2 = $('#discounted_lbs1_1_2').attr('data-setting-price');
                var discounted_lbs2_1_3 = $('#discounted_lbs2_1_3').attr('data-setting-price');
                var discounted_lbs3_1_4 = $('#discounted_lbs3_1_4').attr('data-setting-price');
                //
                // LBS

                $('#postage_default4').val(default_postage4);
                $('#postage_default8').val(default_postage8);
                $('#postage_default12').val(default_postage12);
                $('#postage_default16').val(default_postage16);
                //
                if ($('#set_discounted_default_postage').is(':checked')) {
                    $('#discounted_postage_default4').val(discounted_default_postage4);
                    $('#discounted_postage_default8').val(discounted_default_postage8);
                    $('#discounted_postage_default12').val(discounted_default_postage12);
                    $('#discounted_postage_default16').val(discounted_default_postage16);
                }
                //
                // LBS

                $('#lbs1_1_99').val(lbs1_1_99);
                $('#lbs1_1_2').val(lbs1_1_2);
                $('#lbs2_1_3').val(lbs2_1_3);
                $('#lbs3_1_4').val(lbs3_1_4);
                //
                if ($('#set_discounted_default_postage').is(':checked')) {
                    $('#discounted_lbs1_1_99').val(discounted_lbs1_1_99);
                    $('#discounted_lbs1_1_2').val(discounted_lbs1_1_2);
                    $('#discounted_lbs2_1_3').val(discounted_lbs2_1_3);
                    $('#discounted_lbs3_1_4').val(discounted_lbs3_1_4);
                }
                //
                // LBS

                $('#postage_default_custom').val(default_custom_postage);
                $('#discounted_postage_default_custom').val(discounted_default_custom_postage);
                // disable inputs
                $('#postage_default4').attr('readonly', true);
                $('#postage_default8').attr('readonly', true);
                $('#postage_default12').attr('readonly', true);
                $('#postage_default16').attr('readonly', true);
                //
                
                $('#discounted_postage_default4').attr('readonly', true);
                $('#discounted_postage_default8').attr('readonly', true);
                $('#discounted_postage_default12').attr('readonly', true);
                $('#discounted_postage_default16').attr('readonly', true);
                //

                //LBS
                $('#lbs1_1_99').attr('readonly', true);
                $('#lbs1_1_2').attr('readonly', true);
                $('#lbs2_1_3').attr('readonly', true);
                $('#lbs3_1_4').attr('readonly', true);
                //
                
                $('#discounted_lbs1_1_99').attr('readonly', true);
                $('#discounted_lbs1_1_2').attr('readonly', true);
                $('#discounted_lbs2_1_3').attr('readonly', true);
                $('#discounted_lbs3_1_4').attr('readonly', true);
                //

                //LBS
                $('#postage_default_custom').attr('readonly', true);
                $('#discounted_postage_default_custom').attr('readonly', true);
            } else {
                $('#postage_default4').attr('readonly', false);
                $('#postage_default8').attr('readonly', false);
                $('#postage_default12').attr('readonly', false);
                $('#postage_default16').attr('readonly', false);
                //
                $('#discounted_postage_default4').attr('readonly', false);
                $('#discounted_postage_default8').attr('readonly', false);
                $('#discounted_postage_default12').attr('readonly', false);
                $('#discounted_postage_default16').attr('readonly', false);
                //
                //LBS
                $('#lbs1_1_99').attr('readonly', false);
                $('#lbs1_1_2').attr('readonly', false);
                $('#lbs2_1_3').attr('readonly', false);
                $('#lbs3_1_4').attr('readonly', false);
                //
                
                $('#discounted_lbs1_1_99').attr('readonly', false);
                $('#discounted_lbs1_1_2').attr('readonly', false);
                $('#discounted_lbs2_1_3').attr('readonly', false);
                $('#discounted_lbs3_1_4').attr('readonly', false);

                //

                //LBS
                $('#postage_default_custom').attr('readonly', true);
                $('#discounted_postage_default_custom').attr('readonly', true);
                var default_label_input = $('#postage_default4').attr('data-input-price');
                var default_label_price = $('#postage_default4').attr('data-saved-price');

                var default_pick_input = $('#postage_default8').attr('data-input-price');
                var default_pick_price = $('#postage_default8').attr('data-saved-price');

                var default_pack_input = $('#postage_default12').attr('data-input-price');
                var default_pack_price = $('#postage_default12').attr('data-saved-price');

                var default_mailer_input = $('#postage_default16').attr('data-input-price');
                var default_mailer_price = $('#postage_default16').attr('data-saved-price');

                //

                
                var discounted_default_label_input = $('#discounted_postage_default4').attr('data-input-price');
                var discounted_default_label_price = $('#discounted_postage_default4').attr('data-saved-price');

                var discounted_default_pick_input = $('#discounted_postage_default8').attr('data-input-price');
                var discounted_default_pick_price = $('#discounted_postage_default8').attr('data-saved-price');

                var discounted_default_pack_input = $('#discounted_postage_default12').attr('data-input-price');
                var discounted_default_pack_price = $('#discounted_postage_default12').attr('data-saved-price');

                var discounted_default_mailer_input = $('#discounted_postage_default16').attr('data-input-price');
                var discounted_default_mailer_price = $('#discounted_postage_default16').attr('data-saved-price');

                //

                // LBS


                var default_lbs1_1_99_input = $('#lbs1_1_99').attr('data-input-price');
                var default_lbs1_1_99_price = $('#lbs1_1_99').attr('data-saved-price');

                var default_lbs1_1_2_input = $('#lbs1_1_2').attr('data-input-price');
                var default_lbs1_1_2_price = $('#lbs1_1_2').attr('data-saved-price');

                var default_lbs2_1_3_input = $('#lbs2_1_3').attr('data-input-price');
                var default_lbs2_1_3_price = $('#lbs2_1_3').attr('data-saved-price');

                var default_lbs3_1_4_input = $('#lbs3_1_4').attr('data-input-price');
                var default_lbs3_1_4_price = $('#lbs3_1_4').attr('data-saved-price');
                //

                var discounted_default_lbs1_1_99_input = $('#discounted_lbs1_1_99').attr('data-input-price');
                var discounted_default_lbs1_1_99_price = $('#discounted_lbs1_1_99').attr('data-saved-price');

                var discounted_default_lbs1_1_2_input = $('#discounted_lbs1_1_2').attr('data-input-price');
                var discounted_default_lbs1_1_2_price = $('#discounted_lbs1_1_2').attr('data-saved-price');

                var discounted_default_lbs2_1_3_input = $('#discounted_lbs2_1_3').attr('data-input-price');
                var discounted_default_lbs2_1_3_price = $('#discounted_lbs2_1_3').attr('data-saved-price');

                var discounted_default_lbs3_1_4_input = $('#discounted_lbs3_1_4').attr('data-input-price');
                var discounted_default_lbs3_1_4_price = $('#discounted_lbs3_1_4').attr('data-saved-price');

                //


                // LBS
                
                var default_custom_postage_input = $('#postage_default_custom').attr('data-input-price');
                var default_custom_postage_price = $('#postage_default_custom').attr('data-saved-price');
                //
                
                var discounted_default_custom_postage_input = $('#discounted_postage_default_custom').attr('data-input-price');
                var discounted_default_custom_postage_price = $('#discounted_postage_default_custom').attr('data-saved-price');

                //

                if(default_label_input != '' || default_label_input != 0) {
                    $('#postage_default4').val(default_label_price);
                } else if(default_label_price != '' || default_label_price != 0) {
                    $('#postage_default4').val(default_label_price);
                } else {
                    $('#postage_default4').val('0.00');
                }
                //
                if(discounted_default_label_input != '' || discounted_default_label_input != 0) {
                    $('#discounted_postage_default4').val(discounted_default_label_price);
                } else if(discounted_default_label_price != '' || discounted_default_label_price != 0) {
                    $('#discounted_postage_default4').val(discounted_default_label_price);
                } else {
                    $('#discounted_postage_default4').val('0.00');
                }
                //


                if(default_pick_input != '' || default_pick_input != 0) {
                    $('#postage_default8').val(default_pick_price);
                } else if(default_pick_price != '' || default_pick_price != 0) {
                    $('#postage_default8').val(default_pick_price);
                } else {
                    $('#postage_default8').val('0.00');
                }
                //
                if(discounted_default_pick_input != '' || discounted_default_pick_input != 0) {
                    $('#discounted_postage_default8').val(discounted_default_pick_price);
                } else if(discounted_default_pick_price != '' || discounted_default_pick_price != 0) {
                    $('#discounted_postage_default8').val(discounted_default_pick_price);
                } else {
                    $('#discounted_postage_default8').val('0.00');
                }
                //

                if(default_pack_input != '' || default_pack_input != 0) {
                    $('#postage_default12').val(default_pack_price);
                } else if(default_pack_price != '' || default_pack_price != 0) {
                    $('#postage_default12').val(default_pack_price);
                } else {
                    $('#postage_default12').val('0.00');
                }
                //
                if(discounted_default_pack_input != '' || discounted_default_pack_input != 0) {
                    $('#discounted_postage_default12').val(discounted_default_pack_price);
                } else if(discounted_default_pack_price != '' || discounted_default_pack_price != 0) {
                    $('#discounted_postage_default12').val(discounted_default_pack_price);
                } else {
                    $('#discounted_postage_default12').val('0.00');
                }
                //

                if(default_mailer_input != '' || default_mailer_input != 0) {
                    $('#postage_default16').val(default_mailer_price);
                } else if(default_mailer_price != '' || default_mailer_price != 0) {
                    $('#postage_default16').val(default_mailer_price);
                } else {
                    $('#postage_default16').val('0.00');
                }
                //
                if(discounted_default_mailer_input != '' || discounted_default_mailer_input != 0) {
                    $('#discounted_postage_default16').val(discounted_default_mailer_price);
                } else if(discounted_default_mailer_price != '' || discounted_default_mailer_price != 0) {
                    $('#discounted_postage_default16').val(discounted_default_mailer_price);
                } else {
                    $('#discounted_postage_default16').val('0.00');
                }
                //

                if(default_custom_postage_input != '' || default_custom_postage_input != 0) {
                    $('#postage_default_custom').val(default_custom_postage_price);
                } else if(default_custom_postage_price != '' || default_custom_postage_price != 0) {
                    $('#postage_default_custom').val(default_custom_postage_price);
                } else {
                    $('#postage_default_custom').val('0.00');
                }

                //

                
                if(discounted_default_custom_postage_input != '' || discounted_default_custom_postage_input != 0) {
                    $('#discounted_postage_default_custom').val(discounted_default_custom_postage_price);
                } else if(discounted_default_custom_postage_price != '' || discounted_default_custom_postage_price != 0) {
                    $('#discounted_postage_default_custom').val(discounted_default_custom_postage_price);
                } else {
                    $('#discounted_postage_default_custom').val('0.00');
                }

                //
                // LBS
                if(default_lbs1_1_99_input != '' || default_lbs1_1_99_input != 0) {
                    $('#lbs1_1_99').val(default_lbs1_1_99_price);
                } else if(default_lbs1_1_99_price != '' || default_lbs1_1_99_price != 0) {
                    $('#lbs1_1_99').val(default_lbs1_1_99_price);
                } else {
                    $('#lbs1_1_99').val('0.00');
                }
                //
                
                if(discounted_default_lbs1_1_99_input != '' || discounted_default_lbs1_1_99_input != 0) {
                    $('#discounted_lbs1_1_99').val(discounted_default_lbs1_1_99_price);
                } else if(discounted_default_lbs1_1_99_price != '' || discounted_default_lbs1_1_99_price != 0) {
                    $('#discounted_lbs1_1_99').val(discounted_default_lbs1_1_99_price);
                } else {
                    $('#discounted_lbs1_1_99').val('0.00');
                }
                //
                
                if(default_lbs1_1_2_input != '' || default_lbs1_1_2_input != 0) {
                    $('#lbs1_1_2').val(default_lbs1_1_2_price);
                } else if(default_lbs1_1_2_price != '' || default_lbs1_1_2_price != 0) {
                    $('#lbs1_1_2').val(default_lbs1_1_2_price);
                } else {
                    $('#lbs1_1_2').val('0.00');
                }
                //
                
                if(discounted_default_lbs1_1_2_input != '' || discounted_default_lbs1_1_2_input != 0) {
                    $('#discounted_lbs1_1_2').val(discounted_default_lbs1_1_2_price);
                } else if(discounted_default_lbs1_1_2_price != '' || discounted_default_lbs1_1_2_price != 0) {
                    $('#discounted_lbs1_1_2').val(discounted_default_lbs1_1_2_price);
                } else {
                    $('#discounted_lbs1_1_2').val('0.00');
                }
                //
                
                if(default_lbs2_1_3_input != '' || default_lbs2_1_3_input != 0) {
                    $('#lbs2_1_3').val(default_lbs2_1_3_price);
                } else if(default_lbs2_1_3_price != '' || default_lbs2_1_3_price != 0) {
                    $('#lbs2_1_3').val(default_lbs2_1_3_price);
                } else {
                    $('#lbs2_1_3').val('0.00');
                }
                //
                
                if(discounted_default_lbs2_1_3_input != '' || discounted_default_lbs2_1_3_input != 0) {
                    $('#discounted_lbs2_1_3').val(discounted_default_lbs2_1_3_price);
                } else if(discounted_default_lbs2_1_3_price != '' || discounted_default_lbs2_1_3_price != 0) {
                    $('#discounted_lbs2_1_3').val(discounted_default_lbs2_1_3_price);
                } else {
                    $('#discounted_lbs2_1_3').val('0.00');
                }

                //
                
                if(default_lbs3_1_4_input != '' || default_lbs3_1_4_input != 0) {
                    $('#lbs3_1_4').val(default_lbs3_1_4_price);
                } else if(default_lbs3_1_4_price != '' || default_lbs3_1_4_price != 0) {
                    $('#lbs3_1_4').val(default_lbs3_1_4_price);
                } else {
                    $('#lbs3_1_4').val('0.00');
                }
                //
                
                if(discounted_default_lbs3_1_4_input != '' || discounted_default_lbs3_1_4_input != 0) {
                    $('#discounted_lbs3_1_4').val(discounted_default_lbs3_1_4_price);
                } else if(discounted_default_lbs3_1_4_price != '' || discounted_default_lbs3_1_4_price != 0) {
                    $('#discounted_lbs3_1_4').val(discounted_default_lbs3_1_4_price);
                } else {
                    $('#discounted_lbs3_1_4').val('0.00');
                }
                //
            }
        });
        $(document).on('click', '.edit_product', function() {
            var customer_id = $(this).attr('data-customer_id');
            var product_id = $(this).attr('data-product_id');
            var selling_price = $(this).attr('data-selling_price');
            $('#modal_selling_price').attr('data-product_id', product_id);
            $('#modal_selling_price').attr('data-customer_id', customer_id);
            $('#modal_selling_price').val(selling_price);
            $('.selling_price_err').addClass('d-none');
        });
        $(document).on('click', '#save_product_selling_price', function(e) {
            e.preventDefault();
            var selling_price = $('#modal_selling_price').val();
            var customer_id = $('#modal_selling_price').attr('data-customer_id');
            var product_id = $('#modal_selling_price').attr('data-product_id');
            if (Number(selling_price) < 0) {
                $('.selling_price_err').removeClass('d-none');
                return false;
            } else {
                $.ajax({
                    url: '{{ route("update_customer_prod_selling_price") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        selling_price: selling_price,
                        customer_id: customer_id,
                        product_id: product_id
                    },
                    success:function(response) {
                        // location.reload();
                        if (response.success == true) {
                            $('#edit_product').modal('hide');
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                        } else if(response.error == true) {
                            $('#edit_product').modal('hide');
                            $('.toast .me-auto').html('Error');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                            return false;
                        }
                        window.setTimeout(
                            function(){
                                location.reload(true);
                            },
                            500
                        );
                    }
                });
            }
        });
        // Labels / Label charges
        $(document).on('click', '.add-labels', function() {
            var product_id = $(this).data('product-id');
            alert(product_id);
            var brand_id = $(this).data('brand-id');
            $('#add-labels').data('product-id', product_id);
            $('#add-labels').data('brand-id', brand_id);
        });
        $(document).on('click', '.reduce-labels', function() {
            var product_id = $(this).data('product-id');
            var brand_id = $(this).data('brand-id');
            $('#reduce-labels').data('product-id', product_id);
            $('#reduce-labels').data('brand-id', brand_id);
        });
        $(document).on('click', '#reduce-labels', function() {
            var customer_id = $('#custId').val();
            var brand_id = $(this).data('brand-id');
            var product_id = $(this).data('product-id');
            var qty = $('#reduceqtyPrice').val();
            var url = '{{ route("reduce_label_to_product") }}';
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'customer_id': customer_id,
                    'brand_id': brand_id,
                    'product_id': product_id,
                    'qty': qty,
                },
                success:function(response) {
                    window.location.reload();
                }
            });
        });
        $(document).on('click', '#add-labels', function() {
            var customer_id = $('#custId').val();
            var brand_id = $(this).data('brand-id');
            var product_id = $(this).data('product-id');
            var qty = $('#qtyPrice').val();
            var url = '{{ route("add_label_to_product") }}';
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'customer_id': customer_id,
                    'brand_id': brand_id,
                    'product_id': product_id,
                    'qty': qty,
                },
                success:function(response) {
                    window.location.reload();
                }
            });
        });
        $(document).on('click', '.add-label-cost', function() {
            var product_id = $(this).data('product-id');
            var brand_id = $(this).data('brand-id');
            $('#add-label-cost').data('product-id', product_id);
            $('#add-label-cost').data('brand-id', brand_id);
        });
        $(document).on('click', '#add-label-cost', function() {
            var customer_id = $('#custId').val();
            var brand_id = $(this).data('brand-id');
            var product_id = $(this).data('product-id');
            var label_cost = $('#labelCost').val();
            var url = '{{ route("add_label_cost_to_product") }}';
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'customer_id': customer_id,
                    'brand_id': brand_id,
                    'product_id': product_id,
                    'label_cost': label_cost,
                },
                success:function(response) {
                    window.location.reload();
                }
            });
        });
        // Select Product to Add
        $(document).on('change', '.product', function() {
            var id = $(this).val();
            var context = $(this);
            var customer_id = $('#custId').val();
            var brand_id = $('#brand').val();
            var url = '{{ route("get_product_labels", ":id") }}';
            var url2 = '{{ route("getProductstoCustomer") }}';
            url2 = url2.replace(':id', id);
            url = url.replace(':id', id);
            if(id=="") {

            } else {
                $.ajax({
                    url: url2,
                    type: 'GET',
                    data: {
                        _token: '{{csrf_token()}}',
                        'product_id': id
                    },
                    success:function(res) {
                        if(res) {
                            context.closest('tr').find('.purchasing-price').html('$ '+Number(res.price).toFixed(2));
                            context.closest('tr').find('.selling_price').val('0.00');
                            context.closest('tr').find('.product-weight').html(Number(res.weight).toFixed(2));
                            context.closest('tr').find('.p-weight').val(Number(res.weight).toFixed(2));
                        } else {
                            $('.product-error').html(res.message);
                        }
                    }
                });
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        _token: '{{csrf_token()}}',
                        'customer_id': customer_id,
                        'brand_id': brand_id,
                    },
                    success:function(response) {
                        if (response > 0) {
                            context.closest('tr').find('.label_qty').html(response);
                            context.closest('tr').find('.label-qty-val').val(response);
                        } else{
                            context.closest('tr').find('.label_qty').html(response);
                            context.closest('tr').find('.label-qty-val').val(0);
                        }
                    }
                });
            }
        });
        $(document).on('click', '.more_btn', function() {
            var customer_id = $('#custId').val();
            var brand_id = $(this).closest('tr').find('.brandId').val();
            var product_id = $(this).closest('tr').find('.productId').val();
            $(this).data('product-id', product_id);
            $('.add-labels').data('product-id', product_id);
            $('.reduce-labels').data('product-id', product_id);
            $('.add-label-cost').data('product-id', product_id);
            $('.history_more_btn').data('product-id', product_id);
            $('.history_more_btn').attr('href', '/customer/'+customer_id+'/brand/'+brand_id+'/product/'+product_id+'/labels');
            $('.forecast_more_btn').data('product-id', product_id);
            $('.forecast_more_btn').attr('href', '/customer/'+customer_id+'/brand/'+brand_id+'/product/'+product_id+'/labelforecast');
        });
        $(document).on('change', '.sellerCostSwitch', function() { // Save Status
            var custId = $('#custId').val();
            // var brandId = $(this).data('brand_id');
            var prodId = $(this).data('labelpid');
            var status = 1;
            if ($(this).prop('checked') == true) {
                status = 1;
            } else {
                status = 0;
            }
            $.ajax({
                url: '{{ route("set_seller_cost_status") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    customer_id: custId,
                    // brand_id: brandId,
                    product_id: prodId,
                    status: status
                },
                success:function(response) {
                    if (response.success == true) {
                        $('#edit_product').modal('hide');
                        $('.toast .me-auto').html('Success');
                        $('.toast .toast-header').addClass('bg-success');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                    } else if(response.error == true) {
                        $('#edit_product').modal('hide');
                        $('.toast .me-auto').html('Error');
                        $('.toast .toast-header').addClass('bg-danger');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                        return false;
                    }
                    // window.setTimeout(
                    //     function(){
                    //         location.reload(true);
                    //     },
                    //     500
                    // );
                }
            });
        });
        // Switches / Buttons to Add Product
        $(document).on('keyup', '.selling_price', function() {
            var purchasing_cost = $(this).closest('tr').find('.purchasing-price').html();
            purchasing_cost = purchasing_cost.substring(1);
            purchasing_cost = purchasing_cost.trim(purchasing_cost);
            if ($(this).closest('tr').find('.addSellerCostSwitch').prop('checked') == true) {
                // if (Number($(this).val()) <= Number(purchasing_cost)) {
                //     $(this).closest('tr').find('.selling_price').css('border', '1px solid red');
                //     $(this).closest('tr').find('.sellingMsg').removeClass('d-none');
                //     count = 0;
                //     // return false;
                // } else {
                //     $(this).closest('tr').find('.selling_price').css('border', '1px solid lightgray');
                //     $(this).closest('tr').find('.sellingMsg').addClass('d-none');
                // }
            }
        });
        $(document).on('change', '.addSellerCostSwitch', function() {
            var _this = $(this);
            var purchasingCost = _this.closest('tr').find('.purchasing-price').html();
            purchasingCost = purchasingCost.substring(1);
            purchasingCost = purchasingCost.trim(purchasingCost);
            var sellingPrice = _this.closest('tr').find('.add_selling_price').val();
            if (_this.prop('checked') == true) {
                _this.closest('tr').find('.add_selling_price').attr('disabled', false);
                // if (Number(sellingPrice) <= Number(purchasingCost)) {
                //     _this.closest('tr').find('.add_selling_price').css('border', '1px solid red');
                //     _this.closest('tr').find('.sellingMsg').removeClass('d-none');
                // } else {
                //     _this.closest('tr').find('.add_selling_price').css('border', '1px solid lightgray');
                //     _this.closest('tr').find('.sellingMsg').addClass('d-none');
                // }
            } else {
                _this.closest('tr').find('.add_selling_price').attr('disabled', true);
                _this.closest('tr').find('.add_selling_price').val('0.00');
                _this.closest('tr').find('.add_selling_price').css('border', '1px solid lightgray');
                _this.closest('tr').find('.sellingMsg').addClass('d-none');
            }
        });
        $(document).on('change', '.labelSwitch', function() {
            var custId = $('#custId').val();
            // var brandId = $(this).data('brand_id');
            var prodId = $(this).data('labelpid');
            var status = 1;
            if ($(this).prop('checked') == true) {
                status = 0;
            } else {
                status = 1;
            }
            $.ajax({
                url: '{{ route("set_label_status") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    customer_id: custId,
                    // brand_id: brandId,
                    product_id: prodId,
                    status: status
                },
                success:function(response) {
                    if (response.success == true) {
                        $('#edit_product').modal('hide');
                        $('.toast .me-auto').html('Success');
                        $('.toast .toast-header').addClass('bg-success');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                    } else if(response.error == true) {
                        $('#edit_product').modal('hide');
                        $('.toast .me-auto').html('Error');
                        $('.toast .toast-header').addClass('bg-danger');
                        $('.toast .text-muted').html('Now');
                        $('.toast .toast-body').html(response.message);
                        $('#toast-btn').click();
                        return false;
                    }
                    // window.setTimeout(
                    //     function(){
                    //         location.reload(true);
                    //     },
                    //     500
                    // );
                }
            });
            // else {
            //     status = 1;
            //     $.ajax({
            //         url: '{{ route("set_label_status") }}',
            //         type: 'POST',
            //         data: {
            //             _token: '{{ csrf_token() }}',
            //             customer_id: custId,
            //             // brand_id: brandId,
            //             product_id: prodId,
            //             status: status
            //         },
            //         success:function(response) {

            //         }
            //     });
            // }
        });
        $(document).on('click', '.add_prod_btn', function(e) {
            e.preventDefault();
            var _this = $(this);
            var checkProd = 1;
            $('.product').each(function() {
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).closest('tr').find('.product-error').html('Required');
                    checkProd = 0;
                    return false;
                } else {
                    $(this).closest('tr').find('.product-error').html('');
                }
            });
            if (checkProd == 0) {
                return false;
            }
            var count = 1;
            var count2 = 1;
            $('.purchasing-price').each(function() {
                var This = $(this);
                var selling_cost = This.closest('tr').find('.selling_price').val();
                var purchasing_cost = This.html();
                purchasing_cost = purchasing_cost.substring(1);
                purchasing_cost = purchasing_cost.trim(purchasing_cost);
                if (This.closest('tr').find('.addSellerCostSwitch').prop('checked') == true) {
                    // if (Number(selling_cost) <= Number(purchasing_cost)) {
                    //     This.closest('tr').find('.selling_price').css('border', '1px solid red');
                    //     This.closest('tr').find('.sellingMsg').removeClass('d-none');
                    //     count = 0;
                    //     // return false;
                    // } else {
                    //     This.closest('tr').find('.selling_price').css('border', '1px solid lightgray');
                    //     This.closest('tr').find('.sellingMsg').addClass('d-none');
                    // }
                }
            });
            if (count == 1) {
                var customer_id = $('#custId').val();
                var prodIds = [];
                var labelsStatuses = [];
                var sellerCostStatuses = [];
                var selling_costs = [];
                var labelStatus = 1;
                var sellerCostStatus = 1;
                var html = '';
                var url = '{{ route("save_customer_product", ":id") }}';
                url = url.replace(':id', customer_id);
                $('.product').each(function() {
                    prodIds.push($(this).val());
                    if ($(this).closest('tr').find('.labelSwitch').prop('checked') == true) {
                        labelStatus = 0;
                    } else {
                        labelStatus = 1;
                    }
                    if ($(this).closest('tr').find('.addSellerCostSwitch').prop('checked') == true) {
                        sellerCostStatus = 1;
                    } else {
                        sellerCostStatus = 0;
                    }
                    labelsStatuses.push(labelStatus);
                    sellerCostStatuses.push(sellerCostStatus);
                    if ($(this).closest('tr').find('.sellerCostStatus').prop('checked') == false) {
                        selling_costs.push('0');
                    } else {
                        selling_costs.push($(this).closest('tr').find('.selling_price').val());
                    }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_id: customer_id,
                        prod_ids : prodIds,
                        labelStatus: labelsStatuses,
                        sellerCostStatus: sellerCostStatuses,
                        selling_cost: selling_costs
                    },
                    success:function(response) {
                        if (response.success == true) {
                            html += `
                                <tr>
                                    <td>`+response.data.product_name+`</td>
                                    <td>
                                        <div class="font-weight-bold text-success text-center">$ `+response.data.purchasing_cost+` </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-success text-center">`+response.data.weight+` oz</div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-success text-center"> $ `+response.data.selling_price+`</div>
                                    </td>
                                    <td>
                                        <div class="col-sm-9">
                                            <div class="form-check form-switch form-check-primary">
                                                <input type="checkbox"`;
                                                if(response.data.seller_cost_status == 1) {
                                                    html += ` checked `;
                                                }
                                                html += `name="selling_cost_status" class="form-check-input sellerCostSwitch" data-labelPid="`+prodIds[0]+`" style="font-size: 30px">
                                                <label class="form-check-label">
                                                    <span class="switch-icon-left" style="font-size: 9px; margin-top: 6px">ON</span>
                                                    <span class="switch-icon-right" style="font-size: 9px; margin-top: 6px; margin-left: -6px; color: black">OFF</span>
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col-sm-9">
                                            <div class="form-check form-check-primary form-switch">
                                                <input type="checkbox"`;
                                                if(response.data.is_active == 0) {
                                                    html += ` checked `;
                                                }
                                                html += `name="is_active" class="form-check-input labelSwitch" data-labelPid="`+prodIds[0]+`">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow more_btn" data-customer_id="`+customer_id+`" data-product_id="`+prodIds[0]+`" data-bs-toggle="dropdown">
                                            . . .
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item edit_product" href="#" data-bs-target="#edit_product" data-customer_id="`+customer_id+`" data-product_id="`+prodIds[0]+`" data-selling_price="`+response.data.selling_price+`"`;
                                                if (response.data.seller_cost_status == 0) {
                                                    html += `style="cursor: not-allowed"`;
                                                } else {
                                                    html += 'data-bs-toggle="modal"';
                                                }
                                                html += `>
                                                    <span>Edit Selling Price</span>
                                                </a>
                                                <a href="/delete_customer_prod/`+customer_id+`/`+prodIds[0]+`" onclick="confirmDelete(event)" class="dropdown-item">Delete Product</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            $('#products_table').append(html);
                            $('.product').each(function(){
                                $(this).find('option:selected').remove();
                                $(this).closest('tr').find('.purchasing-price').html('$ 0.00');
                                $(this).closest('tr').find('.product-weight').html('0.00');
                                $(this).closest('tr').find('.selling_price').val('');
                                $(this).closest('tr').find('.labelSwitch').prop('checked', false);
                                $(this).closest('tr').find('.addSellerCostSwitch').prop('checked', true);
                                $(this).closest('tr').find('.selling_price').attr('disabled', false);
                            });
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                        } else if (response.error == true) {
                            $('.toast .me-auto').html('Error');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.message);
                            $('#toast-btn').click();
                        }
                    }
                });
            }
        });
        // Old
        var count = 1;
        $(document).on('click', '.add-product-row', function() {
            var html = `
                <tr>
                    <td>
                        <select name="products[]" class="product form-select select2" id="product-list" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{$product['prod_id']}}">{{$product['prod_name']}}</option>
                            @endforeach
                        </select>
                        <div class="product-error text-danger"></div>
                    </td>
                    <td>
                        <div class="col-sm-9">
                            <div class="form-check form-check-primary form-switch">
                                <input type="checkbox" name="is_active[]" class="form-check-input labelSwitch">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="purchasing-price font-weight-bold text-success text-center">$ 0.00 </div>
                    </td>
                    <td>
                        <div class="total-weight font-weight-bold text-success text-center"><span class="product-weight">0.00</span><span class="unit">oz</span></div>
                        <input type="hidden" name="" class="p-weight" value="">
                    </td>
                    <td>
                        <div class="input-icon">
                            <input type="number" name="selling_price[]" class="selling_price form-control" step="0.01" placeholder="Selling Cost" value="" min="0.01">
                            <i>$</i>
                        </div>
                        <div class="sellingMsg d-none" style="color: red; font-size: 12px">Should be greater than Purchasing Cost</div>
                    </td>
                    <td>
                        <button class="border-0 rounded-circle shadow btn-danger remove-product-row" type="button"><i data-feather="minus"></i></button>
                    </td>

                </tr>

            `;
            count++;
            $('table tbody#addProductTable').append(html);
        });
        $(document).on('click', '.remove-product-row', function() {
            $(this).closest('tr').remove();
        });
        $(document).on('click', '.EditPencil', function() {
            var skuid = $(this).data('skuid');
            $(this).closest('tr').find('#SkuDiv'+skuid).toggle(
                function() {
                    $('#SkuDiv'+skuid).addClass('d-none');
                },
                function() {
                    $('#SkuDiv'+skuid).removeClass('d-none');
                }
            );
        });
        $(document).on('keyup', '.lbs_rates', function() {
            var _this = $(this);
            var res = _this.val() * Number(16);
            // _this.closest('tr').find('.lbs_result').html((res.toFixed(2))+'oz');
        });
    });
    (function() {
        // trim polyfill : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
        if (!String.prototype.trim) {
            (function() {
                // Make sure we trim BOM and NBSP
                var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
                String.prototype.trim = function() {
                    return this.replace(rtrim, '');
                };
            })();
        }
        [].slice.call( document.querySelectorAll( 'input.input__field' ) ).forEach( function( inputEl ) {
            // in case the input is already filled..
            if( inputEl.value.trim() !== '' ) {
                classie.add( inputEl.parentNode, 'input--filled' );
            }

            // events:
            inputEl.addEventListener( 'focus', onInputFocus );
            inputEl.addEventListener( 'blur', onInputBlur );
        } );

        function onInputFocus( ev ) {
            classie.add( ev.target.parentNode, 'input--filled' );
        }

        function onInputBlur( ev ) {
            if( ev.target.value.trim() === '' ) {
                classie.remove( ev.target.parentNode, 'input--filled' );
            }
        }
    })();
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
</script>
@endsection
