@extends('admin.layout.loginapp')

@section('content')
    <div class="auth-wrapper auth-v1 px-2">
        <div class="auth-inner py-2">
            <!-- Register v1 -->
            <div class="card mb-0">
                <div class="card-body">
                    <a href="#" class="brand-logo">
                        <div class="">
                            <span class="brand-text" style="margin-top: 0px; color: rgb(255, 135, 55); font-size: 25px; font-weight: bold">
                                Warehouse
                            </span>
                            {{-- <h6 style="margin: -14px 55px; color: black; font-weight: bold; ">Warehouse</span> --}}
                        </div>
                    </a>
                    <form class="auth-register-form mt-2" method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-1">
                            <label for="register-username" class="form-label">Username</label>
                            <input id="name" type="text" class="form-control  @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                placeholder="johndoe" aria-describedby="register-username" tabindex="1" />
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-1">
                            <label for="register-email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" placeholder="john@example.com" required
                                autocomplete="email" aria-describedby="register-email" tabindex="2" />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-1">
                            <label for="register-password" class="form-label">Password</label>

                            <div class="input-group input-group-merge form-password-toggle">
                                <input id="password" type="password"
                                    class="form-control form-control-merge  @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="new-password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="register-password" tabindex="3" />
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-1">
                            <label for="register-password" class="form-label">Confirm Password</label>

                            <div class="input-group input-group-merge form-password-toggle">
                                <input id="password-confirm" type="password" class="form-control form-control-merge"
                                    name="password_confirmation" required autocomplete="new-password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="register-password" tabindex="3" />
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>

                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" tabindex="5">Sign up</button>
                    </form>

                    <p class="text-center mt-2">
                        <span>Already have an account?</span>
                        <a href="{{ route('login') }}">
                            <span>Sign in instead</span>
                        </a>
                    </p>

                </div>
            </div>
            <!-- /Register v1 -->
        </div>
    </div>
@endsection
