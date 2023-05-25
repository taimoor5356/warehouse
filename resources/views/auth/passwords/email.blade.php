@extends('admin.layout.loginapp')

@section('content')
<div class="auth-wrapper auth-v1 px-2">
                    <div class="auth-inner py-2">
                        <!-- Forgot Password v1 -->
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="#" class="brand-logo">
                                    
                                    <h2 class="brand-text text-primary ms-1">Warehouse</h2>
                                </a>

                                <h4 class="card-title mb-1">Forgot Password? 🔒</h4>
                                <p class="card-text mb-2">Enter your email and we'll send you instructions to reset your password</p>

                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <form class="auth-forgot-password-form mt-2" method="POST" action="{{ route('password.email') }}">
                                    @csrf
                                    <div class="mb-1">
                                        <label for="forgot-password-email" class="form-label">Email</label>
                                        <input id="email" type="email" class="form-control" required name="email" placeholder="john@example.com" value="{{ old('email') }}" autocomplete="email" aria-describedby="forgot-password-email" tabindex="1" autofocus />
                                        @error('email')
                                            <span class="invalid-feedback" style="display:block;" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100" tabindex="2">Send reset link</button>
                                </form>

                                <p class="text-center mt-2">
                                    <a href="{{ route('login') }}"> <i data-feather="chevron-left"></i> Back to login </a>
                                </p>
                            </div>
                        </div>
                        <!-- /Forgot Password v1 -->
                    </div>
                </div>


@endsection
