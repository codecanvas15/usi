@extends('layouts.app')

@section('title', 'Reset Pasword - ')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh">
            <div class="col-md-5 col-lg-4">
                <div class="card">
                    <div class="box-header text-center">
                        <h2 class="text-primary">Reset Password</h2>
                        <p class="mb-0">One more step for resetting your password.</p>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}">
                        <div class="card-body">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <x-input type="email" name="email" required autofocus />
                            </div>

                            <div class="form-group">
                                <x-input type="password" name="password" required />
                            </div>

                            <div class="form-group">
                                <x-input type="password" name="password_confirmation" required />
                            </div>

                        </div>
                        <div class="box-footer">
                            <div class="row px-10">
                                <x-button type="submit" color="danger" label="Reset Password" icon="send" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
