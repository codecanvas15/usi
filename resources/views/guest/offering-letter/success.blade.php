@extends('guest.layout.app')
@php
    $main = 'labor-application';
    $title = 'lamaran pekerjaan';
@endphp

@section('title', Str::headline("$title") . ' - ')

@section('content')
    <section class="error-page h-p100">
        <div class="container h-p100">
            <div class="row h-p100 align-items-center justify-content-center text-center">
                <div class="col-lg-7 col-md-10 col-12">
                    <div class="rounded10 p-50">
                        <lord-icon src="https://cdn.lordicon.com/puvaffet.json" trigger="loop" colors="primary:#121331,secondary:#3ba4f8" style="width:250px;height:250px">
                        </lord-icon>
                        <img src="../images/auth-bg/404.jpg" class="max-w-200" alt="">
                        <h3 class="text-info">Terima kasih atas tanggapan Anda terhadap penawaran kerja yang kami berikan. Kami menghargai apresiasi dan antusiasme Anda terhadap kesempatan ini</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script src="https://cdn.lordicon.com/bhenfmcm.js"></script>
@endsection
