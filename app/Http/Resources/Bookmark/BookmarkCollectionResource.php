<?php

namespace App\Http\Resources\Bookmark;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class BookmarkCollectionResource extends JsonResource
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
            'name' => $resource->name,
            'collection_count' => $resource->collection_count,
        ];
    }
}
