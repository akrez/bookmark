@extends('layouts.app')

@section('title', __('Register'))

@section('content')
    <div class="container min-vh-100 d-flex justify-content-center flex-column">
        <div class="row bg-white p-4 rounded-4 position-relative my-5 pt-5 align-items-center">
            <span
                class="position-absolute top-0 start-50 translate-middle badge bg-info rounded-pill w-auto px-5 border border-1 border-white">
                <h1 class="h2">{{ __('Register') }}</h1>
            </span>
            <div class="col-lg-6">
                <div class="text-center">
                    <img class="w-50" src="{{ url('images/story/register.svg') }}">
                </div>
            </div>
            <div class="col-lg-6">
                <form class="w-100" method="POST" action="{{route('api.auth.register')}}">
                    @csrf
                    <div class="row mb-3">
                        <label for="name" class="col-md-4 col-form-label text-md-end">
                            Name
                        </label>
                        <div class="col-md-8">
                            <input id="name" type="text" class="form-control" name="name" required autofocus>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="email" class="col-md-4 col-form-label text-md-end">
                            Email Address
                        </label>
                        <div class="col-md-8">
                            <input id="email" type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="password" class="col-md-4 col-form-label text-md-end">
                            Password
                        </label>
                        <div class="col-md-8">
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="password-confirm" class="col-md-4 col-form-label text-md-end">
                            Confirm Password
                        </label>
                        <div class="col-md-8">
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                Register
                            </button>
                            <a class="btn btn-link" href="{{ route('auth.login') }}">
                                {{ __('Already have an account? Login') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
