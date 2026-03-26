<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ url('storage/' . getCompany()->logo) }}">

    <title>@yield('title') {{ getCompany()->name }}</title>

    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
            white-space: normal;
        }
    </style>
    <script>
        const base_url = '{{ url('') }}';
        const base_api_url = 'https://api-usi.intivestudio.com';
        const role_name = '{{ Auth::user()->role->name ?? '' }}';
        const user_id = '{{ Auth::user()->id }}';
        const branchId = '{{ Auth::user()->branch_id ?? Auth::user()->temp_branch_id }}';
        const branchIsPrimary = '{{ Auth::user()->branch_id ?? Auth::user()->branch?->is_primary }}';
        const token = '{{ csrf_token() }}';
    </script>
    @yield('css')
    @stack('style')
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed">

    <div class="wrapper">
        <div id="loader" class="d-none"></div>

        @include('layouts.admin.header')

        @include('layouts.admin.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="container-full">
                <!-- Main content -->
                <section class="content">
                    @yield('breadcrumbs')
                    @yield('content')
                    {{-- @include('layouts.admin.dashboard.main') --}}
                </section>
                <!-- /.content -->
            </div>
        </div>
        <!-- /.content-wrapper -->

        @include('layouts.admin.footer')

        {{-- @include('layouts.admin.sidebar-control') --}}

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

    <div class="modal fade" id="print-out-modal" tabindex="-1" role="dialog" aria-labelledby="print-out-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="print-out-modal-label">Paper and Orientation Selection</h5>
                    <a href="javascript:;" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Paper Selection -->
                    <div class="form-group">
                        <label for="print-out-paper">Select Paper:</label>
                        <select class="form-control" id="print-out-paper">
                            <option value="a4">A4</option>
                            <option value="a5">A5</option>
                            <!-- Add more paper options as needed -->
                        </select>
                    </div>

                    <!-- Orientation Selection -->
                    <div class="form-group">
                        <label for="print-out-orientation">Select Orientation:</label>
                        <select class="form-control" id="print-out-orientation">
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Landscape</option>
                            <!-- Add more orientation options as needed -->
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" id="print-out-preview">Preview</button>
                    <button type="submit" class="btn btn-primary" id="print-out-submit">Print</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="print-option-modal" tabindex="-1" role="dialog" aria-labelledby="print-option-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="print-option-modal-label">Opsi Export</h5>
                    <a href="javascript:;" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Paper Selection -->
                    <div class="form-group">
                        <x-input-checkbox name="original" id="original" label="asli" value="1" />
                    </div>
                    <div class="form-group">
                        <x-input-checkbox name="copy" id="copy" label="copy" value="1" />
                    </div>

                    <div class="form-group">
                        <x-input type="number" id="copies" name="copies" label="jumlah copy" value="0" readonly />
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" id="print-option-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="print-preview" tabindex="-1" role="dialog" aria-labelledby="print-preview-label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="print-preview-label">Preview</h5>
                    <a href="javascript:;" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <embed id="pdfPreview" width="100%" height="600"></embed>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="print-request" tabindex="-1" role="dialog" aria-labelledby="print-request-label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.request-print') }}" id="form-request-print">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="print-request-label">Request Print</h5>
                        <a href="javascript:;" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="form-group">
                            <x-input type="text" id="print_reason" name="print_reason" label="Alasan" value="" required />
                        </div>

                        <button type="submit" class="btn btn-primary">Ajukan Print</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- @include('dashboard.partials.toast') --}}

    <!-- ./wrapper -->

    {{-- @include('layouts.admin.floating-menu') --}}

    {{-- @include('layouts.admin.chatbox') --}}
    <!-- Page Content overlay -->

    {{-- ! ADD FIREBASE SETUP --}}
    <script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js"></script>
    <script>
        var firebaseConfig = {
            apiKey: 'AIzaSyDTnudDUlLG0zwADT6GL7_FR4ZeBiQNeuw',
            authDomain: 'usi-project.firebaseapp.com',
            databaseURL: 'https://usi-project.firebaseio.com',
            projectId: 'usi-project',
            storageBucket: 'usi-project.appspot.com',
            messagingSenderId: '1005984774841',
            appId: '1:1005984774841:web:3c006a224bd1d87109f968',
        };
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        startFCM()

        function startFCM() {
            messaging
                .requestPermission()
                .then(function() {
                    return messaging.getToken()
                })
                .then(function(response) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.user.store-token') }}",
                        type: 'POST',
                        data: {
                            token: response
                        },
                        dataType: 'JSON',
                        success: function(response) {},
                        error: function(error) {},
                    });
                }).catch(function(error) {});
        }
        messaging.onMessage(function(payload) {
            const title = payload.notification.title;
            const options = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            new Notification(title, options);
        });
    </script>

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
    <script src="{{ asset('/js/admin/sidebar.js') }}"></script>
    <script src="{{ asset('/js/input/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('/js/identity.js') }}"></script>
    <script src="{{ asset('js/sweet-alert/sweetalert2.all.min.js') }}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js" integrity="sha512-JZSo0h5TONFYmyLMqp8k4oPhuo6yNk9mHM+FY50aBjpypfofqtEWsAgRDQm94ImLCzSaHeqNvYuD9382CEn2zw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/notification.js') }}"></script>
    <script src="{{ asset('js/admin/authorization.js') }}"></script>
    <script>
        $('.money').mask('000.000.000.000.000', {
            reverse: true
        });
        $('.input-number').mask('000000000000000', {
            reverse: true
        });
    </script>
    <!-- Include PDF.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>

    <script>
        $(document).ready(function() {
            $('form').each(function() {
                $(this).on('keyup keypress', function(e) {
                    var keyCode = e.keyCode || e.which;
                    if (keyCode === 13 && ($(event.target)[0] != $("textarea")[0]) && ($(event
                            .target)[0] != $(".ck-restricted-editing_mode_standard")[0])) {
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

        $('.year-picker-input').datepicker({
            format: 'yyyy',
            viewMode: "years",
            minViewMode: "years",
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

            $('.datepicker-input:not([readonly])').attr('autocomplete', 'off');
        }

        $('.datepicker-input:not([readonly])').attr('autocomplete', 'off');

        var data_model = "";
        var data_id = "";
        var data_link = "";
        var data_type = "";
        var data_code = "";
        var data_print_type = "";
        var data_export_link = "";
        var data_symbol = "";

        function show_print_out_modal(event, symbol = '?') {
            event.preventDefault();
            data_export_link = $(event.target).attr('href') ?? '';
            var element = $(event.target);

            if (data_export_link == '') {
                element = $(event.target).parent();
            }

            data_export_link = $(element).attr('href') ?? '';
            data_model = $(element).data('model') ?? '';
            data_id = $(element).data('id') ?? '';
            data_link = $(element).data('link') ?? '';
            data_type = $(element).data('type') ?? '';
            data_code = $(element).data('code') ?? '';
            data_print_type = $(element).data('print-type') ?? '';
            data_symbol = symbol;
            var module = $(element).data('module') ?? '';

            if (data_model) {
                $('#print-out-preview').removeClass('d-none');
            } else {
                $('#print-out-preview').addClass('d-none');
            }

            $('#print-out-modal').modal('show');

            if (module === 'leave') {
                $('#print-out-paper').val('a4');
                $('#print-out-paper').prop('disabled', true);
            }

            if (module === 'permission-letter-employee') {
                $('#print-out-paper').val('a4');
                $('#print-out-orientation').val('portrait');
                // $('#print-out-paper').prop('disabled', true);
            }
        }

        $('#print-out-submit').click(function(e) {
            e.preventDefault();

            if (data_model != '') {
                check_can_print();
            } else {
                let final_link = data_export_link + data_symbol + 'paper=' + $('#print-out-paper').val() +
                    '&orientation=' + $('#print-out-orientation').val();
                window.open(final_link, '_blank');
            }
        });

        function showPrintOption(event) {
            event.preventDefault();

            $('#print-option-submit').attr('href', $(event.target).attr('href'));
            $('#print-option-submit').attr('data-model', $(event.target).data('model') ?? null);
            $('#print-option-submit').attr('data-id', $(event.target).data('id') ?? null);
            $('#print-option-submit').attr('data-link', $(event.target).data('link') ?? null);
            $('#print-option-submit').attr('data-type', $(event.target).data('type') ?? null);
            $('#print-option-submit').attr('data-code', $(event.target).data('code') ?? null);
            $('#print-option-submit').attr('data-print-type', $(event.target).data('print-type') ?? null);


            $('#print-option-modal').modal('show');
        }

        $('#copy').on('click', function(e) {
            if ($(this).is(':checked')) {
                $('#copies').val(1).attr('readonly', false).attr("min", 1);
            } else {
                $('#copies').val(0).attr('readonly', true).attr("min", 0);
            }
        })

        $('#print-option-submit').on('click', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            href +=
                `?original=${$('#original').is(':checked')}&copy=${$('#copy').is(':checked')}&copies=${$('#copies').val()}`;

            $('#print-option-submit').attr('href', href);

            $('#original').prop('checked', false);
            $('#copy').prop('checked', false);
            $('#copies').val(0).attr('readonly', true).attr("min", 0);
            $('#print-option-modal').modal('hide');
            show_print_out_modal(event, '&');
        });

        $('#print-out-preview').on('click', function(e) {
            e.preventDefault();
            let final_link = data_export_link + data_symbol + 'paper=' + $('#print-out-paper').val() +
                '&orientation=' + $('#print-out-orientation').val() + '&preview=1';
            $.ajax({
                url: final_link,
                type: 'GET',
                responseType: 'blob',
                success: function(data) {
                    var file = base_url + '/storage/' + data + '#toolbar=0&navpanes=0';
                    $('#pdfPreview').attr('src', file);
                    $('#print-preview').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching PDF:', status, error);
                    console.log('Server response:', xhr.responseText);
                }
            })
        });

        function check_can_print() {
            $.ajax({
                url: '{{ route('admin.check-can-print') }}',
                method: "POST",
                data: {
                    model: data_model,
                    model_id: data_id,
                    link: data_link,
                    print_type: data_print_type,
                    _token: token,
                },
                success: function(res) {
                    if (res.data.can_print) {
                        let final_link = data_export_link + data_symbol + 'paper=' + $('#print-out-paper')
                            .val() + '&orientation=' + $('#print-out-orientation').val();
                        window.open(final_link, '_blank');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '',
                            text: res.data.message,
                        });

                        if (res.data.show_request_modal) {
                            $('#print-out-modal').modal('hide');
                            $('#print-request').modal('show');
                        }
                    }
                }
            })
        }

        $('#form-request-print').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: "POST",
                data: {
                    model: data_model,
                    model_id: data_id,
                    link: data_link,
                    print_type: data_print_type,
                    type: data_type,
                    code: data_code,
                    export_link: data_export_link,
                    reason: $('#print_reason').val(),
                    _token: token,
                },
                success: function(res) {
                    $('#print-request').modal('hide');

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '',
                            text: res.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '',
                            text: res.message,
                        });
                    }

                    $('#form-request-print').find('button[type="submit"]').attr('disabled', false);
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: '',
                        text: xhr.responseJSON.message,
                    });

                    setTimeout(() => {
                        $('#form-request-print').find('button[type="submit"]').attr('disabled',
                            false);
                    }, 2000);
                }
            });
        })

        function get_request_print_approval(model, model_id, type, container = 'print-request-container') {
            if (container == '') {
                container = 'print-request-container';
            }
            $.ajax({
                url: '{{ route('admin.get-print-request-approval') }}',
                method: "POST",
                data: {
                    model: model,
                    model_id: model_id,
                    type: type,
                    _token: token,
                },
                success: function(res) {
                    if (res) {
                        $('#' + container).html(res.view);
                    }
                }

            })
        }
    </script>

    <div class="toast-container top-0 end-0 p-3" style="position: fixed!important; z-index: 9999">
        @include('components.toast')
    </div>

    @yield('js')
    @stack('script')
</body>

</html>
