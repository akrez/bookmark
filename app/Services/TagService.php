<?php

namespace App\Services;

use App\Http\Resources\Tag\TagResource;
use App\Models\Tag;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Validator;

class TagService extends Service
{
    public function index(int $userId)
    {
        $tags = Tag::where('user_id', $userId)
            ->withCount('bookmarks')
            ->orderByDesc('bookmarks_count')
            ->get();

        return ApiResponse::make(200)->data([
            'tags' => TagResource::collection($tags)->toArray(request()),
        ]);
    }

    public function store(int $userId, array $input)
    {
        $validator = Validator::make($input, [
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'alpha_dash', 'max:64'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }
        $validated = $validator->validated() + [
            'tags' => [],
        ];

        return $this->storeTags($userId, $validated['tags']);
    }

    public function storeTags(int $userId, array $tags)
    {
        $tags = collect($tags)
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique()
            ->map(function ($name) use ($userId) {
                return Tag::firstOrCreate([
                    'name' => $name,
                    'user_id' => $userId,
                ]);
            });

        return ApiResponse::make(200)->data([
            'tags' => TagResource::collection($tags),
        ]);
    }
}
