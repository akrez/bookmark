<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        return AuthService::new()->register($request->all());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        return AuthService::new()->login($request->all());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        return AuthService::new()->logout($request->user()?->currentAccessToken());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        return AuthService::new()->profile($request->user());
    }
}
