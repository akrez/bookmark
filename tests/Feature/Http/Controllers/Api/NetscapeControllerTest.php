<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NetscapeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function login()
    {
        $authService = AuthService::new();
        $user = User::factory()->make();
        $password = '12345678';
        $registeredUser = $authService->register([
            'name' => $user->name, 
            'email' => $user->email, 
            'password' => $password, 
            'password_confirmation' => $password,
        ]);

        return $authService->login(['email' => $user->email, 'password' => $password]);
    }

    public function test_successful_import(): void
    {
        $loginResponse = $this->login();
        $userId = $loginResponse->getData('user.id');
        $token = $loginResponse->getData('token');

        $response = $this->post(route('api.netscape.import'), [
            'html' => file_get_contents(base_path('tests/netscape.html')),
        ], ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);
    }
}
