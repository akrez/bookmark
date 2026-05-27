<?php

namespace App\Http\Resources\Tag;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class TagResource extends JsonResource
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
            'name' => $resource->name,
            'updated_at' => $resource?->updated_at?->toIso8601String(),
            'created_at' => $resource?->created_at?->toIso8601String(),
        ];
    }
}
