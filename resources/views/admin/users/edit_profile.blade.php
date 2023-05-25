@extends('admin.layout.app')
@section('title', 'Edit User Profile')
@section('content')
<section id="basic-horizontal-layouts">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit  Profile</h4>
                                </div>
                                <div class="card-body">
                                    <form class="auth-register-form mt-2" method="POST" action="{{url('editProfileProcess')}}">
                                    @csrf
                                     
                                     <input type="hidden" name="id" value="{{$dataSet['id']}}" />
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-1 row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label" for="first-name">Username</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input id="name" value="{{$dataSet['name']}}" type="text" class="form-control  @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="johndoe" aria-describedby="register-username" tabindex="1" />
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
                                                       <input id="email" value="{{$dataSet['email']}}" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="john@example.com" required autocomplete="email" aria-describedby="register-email" tabindex="2" />
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
                                                       <input  id="password" type="password" class="form-control form-control-merge  @error('password') is-invalid @enderror" name="password" autocomplete="new-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="register-password" tabindex="3" />
                                    

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
                                                       <input id="password-confirm" type="password" class="form-control form-control-merge" name="password_confirmation" autocomplete="new-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="register-password" tabindex="3" />
                                            
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

@endsection
