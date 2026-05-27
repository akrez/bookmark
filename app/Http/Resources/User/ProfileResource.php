<?php

namespace App\Http\Resources\User;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(?Request $request = null)
    {
        $resource = $this->resource;

        return [
            'name' => $resource->name,
            'email' => $resource->email,
        ];
    }
}
