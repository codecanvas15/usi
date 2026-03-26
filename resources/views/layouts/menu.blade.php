<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('/images/icon.png') }}">

    <title>@yield('title') {{ Str::headline(config('app.name', 'United Shipping Indonesia')) }}</title>

    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed" style="background-image: url({{ asset('images/auth/bg-auth.jpg') }})">

    <div id="loader"></div>

    @yield('breadcrumbs')
    @yield('content')

    <!-- Vendor JS -->
    <script src="{{ asset('/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('/vendors/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('/vendors/js/jquery-price-format/jquery.priceformat.min.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-select/dist/js/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.js') }}"></script>

    <!-- Deposito Admin App -->
    <script src="{{ asset('/vendors/js/template.js') }}"></script>
    <script src="{{ asset('/js/select-2.js') }}"></script>
    <script src="{{ asset('/js/moment.js') }}"></script>
    <script src="{{ asset('/js/admin/sidebar.js') }}"></script>
    <script src="{{ asset('js/notification.js') }}"></script>

    <div class="toast-container top-0 end-0 p-3" style="position: fixed!important;">
        @include('components.toast')
    </div>
    @yield('js')
    @stack('script')
</body>

</html>
