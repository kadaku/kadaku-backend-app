<!doctype html>
<html lang="en" 
    data-layout="vertical" 
    data-layout-style="detached" 
    {{-- data-layout-style="default"  --}}
    data-sidebar="dark" 
    data-topbar="dark"
    data-sidebar-size="lg" 
    data-sidebar-image="img-2" 
    data-preloader="disable">
<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="{{ brand()->name }}">
    <meta name="description" content="Ini adalah Content Management System {{ brand()->name }}">
    <meta name="keywords" content="website, aplikasi, sistem informasi manajemen">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Indonesian, English">
    <meta name="revisit-after" content="1 days">
    <meta name="author" content="Cafeweb Indonesia">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="theme-color" content="#132649">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ brand()->name }}</title>
    
    <!-- favicon -->
    <link rel="apple-touch-icon" href="{{ asset('storage/images/brand/'.base64_decode(brand()->favicon).'.'.brand()->favicon_ext) }}" sizes="180x180">
    <link rel="icon" type="image/png" href="{{ asset('storage/images/brand/'.base64_decode(brand()->favicon).'.'.brand()->favicon_ext) }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('storage/images/brand/'.base64_decode(brand()->favicon).'.'.brand()->favicon_ext) }}" sizes="16x16">

    <link href="{{ asset('main/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('main/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('main/css/app.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('main/css/custom.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('extend/css/syam.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('extend/css/fancybox.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('extend/plugins/toastr/toastr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('extend/plugins/fontawesome-6.5.2-web/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <script src="{{ asset('main/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('main/js/layout.js') }}"></script>
    <script src="{{ asset('main/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('main/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('main/js/waves.min.js') }}"></script>
    <script src="{{ asset('main/js/feather.min.js') }}"></script>
    <script src="{{ asset('main/js/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('main/js/pickr.min.js') }}"></script>
    <script src="{{ asset('main/js/flatpickr.min.js') }}"></script>
    <script src="{{ asset('main/js/choices.min.js') }}"></script>
    <script src="{{ asset('extend/js/blockui.js') }}"></script>
    <script src="{{ asset('extend/js/bootbox.js') }}"></script>
    <script src="{{ asset('extend/js/pdfobject.min.js') }}"></script>
    <script src="{{ asset('extend/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('extend/js/fancybox.js') }}"></script>
    <script src="{{ asset('extend/js/syam.min.js') }}"></script>
    <script src="{{ asset('extend/plugins/toastr/toastr.min.js') }}"></script>

</head>

<body>
    <div id="layout-wrapper">
        @include('layouts.admin._header')
        @include('layouts.admin._sidebar')
        <div class="vertical-overlay"></div>
        <!-- main content -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @include('layouts.admin._breadcrumb')
                    @yield('content')
                </div>
                <!-- container fluid -->
            </div>
            <!-- end page content -->
            @include('layouts.admin._footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- customizer -->
    <!-- end layout wrapper -->
    <script src="{{ asset('main/js/app.js') }}"></script>
    <script>
        var baseUrl = '{{ url("/") }}';
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
    </script>
</body>
</html>