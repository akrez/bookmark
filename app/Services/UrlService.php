<?php

namespace App\Services;

use App\Http\Resources\Url\UrlResource;
use App\Models\Url;
use App\Support\ApiResponse;
use DOMDocument;
use DOMElement;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UrlService extends Service
{
    const DESCRIPTION_LENGTH = 1024;

    public function index()
    {
        $urls = Url::query()
            ->paginate();

        return ApiResponse::make(200)->data([
            'urls' => UrlResource::collection($urls)->toArray(request()),
        ])->paginator($urls);
    }

    public function firstOrCreate(?string $url)
    {
        $validator = Validator::make(['url' => $url], [
            'url' => ['required', 'url', 'max:2048'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }

        $validated = $validator->validated();

        $url = Url::firstOrCreate([
            'url' => $validated['url'],
        ]);

        return ApiResponse::make($url->wasRecentlyCreated ? 201 : 200)->data([
            'url' => (new UrlResource($url))->toArray(request()),
        ]);
    }

    public function show(int $id)
    {
        $url = $this->getUrl($id);
        if (! $url) {
            return ApiResponse::make(404);
        }

        return ApiResponse::make(200)->data([
            'url' => (new UrlResource($url))->toArray(request()),
        ]);
    }

    public function update(int $id, array $input)
    {
        $url = $this->getUrl($id);
        if (! $url) {
            return ApiResponse::make(404);
        }

        $validator = Validator::make($input, [
            'title' => ['nullable', 'string', 'max:512'],
            'description' => ['nullable', 'string', 'max:1024'],
            'favicon' => ['required', 'url', 'max:2048'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }

        $isSuccessful = $url->update([
            'title' => $input['title'],
            'description' => $input['description'],
            'favicon' => $input['favicon'],
        ]);
        if ($isSuccessful) {
            return ApiResponse::make(200)->data([
                'url' => (new UrlResource($url))->toArray(request()),
            ]);
        }

        return ApiResponse::make(500);
    }

    public function destroy(int $id)
    {
        $url = $this->getUrl($id);
        if (! $url) {
            return ApiResponse::make(404);
        }

        return ApiResponse::make($url->delete() ? 200 : 500);
    }

    public function fetch(int $limit = 50): void
    {
        $groups = Url::query()
            ->whereNull('fetched_at')
            ->select('base_url')
            ->selectRaw('MIN(created_at) as created_at_min')
            ->groupBy('base_url')
            ->orderBy('created_at_min')
            ->limit($limit)
            ->get();

        foreach ($groups as $group) {

            $urls = Url::query()
                ->where('base_url', $group->base_url)
                ->whereNull('fetched_at')
                ->orderBy('created_at')
                ->limit($limit)
                ->get()
                ->pluck(null, 'id');

            $headResponses = Http::pool(function (Pool $pool) use ($urls) {
                $requests = [];
                foreach ($urls as $id => $url) {
                    $requests[$id] = $pool->as($id)->withoutVerifying()->timeout(10)->head($url->url);
                }

                return $requests;
            });

            $getResponses = Http::pool(function (Pool $pool) use ($urls, $headResponses) {
                $requests = [];
                foreach ($headResponses as $id => $response) {
                    if (
                        ($response instanceof Response) &&
                        $response->successful() &&
                        str_contains(strtolower($response->header('Content-Type')), 'text/html')
                    ) {
                        $requests[$id] = $pool->as($id)->withoutVerifying()->timeout(10)->get($urls[$id]->url);
                    }
                }

                return $requests;
            });

            foreach ($urls as $id => $url) {
                if (isset($getResponses[$id]) && ($getResponses[$id] instanceof Response) && $getResponses[$id]->successful()) {
                    $data = $this->extract($getResponses[$id]);
                } else {
                    $data = [];
                }
                //
                if (! empty($data['favicon'])) {
                    $data['favicon'] = $this->toAbsoluteUrl($url->url, $data['favicon'])->__toString();
                }
                if (! empty($data['description'])) {
                    $data['description'] = Str::limit($data['description'], 1024, '');
                }
                $data['fetched_at'] = now();
                //
                $url->update($data);
            }
        }
    }

    protected function extract(Response $response)
    {
        $html = $response->body();

        libxml_use_internal_errors(true);
        $doms = new DOMDocument;
        $doms->loadHTML($html);

        $title = $doms->getElementsByTagName('title')->item(0)?->textContent;

        $dom = $this->extractFirstTag($doms, 'meta', 'name', 'description');
        $description = ($dom === null ? null : $dom->getAttribute('content'));

        $dom = $this->extractFirstTag($doms, 'link', 'rel', 'icon');
        $favicon = ($dom === null ? '/favicon.ico' : $dom->getAttribute('href'));

        return [
            'title' => $title,
            'description' => $description,
            'favicon' => $favicon,
        ];
    }

    protected function extractFirstTag(DOMDocument $doms, string $qualifiedName, string $attributeName, string $exceptedAttributeValue): ?DOMElement
    {
        /** @var DOMElement $dom */
        foreach ($doms->getElementsByTagName($qualifiedName) as $dom) {
            $attributeValue = strtolower($dom->getAttribute($attributeName));
            if (str_contains($attributeValue, $exceptedAttributeValue)) {
                return $dom;
            }
        }

        return null;
    }

    protected function toAbsoluteUrl(string $base, string $rel)
    {
        return UriResolver::resolve(
            new Uri($base),
            new Uri($rel)
        );
    }

    protected function getUrl(int $id)
    {
        return Url::query()->find($id);
    }
}
