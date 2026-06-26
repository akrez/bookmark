<?php

namespace App\Http\Resources\Bookmark;

use App\Http\Resources\JsonResource;
use App\Http\Resources\Tag\TagNameResource;
use Illuminate\Http\Request;

class BookmarkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(?Request $request = null)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'user_id' => $resource->user_id,
            'collection' => $resource->collection,
            'url' => $resource->url,
            'note' => $resource->note,
            'read_at' => $resource?->read_at?->toIso8601String(),
            'archived_at' => $resource?->archived_at?->toIso8601String(),
            'shared_at' => $resource?->shared_at?->toIso8601String(),
            'favorited_at' => $resource?->favorited_at?->toIso8601String(),
            'updated_at' => $resource?->updated_at?->toIso8601String(),
            'created_at' => $resource?->created_at?->toIso8601String(),
            'tags' => $this->whenLoaded('tags', fn ($tags) => TagNameResource::collection($tags)),
        ];
    }
}
