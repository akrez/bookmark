<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookmarkService;
use App\Services\NetscapeService;
use App\Services\UrlService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class NetscapeController extends Controller
{
    public function import(Request $request)
    {
        $userId = $request->user()->id;
        $now = now();
        $bookmarkService = BookmarkService::new();
        $urlService = UrlService::new();

        $netscapeImportRes = NetscapeService::new()->import($request->post());
        if (! $netscapeImportRes->isSuccessful()) {
            return ApiResponse::make($netscapeImportRes->getStatus());
        }

        $bookmarks = $netscapeImportRes->getData('bookmarks');
        foreach ($bookmarks as $bookmark) {
            $bookmarkService->store($userId, [
                'url_id' => $urlService->firstOrCreate($bookmark['href'])?->getData('url.id'),
            ], $now);
        }

        return ApiResponse::make(200);
    }

    public function export(Request $request)
    {
        return NetscapeService::new()->export(
            $request->user()->id,
            (array) $request->all()
        );
    }
}
