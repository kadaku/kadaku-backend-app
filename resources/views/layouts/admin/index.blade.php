<!doctype html>
<html
    lang="en" 
    data-layout="vertical" 
    data-layout-style="detached" 
    {{-- data-layout-style="default"  --}}
    data-layout-mode="light" 
    data-sidebar="dark" 
    data-topbar="dark"
    data-sidebar-size="lg" 
    data-sidebar-image="img-2" 
    data-preloader="disable"
    data-layout-position="fixed" 
    data-layout-width="fluid
">
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
    <link href="{{ asset('extend/css/sweetalert2-dark.css') }}" rel="stylesheet" type="text/css">

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
    <script src="{{ asset('extend/plugins/tinymce5.5.1/tinymce.min.js') }}"></script>

    <script>
        $(function() {
            // tinymce
            if (sessionStorage.getItem("data-layout-mode") == 'dark') {
                useDarkMode = true;
            } else {
                useDarkMode = false;
            }

            $('button.light-dark-mode').click(function() {
                var layoutMode = sessionStorage.getItem("data-layout-mode");
                if (layoutMode === 'dark') {
                    useDarkMode = true;
                } else {
                    useDarkMode = false;
                }
                tinymce.remove();
                if ($('#content').is(':visible')) {
                    tinymcePlugin('#content', useDarkMode);
                }
            });
        });
    </script>
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

        function tinymcePlugin(element, darkMode) {
            if (sessionStorage.getItem("data-layout-mode") == 'dark') {
                darkMode = true;
            }

            var fontSurat = 'Arial';
            var fontSizeSurat = '14px';
            tinyMCE.init({
                selector: element,
                branding: false,
                nowrap: true,
                plugins: `preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons`,
                editimage_cors_hosts: ['picsum.photos'],
                menubar: false,
                toolbar1: "newdocument | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | cut copy paste | searchreplace",
                toolbar2: "bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | inserttime preview | forecolor backcolor | table | hr removeformat | subscript superscript",
                toolbar3: "charmap emoticons | print fullscreen | ltr rtl | visualchars visualblocks nonbreaking  pagebreak restoredraft codesample",
                table_style_by_css: false,
                image_advtab: true,
                importcss_append: true,
                height: 500,
                image_caption: true,
                quickbars_selection_toolbar: `bold italic | quicklink h2 h3 blockquote quickimage quicktable`,
                noneditable_class: 'mceNonEditable',
                toolbar_mode: 'show',
                contextmenu: 'link image table',
                skin: darkMode ? 'oxide-dark' : 'oxide',
                content_css: darkMode ? 'dark' : 'default',
                content_style: 'body { font-family: ' + fontSurat + ',sans-serif; font-size:' + fontSizeSurat + ' }',
                init_instance_callback: function(editor) {
                    editor.on('keydown', function(e) {
                    if (e.keyCode == 9) {
                        e.preventDefault();
                        tinymce.activeEditor.execCommand('mceInsertContent', false, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
                    }
                    });
                },
                paste_preprocess: function (plugin, args) {
                  toastrAlert('warning', 'Validation', 'Copy/Paste not allowed.');
                  // replace copied text with empty string
                  args.content = '';
                },
                invalid_styles: {
                    'table': 'width height',
                    'tr': 'width height',
                    'th': 'width height',
                    'td': 'width height'
                }
            });
        }
    </script>
</body>
</html>