<?php

namespace App\Http\Resources\Url;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class UrlResource extends JsonResource
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
            'url' => $resource->url,
            'base_url' => $resource->base_url,
            'title' => $resource->title,
            'description' => $resource->description,
            'favicon' => $resource->favicon,
            'fetched_at' => $resource?->fetched_at?->toIso8601String(),
        ];
    }
}
