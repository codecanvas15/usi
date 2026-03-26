<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('/images/icon.png') }}">

    <title>@yield('title') {{ getCompany()->name }}</title>

    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
    <script>
        const base_url = '{{ url('') }}';
        const base_api_url = 'https://api-usi.intivestudio.com';
        const token = '{{ csrf_token() }}';
    </script>
    @yield('css')
    @stack('style')
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
    <div class="wrapper">
        <div id="loader" class="d-none"></div>
        <!-- Content Wrapper. Contains page content -->
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                @yield('breadcrumbs')
                @yield('content')
            </section>
            <!-- /.content -->
            @isset($footer)
                @include('guest.layout.footer')
            @endisset
        </div>
        <!-- /.content-wrapper -->

        <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
        <div class="d-flex gap-3" style="position: fixed; right: 30px; bottom: 30px; z-index: 999999;">
            <div class="btn btn-sm btn-dark" id="previous-pages">
                <i class="fa-solid fa-backward"></i>
            </div>
            <div class="btn btn-sm btn-danger" id="refresh-pages">
                <i class="fa-solid fa-arrows-rotate"></i>
            </div>
            <div class="btn btn-sm btn-dark" id="darkmode-toggle">
                <i class="fa-solid fa-moon" id="toggle-icon"></i>
            </div>
        </div>
    </div>
    <!-- ./wrapper -->

    <!-- Vendor JS -->
    <script src="{{ asset('/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('/vendors/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('/vendors/js/jquery-price-format/jquery.priceformat.min.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-select/dist/js/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js') }}"></script>

    <!-- Deposito Admin App -->
    <script src="{{ asset('/vendors/js/template.js') }}"></script>
    <script src="{{ asset('/js/select-2.js') }}"></script>
    <script src="{{ asset('/js/moment.js') }}"></script>
    {{-- <script src="{{ asset('/js/admin/sidebar.js') }}"></script> --}}
    <script src="{{ asset('/js/input/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('/js/identity.js') }}"></script>
    <script src="{{ asset('js/sweet-alert/sweetalert2.all.min.js') }}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js" integrity="sha512-JZSo0h5TONFYmyLMqp8k4oPhuo6yNk9mHM+FY50aBjpypfofqtEWsAgRDQm94ImLCzSaHeqNvYuD9382CEn2zw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- <script src="{{ asset('js/notification.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/admin/authorization.js') }}"></script> --}}
    <script>
        $('.money').mask('000.000.000.000.000', {
            reverse: true
        });
        $('.input-number').mask('000000000000000', {
            reverse: true
        });
    </script>

    <script>
        $(document).ready(function() {
            $('form').each(function() {
                $(this).on('keyup keypress', function(e) {
                    var keyCode = e.keyCode || e.which;
                    if (keyCode === 13 && ($(event.target)[0] != $("textarea")[0]) && ($(event.target)[0] != $(".ck-restricted-editing_mode_standard")[0])) {
                        e.preventDefault();
                        return false;
                    }
                });

                $(this).submit(function() {
                    $(this).find('input[type=submit]').prop('disabled', true);
                    $(this).find('button[type=submit]').prop('disabled', true);
                });
            });

            $('#refresh-pages').click(function(e) {
                e.preventDefault();
                location.reload(true)

            });

            $('#previous-pages').click(function(e) {
                e.preventDefault();
                location.href = "{{ url()->previous() }}";
            });

            if (
                localStorage.getItem("color-theme") === "dark" ||
                (!("color-theme" in localStorage) &&
                    window.matchMedia("(prefers-color-scheme: dark)").matches)
            ) {
                document.body.classList.remove("light-skin");
                document.body.classList.add("dark-skin");
            } else {
                document.body.classList.remove("dark-skin");
                document.body.classList.add("light-skin");
            }

            if (
                localStorage.getItem("color-theme") === "dark" ||
                (!("color-theme" in localStorage) &&
                    window.matchMedia("(prefers-color-scheme: dark)").matches)
            ) {
                $('#toggle-icon').removeClass('fa-solid fa-moon');
                $('#toggle-icon').addClass('fa-solid fa-sun');
            } else {
                $('#toggle-icon').removeClass('fa-solid fa-sun');
                $('#toggle-icon').addClass('fa-solid fa-moon');
            }

            $('#darkmode-toggle').click(function(e) {
                e.preventDefault();

                if (
                    localStorage.getItem("color-theme") === "dark" ||
                    (!("color-theme" in localStorage) &&
                        window.matchMedia("(prefers-color-scheme: dark)").matches)
                ) {
                    $('#toggle-icon').removeClass('fa-solid fa-moon');
                    $('#toggle-icon').addClass('fa-solid fa-sun');
                } else {
                    $('#toggle-icon').removeClass('fa-solid fa-sun');
                    $('#toggle-icon').addClass('fa-solid fa-moon');
                }

                // if set via local storage previously
                if (localStorage.getItem("color-theme")) {
                    if (localStorage.getItem("color-theme") === "light") {
                        document.body.classList.remove("light-skin");
                        document.body.classList.add("dark-skin");
                        localStorage.setItem("color-theme", "dark");
                    } else {
                        document.body.classList.remove("dark-skin");
                        document.body.classList.add("light-skin");
                        localStorage.setItem("color-theme", "light");
                    }

                    // if NOT set via local storage previously
                } else {
                    if (document.body.classList.contains("dark-skin")) {
                        document.body.classList.add("light-skin");
                        document.body.classList.remove("dark-skin");
                        localStorage.setItem("color-theme", "light");
                    } else {
                        document.body.classList.remove("light-skin");
                        document.body.classList.add("dark-skin");
                        localStorage.setItem("color-theme", "dark");
                    }
                }
            });

            let sidebarMenuId = localStorage.getItem('current_sidebar_menu_id');
            if (sidebarMenuId) {
                scrollToMenuSidebar(sidebarMenuId);
            }
        });

        if (localStorage.getItem('is_sidebar_open') == 'false') {
            $('body').removeClass('sidebar-collapse');
        } else {
            $('body').addClass('sidebar-collapse');
        }

        $('#toggle-sidebar-button').click(function(e) {
            console.log(localStorage.getItem('is_sidebar_open'));
            if (localStorage.getItem('is_sidebar_open') == 'true') {
                localStorage.setItem('is_sidebar_open', 'false');
            } else {
                localStorage.setItem('is_sidebar_open', 'true');
            }
        });

        $('.select2').select2({
            width: '100%'
        });

        $('.datepicker-input:not([readonly])').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        });

        $('.month-year-picker-input').datepicker({
            format: 'mm-yyyy',
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        $('.scroll-top').click(function() {
            let id = '';
            if ($(this).find('ul > li').hasClass('active')) {
                id = $(this).find('ul > li.active').attr('id');
            } else {
                id = $(this).attr('id');
            }
            localStorage.setItem('current_sidebar_menu_id', id);
        });

        function scrollToMenuSidebar(id) {
            $(".ps").animate({
                scrollTop: $(`#${id}`).offset().top - 160
            }, "slow");
        };

        function checkClosingPeriod(e) {
            var date = $(e).val();

            $.ajax({
                url: '{{ route('admin.closing-period.check') }}',
                method: "POST",
                data: {
                    date: date,
                    _token: token,
                },
                success: function(res) {
                    if (!res.success) {
                        $(e).val('');
                        Swal.fire({
                            title: '',
                            text: res.message,
                            icon: 'warning',
                            confirmButtonColor: '#303179',
                            confirmButtonText: 'Mengerti',
                        });
                    }
                }
            })
        }

        function initDatePicker() {
            $('.datepicker-input:not([readonly])').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true
            });
        }

        function show_print_out_modal(event, symbol = '?') {
            event.preventDefault();
            $('#print-out-link').val($(event.target).attr('href'));
            $('#print-out-modal').modal('show');
            $('#print-out-symbol').val(symbol);
        }

        $('#print-out-submit').click(function(e) {
            e.preventDefault();
            let symbol = $('#print-out-symbol').val() ?? '?';
            let final_link = $('#print-out-link').val() + symbol + 'paper=' + $('#print-out-paper').val() + '&orientation=' + $('#print-out-orientation').val();
            window.open(final_link, '_blank');
        });
    </script>

    <div class="toast-container top-0 end-0 p-3" style="position: fixed!important; z-index: 9999">
        @include('components.toast')
    </div>

    @yield('js')
    @stack('script')
</body>

</html>
