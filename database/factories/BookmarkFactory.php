<?php

namespace Database\Factories;

use App\Models\Bookmark;
use App\Models\Url;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookmarkFactory extends Factory
{
    protected $model = Bookmark::class;

    public function definition(): array
    {
        return [
            'url_id' => Url::factory(),
            'collection' => $this->faker->randomElement([null, 'work', 'personal', 'reading', 'research', 'inspiration']),
            'note' => $this->faker->optional(0.5)->paragraph(3),
            'read_at' => $this->faker->optional(0.5)->dateTimeBetween('-6 months', 'now'),
            'archived_at' => $this->faker->optional(0.5)->dateTimeBetween('-3 months', 'now'),
            'shared_at' => $this->faker->optional(0.5)->dateTimeBetween('-4 months', 'now'),
            'favorited_at' => $this->faker->optional(0.5)->dateTimeBetween('-5 months', 'now'),
            'user_id' => User::factory(),
        ];
    }
}
