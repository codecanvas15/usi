@extends('layouts.app')

@section('title', 'Login - ')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh">
            <div class="col-lg-5 col-xl-4">
                <div class="box">
                    <div class="box-header text-center">
                        <img src="{{ url('storage/' . getCompany()->logo) }}" alt="" width="90">
                    </div>
                    <div class="box-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group">
                                <x-input type="email" label="email" name="email" required autofocus />
                            </div>

                            {{-- <div class="form-group">
                                <x-input type="username" label="username" name="username" required autofocus />
                            </div> --}}

                            <div class="form-group">
                                <label for="password" class="form-label">Password <span class="text-danger"></span></label>
                                <div class="input-group mb-3">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="password" required>
                                    <span class="input-group-text" onclick="togglePassword()"><i class="fa fa-eye" id="icon-toggle-password"></i></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>

                            <div class="row px-10">
                                <x-button type="submit" color="danger" label="Login" fontawesome icon="fa-solid fa-right-to-bracket" />
                            </div>
                        </form>
                    </div>
                    <div class="box-footer">
                        <p class="text-center">
                            {{-- Forgot your password? <a href="{{ route('password.request') }}" class="text-primary">Click here</a> --}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const togglePassword = () => {
            if ($('#password').prop('type') == "password") {
                $('#password').prop('type', 'text');
                $('#icon-toggle-password').prop('class', 'fa fa-eye-slash');
            } else {
                $('#password').prop('type', 'password');
                $('#icon-toggle-password').prop('class', 'fa fa-eye');
            }
        }
    </script>
@endsection
