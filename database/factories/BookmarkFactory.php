<?php

namespace Database\Factories;

use App\Models\Bookmark;
use App\Models\User;
use App\Support\Helper;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookmarkFactory extends Factory
{
    protected $model = Bookmark::class;

    public function definition(): array
    {
        $url = $this->faker->url();

        return [
            'url' => $url,
            'base_url' => app(Helper::class)->extractBaseUrl($url),
            'collection' => $this->faker->randomElement(['work', 'personal', 'reading', 'research', 'inspiration']),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional(0.7)->paragraph(2),
            'notes' => $this->faker->optional(0.5)->paragraph(3),
            'read_at' => $this->faker->optional(0.3)->dateTimeBetween('-6 months', 'now'),
            'archived_at' => $this->faker->optional(0.1)->dateTimeBetween('-3 months', 'now'),
            'shared_at' => $this->faker->optional(0.2)->dateTimeBetween('-4 months', 'now'),
            'favorited_at' => $this->faker->optional(0.25)->dateTimeBetween('-5 months', 'now'),
            'user_id' => User::factory(),
        ];
    }
}
