<?php

namespace App\Services;

use App\Http\Resources\User\ProfileResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthService extends Service
{
    public function register(array $input)
    {
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        return ApiResponse::make(201)->data([
            'user' => new ProfileResource($user),
        ]);
    }

    public function login(array $input)
    {
        $validator = Validator::make($input, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }

        $user = User::where('email', $input['email'])->first();
        if ($user && Hash::check($input['password'], $user->password)) {
            return ApiResponse::make(200)->data([
                'user' => new ProfileResource($user),
                'token' => $user->createToken('token')->plainTextToken,
            ]);
        }

        return ApiResponse::make(401)->message(__('auth.failed'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout($accessToken)
    {
        return ApiResponse::make(($accessToken && $accessToken->delete()) ? 200 : 403);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(User $user)
    {
        return ApiResponse::make(200)->data([
            'user' => new ProfileResource($user),
        ]);
    }
}
