@extends('admin.layout.app')
@section('title', 'Edit User Info')
@section('content')
<section id="basic-horizontal-layouts">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Update User</h4>
                                </div>
                                <div class="card-body">
                                    <form class="auth-register-form mt-2" method="POST" action="{{ route('user.update',$dataSet['id'])}}">
                                    @csrf
                                     @method('PUT')
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Username</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input id="name" maxlength="50" value="{{$dataSet['name']}}" type="text" class="form-control  @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="johndoe" aria-describedby="register-username" tabindex="1" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Email</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                       <input id="email" maxlength="50" value="{{$dataSet['email']}}" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="john@example.com" required autocomplete="email" aria-describedby="register-email" tabindex="2" />
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Password</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                       <input  id="password" maxlength="10" type="password" class="form-control form-control-merge  @error('password') is-invalid @enderror" name="password_confirmation" autocomplete="new-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="register-password" tabindex="3" />
                                    

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Confirm Password</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                       <input id="password-confirm" maxlength="10" type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" name="password" autocomplete="new-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="register-password" tabindex="3" />
                                            
                                                    </div>
                                                </div>
                                            </div>
                                            @if(Auth::user()->hasRole('admin'))
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="catselect">Select Role</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <select name="role" class="form-select select2" id="basicSelect" required>
                                                            @if ($dataSet['customer_status'] == 0)
                                                                <option value="" >--Select Role--</option>
                                                                @foreach($roles as $role)
                                                                    <option @if($role->id == $roleId) selected @endif  value="{{$role->name}}">{{ucwords($role->name)}}</option>
                                                                @endforeach
                                                            @else
                                                                <option selected value="customer">Customer</option>
                                                            @endif
                                                        </select>
                                                        @error('role_id')
                                                            <p class="text-danger">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-sm-9 offset-sm-3">
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
@section('page_js')
<script src="{{asset('admin/app-assets/js/scripts/classie.js')}}"></script>
<script>
    $(document).ready(function() {
        $(document).on('keyup', '#password-confirm', function(e) {
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
            if($('#password-confirm').val() != $(this).val()) {
                $('#password-confirm').css('border', '1px solid red');
                $('#submitbtn').attr('disabled', true);
            } else {
                $('#password-confirm').css('border', '1px solid green');
                $('#submitbtn').attr('disabled', false);
            }
        });
    });
</script>
@endsection