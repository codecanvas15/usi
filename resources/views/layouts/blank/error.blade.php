<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('/images/icon.png') }}">

    <title>@yield('title') {{ getCompany()->name }}</title>
    {{-- <link rel="stylesheet" href="{{ asset('/css/app.css') }}"> --}}

    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @yield('css')

    <style>
        .up-hover:hover {
            animation: up_hover .5s ease-in;
        }

        @keyframes up_hover {
            0% {
                transform: translateY(0);
            }

            25% {
                transform: translateY(5px);
            }

            50% {
                transform: rotate(5deg);
            }

            60% {
                transform: translateY(-5px);
            }

            75% {
                transform: rotate(-5deg);
            }
        }
    </style>
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed" style="background-image: url('{{ asset('images/error/bg-error.svg') }}'); background-position: center; background-repeat: no-repeat; background-size: contain;">

    <section class="error-page h-p100">
        <div class="container h-p100">
            <div class="row h-p100 align-items-center justify-content-center text-center">
                <div class="col-lg-7 col-md-10 col-12">
                    <div class="box border border-danger up-hover">
                        <div class="box-body">
                            <div class="rounded10 p-50">
                                <h1 style="font-size: 8rem">@yield('code')</h1>
                                <h3 class="text-danger">@yield('text')</h3>
                                <h4 class="capitalize">@yield('sub_text')</h4>
                                <x-button link="{{ url()->previous() }}" color="danger" class="mt-30" icon="arrow-left" fontawesome label="Previous Page" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vendor JS -->
    <script src="{{ asset('/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('/vendors/assets/icons/feather-icons/feather.min.js') }}"></script>

    <div class="toast-container top-0 end-0 p-3" style="position: fixed!important;">
        @include('components.toast')
    </div>

    @yield('js')
    @stack('script')
</body>

</html>
