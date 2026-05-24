<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class JsonResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    public function toArray(?Request $request = null)
    {
        return (array) @json_decode(json_encode(parent::toArray($request ?? request())), true);
    }
}
