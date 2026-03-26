@extends('layouts.menu')

@section('content')
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
        
        #notif-sidebar.notempty::after {
            content: attr(counter);
            padding-top: 5px;
            background-color: red;
            width: 25px;
            height: 25px;
            border-radius: 15px;
            position: absolute;
            right: -5px;
            top: -5px;
            font-size: 14px;
            color: #fff;
            text-align: center;
            z-index: 20000000;
        }

        .menu-button {
            transition: 500ms;
            border-radius: 10px;
        }

        .menu-button:hover {
            cursor: pointer;
            transition: 500ms;
            transform: scale(1.1,1.1);
            box-shadow: -5px 5px 10px
        }

        @media only screen and (max-width: 767px) {
            #notif-toggle.notempty::after {
                top: 8px;
            }
            #notif-sidebar.notempty::after {
                top: 8px;
            }
        }
    </style>
    @include('layouts.header-menu')

    <div class="container" style="margin-top: 5rem; padding: 0rem 10rem;">
        <div class="position-absolute top-0 bottom-0 end-0 start-0" style="background-color: rgba(0,0,0,0.5)"></div>
        <div class="d-flex justify-content-center">
            <div class="row align-items-center justify-content-start animated bounceIn" style="gap: 5rem">
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px" href="/">
                    <div class="bg-danger  d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(0, 0, 25), rgb(13, 0, 110), rgb(0, 0, 255)">
                        <i class="fa-solid fa-gauge" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2"  style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Dasbor</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px" href="/authorization">
                    <div class="position-relative d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(0,0,0), rgb(86, 86, 86), rgb(245, 245, 245));">
                        <i class="fa-solid fa-at text-white" style="font-size: 50px;" id="notif-sidebar"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2"  style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Otorisasi</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px" href="/branch">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(26, 20, 0), rgb(120, 92, 0), rgb(255, 208, 0));">
                        <i class="fa-solid fa-box-archive text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2"  style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Master</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px" href="/sales">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(28, 36, 0), rgb(45, 128, 0), rgb(195, 255, 0));">
                        <i class="fa-solid fa-chart-line text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2"  style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Penjualan</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px" href="/purchase">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(34, 0, 0), rgb(103, 0, 0), rgb(255, 0, 0));">
                        <i class="fa-solid fa-credit-card text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2"  style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Pembelian</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px;" href="/stock-card">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(0, 18, 14), rgb(1, 97, 94), rgb(0, 251, 255));">
                        <i class="fa-solid fa-cubes text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2" style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Gudang</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px;" href="/labor-demand">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(18, 0, 14), rgb(97, 1, 84), rgb(255, 0, 247));">
                        <i class="fa-brands fa-osi text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2" style="width: 130px; font-size: 16px; transform: translate(-10%,0)">HRD</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px;" href="/cash-advance-receive">
                    <div class="position-relative d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(22, 95, 20), rgb(6, 48, 0), rgb(35, 185, 62));">
                        <i class="fa-solid fa-dollar-sign text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2" style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Keuangan & Akuntansi</p>
                </a>
                {{-- <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px;" href="/project">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(40, 119, 40), rgb(1, 97, 1), rgb(255, 247, 0));">
                        <i class="fa-solid fa-puzzle-piece text-white" style="font-size: 50px; transform: translate(5px,-5px)"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2" style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Proyek</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px;" href="/cash-advance-receive">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(119, 40, 40), rgb(97, 1, 1), rgb(255, 247, 0));">
                        <i class="fa-solid fa-dollar-sign text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2" style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Akuntansi</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px;" href="/journal">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(40, 119, 52), rgb(6, 97, 1), rgb(255, 247, 0));">
                        <i class="fa-solid fa-book-journal-whills text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2" style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Journal</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px;" href="/finance-report">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(47, 40, 119), rgb(9, 1, 97), rgb(26, 255, 0));">
                        <i class="fa-solid fa-newspaper text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2" style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Finance Report</p>
                </a>
                <a class="col-6 col-md-2 text-center" style="height: 100px; width: 100px;" href="/attendance">
                    <div class=" d-flex align-items-center justify-content-center menu-button" style="height: 100px; width: 100px; background-image: linear-gradient(45deg, rgb(208, 214, 35), rgb(85, 52, 3), rgb(26, 255, 0));">
                        <i class="fa-solid fa-clipboard-user text-white" style="font-size: 50px;"></i>
                    </div>
                    <p class="fw-semibold text-white text-center mt-2" style="width: 130px; font-size: 16px; transform: translate(-10%,0)">Presensi</p>
                </a> --}}
            </div>
        </div>
    </div>
@endsection