<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookmarkService;
use App\Services\UrlService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class BookamrkController extends Controller
{
    public function index(Request $request)
    {
        return BookmarkService::new()->index(
            $request->user()->id,
            (array) $request->all()
        );
    }

    public function store(Request $request)
    {
        $urlRes = UrlService::new()->firstOrCreate($request->post('url'));
        if ($urlRes->getStatus() == 422) {
            return $urlRes;
        } elseif (! $urlRes->isSuccessful()) {
            return ApiResponse::make($urlRes->getStatus())->message($urlRes->getMessage());
        }

        return BookmarkService::new()->store($request->user()->id, ['url_id' => $urlRes->getData('url.id')] + $request->post());
    }

    public function show(Request $request, int $id)
    {
        return BookmarkService::new()->show($id, $request->user()->id);
    }

    public function update(Request $request, int $id)
    {
        return BookmarkService::new()->update($id, $request->user()->id, $request->post());
    }

    public function destroy(Request $request, int $id)
    {
        return BookmarkService::new()->destroy($id, $request->user()->id);
    }

    public function collections(Request $request)
    {
        return BookmarkService::new()->collections($request->user()->id);
    }

    public function updateAttributes(Request $request)
    {
        return BookmarkService::new()->updateAttributes($request->user()->id, $request->post());
    }
}
