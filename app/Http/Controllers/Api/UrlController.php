<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UrlService;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function fetch(Request $request)
    {
        return UrlService::new()->fetch();
    }
}
