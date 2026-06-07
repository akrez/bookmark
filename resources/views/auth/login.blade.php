@extends('layouts.app')

@section('title', __('Login'))

@php
    $params = [
        'urls' => [
            'api.auth.login' => route('api.auth.login'),
            'bookmarks.index' => route('bookmarks.index'),
        ],
    ];
@endphp

@section('content')
    <div class="container-fluid min-vh-100 d-flex justify-content-center flex-column bg-info" x-data="data()"
        x-init="initData({{ json_encode($params) }})">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="row bg-white p-4 rounded-4 position-relative my-5 pt-5 align-items-center">
                    <span
                        class="position-absolute top-0 start-50 translate-middle badge bg-info rounded-pill w-auto px-5 border border-2 border-white">
                        <h1 class="h2">{{ __('Login') }}</h1>
                    </span>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <img class="w-50" src="{{ url('images/story/login.svg') }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form class="w-100" method="POST">
                            <div class="row mb-3">
                                <label for="email" class="col-lg-4 col-form-label text-lg-end">
                                    Email Address
                                </label>
                                <div class="col-lg-8">
                                    <input id="email" type="email"
                                        :class="{ 'form-control': true, 'is-invalid': !models.email }" name="email"
                                        required x-model="models.email">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="password" class="col-lg-4 col-form-label text-lg-end">
                                    Password
                                </label>
                                <div class="col-lg-8">
                                    <input id="password" type="password"
                                        :class="{ 'form-control': true, 'is-invalid': !models.password }" name="password"
                                        required x-model="models.password">
                                </div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-lg-8 offset-lg-4">
                                    <button type="button" class="btn btn-primary" @click="authLogin()"
                                        :disabled="loading.callAuthLogin || !models.email || !models.password">
                                        Login
                                    </button>
                                    <a class="btn btn-link d-inline" href="{{ route('auth.register') }}">
                                        {{ __('Don\'t have an account? Register') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function data() {
            return {
                urls: {},
                loading: {
                    callAuthLogin: false,
                },
                models: {
                    email: null,
                    password: null,
                },
                async initData(initParams) {
                    this.urls = initParams.urls;
                },
                async authLogin() {
                    this.callAuthLogin({
                        email: this.models.email,
                        password: this.models.password,
                    });
                },
                async callAuthLogin(data) {
                    try {
                        if (this.loading.callAuthLogin) return;
                        this.loading.callAuthLogin = true;

                        const res = await this.$store.call.postJson(this.urls['api.auth.login'], data);
                        const resJson = await res.json();

                        if (res.ok) {
                            this.$store.alert.success(resJson.message);
                            this.$store.auth.set(resJson.data.user, resJson.data.token);
                            window.location.href = this.urls['bookmarks.index'];
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error');
                    } finally {
                        this.loading.callAuthLogin = false;
                    }
                },
            };
        }
    </script>
@endsection
