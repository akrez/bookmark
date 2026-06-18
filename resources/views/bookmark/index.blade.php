@extends('layouts.app')

@section('title', 'Bookmarks')

@php
    $params = [
        'urls' => [
            'api.bookmarks.collections' => route('api.bookmarks.collections'),
            'api.tags.index' => route('api.tags.index'),
            'api.bookmarks.index' => route('api.bookmarks.index'),
            'api.auth.logout' => route('api.auth.logout'),
        ],
    ];
@endphp

@section('content')
    <div class="container-fluid" x-data="data()" x-init="initData({{ json_encode($params) }})">
        <div class="row py-4">
            <div class="col-10 col-lg-7 offset-lg-1 d-flex justify-content-between align-items-center">
                <input type="text" class="form-control rounded-pill py-2 px-4" x-model="filters.q">
            </div>
            <div
                class="col-1 col-lg-1 offset-1 offset-lg-2 d-flex justify-content-between align-items-center flex-row-reverse">
                <button class="btn btn-primary rounded-circle" @click="isProfileModalOpen = !isProfileModalOpen">
                    A
                </button>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-lg-10 offset-lg-1 d-flex flex-row flex-wrap justify-content-between">
                <div class="d-flex flex-grow-1 flex-wrap">
                    <div class="fs-7 d-inline-block px-0 py-2 me-3 fw-bold fw-semibold cursor-pointer user-select-none border-3 border-bottom"
                        @click="doFilter(() => filters.collection = null)"
                        :class="(filters.collection === null ? 'border-dark fw-bold' : 'border-white')">
                        All
                    </div>
                    <div class="fs-7 d-inline-block px-0 py-2 me-3 cursor-pointer user-select-none border-3 border-bottom"
                        :class="(loading.callBookmarksCollections ? 'd-inline-block' : 'd-none')">
                        <div class="spinner-border spinner-border-sm"></div>
                    </div>
                    <template x-for="collection in collections" x-show="!loading.callBookmarksCollections">
                        <div class="fs-7 d-inline-block px-0 py-2 me-3 cursor-pointer user-select-none border-3 border-bottom"
                            :class="(filters.collection === collection.name ? 'border-dark fw-bold' : 'border-white')"
                            x-text="collection.name" @click="doFilter(() => filters.collection = collection.name)">
                        </div>
                    </template>
                    <div class="fs-7 d-inline-block px-0 py-2 me-3 fw-semibold cursor-pointer user-select-none border-3 border-bottom"
                        :class="(filters.collection === '' ? 'border-dark fw-bold' : 'border-white')"
                        @click="doFilter(() => filters.collection = '')">
                        None
                    </div>
                </div>
                <div class="dropdown" @click.outside="dropdown = null">

                    <div class="fs-7 d-inline-block px-0 py-2 me-0 fw-bold fw-semibold cursor-pointer user-select-none border-3 border-bottom dropdown-toggle"
                        :class="(filters.read != 'ALL' ? 'border-dark fw-bold' : 'border-white')"
                        @click="setDropdown('read')">
                        Read
                    </div>
                    <ul class="dropdown-menu w-100" :class="getDropdownClass('read')">
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.read = 'ALL')">
                            <div>All</div>
                            <template x-if="filters.read == 'ALL'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.read = 'READ')">
                            <div>Read</div>
                            <template x-if="filters.read == 'READ'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.read = 'UNREAD')">
                            <div>UnRead</div>
                            <template x-if="filters.read == 'UNREAD'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                    </ul>

                    <div class="fs-7 d-inline-block px-0 py-2 ms-3 fw-bold fw-semibold cursor-pointer user-select-none border-3 border-bottom dropdown-toggle"
                        :class="(filters.share != 'ALL' ? 'border-dark fw-bold' : 'border-white')"
                        @click="setDropdown('share')">
                        Share
                    </div>
                    <ul class="dropdown-menu w-100" :class="getDropdownClass('share')">
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.share = 'ALL')">
                            <div>All</div>
                            <template x-if="filters.share == 'ALL'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.share = 'SHARED')">
                            <div>Shared</div>
                            <template x-if="filters.share == 'SHARED'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.share = 'UNSHARED')">
                            <div>UnShared</div>
                            <template x-if="filters.share == 'UNSHARED'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                    </ul>

                    <div class="fs-7 d-inline-block px-0 py-2 ms-3 fw-bold fw-semibold cursor-pointer user-select-none border-3 border-bottom dropdown-toggle"
                        :class="(filters.favorite != 'ALL' ? 'border-dark fw-bold' : 'border-white')"
                        @click="setDropdown('favorite')">
                        Favorite
                    </div>
                    <ul class="dropdown-menu w-100" :class="getDropdownClass('favorite')">
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.favorite = 'ALL')">
                            <div>All</div>
                            <template x-if="filters.favorite == 'ALL'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.favorite = 'FAVORITED')">
                            <div>Favorited</div>
                            <template x-if="filters.favorite == 'FAVORITED'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.favorite = 'UNFAVORITED')">
                            <div>UnFavorited</div>
                            <template x-if="filters.favorite == 'UNFAVORITED'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                    </ul>

                    <div class="fs-7 d-inline-block px-0 py-2 ms-3 fw-bold fw-semibold cursor-pointer user-select-none border-3 border-bottom dropdown-toggle"
                        :class="(filters.archive != 'ALL' ? 'border-dark fw-bold' : 'border-white')"
                        @click="setDropdown('archive')">
                        Archive
                    </div>
                    <ul class="dropdown-menu w-100" :class="getDropdownClass('archive')">
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.archive = 'ALL')">
                            <div>All</div>
                            <template x-if="filters.archive == 'ALL'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.archive = 'ARCHIVED')">
                            <div>Archived</div>
                            <template x-if="filters.archive == 'ARCHIVED'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                        <li class="d-flex justify-content-between px-3 cursor-pointer"
                            @click="doFilter(() => filters.archive = 'UNARCHIVED')">
                            <div>UnArchived</div>
                            <template x-if="filters.archive == 'UNARCHIVED'">
                                <i class="bi bi-check2"></i>
                            </template>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
        <div class="row pt-4">
            <div class="col-lg-10 offset-lg-1">
                <div class="d-flex flex-wrap gap-2 pb-5">
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
                <template x-if="loading.callBookmarksIndex">
                    <div class="fs-7 rounded-pill pe-3 py-2 bg-white text-dark border border-light fw-bold pb-5">
                        <div class="spinner-border spinner-border-sm"></div>
                    </div>
                </template>
                <template x-if="!loading.callBookmarksIndex && (bookmarks.length < 1)">
                    <div class="fs-7 pb-5">
                        Your search did not match any documents.
                    </div>
                </template>
                <template x-if="!loading.callBookmarksIndex">
                    <template x-for="bookmark in bookmarks">
                        <div class="d-flex flex-column pb-5">
                            <div class="d-flex">
                                <div class="d-flex flex-grow-0 me-2 justify-content-center align-items-center">
                                    <img class="w-32" :src="bookmark.url.base_url + '/favicon.ico'">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <div class="fs-7">
                                        <span class="pe-1" x-text="bookmark.collection"></span>
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </div>
                                    <div class="fs-8" x-text="bookmark.url.url"></div>
                                </div>
                            </div>
                            <a class="fs-5 text-primary text-decoration-none" x-text="bookmark.url.title" target="_blank"
                                :href="bookmark.url.url"></a>
                            <div class="fs-7" x-text="bookmark.url.description"></div>
                            <div class="fs-7 text-decoration-underline" x-text="bookmark.note"></div>
                            <template x-if="bookmark.tags">
                                <div class="d-flex gap-3 fs-7">
                                    <template x-for="tag in bookmark.tags">
                                        <span class="p-0 text-secondary">
                                            <i class="bi bi-tag pe-1"></i>
                                            <span x-text="tag.name"></span>
                                        </span>
                                    </template>
                                </div>
                            </template>
                            <div class="d-flex gap-3 fs-7">
                                <span class="p-0 text-secondary" :class="{ 'fw-bold': bookmark.read_at }">
                                    <i class="bi bi-bookmark-check pe-1"></i>
                                    <span x-text="bookmark.read_at ? 'Read' : 'Read'"></span>
                                </span>
                                <span class="p-0 text-secondary" :class="{ 'fw-bold': bookmark.shared_at }">
                                    <i class="bi bi-share pe-1"></i>
                                    <span x-text="bookmark.shared_at ? 'Shared' : 'Share'"></span>
                                </span>
                                <span class="p-0 text-secondary" :class="{ 'fw-bold': bookmark.favorited_at }">
                                    <i class="bi bi-heart pe-1"></i>
                                    <span x-text="bookmark.favorited_at ? 'Favorited' : 'Favorite'"></span>
                                </span>
                                <span class="p-0 text-secondary" :class="{ 'fw-bold': bookmark.archived_at }">
                                    <i class="bi bi-archive pe-1"></i>
                                    <span x-text="bookmark.archived_at ? 'Archived' : 'Archive'"></span>
                                </span>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </div>
        <template x-if="!loading.callBookmarksIndex && paginator && !(paginator.currentPage == 1 && paginator.onLastPage)">
            <div class="row pb-5">
                <div class="col-lg-7 offset-lg-1">
                    <div class="d-flex justify-content-center gap-3">
                        <a x-show="paginator?.currentPage > 2" @click="doFilter(() => filters.page = 1, false)"
                            class="cursor-pointer me-3 text-decoration-none text-primary">
                            <i class="bi bi-chevron-double-left"></i>
                        </a>
                        <a x-show="paginator?.currentPage > 1"
                            @click="doFilter(() => filters.page = paginator?.currentPage - 1, false)"
                            class="cursor-pointer me-3 text-decoration-none text-primary">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                        <template x-for="n in pageRange()">
                            <span @click="doFilter(() => filters.page = n, false)" class="text-decoration-none"
                                x-text="n"
                                :class="{ 'cursor-pointer text-primary': paginator?.currentPage != n }"></span>
                        </template>
                        <a x-show="!paginator?.onLastPage"
                            @click="doFilter(() =>filters.page = paginator?.currentPage + 1, false)"
                            class="cursor-pointer ms-3 text-decoration-none text-primary">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </template>
        <div class="modal bg-black-50" tabindex="-1" :class="isProfileModalOpen ? 'd-block' : 'd-none'"
            @click="isProfileModalOpen = false">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content bg-profile-modal rounded rounded-5" @click.stop>
                    <div class="modal-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 fs-7">
                            <span class="text-center flex-grow-1" x-text="$store.auth.user().name">Modal title</span>
                            <i class="bi bi-x-lg cursor-pointer" @click="isProfileModalOpen = false"></i>
                        </div>
                        <div class="d-flex justify-content-center p-3 border-bottom pt-0">
                            <span class="text-center flex-grow-1 fs-3"
                                x-text="'Hi, ' + $store.auth.user().email + '!'"></span>
                        </div>
                        <div class="btn-group btn-group-lg p-3 w-100">
                            <button type="button"
                                class="btn btn-outline-secondary rounded-5 d-flex justify-content-center align-items-center gap-2 rounded-end"
                                @click="console.log(1);" :disabled="loading.callAuthLogout">
                                <i
                                    :class="loading.callAuthLogout ? 'spinner-border spinner-border-sm' : 'bi bi-plus-circle'"></i>
                                Import
                            </button>
                            <button type="button"
                                class="btn btn-outline-secondary rounded-5 d-flex justify-content-center align-items-center gap-2 rounded-start"
                                @click="callAuthLogout()" :disabled="loading.callAuthLogout">
                                <i
                                    :class="loading.callAuthLogout ? 'spinner-border spinner-border-sm' :
                                        'bi bi-box-arrow-right'"></i>
                                Sign out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function data() {
            return {
                urls: {},
                isProfileModalOpen: false,
                loading: {
                    callBookmarksCollections: false,
                    callTagsIndex: false,
                    callBookmarksIndex: false,
                    callAuthLogout: false,
                },
                collections: [],
                tags: [],
                bookmarks: [],
                filters: {
                    q: '',
                    collection: null,
                    tags: [],
                    read: "ALL",
                    archive: "UNARCHIVED",
                    share: "ALL",
                    favorite: "ALL",
                    page: 1
                },
                dropdown: null,
                paginator: null,
                init() {
                    this.$watch('filters', () => {
                        this.callBookmarksIndex();
                    }, {
                        deep: true
                    });
                },
                async initData(initParams) {
                    this.urls = initParams.urls;
                    this.callBookmarksCollections();
                    this.callTagsIndex();
                    this.callBookmarksIndex();
                },
                doFilter(func, resetPage = true) {
                    func();
                    if (resetPage) {
                        this.filters.page = 1;
                    }
                },
                pageRange(sideCount = 2) {
                    let s = this.paginator.currentPage - sideCount;
                    if (s < 1) s = 1;

                    let e = s + (sideCount * 2);

                    const arr = [];
                    for (let i = s; i <= e; i++) {
                        if (this.paginator.onLastPage) {
                            if (i <= this.paginator.currentPage) {
                                arr.push(i);
                            }
                        } else {
                            arr.push(i);
                        }
                    };
                    return arr;
                },
                getDropdownClass(dropdown) {
                    if (this.dropdown == dropdown) {
                        return 'd-block';
                    }
                    return 'd-none';
                },
                setDropdown(newDropdown) {
                    if (newDropdown && (this.dropdown != newDropdown)) {
                        this.dropdown = newDropdown;
                    } else {
                        this.dropdown = null;
                    }
                },
                toggleTag(tagName) {
                    if (this.filters.tags.includes(tagName)) {
                        this.filters.tags = this.filters.tags.filter(t => t !== tagName)
                    } else {
                        this.filters.tags.push(tagName)
                    }
                },
                async callBookmarksCollections() {
                    try {
                        if (this.loading.callBookmarksCollections) return;
                        this.loading.callBookmarksCollections = true;

                        const res = await this.$store.call.callJson(
                            'GET', this.urls['api.bookmarks.collections'], null, null, true
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
                async callTagsIndex() {
                    try {
                        if (this.loading.callTagsIndex) return;
                        this.loading.callTagsIndex = true;

                        const res = await this.$store.call.callJson(
                            'GET', this.urls['api.tags.index'], null, null, true
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
                },
                async callBookmarksIndex() {
                    try {
                        if (this.loading.callBookmarksIndex) return;
                        this.loading.callBookmarksIndex = true;

                        const filters = JSON.parse(JSON.stringify(this.filters));
                        if (this.filters.collection === null) {
                            delete filters.collection;
                        }
                        const res = await this.$store.call.callJson(
                            'GET', this.urls['api.bookmarks.index'], filters, null, true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            this.bookmarks = resJson.data.bookmarks;
                            this.paginator = resJson.paginator;
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error');
                    } finally {
                        this.loading.callBookmarksIndex = false;
                    }
                },
                async callAuthLogout() {
                    try {
                        if (this.loading.callAuthLogout) return;
                        this.loading.callAuthLogout = true;

                        const res = await this.$store.call.callJson(
                            'POST', this.urls['api.auth.logout'], null, null, true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            window.location.href = "/auth/login";
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error');
                    } finally {
                        this.loading.callAuthLogout = false;
                    }
                },
            };
        }
    </script>
@endsection
