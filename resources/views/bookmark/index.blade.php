@extends('layouts.app')

@section('title', 'Bookmarks')

@php
    $params = [
        'urls' => [
            'api.bookmarks.collections' => route('api.bookmarks.collections'),
            'api.tags.index' => route('api.tags.index'),
        ],
    ];
@endphp

@section('content')
    <div class="container-fluid" x-data="data()" x-init="initData({{ json_encode($params) }})">
        <div class="row py-4">
            <div class="col-10 col-lg-7 offset-lg-1 d-flex justify-content-between align-items-center">
                <input type="text" class="form-control rounded-pill py-2 px-4">
            </div>
            <div
                class="col-1 col-lg-1 offset-1 offset-lg-2 d-flex justify-content-between align-items-center flex-row-reverse">
                <button class="btn btn-primary rounded-pill">
                    A
                </button>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-lg-10 offset-lg-1">
                <div class="fs-7 d-inline-block px-0 py-2 me-3 fw-bold fw-semibold cursor-pointer user-select-none"
                    @click="filters.collection = null"
                    :class="{ 'border-3 border-bottom border-dark fw-bold': filters.collection === null }">
                    All
                </div>
                <template x-if="loading.callBookmarksCollections">
                    <div class="fs-7 d-inline-block px-0 py-2 me-3 cursor-pointer user-select-none">
                        <div class="spinner-border spinner-border-sm"></div>
                    </div>
                </template>
                <template x-for="collection in collections" x-show="!loading.callBookmarksCollections">
                    <div class="fs-7 d-inline-block px-0 py-2 me-3 cursor-pointer user-select-none"
                        :class="{ 'border-3 border-bottom border-dark fw-bold': filters.collection === collection.name }"
                        x-text="collection.name" @click="filters.collection = collection.name">
                    </div>
                </template>
                <div class="fs-7 d-inline-block px-0 py-2 me-3 fw-semibold cursor-pointer user-select-none"
                    :class="{ 'border-3 border-bottom border-dark fw-bold': filters.collection === '' }"
                    @click="filters.collection = ''">
                    None
                </div>
            </div>
        </div>
        <div class="row pt-4">
            <div class="col-lg-10 offset-lg-1">
                <div class="d-flex flex-wrap gap-2 pb-4">
                    <template x-if="loading.callTagsIndex">
                        <div class="fs-7 rounded-pill pe-3 py-2 bg-white text-dark border border-light fw-bold">
                            <div class="spinner-border spinner-border-sm"></div>
                        </div>
                    </template>
                    <template x-for="tag in tags" x-show="!loading.callTagsIndex">
                        <div class="fs-7 rounded-pill px-3 py-2 bg-white text-dark border border-secondary cursor-pointer user-select-none"
                            :class="{ 'bg-secondary-subtle fw-bold': filters.tags.includes(tag.name) }" x-text="tag.name"
                            @click="toggleTag(tag.name)"></div>
                    </template>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-7 offset-lg-1">

                <div class="d-flex flex-column pb-4">
                    <div class="d-flex">
                        <div class="d-flex flex-grow-0 me-2 justify-content-center align-items-center">
                            <img class="w-32" src="https://play.google.com/favicon.ico">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <div class="fs-7">Collection<i class="bi bi-three-dots-vertical"></i></div>
                            <div class="fs-8">
                                https://play.google.com/store/apps/details?id=com.facebook.lite&hl=en
                            </div>
                        </div>
                    </div>
                    <div class="fs-5 text-primary">
                        لندو: خرید قسطی کالا و خدمات از فروشگاه‌های آنلاین
                    </div>
                    <div class="fs-7">
                        The Facebook Lite app is small, allowing you to save space on your phone and use Facebook in 2G
                        conditions.Read more
                    </div>
                    <div class="fs-7 text-decoration-underline">
                        Note Note Note Note Note Note Note Note Note Note Note
                    </div>
                    <div class="d-flex gap-3 fs-7">
                        <span class="p-0 text-secondary">
                            <i class="bi bi-tag"></i>
                            tag1
                        </span>
                        <span class="p-0 text-secondary">
                            <i class="bi bi-tag"></i>
                            tag2
                        </span>
                        <span class="p-0 text-secondary">
                            <i class="bi bi-tag"></i>
                            tag3
                        </span>
                    </div>
                    <div class="d-flex gap-3 fs-7">
                        <span class="p-0 text-secondary">
                            <i class="bi bi-bookmark-check"></i>
                            Read
                        </span>
                        <span class="p-0 text-secondary">
                            <i class="bi bi-archive"></i>
                            Archive
                        </span>
                        <span class="p-0 text-secondary">
                            <i class="bi bi-share"></i>
                            Share
                        </span>
                        <span class="p-0 text-secondary">
                            <i class="bi bi-heart"></i>
                            Favorite
                        </span>
                    </div>
                    <ul class="list-group mt-1">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-12 mt-1">
                                    <label class="form-label mb-1">Collection</label>
                                    <input type="text" name="collection" class="form-control rounded-pill">
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12 d-flex flex-wrap gap-2 justify-content-end mt-1">
                                    <button type="submit" class="btn btn-primary rounded-pill shadow-sm">
                                        Save Collection
                                    </button>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-12 mt-1">
                                    <label class="form-label mb-1">Note</label>
                                    <textarea name="note" rows="3" class="form-control rounded-4"></textarea>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12 d-flex flex-wrap gap-2 justify-content-end mt-1">
                                    <button type="submit" class="btn btn-primary rounded-pill shadow-sm">
                                        Save Note
                                    </button>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-12 mt-1">
                                    <label class="form-label mb-1">Tags</label>
                                </div>
                                <div class="col-12 mt-1">
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="fs-7 rounded-pill px-3 py-2 bg-white text-dark border border-secondary">
                                            Css</div>
                                        <div class="fs-7 rounded-pill px-3 py-2 bg-white text-dark border border-secondary">
                                            Bootstrap</div>
                                        <div class="fs-7 rounded-pill px-3 py-2 bg-white text-dark border border-secondary">
                                            Navbar</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12 d-flex flex-wrap gap-2 justify-content-end mt-1">
                                    <button type="submit" class="btn btn-primary rounded-pill shadow-sm">
                                        Save Tags
                                    </button>
                                </div>
                            </div>

                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        function data() {
            return {
                urls: {},
                loading: {
                    callBookmarksCollections: false,
                    callTagsIndex: false,
                },
                models: {
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null,
                },
                collections: [],
                tags: [],
                filters: {
                    collection: null,
                    tags: ['in'],
                },
                toggleTag(tagName) {
                    if (this.filters.tags.includes(tagName)) {
                        this.filters.tags = this.filters.tags.filter(t => t !== tagName)
                    } else {
                        this.filters.tags.push(tagName)
                    }
                },
                async initData(initParams) {
                    this.urls = initParams.urls;
                    this.callBookmarksCollections();
                    this.callTagsIndex();
                },
                async callBookmarksCollections(data) {
                    try {
                        if (this.loading.callBookmarksCollections) return;
                        this.loading.callBookmarksCollections = true;

                        const res = await this.$store.call.callJson(
                            this.urls['api.bookmarks.collections'], null, 'GET', true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            this.collections = resJson.data.collections;
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error');
                    } finally {
                        this.loading.callBookmarksCollections = false;
                    }
                },
                async callTagsIndex(data) {
                    try {
                        if (this.loading.callTagsIndex) return;
                        this.loading.callTagsIndex = true;

                        const res = await this.$store.call.callJson(
                            this.urls['api.tags.index'], null, 'GET', true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            this.tags = resJson.data.tags;
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error');
                    } finally {
                        this.loading.callTagsIndex = false;
                    }
                }
            };
        }
    </script>
@endsection
