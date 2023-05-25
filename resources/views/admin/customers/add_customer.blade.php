@extends('admin.layout.app')
@section('title', 'Add Customer')
@section('content')

<section id="basic-horizontal-layouts">
    <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Add Customer</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">Customers
                                    </li>
                                    <li class="breadcrumb-item active">Add Customer
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
                                    <h4 class="card-title">Add Customer</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" id="form" enctype='multipart/form-data' action="{{route('customers.store')}}" method="post">
                                       {{@csrf_field()}}
                                        <div class="row">

                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-md-3">
                                                        <label class="col-form-label" for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" id="customer_name" class="form-control" placeholder="Enter Customer Name" value="{{old('customer_name')}}" name="customer_name" maxlength="50" required/>
                                                        @error('customer_name')
                                                            <p class="text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-md-3">
                                                        <label class="col-form-label" for="phone">Phone <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="number" value="{{old('phone')}}" id="phone" placeholder="Enter Phone Number" class="form-control" name="phone" maxlength="15" onKeyDown="if(this.value.length==15 && event.keyCode!=8) return false;" required/>
                                                        @error('phone')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-md-3">
                                                        <label class="col-form-label" for="email">Email <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="email" value="{{old('email')}}" id="email" placeholder="Enter Email Address" class="form-control" name="email" maxlength="40" required/>
                                                        @error('email')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="password">Password</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="password" minlength="8" maxlength="10" id="password" placeholder="Min 8 Characters Password" class="form-control" name="confirm_password" value="{{old('confirm_password')}}" required/>


                                                        @error('password')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="confirm_password">Confirm Password</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="password" minlength="8" maxlength="10" id="confirm_password" placeholder="Min 8 Characters Password" class="form-control" name="password" value="{{old('password')}}" required/>
                                                        @error('confirm_password')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-md-3">
                                                        <label class="col-form-label" for="address">Address <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" value="{{old('address')}}" id="address" placeholder="Enter Address" class="form-control" name="address" maxlength="80" required/>
                                                        @error('address')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-md-3">
                                                        <label class="col-form-label" for="po_box_number">Po Box Number</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" value="{{old('po_box_number')}}" id="po_box_number" placeholder="Enter Po Box Number" class="form-control" name="po_box_number" maxlength="100" required/>
                                                        @error('po_box_number')
                                                              <p class="text-danger">{{ $message }}</p>
                                                          @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-md-3">
                                                        <label class="col-form-label" for="customSwitch3">Status</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div class="form-check form-check-primary form-switch">
                                                            <input type="checkbox" name="is_active" value="1" {{ old('is_active') == 1 ? 'checked' : '' }} checked class="form-check-input" id="customSwitch3">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-9 offset-sm-3">
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
@section('page_js')
    <script src="{{asset('admin/app-assets/js/scripts/classie.js')}}"></script>
    <script>
        $(document).ready(function() {
            @if (session('success'))
                $('.toast .toast-header').removeClass('bg-danger')
                $('.toast .me-auto').html('Success');
                $('.toast .toast-header').addClass('bg-success');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{ session('success') }}");
                $('#toast-btn').click();
            @elseif (session('error'))
                $('.toast .toast-header').removeClass('bg-success')
                $('.toast .me-auto').html('Error');
                $('.toast .toast-header').addClass('bg-danger');
                $('.toast .text-muted').html('Now');
                $('.toast .toast-body').html("{{ session('error') }}");
                $('#toast-btn').click();
            @endif
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
            $(document).on('submit', '#form', function(e) {
                e.preventDefault();
                $('#submitbtn').attr('disabled', true);
                var cName = $('#customer_name').val();
                var cPhone = $('#phone').val();
                var cEmail = $('#email').val();
                var cPassword = $('#password').val();
                var cConfirm = $('#confirm_password').val();
                var cAddress = $('#address').val();
                var cPoBox = $('#po_box_number').val();
                var cStatus = $('#customSwitch3').val();
                $.ajax({
                    url: "{{ route('customers.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer_name: $('#customer_name').val(),
                        phone: $('#phone').val(),
                        email: $('#email').val(),
                        password: $('#confirm_password').val(),
                        address: $('#address').val(),
                        po_box_number: $('#po_box_number').val(),
                        is_active: $('#customSwitch3').val(),
                    },
                    success:function(response) {
                        if (response.status == false) {
                            $('.toast .toast-header').removeClass('bg-success')
                            $('.toast .me-auto').html('Error');
                            $('.toast .toast-header').addClass('bg-danger');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.msg);
                            $('#toast-btn').click();
                            $('#submitbtn').attr('disabled', false);
                        } else {
                            $('.toast .toast-header').removeClass('bg-danger')
                            $('.toast .me-auto').html('Success');
                            $('.toast .toast-header').addClass('bg-success');
                            $('.toast .text-muted').html('Now');
                            $('.toast .toast-body').html(response.msg);
                            $('#toast-btn').click();
                            $('#submitbtn').attr('disabled', false);
                            setTimeout(() => {
                                window.location.href = "/customer/"+response.customer_id+"/show_all";
                            }, 2000);
                        }
                    }
                });
            });
        });
        // (function() {
        //     // trim polyfill : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
        //     if (!String.prototype.trim) {
        //         (function() {
        //             // Make sure we trim BOM and NBSP
        //             var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
        //             String.prototype.trim = function() {
        //                 return this.replace(rtrim, '');
        //             };
        //         })();
        //     }

        //     [].slice.call( document.querySelectorAll( 'input.input__field' ) ).forEach( function( inputEl ) {
        //         // in case the input is already filled..
        //         if( inputEl.value.trim() !== '' ) {
        //             classie.add( inputEl.parentNode, 'input--filled' );
        //         }

        //         // events:
        //         inputEl.addEventListener( 'focus', onInputFocus );
        //         inputEl.addEventListener( 'blur', onInputBlur );
        //     } );

        //     function onInputFocus( ev ) {
        //         classie.add( ev.target.parentNode, 'input--filled' );
        //     }

        //     function onInputBlur( ev ) {
        //         if( ev.target.value.trim() === '' ) {
        //             classie.remove( ev.target.parentNode, 'input--filled' );
        //         }
        //     }
        // })();

    </script>
@endsection
