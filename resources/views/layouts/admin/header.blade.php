<style>
    #notif-toggle.notempty::after {
        content: attr(counter);
        background-color: red;
        width: 15px;
        height: 15px;
        border-radius: 15px;
        position: absolute;
        right: 10px;
        top: 24px;
        font-size: 10px;
        color: #fff;
        text-align: center;
        z-index: 20000000;
    }

    @media only screen and (max-width: 767px) {
        #notif-toggle.notempty::after {
            top: 8px;
        }
    }
</style>
<header class="main-header">
    <div class="d-flex align-items-center logo-box justify-content-start">
        <!-- Logo -->
        <a href="/" class="logo d-flex align-items-center">
            <!-- logo-->
            <div class="logo-mini w-50">
                <span class="light-logo rounded-pill"><img src="{{ url('storage/' . getCompany()->logo) }}" alt="logo"></span>
                <span class="dark-logo rounded-pill"><img src="{{ url('storage/' . getCompany()->logo) }}" alt="logo"></span>
            </div>
            <div class="logo-lg">
                <h5 class="text-white ml-5 m-0">{{ getCompany()->name }}</h5>
                {{-- <span class="light-logo"><img src="{{ asset('images/icon.png') }}" alt="logo"></span> --}}
                {{-- <span class="dark-logo"><img src="{{ asset('images/icon.png') }}" alt="logo"></span> --}}
            </div>
        </a>
    </div>
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <div class="app-menu">
            <ul class="header-megamenu nav">
                <li class="btn-group nav-item">
                    <a href="#" class="waves-effect waves-light nav-link push-btn btn-primary-light" data-toggle="push-menu" role="button" id="toggle-sidebar-button">
                        <i data-feather="align-left"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="navbar-custom-menu r-side">
            <ul class="nav navbar-nav">

                <!-- Notifications -->
                <li class="dropdown notifications-menu position-relative" id="notif-toggle" counter="">
                    <a href="#" class="waves-effect waves-light dropdown-toggle btn-info-light" data-bs-toggle="dropdown" title="Notifications" onclick="get_notification()">
                        <i data-feather="bell"></i>
                    </a>
                    <ul class="dropdown-menu animated bounceIn" style="max-width: 500px; border: 1px solid #ecf0f1;">
                        <li class="header">
                            <div class="p-20">
                                <div class="flexbox">
                                    <div>
                                        <h4 class="mb-0 mt-0">Notifikasi</h4>
                                    </div>
                                    <div>
                                        <a href="javascript:;" onclick="clear_notification()" class="text-danger">Bersihkan</a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="slimScrollDiv" style="position: relative; width: auto; height: 50vh;">
                                <ul class="menu sm-scrol" style="overflow-y: scroll; width: auto; height: 50vh;" id="notification_list_wrap">

                                </ul>
                                <div class="slimScrollBar" style="background: rgb(0, 0, 0); width: 7px; position: absolute; top: 0px; opacity: 0.1; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 220.07px;"></div>
                                <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>
                            </div>
                        </li>
                        <li class="footer">
                            <a href="{{ route('admin.authorization.index') }}">View all</a>
                        </li>
                    </ul>
                </li>

                <!-- User Account-->
                <li class="dropdown user user-menu">
                    <a href="#" class="waves-effect waves-light dropdown-toggle w-auto l-h-12 bg-transparent py-0 no-shadow" data-bs-toggle="dropdown" title="User">
                        <div class="avatar rounded-10 bg-primary-light h-40 w-40">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu animated flipInX">
                        <li class="user-body">
                            <a href="{{ route('admin.download-report.index') }}" class="dropdown-item">
                                <i>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-download">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="7 10 12 15 17 10" />
                                        <line x1="12" x2="12" y1="15" y2="3" />
                                    </svg>
                                </i>
                                <span>
                                    Unduhan
                                </span>
                            </a>
                            <a class="dropdown-item" data-toggle="control-sidebar" title="Setting" href="javascript;"><i class="ti-user text-muted me-2"></i>{{ Str::headline(Auth::user()->name) }}</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}" id="logout"><i class="ti-lock text-muted me-2"></i> Logout</a>
                            <form action="{{ route('logout') }}" method="post" id="form-logout">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<!-- Control Sidebar -->
<aside class="control-sidebar">

    <div class="rpanel-title"><span class="pull-right btn btn-circle btn-danger" data-toggle="control-sidebar"><i class="ion ion-close text-white"></i></span> </div> <!-- Create the tabs -->

    @if ($employee)
        <div class="form-group">
            <label for="">Nama</label>
            <p>{{ $employee->name }} - {{ $employee->NIK }}</p>
        </div>

        <div class="form-group">
            <label for="">Jabatan</label>
            <p>{{ $employee->position?->nama }}</p>
        </div>

        <div class="form-group">
            <label for="">No Telp.</label>
            <p>{{ $employee->nomor_telepone }}</p>
        </div>

        <div class="form-group">
            <label for="">Tanggal Lahir</label>
            <p>{{ $employee->tempat_lahir }} {{ localDate($employee->tanggal_lahir) }}</p>
        </div>

        <div class="form-group">
            <label for="">Email</label>
            <p>{{ $employee->email }}</p>
        </div>

        <div class="form-group">
            <img src="{{ url('storage/' . $employee->file) }}" alt="">
        </div>
    @else
        <h5>User tidak mempunyai data pegawai.</h5>
    @endif
</aside>
<!-- /.control-sidebar -->

<!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>

@push('script')
    <script>
        $('#logout').on('click', function(e) {
            e.preventDefault();
            $('#form-logout').submit();
        });
    </script>
@endpush
