<!doctype html>
<html lang="en" 
	data-layout="vertical" 
	data-layout-style="detached" 
	data-sidebar="light" 
	data-topbar="dark" 
	data-sidebar-size="lg" 
	data-sidebar-image="none" 
	data-preloader="disable">
<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="Login | {{ brand()->name }}">
    <meta name="description" content="Login | {{ brand()->name }}">
	<meta name="keywords" content="website, aplikasi, sistem informasi manajemen">
	<meta name="robots" content="index, follow">
	<meta name="language" content="Indonesian, English">
	<meta name="revisit-after" content="1 days">
	<meta name="author" content="Cafeweb Indonesia">
	<meta name="msapplication-tap-highlight" content="no">
    <meta name="theme-color" content="#132649">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | {{ brand()->name }}</title>
	
	<!-- favicon -->
	<link rel="apple-touch-icon" href="{{ asset('storage/images/brand/'.base64_decode(brand()->favicon).'.'.brand()->favicon_ext) }}" sizes="180x180">
    <link rel="icon" type="image/png" href="{{ asset('storage/images/brand/'.base64_decode(brand()->favicon).'.'.brand()->favicon_ext) }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('storage/images/brand/'.base64_decode(brand()->favicon).'.'.brand()->favicon_ext) }}" sizes="16x16">

    <link href="{{ asset('main/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('main/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('main/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('main/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('main/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('main/js/bootstrap.bundle.min.js') }}"></script>
	<script src="{{ asset('main/js/layout.js') }}"></script>
</head>

<body>
    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mx-auto">
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <div class="col-lg-12">
                                    @yield('content')
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                            	{{ '2023 - ' . date('Y') }} {{ brand()->name }} Crafted with <i class="mdi mdi-heart text-danger"></i> by PT. Cafe Media Inovasi
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->
</body>
</html>