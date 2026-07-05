<?php

namespace Database\Factories;

use App\Models\Url;
use App\Support\Helper;
use Illuminate\Database\Eloquent\Factories\Factory;

class UrlFactory extends Factory
{
    protected $model = Url::class;

    public function definition(): array
    {
        $url = $this->faker->url();

        return [
            'url' => $url,
            'base_url' => app(Helper::class)->extractBaseUrl($url),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional(0.7)->paragraph(2),
            'favicon' => 'https://www.gstatic.com/images/branding/searchlogo/ico/favicon.ico',
        ];
    }
}
