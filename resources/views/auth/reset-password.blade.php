@extends('auth.layout')

@section('title', 'Reset Password')

@section('content')
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                <div class="col-lg-6">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-2">Reset Password</h1>
                            <p class="mb-4">Choose a new password to access your account.</p>
                        </div>

                        @include('auth.partials.alerts')

                        <form class="user" method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <input type="email" name="email" class="form-control form-control-user"
                                       value="{{ old('email', $email) }}" required placeholder="Email Address">
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="password" name="password" class="form-control form-control-user"
                                           required placeholder="New Password">
                                </div>
                                <div class="col-sm-6">
                                    <input type="password" name="password_confirmation" class="form-control form-control-user"
                                           required placeholder="Confirm Password">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Reset Password
                            </button>
                        </form>
                        <hr>
                        <div class="text-center">
                            <a class="small" href="{{ route('login') }}">Back to Login</a>
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
