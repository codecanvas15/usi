@extends('layouts.app')

@section('title', 'Forgot Password - ')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh">
            <div class="col-lg-5 col-xl-4">
                <div class="box">

                    <div class="box-header text-center">
                        <h2 class="text-primary">You forgot your password?</h2>
                        <p class="mb-0">You seem to have forgotten your password. Don't worry we will help you reset your password.</p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}">
                        <div class="box-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            @include('components.validate-error')
                            @csrf
                            <div class="form-group">
                                <x-input type="email" name="email" required autofocus />
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="row px-10">
                                <x-button type="submit" color="danger" label="Send password reset link" icon="send" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
