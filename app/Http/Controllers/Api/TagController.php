<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        return TagService::new()->index($request->user()->id);
    }
}
