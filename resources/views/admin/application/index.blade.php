@php
    $main = 'application';
    $title = 'Formulir Lamaran - ';
@endphp

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
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css') }}">
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
    <title>{{ $title . Str::upper(config('app.name', 'United Shipping Indonesia')) }}</title>
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
    <div class="wrapper">
        <div id="loader"></div>
        <header class="main-header">
            <div class="d-flex align-items-center logo-box justify-content-start">
                <a href="/" class="logo d-flex align-items-center">
                    <div class="logo-mini w-50">
                        <span class="light-logo rounded-pill"><img src="{{ asset('images/icon.png') }}" alt="logo"></span>
                        <span class="dark-logo rounded-pill"><img src="{{ asset('images/icon.png') }}" alt="logo"></span>
                    </div>
                    <div class="logo-lg">
                        <h5 class="text-white ml-5 m-0">{{ getCompany()->name }}</h5>
                    </div>
                </a>
            </div>
            <nav class="navbar navbar-static-top">
                <div class="app-menu">
                    <ul class="header-megamenu nav">
                        <li class="btn-group nav-item">
                            <a href="#" class="waves-effect waves-light nav-link push-btn btn-primary-light" data-toggle="push-menu" role="button">
                                <i data-feather="align-left"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="navbar-custom-menu r-side">
                    <ul class="nav navbar-nav"></ul>
                </div>
            </nav>
        </header>
        <aside class="main-sidebar">
            <section class="sidebar position-relative">
                <div class="multinav">
                    <div class="multinav-scroll" style="height: 100%;">
                        <ul class="sidebar-menu" data-widget="tree">
                            <li id="application">
                                <a href="{{ route('application') }}"><i data-feather="file-text"></i><span>Formulir Lamaran</span></a>
                            </li>
                        </ul>
                        <div class="sidebar-widgets">
                            <div class="copyright text-center m-25 text-white-50">
                                <p><strong class="d-block">{{ getCompany()->name }}</strong>
                                    ©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> All Rights Reserved
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </aside>
        <div class="content-wrapper">
            <div class="container-full">
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item">
                                        <a href="http://ptusi.co.id/" target="_blank" rel="noopener noreferrer">{{ getCompany()->name }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        {{ Str::headline($title) }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <form action="{{ route('application.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Formulir Lamaran</h3>
                            </div>
                            <div class="box-body">
                                @if ($is_expiry == true)
                                    <p class="mb-0">Maaf, batas waktu pengisian formulir lamaran anda sudah habis!</p>
                                @else
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <input type="hidden" name="labor_application_id" value="{{ $model?->labor_application_id }}">
                                @endif
                            </div>
                            <div class="box-footer">
                                <div class="d-flex {{ $is_expiry == true ? 'justify-content-center' : 'justify-content-end' }} gap-2">
                                    @if ($is_expiry == true)
                                        <x-button color="p rimary" link="http://ptusi.co.id/" label="Kembali" />
                                    @else
                                        <x-button type="submit" color="primary" label="simpan" icon="save" fontawesome size="sm" />
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
        <footer class="main-footer">
            &copy;
            <script>
                document.write(new Date().getFullYear())
            </script>
            <a href="http://ptusi.co.id/" target="_blank" rel="noopener noreferrer">{{ Str::upper(config('app.name', 'United Shiping Indonesia')) }}</a>. All Rights Reserved.
        </footer>
    </div>

    <script src="{{ asset('/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('/vendors/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('/vendors/js/jquery-price-format/jquery.priceformat.min.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-select/dist/js/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('/vendors/js/template.js') }}"></script>
    <script src="{{ asset('/js/select-2.js') }}"></script>
    <script src="{{ asset('/js/moment.js') }}"></script>
    <script src="{{ asset('/js/admin/sidebar.js') }}"></script>
    <script src="{{ asset('/js/input/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('/js/identity.js') }}"></script>
    <script src="{{ asset('js/sweet-alert/sweetalert2.all.min.js') }}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js" integrity="sha512-JZSo0h5TONFYmyLMqp8k4oPhuo6yNk9mHM+FY50aBjpypfofqtEWsAgRDQm94ImLCzSaHeqNvYuD9382CEn2zw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('.select2').select2({
            width: '100%'
        });

        $('.datepicker-input:not([readonly])').datepicker({
            format: 'dd-mm-yyyy',
        });

        $('.month-year-picker-input').datepicker({
            format: 'mm-yyyy',
            viewMode: "months",
            minViewMode: "months"
        });
    </script>
    <script>
        let emergencyContactIndex = 0;

        $(document).ready(function() {
            $('form').each(function() {
                $(this).submit(function() {
                    $(this).find('input[type=submit]').prop('disabled', true);
                    $(this).find('button[type=submit]').prop('disabled', true);
                });
            });

            const initializeEmergencyContact = () => {

                const deleteEmergencyContact = (row_index) => {
                    $(`#emergency-row-${row_index}`).remove();
                };

                const addEmergencyContact = (row_index) => {
                    emergencyContactIndex++;

                    let btn = '';

                    if (row_index == 0) {
                        btn = `<x-button color="primary" id="add-emergency-contact" icon="plus" fontawesome size="sm" />`;
                    } else {
                        btn = `<x-button color="danger" id="remove-emergency-contact-${row_index}" icon="minus" fontawesome size="sm" />`;
                    }

                    $('#row-emergency-contact').append(`
                        <div class="row" id="emergency-row-${row_index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_names[]" label="nama" id="" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_relationships[]" label="hubungan" id="" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_phones[]" label="nomor hp" id="" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="emergency_contact_addresses[]" label="alamat" id="" required />
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-self-end">
                                <div class="form-group">
                                    ${btn}
                                </div>
                            </div>
                        </div>
                    `);

                    if (row_index == 0) {
                        $('#add-emergency-contact').click(function(e) {
                            e.preventDefault();
                            addEmergencyContact(emergencyContactIndex);
                        });
                    } else {
                        $(`#remove-emergency-contact-${row_index}`).click(function(e) {
                            e.preventDefault();
                            deleteEmergencyContact(row_index);
                        });
                    }
                };

                addEmergencyContact(emergencyContactIndex);
            };
            initializeEmergencyContact();
        });

        sidebarActive('#{{ $main }}');
    </script>

</body>

</html>
