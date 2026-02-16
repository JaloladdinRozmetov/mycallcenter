@extends('auth.layout')

@section('title', 'Login')

@section('content')
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                <div class="col-lg-6">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                        </div>

                        @include('auth.partials.alerts')

                        <form class="user" method="POST" action="{{ route('login.attempt') }}">
                            @csrf
                            <div class="form-group">
                                <input type="email" name="email" class="form-control form-control-user"
                                       value="{{ old('email') }}" required autofocus placeholder="Enter Email Address">
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control form-control-user"
                                       required placeholder="Password">
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox small">
                                    <input type="checkbox" class="custom-control-input" id="rememberMe" name="remember" value="1">
                                    <label class="custom-control-label" for="rememberMe">Remember Me</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Login
                            </button>
                        </form>
                        <hr>
                        <div class="text-center">
                            <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>
                        </div>
                        <div class="text-center">
                            <a class="small" href="{{ route('register') }}">Create an Account!</a>
                        </div>
                        <div class="text-center mt-3">
                            <a class="small" href="{{ route('home') }}">Back to site</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
