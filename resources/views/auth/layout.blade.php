<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Authentication">
    <meta name="author" content="My Call Center">

    <title>@yield('title', 'Auth')</title>

    <link href="{{ asset('admin-template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('admin-template/css/sb-admin-2.min.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('admin-template/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin-template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin-template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('admin-template/js/sb-admin-2.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
