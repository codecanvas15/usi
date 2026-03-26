<nav class="navbar navbar-static-top">
    <div class="navbar-custom-menu r-side ms-auto ">
        <ul class="nav navbar-nav">
            <li class="dropdown notifications-menu position-relative" id="notif-toggle" counter="">
                <a href="#" class="waves-effect waves-light dropdown-toggle btn-info-light" data-bs-toggle="dropdown" title="Notifications" onclick="get_notification()">
                    <i class="fa-solid fa-comments"></i>
                </a>
                <ul class="dropdown-menu animated bounceIn" style="width: 500px; border: 1px solid #ecf0f1;">
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
            <li class="dropdown user user-menu">
                <a href="#" class="waves-effect waves-light dropdown-toggle w-auto l-h-12 bg-transparent py-0 no-shadow" data-bs-toggle="dropdown" title="User">
                    <div class="avatar rounded-10 bg-primary-light h-40 w-40">
                        @if (auth()->user()->employee?->file)
                            <img src="{{ asset('/storage/' . auth()->user()->employee?->file) }}" alt="logo_profile">
                        @else
                            <i class="fa-solid fa-user"></i>
                        @endif
                    </div>
                    <span class="text-white">{{ Auth::user()->name }}</span>
                    {{-- <img src="{{ asset('images/default.jpg') }}" class="avatar rounded-10 bg-primary-light h-40 w-40" alt="" /> --}}
                </a>
                <ul class="dropdown-menu animated flipInX">
                    <li class="user-body">
                        <a class="dropdown-item" href="javascript;"><i class="ti-user text-muted me-2"></i>{{ Str::headline(Auth::user()->name) }}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" id="logout"><i class="ti-lock text-muted me-2"></i> Logout</a>
                        <form action="{{ route('logout') }}" method="post" id="form-logout">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
</nav>