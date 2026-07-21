<?php

namespace App\Services;

use App\Enums\BookmarkArchiveEnum;
use App\Enums\BookmarkFavoriteEnum;
use App\Enums\BookmarkReadEnum;
use App\Enums\BookmarkShareEnum;
use App\Http\Resources\Bookmark\BookmarkCollection;
use App\Http\Resources\Bookmark\BookmarkCollectionResource;
use App\Http\Resources\Bookmark\BookmarkResource;
use App\Models\Bookmark;
use App\Support\ApiResponse;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BookmarkService extends Service
{
    public function index(int $userId, array $payload, bool $paginated = true)
    {
        $payload['page'] = empty($payload['page']) ? 1 : intval($payload['page']);
        $validator = Validator::make($payload, [
            'collection' => ['sometimes', 'nullable', 'string', 'max:512'],
            'q' => ['sometimes', 'nullable', 'string'],
            'archive' => ['sometimes', Rule::in(BookmarkArchiveEnum::names())],
            'share' => ['sometimes', Rule::in(BookmarkShareEnum::names())],
            'favorite' => ['sometimes', Rule::in(BookmarkFavoriteEnum::names())],
            'read' => ['sometimes', Rule::in(BookmarkReadEnum::names())],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }
        $validated = $validator->validated();

        $query = Bookmark::query()
            ->where('user_id', $userId)
            ->with(['url']);

        $query->when(array_key_exists('read', $validated), function ($query) use ($validated) {
            if ($validated['read'] == BookmarkReadEnum::UNREAD->name) {
                $query = $query->whereNull('read_at');
            } elseif ($validated['read'] == BookmarkReadEnum::READ->name) {
                $query = $query->whereNotNull('read_at');
            }
        });
        $query->when(array_key_exists('share', $validated), function ($query) use ($validated) {
            if ($validated['share'] == BookmarkShareEnum::UNSHARED->name) {
                $query = $query->whereNull('shared_at');
            } elseif ($validated['share'] == BookmarkShareEnum::SHARED->name) {
                $query = $query->whereNotNull('shared_at');
            }
        });
        $query->when(array_key_exists('favorite', $validated), function ($query) use ($validated) {
            if ($validated['favorite'] == BookmarkFavoriteEnum::UNFAVORITED->name) {
                $query = $query->whereNull('favorited_at');
            } elseif ($validated['favorite'] == BookmarkFavoriteEnum::FAVORITED->name) {
                $query = $query->whereNotNull('favorited_at');
            }
        });
        $query->when(array_key_exists('archive', $validated), function ($query) use ($validated) {
            if ($validated['archive'] == BookmarkArchiveEnum::UNARCHIVED->name) {
                $query = $query->whereNull('archived_at');
            } elseif ($validated['archive'] == BookmarkArchiveEnum::ARCHIVED->name) {
                $query = $query->whereNotNull('archived_at');
            }
        });

        $query->when(array_key_exists('collection', $validated), function ($query) use ($validated) {
            $query = $query->where('collection', $validated['collection']);
        });

        $query->when(array_key_exists('q', $validated) && $validated['q'], function ($query) use ($validated) {
            $q = $validated['q'];
            $query = $query->where(function ($query) use ($q) {
                $query
                    ->orWhere('note', 'LIKE', "%$q%")
                    ->orWhereHas('url', function ($urlQuery) use ($q) {
                        $urlQuery->where(function ($urlSubQuery) use ($q) {
                            $urlSubQuery
                                ->orWhere('url', 'LIKE', "%{$q}%")
                                ->orWhere('title', 'LIKE', "%{$q}%")
                                ->orWhere('description', 'LIKE', "%{$q}%");
                        });
                    });
            });
        });

        $query = $query->orderByRaw('`favorited_at` IS NULL, `favorited_at` DESC, `created_at` DESC, `id` ASC');

        $bookmarks = $paginated ?
            $query->paginate(page: $validated['page'], perPage: 20) :
            $query->get();

        return ApiResponse::make(200)->data([
            'bookmarks' => (new BookmarkCollection($bookmarks))->toArray(request()),
        ])->paginator($paginated ? $bookmarks : null);
    }

    public function store(int $userId, array $input, ?CarbonInterface $createdAt = null)
    {
        $validator = Validator::make($input, [
            'url_id' => ['required', 'integer', Rule::exists('urls', 'id')],
            'collection' => ['nullable', 'string', 'max:512'],
            'note' => ['nullable', 'string'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }
        $validated = $validator->validated() + [
            'collection' => null,
            'note' => null,
        ];

        $bookmark = Bookmark::create([
            'url_id' => $validated['url_id'],
            'collection' => $validated['collection'],
            'note' => $validated['note'],
            'user_id' => $userId,
            'created_at' => ($createdAt ?? now()),
        ]);

        return ApiResponse::make(201)->data([
            'bookmark' => new BookmarkResource($bookmark),
        ]);
    }

    public function show(int $id, int $userId)
    {
        $bookmark = $this->getBookmark($id, $userId);
        if (! $bookmark) {
            return ApiResponse::make(404);
        }

        return ApiResponse::make(200)->data([
            'bookmark' => (new BookmarkResource($bookmark))->toArray(request()),
        ]);
    }

    public function update(int $id, int $userId, array $input)
    {
        $bookmark = $this->getBookmark($id, $userId);
        if (! $bookmark) {
            return ApiResponse::make(404);
        }

        $validator = Validator::make($input, [
            'collection' => ['nullable', 'string', 'max:512'],
            'note' => ['nullable', 'string'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }

        $isSuccessful = $bookmark->update([
            'url' => $input['url'],
            'collection' => $input['collection'],
            'note' => $input['note'],
            'user_id' => $userId,
        ]);
        if ($isSuccessful) {
            return ApiResponse::make(200)->data([
                'user' => new BookmarkResource($bookmark),
            ]);
        }

        return ApiResponse::make(500);
    }

    public function destroy(int $id, int $userId)
    {
        $bookmark = $this->getBookmark($id, $userId);
        if (! $bookmark) {
            return ApiResponse::make(404);
        }

        return ApiResponse::make($bookmark->delete() ? 200 : 500);
    }

    public function updateAttributes(int $userId, array $input)
    {
        $ids = collect($input['bookmarks'] ?? [])
            ->pluck('id')
            ->unique()
            ->values();
        $bookmarks = Bookmark::query()
            ->where('user_id', $userId)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');
        $bookmarkIds = $bookmarks->keys();

        $validator = Validator::make($input, [
            'bookmarks' => ['required', 'array'],
            'bookmarks.*.id' => ['required', 'integer', Rule::in($bookmarkIds)],
            'bookmarks.*.collection' => ['sometimes', 'nullable', 'string', 'max:512'],
            'bookmarks.*.is_favorited' => ['sometimes', 'nullable', 'boolean'],
            'bookmarks.*.is_read' => ['sometimes', 'nullable', 'boolean'],
            'bookmarks.*.is_archived' => ['sometimes', 'nullable', 'boolean'],
            'bookmarks.*.is_shared' => ['sometimes', 'nullable', 'boolean'],
            'bookmarks.*.note' => ['sometimes', 'string'],
        ]);
        if ($validator->fails()) {
            return ApiResponse::makeFromValidator($validator);
        }
        $validatedDatas = $validator->validated();

        foreach ($validatedDatas['bookmarks'] as $validated) {
            $updateData = [];
            foreach ($validated as $validatedAttributeName => $validatedAttributeValue) {
                if ($validatedAttributeName === 'collection') {
                    $updateData['collection'] = $validatedAttributeValue;
                } elseif ($validatedAttributeName === 'is_read') {
                    $updateData['read_at'] = $this->booleanToDate($validatedAttributeValue);
                } elseif ($validatedAttributeName === 'is_archived') {
                    $updateData['archived_at'] = $this->booleanToDate($validatedAttributeValue);
                } elseif ($validatedAttributeName === 'is_shared') {
                    $updateData['shared_at'] = $this->booleanToDate($validatedAttributeValue);
                } elseif ($validatedAttributeName === 'is_favorited') {
                    $updateData['favorited_at'] = $this->booleanToDate($validatedAttributeValue);
                } elseif ($validatedAttributeName === 'note') {
                    $updateData['note'] = $validatedAttributeValue;
                }
            }
            $id = $validated['id'];
            if ($updateData && ! $bookmarks[$id]->update($updateData)) {
                return ApiResponse::make(500);
            }
        }

        return ApiResponse::make(200)->data([
            'bookamrks' => BookmarkResource::collection($bookmarks),
        ]);
    }

    public function collections(int $userId)
    {
        $collections = Bookmark::select(
            DB::raw('collection as name'),
            DB::raw('COUNT(*) as collection_count')
        )
            ->where('user_id', $userId)
            ->whereNotNull('collection')
            ->where('collection', '!=', '')
            ->groupBy('name')
            ->orderBy('collection_count', 'DESC')
            ->get();

        return ApiResponse::make(200)->data([
            'collections' => BookmarkCollectionResource::collection($collections)->toArray(request()),
        ]);
    }

    protected function getBookmark(int $id, int $userId)
    {
        return Bookmark::query()->where('user_id', $userId)->find($id);
    }

    protected function booleanToDate(mixed $value): ?CarbonInterface
    {
        return boolval($value) ? now() : null;
    }
}
