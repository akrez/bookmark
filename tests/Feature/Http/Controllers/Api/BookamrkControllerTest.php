<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Bookmark;
use App\Models\Url;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookamrkControllerTest extends TestCase
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

        Bookmark::factory(100)->create(['user_id' => $userId]);

        $response = $this->get(route('api.bookmarks.index'), ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);
    }

    public function test_successful_store(): void
    {
        $loginResponse = $this->login();
        $userId = $loginResponse->getData('user.id');
        $token = $loginResponse->getData('token');

        $url = Url::factory()->make();
        $bookmark = Bookmark::factory()->make(['user_id' => $userId]);

        $response = $this->postJson(route('api.bookmarks.store'), [
            'url' => $url->url,
            'collection' => $bookmark->collection,
            'title' => $bookmark->title,
            'description' => $bookmark->description,
            'note' => $bookmark->note,
        ], ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(201);
    }

    public function test_successful_show(): void
    {
        $loginResponse = $this->login();
        $userId = $loginResponse->getData('user.id');
        $token = $loginResponse->getData('token');

        $bookmark = Bookmark::factory()->create(['user_id' => $userId]);

        $response = $this->get(route('api.bookmarks.show', ['id' => $bookmark->id]), ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);
    }

    public function test_successful_update(): void
    {
        $loginResponse = $this->login();
        $userId = $loginResponse->getData('user.id');
        $token = $loginResponse->getData('token');

        $url = Url::factory()->make();
        $oldBookmark = Bookmark::factory()->create(['user_id' => $userId]);
        $bookmark = Bookmark::factory()->make(['user_id' => $userId]);

        $response = $this->put(route('api.bookmarks.update', ['id' => $oldBookmark->id]), [
            'url' => $url->url,
            'collection' => $bookmark->collection,
            'title' => $bookmark->title,
            'description' => $bookmark->description,
            'note' => $bookmark->note,
        ], ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);
    }

    public function test_successful_destroy(): void
    {
        $loginResponse = $this->login();
        $userId = $loginResponse->getData('user.id');
        $token = $loginResponse->getData('token');

        $bookmark = Bookmark::factory()->create(['user_id' => $userId]);

        $response = $this->get(route('api.bookmarks.destroy', ['id' => $bookmark->id]), ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);
    }

    public function test_successful_update_attributes_to_null()
    {
        $loginResponse = $this->login();
        $userId = $loginResponse->getData('user.id');
        $token = $loginResponse->getData('token');

        $oldBookmark = Bookmark::factory()->create([
            'user_id' => $userId,
            'read_at' => now(),
        ]);

        $response = $this->patchJson(route('api.bookmarks.updateAttribute', ['id' => $oldBookmark->id]), [
            'is_read' => false,
        ], ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.bookamrk.read_at', null);
    }

    public function test_successful_store_tags()
    {
        $loginResponse = $this->login();
        $userId = $loginResponse->getData('user.id');
        $token = $loginResponse->getData('token');

        $oldBookmark = Bookmark::factory()->create(['user_id' => $userId]);

        $response = $this->postJson(route('api.bookmarks.tags.store', ['id' => $oldBookmark->id]), [
            'tags' => fake()->words(3),
        ], ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);
    }

    public function test_successful_collections(): void
    {
        $loginResponse = $this->login();
        $userId = $loginResponse->getData('user.id');
        $token = $loginResponse->getData('token');

        Bookmark::factory(100)->create(['user_id' => $userId]);

        $response = $this->get(route('api.bookmarks.collections'), ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200);
    }
}
