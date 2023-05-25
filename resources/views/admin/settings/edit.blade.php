@extends('admin.layout.app')
@section('title', 'Settings')
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
                            <h2 class="content-header-title float-start mb-0">Settings</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Settings
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
                        
                        <div class="card-body">
                            <form class="form form-horizontal" enctype='multipart/form-data' action="{{route('settings.update')}}" method="post">
                                {{@csrf_field()}}
                            {{-- <h4>Forecast Orders (Days)</h4> --}}
                            {{-- <hr> --}}
                            {{-- <div class="row">
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="1" min="1" value="@isset($settings){{$settings->forecast_days}}@endisset" name="forecast_days" id="forecast_days" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="forecast_days">
                                                            <span class="input__label-content input__label-content--hoshi">Forecast Orders (Days) <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="1" min="1" value="@isset($settings){{$settings->threshold_val}}@endisset" name="threshold_val" id="threshold_val" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="threshold_val">
                                                            <span class="input__label-content input__label-content--hoshi">Threshold <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-3">
                                        <label for="col-form-label">Product and Label Forecast Settings</label>
                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th style="width: 300px">Name</th>
                                                <th>
                                                    Days
                                                </th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Forecast Orders (Days)</td>
                                                    <td><input type="number" class="_input_fields border-0" step="1" value="@isset($settings){{$settings->forecast_days}}@endisset" name="forecast_days" id="forecast_days" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Alert Days</td>
                                                    <td><input type="number" class="_input_fields border-0" step="1" value="@isset($settings){{$settings->threshold_val}}@endisset" name="threshold_val" id="threshold_val" required></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{-- <hr> --}}
                            
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-3">
                                        <label for="col-form-label">Service Charges</label>
                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th style="width: 300px">Name</th>
                                                <th>
                                                    Price
                                                </th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Label</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" value="@isset($settings){{number_format($settings->labels, 2)}}@endisset" name="labels" id="labels" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Pick</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->pick, 2)}}@endisset" name="pick" id="pick" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Pack</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->pack, 2)}}@endisset" name="pack" id="pack" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Mailer</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->mailer, 2)}}@endisset" name="mailer" id="mailer" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Return Service Charges</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->return_service_charges, 2)}}@endisset" name="return_service_charges" id="return_service_charges" required></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-3">
                                        <label for="col-form-label">Pick / Pack Flat Rate</label>
                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th style="width: 300px">Pick / Pack</th>
                                                <th>
                                                    Price
                                                </th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Flat Rate</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" value="@isset($settings){{number_format($settings->pick_pack_flat, 2)}}@endisset" name="pick_pack_flat" id="pick_pack_flat" required></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-3">
                                        <label for="col-form-label">Postage Costs</label>
                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th style="width: 300px">Weight (Ounces)</th>
                                                <th>Discounted Postage Cost</th>
                                                <th>
                                                    Price
                                                </th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1 - 4 oz</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->discounted_postage_cost_lt5, 2)}}@endisset" name="discounted_postage_cost_lt5" id="discounted_postage_cost_lt5" class="input__field input__field--hoshi postage_price" required></td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->postage_cost_lt5, 2)}}@endisset" name="postage_cost_lt5" id="postage_cost_lt5" class="input__field input__field--hoshi postage_price" required></td>
                                                </tr>
                                                <tr>
                                                    <td>5 - 8 oz</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->discounted_postage_cost_lt9, 2)}}@endisset" name="discounted_postage_cost_lt9" id="discounted_postage_cost_lt9" class="input__field input__field--hoshi postage_price" required></td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->postage_cost_lt9, 2)}}@endisset" name="postage_cost_lt9" id="postage_cost_lt9" class="input__field input__field--hoshi postage_price" required></td>
                                                </tr>
                                                <tr>
                                                    <td>9 - 12 oz</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->discounted_postage_cost_lt13, 2)}}@endisset" name="discounted_postage_cost_lt13" id="discounted_postage_cost_lt13" class="input__field input__field--hoshi postage_price" required></td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->postage_cost_lt13, 2)}}@endisset" name="postage_cost_lt13" id="postage_cost_lt13" class="input__field input__field--hoshi postage_price" required></td>
                                                </tr>
                                                <tr>
                                                    <td>13 - 15.99 oz</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->discounted_postage_cost_gte13, 2)}}@endisset" name="discounted_postage_cost_gte13" id="discounted_postage_cost_gte13" class="input__field input__field--hoshi postage_price" required></td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->postage_cost_gte13, 2)}}@endisset" name="postage_cost_gte13" id="postage_cost_gte13" class="input__field input__field--hoshi postage_price" required></td>
                                                </tr>
                                                <!-- LBS -->
                                                <tr>
                                                    <td>1 lbs</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->discounted_lbs1_1_99, 2)}}@endisset" name="discounted_lbs1_1_99" id="discounted_lbs1_1_99" class="input__field input__field--hoshi discounted_lbs1_1_99" required></td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->lbs1_1_99, 2)}}@endisset" name="lbs1_1_99" id="lbs1_1_99" class="input__field input__field--hoshi lbs1_1_99" required></td>
                                                </tr>
                                                <tr>
                                                    <td>1.01 - 2 lbs</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->discounted_lbs1_1_2, 2)}}@endisset" name="discounted_lbs1_1_2" id="discounted_lbs1_1_2" class="input__field input__field--hoshi discounted_lbs1_1_2" required></td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->lbs1_1_2, 2)}}@endisset" name="lbs1_1_2" id="lbs1_1_2" class="input__field input__field--hoshi lbs1_1_2" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.01 - 3 lbs</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->discounted_lbs2_1_3, 2)}}@endisset" name="discounted_lbs2_1_3" id="discounted_lbs2_1_3" class="input__field input__field--hoshi discounted_lbs2_1_3" required></td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->lbs2_1_3, 2)}}@endisset" name="lbs2_1_3" id="lbs2_1_3" class="input__field input__field--hoshi lbs2_1_3" required></td>
                                                </tr>
                                                <tr>
                                                    <td>3.01 - 4 lbs</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->discounted_lbs3_1_4, 2)}}@endisset" name="discounted_lbs3_1_4" id="discounted_lbs3_1_4" class="input__field input__field--hoshi discounted_lbs3_1_4" required></td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->lbs3_1_4, 2)}}@endisset" name="lbs3_1_4" id="lbs3_1_4" class="input__field input__field--hoshi lbs3_1_4" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Custom Postage</td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->discounted_postage_cost, 2)}}@endisset" name="discounted_postage_cost" id="discounted_postage_cost" class="input__field input__field--hoshi discounted_postage_price" required></td>
                                                    <td>$ <input type="number" class="_input_fields border-0" step="0.01" min="0.01" value="@isset($settings){{number_format($settings->postage_cost, 2)}}@endisset" name="postage_cost" id="postage_cost" class="input__field input__field--hoshi postage_price" required></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12">
                                <div class="mb-1 row">
                                    <div class="col-md-3">
                                        <label for="col-form-label">Invoice Settings</label>
                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th style="width: 300px">Name</th>
                                                <th>
                                                    Values
                                                </th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Company Name</td>
                                                    <td><input type="text" class="_input_fields border-0" value="@isset($settings){{$settings->company_name}}@endisset" name="company_name" id="company_name" class="input__field input__field--hoshi company_name" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Company Number</td>
                                                    <td><input type="text" class="_input_fields border-0" value="@isset($settings){{$settings->company_number}}@endisset" name="company_number" id="company_number" class="input__field input__field--hoshi company_number" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Company Address</td>
                                                    <td><input type="text" class="_input_fields border-0 w-100" name="company_address" id="company_address" cols="50" rows="3" value="@isset($settings){{$settings->company_address}}@endisset"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12 offset-md-3 pt-2">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                            {{-- <div class="row">
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="0.01" min="0.01" value="@isset($settings){{$settings->labels}}@endisset" name="labels" id="labels" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="labels">
                                                            <span class="input__label-content input__label-content--hoshi">Labels Cost ($) <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="0.01" min="0.01" value="@isset($settings){{$settings->pick}}@endisset" name="pick" id="pick" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="pick">
                                                            <span class="input__label-content input__label-content--hoshi">Pick Cost ($) <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="0.01" min="0.01" value="@isset($settings){{$settings->pack}}@endisset" name="pack" id="pack" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="pack">
                                                            <span class="input__label-content input__label-content--hoshi">Pack Cost ($) <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="0.01" min="0.01" value="@isset($settings){{$settings->mailer}}@endisset" name="mailer" id="mailer" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="mailer">
                                                            <span class="input__label-content input__label-content--hoshi">Mailer Charges ($) <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="0.01" min="0.01" value="@isset($settings){{$settings->postage_cost}}@endisset" name="postage_cost" id="postage_cost" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="postage_cost">
                                                            <span class="input__label-content input__label-content--hoshi">Postage Cost (1 - 4 oz) <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="0.01" min="0.01" value="@isset($settings){{$settings->postage_cost_lt5}}@endisset" name="postage_cost_lt5" id="postage_cost_lt5" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="postage_cost_lt5">
                                                            <span class="input__label-content input__label-content--hoshi">Postage Cost (5 - 8 oz) <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="0.01" min="0.01" value="@isset($settings){{$settings->postage_cost_lt9}}@endisset" name="postage_cost_lt9" id="postage_cost_lt9" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="postage_cost_lt9">
                                                            <span class="input__label-content input__label-content--hoshi">Postage Cost (9 - 12 oz) <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="0.01" min="0.01" value="@isset($settings){{$settings->postage_cost_lt13}}@endisset" name="postage_cost_lt13" id="postage_cost_lt13" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="postage_cost_lt13">
                                                            <span class="input__label-content input__label-content--hoshi">Postage Cost (13 - 15 oz) <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <span class="input input--hoshi">
                                                        <input type="number" step="0.01" min="0.01" value="@isset($settings){{$settings->postage_cost_gte13}}@endisset" name="postage_cost_gte13" id="postage_cost_gte13" class="input__field input__field--hoshi postage_price" required>
                                                        <label class="input__label input__label--hoshi input__label--hoshi-color-1" for="postage_cost_gte13">
                                                            <span class="input__label-content input__label-content--hoshi">Custom Postage Cost <strong class="text-danger">*</strong></span>
                                                        </label>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 pt-2">
                                    <button class="btn btn-sm btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </div> --}}
                            </form>
                        </div>
                    </div>
                </div>
        
            </div>
        </section>
@endsection

@section('page_js')
    <script src="{{asset('admin/app-assets/js/scripts/classie.js')}}"></script>
    <script>
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
        
    </script>
@endsection