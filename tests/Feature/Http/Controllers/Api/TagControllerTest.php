<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Bookmark;
use App\Models\Tag;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
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

    public function test_successful_index(): void
    {
        $loginResponse = $this->login();
        $userId = $loginResponse->getData('user.id');
        $token = $loginResponse->getData('token');

        $tags = Tag::factory(10)->create(['user_id' => $userId]);
        for ($i = 0; $i < 51; $i++) {
            Bookmark::factory()
                ->hasAttached(fake()->randomElements($tags))
                ->create(['user_id' => $userId]);
        }

        $response = $this->get(route('api.tags.index'), ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);
    }
}
