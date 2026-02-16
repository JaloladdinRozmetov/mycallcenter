@extends('auth.layout')

@section('title', 'Forgot Password')

@section('content')
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                <div class="col-lg-6">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-2">Forgot Your Password?</h1>
                            <p class="mb-4">Enter your email address and we'll send you a link to reset your password.</p>
                        </div>

                        @include('auth.partials.alerts')

                        <form class="user" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group">
                                <input type="email" name="email" class="form-control form-control-user"
                                       value="{{ old('email') }}" required placeholder="Enter Email Address">
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Send Reset Link
                            </button>
                        </form>
                        <hr>
                        <div class="text-center">
                            <a class="small" href="{{ route('login') }}">Back to Login</a>
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
