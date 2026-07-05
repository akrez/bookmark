@extends('layouts.app')

@section('title', 'Bookmarks')

@php
    $params = [
        'urls' => [
            'api.bookmarks.collections' => route('api.bookmarks.collections'),
            'api.bookmarks.index' => route('api.bookmarks.index'),
            'api.auth.logout' => route('api.auth.logout'),
            'api.netscape.import' => route('api.netscape.import'),
            'api.netscape.export' => route('api.netscape.export'),
            'api.bookmarks.updateAttribute' => route('api.bookmarks.index'),
            'api.bookmarks.store' => route('api.bookmarks.index'),
        ],
    ];
@endphp

@section('content')
    <div class="container-fluid" x-data="data()" x-init="initData({{ json_encode($params) }})">
        <div class="row py-4">
            <div class="col-7 col-lg-7 offset-lg-1 d-flex justify-content-between align-items-center">
                <input type="text" class="form-control rounded-pill py-2 px-4" x-model="filters.q">
            </div>
            <div class="col-4 col-lg-1 offset-1 offset-lg-2 d-flex justify-content-end align-items-center gap-3">
                <button
                    class="btn rounded bg-white text-secondary border border border-secondary-subtle rounded-4 d-flex flex-row p-2 px-3"
                    @click="isCreateModalOpen ? closeCreateModal() : (isCreateModalOpen  = true)">
                    <i class="bi bi-pencil"></i>
                    <span class="ms-3">Create</span>
                </button>
                <button class="btn btn-primary rounded-circle" @click="isProfileModalOpen = !isProfileModalOpen"
                    x-text="$store.auth.user().name.charAt(0).toUpperCase()">
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
                        :class="(loading.callBookmarksCollections ? 'd-inline-block border-white' : 'd-none')">
                        <div class="spinner-border spinner-border-sm"></div>
                    </div>
                    <template x-for="collection in collections" x-show="!loading.callBookmarksCollections">
                        <div class="fs-7 d-inline-block px-0 py-2 me-3 cursor-pointer user-select-none border-3 border-bottom"
                            :class="(filters.collection === collection.name ? 'border-dark' : 'border-white')"
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
        <div class="row py-1">
            <div class="col-lg-7 offset-lg-1">
                <div class="d-flex flex-column py-4 gap-4">
                    <template x-if="loading.callBookmarksIndex">
                        <div class="fs-7 rounded-pill pe-3 bg-white text-dark fw-bold">
                            <div class="spinner-border"></div>
                        </div>
                    </template>
                    <template x-if="!loading.callBookmarksIndex && (bookmarks.length < 1)">
                        <div class="fs-7">
                            Your search did not match any documents.
                        </div>
                    </template>
                    <template x-if="!loading.callBookmarksIndex">
                        <template x-for="bookmark in bookmarks">
                            <div class="d-flex flex-column">
                                <div class="d-flex">
                                    <div class="d-flex flex-grow-0 me-2 justify-content-center align-items-start">
                                        <img class="w-32 pt-1"
                                            :src="bookmark.url.favicon ?? bookmark.url.base_url + '/favicon.ico'">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center flex-grow-1 overflow-hidden">
                                        <div class="fs-7 text-decoration-none text-truncate d-block"
                                            x-text="bookmark.url.description" x-show="bookmark.url.description"></div>
                                        <a class="fs-8 text-decoration-none text-truncate d-block text-dark"
                                            x-text="bookmark.url.url" target="_blank" :href="bookmark.url.url"></a>
                                        <a class="fs-5 text-primary text-decoration-none lh-sm text-truncate"
                                            x-text="bookmark.url.title" target="_blank" :href="bookmark.url.url"></a>
                                        <div class="fs-7">
                                            <span class="pe-1" x-text="bookmark.note" x-show="bookmark.note"></span>
                                        </div>
                                        <div class="d-flex gap-3 fs-7">
                                            <span class="p-0 text-secondary cursor-pointer d-flex align-items-center"
                                                :class="{ 'fw-bold': bookmark.read_at }"
                                                @click="callUpdateBookmark(bookmark.id, 'is_read', bookmark.read_at ? false : true)">
                                                <i
                                                    :class="loading.callUpdateBookmark == bookmark.id + '-is_read' ?
                                                        'spinner-border spinner-border-sm' : 'bi-bookmark-check'"></i>
                                                <span class="ps-1" x-text="bookmark.read_at ? 'Read' : 'Unread'"></span>
                                            </span>
                                            <span class="p-0 text-secondary cursor-pointer d-flex align-items-center"
                                                :class="{ 'fw-bold': bookmark.shared_at }"
                                                @click="callUpdateBookmark(bookmark.id, 'is_shared', bookmark.shared_at ? false : true)">
                                                <i
                                                    :class="loading.callUpdateBookmark == bookmark.id + '-is_shared' ?
                                                        'spinner-border spinner-border-sm' : 'bi bi-share'"></i>
                                                <span class="ps-1"
                                                    x-text="bookmark.shared_at ? 'Shared' : 'Share'"></span>
                                            </span>
                                            <span class="p-0 text-secondary cursor-pointer d-flex align-items-center"
                                                :class="{ 'fw-bold': bookmark.favorited_at }"
                                                @click="callUpdateBookmark(bookmark.id, 'is_favorited', bookmark.favorited_at ? false : true)">
                                                <i
                                                    :class="loading.callUpdateBookmark == bookmark.id + '-is_favorited' ?
                                                        'spinner-border spinner-border-sm' : 'bi bi-heart'"></i>
                                                <span class="ps-1"
                                                    x-text="bookmark.favorited_at ? 'Favorited' : 'Favorite'"></span>
                                            </span>
                                            <span class="p-0 text-secondary cursor-pointer d-flex align-items-center"
                                                :class="{ 'fw-bold': bookmark.archived_at }"
                                                @click="callUpdateBookmark(bookmark.id, 'is_archived', bookmark.archived_at ? false : true)">
                                                <i
                                                    :class="loading.callUpdateBookmark == bookmark.id + '-is_archived' ?
                                                        'spinner-border spinner-border-sm' : 'bi bi-archive'"></i>
                                                <span class="ps-1"
                                                    x-text="bookmark.archived_at ? 'Archived' : 'Archive'"></span>
                                            </span>
                                            <span class="p-0 text-secondary cursor-pointer d-flex align-items-center"
                                                @click="collectionModalId = ((collectionModalId && collectionModalId == bookmark.id) ? null : bookmark.id)">
                                                <i
                                                    :class="loading.callUpdateBookmark == bookmark.id + '-collection' ?
                                                        'spinner-border spinner-border-sm' : 'bi bi-collection'"></i>
                                                <span class="ps-1" x-text="bookmark.collection"></span>
                                            </span>
                                        </div>
                                        <template x-if="collectionModalId == bookmark.id">
                                            <div
                                                class="d-flex flex-column justify-content-center p-2 gap-2 rounded bg-white text-secondary border border-secondary-subtle rounded-2">
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control"
                                                        x-model="collectionForms[bookmark.id]">
                                                    <button class="btn text-secondary border border-secondary-subtle"
                                                        @click="callUpdateBookmark(bookmark.id, 'collection',  collectionForms[bookmark.id])">
                                                        Submit
                                                    </button>
                                                </div>
                                                <div class="d-flex gap-2"
                                                    :class="(collections.length > 0 ? 'd-flex' : 'd-none')">
                                                    <template x-for="collection in collections">
                                                        <span class="text-decoration-underline cursor-pointer fs-7"
                                                            x-text="collection.name"
                                                            @click="callUpdateBookmark(bookmark.id, 'collection', collection.name)"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template
                        x-if="!loading.callBookmarksIndex && paginator && !(paginator.currentPage == 1 && paginator.onLastPage)">
                        <div class="d-flex justify-content-center gap-1">
                            <a x-show="paginator?.currentPage > 2" @click="doFilter(() => filters.page = 1, false, false)"
                                class="cursor-pointer px-2 text-decoration-none text-primary">
                                <i class="bi bi-chevron-double-left"></i>
                            </a>
                            <a x-show="paginator?.currentPage > 1"
                                @click="doFilter(() => filters.page = paginator?.currentPage - 1, false, false)"
                                class="cursor-pointer px-2 text-decoration-none text-primary">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                            <template x-for="n in pageRange()">
                                <span @click="doFilter(() => filters.page = n, false, false)"
                                    class="text-decoration-none px-2" x-text="n"
                                    :class="{ 'cursor-pointer text-primary': paginator?.currentPage != n }"></span>
                            </template>
                            <a x-show="!paginator?.onLastPage"
                                @click="doFilter(() =>filters.page = paginator?.currentPage + 1, false, false)"
                                class="cursor-pointer px-2 text-decoration-none text-primary">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="modal bg-black-50" tabindex="-1" :class="isProfileModalOpen ? 'd-block' : 'd-none'"
            @click="isProfileModalOpen = false">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content bg-modal rounded rounded-5" @click.stop>
                    <div class="modal-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 fs-7">
                            <span class="text-center flex-grow-1" x-text="$store.auth.user().name"></span>
                            <i class="bi bi-x-lg cursor-pointer" @click="isProfileModalOpen = false"></i>
                        </div>
                        <div class="d-flex justify-content-center p-3 border-bottom pt-0">
                            <span class="text-center flex-grow-1 fs-3"
                                x-text="'Hi, ' + $store.auth.user().email + '!'"></span>
                        </div>
                        <div class="btn-group p-3 w-100">
                            <button type="button"
                                class="btn btn-light w-100 rounded-5 d-flex justify-content-center align-items-center gap-2 rounded"
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
        <div class="modal bg-black-50" tabindex="-1" :class="isCreateModalOpen ? 'd-block' : 'd-none'"
            @click="closeCreateModal()">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content bg-modal rounded rounded-5" @click.stop>
                    <div class="modal-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 fs-7">
                            <span class="text-center flex-grow-1">Create new bookmark</span>
                            <i class="bi bi-x-lg cursor-pointer" @click="closeCreateModal()"></i>
                        </div>
                        <div class="d-flex flex-column justify-content-center p-3 py-0 gap-3">
                            <input class="form-control" x-model="createForm.url">
                            <button type="button"
                                class="btn btn-light w-100 rounded-5 d-flex justify-content-center align-items-center gap-2 rounded"
                                @click="callStoreBookmark()" :disabled="loading.callStoreBookmark">
                                <i
                                    :class="loading.callStoreBookmark ? 'spinner-border spinner-border-sm' : 'bi bi-plus-lg'"></i>
                                Create
                            </button>
                        </div>
                        <hr class="border-secondary">
                        <div class="d-flex flex-column justify-content-center p-3 pt-0 gap-3">
                            <span class="text-center flex-grow-1">Import netscape html file</span>
                            <input class="form-control" type="file" accept=".html,.htm"
                                x-ref="netscapeImportFileInput" @change="handleNetscapeImportFile($event)">
                            <button type="button"
                                class="btn btn-light w-100 rounded-5 d-flex justify-content-center align-items-center gap-2 rounded"
                                @click="callNetscapeImport()"
                                :disabled="loading.callNetscapeImport || !netscapeImportFile">
                                <i
                                    :class="loading.callNetscapeImport ? 'spinner-border spinner-border-sm' :
                                        'bi bi-file-earmark-arrow-up'"></i>
                                Import
                            </button>
                        </div>
                        <hr class="border-secondary">
                        <div class="d-flex flex-column justify-content-center p-3 pt-0 gap-3">
                            <span class="text-center flex-grow-1">Export netscape html file</span>
                            <button type="button"
                                class="btn btn-light w-100 rounded-5 d-flex justify-content-center align-items-center gap-2 rounded"
                                @click="callNetscapeExport()" :disabled="loading.callNetscapeExport">
                                <i
                                    :class="loading.callNetscapeExport ? 'spinner-border spinner-border-sm' :
                                        'bi bi-file-earmark-arrow-up'"></i>
                                Export
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
                isCreateModalOpen: false,
                collectionModalId: null,
                netscapeImportFile: null,
                loading: {
                    callBookmarksCollections: false,
                    callBookmarksIndex: false,
                    callAuthLogout: false,
                    callNetscapeImport: false,
                    callNetscapeExport: false,
                    callUpdateBookmark: null,
                    callStoreBookmark: false,
                },
                collections: [],
                bookmarks: [],
                filters: {
                    q: '',
                    collection: null,
                    read: "ALL",
                    archive: "UNARCHIVED",
                    share: "ALL",
                    favorite: "ALL",
                    page: 1
                },
                scrollY: null,
                dropdown: null,
                paginator: null,
                collectionForms: {},
                createForm: {
                    url: null,
                },
                init() {
                    this.$watch('filters.q', () => {
                        this.doFilter(() => {});
                    }, {
                        deep: true
                    });
                },
                async initData(initParams) {
                    this.urls = initParams.urls;
                    this.doFilter(() => {}, true);
                },
                async doFilter(func, resetCollection = false, resetPage = true) {
                    func();
                    //
                    this.scrollY = window.scrollY;
                    if (resetCollection) {
                        this.collections = [];
                        this.callBookmarksCollections();
                    }
                    if (resetPage) {
                        this.filters.page = 1;
                    }
                    this.collectionModalId = null,
                    this.closeCreateModal();
                    this.bookmarks = [];
                    await this.callBookmarksIndex();
                    if (! resetPage) {
                        window.scrollTo(0, this.scrollY);
                    }
                },
                pageRange(sideCount = 5) {
                    const {
                        perPage,
                        currentPage,
                        total,
                        onLastPage
                    } = this.paginator;

                    const totalPages = Math.ceil(total / perPage);

                    let minAva = Math.max(1, currentPage - sideCount);
                    let maxAva = Math.min(totalPages, currentPage + sideCount);

                    let start = minAva;
                    let end = maxAva;

                    if (1 == minAva && maxAva == totalPages) {
                        //
                    } else if (1 == minAva) {
                        start = minAva;
                        end = (2 * sideCount) + 1;
                    } else if (maxAva == totalPages) {
                        start = maxAva - (2 * sideCount);
                        end = maxAva;
                    } else {
                        //
                    }

                    const pages = [];
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    return pages;
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
                            //
                            this.collectionForms = {};
                            this.bookmarks.forEach(bookmark => {
                                this.collectionForms[bookmark.id] = bookmark.collection;
                            });
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
                handleNetscapeImportFile(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const validTypes = ['text/html', 'application/xhtml+xml'];
                        const extension = file.name.split('.').pop().toLowerCase();
                        if (validTypes.includes(file.type) || ['html', 'htm'].includes(extension)) {
                            this.netscapeImportFile = file;
                            return;
                        }
                    }

                    this.$store.alert.error('Please select a valid HTML file (netscape bookmark export)');
                    this.resetImportForm();
                    return;
                },
                readFileAsText(file) {
                    return new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.onload = () => resolve(reader.result);
                        reader.onerror = () => reject(reader.error);
                        reader.readAsText(file);
                    });
                },
                resetImportForm() {
                    this.$refs.netscapeImportFileInput.value = null;
                    this.netscapeImportFile = null;
                },
                closeCreateModal() {
                    this.isCreateModalOpen = false;
                    this.createForm.url = null;
                    this.resetImportForm();
                },
                async callNetscapeImport() {
                    try {
                        if (this.loading.callNetscapeImport) return;

                        if (!this.netscapeImportFile) {
                            this.$store.alert.error('Please select a file first');
                            return;
                        }

                        this.loading.callNetscapeImport = true;

                        const fileContent = await this.readFileAsText(this.netscapeImportFile);

                        const res = await this.$store.call.callJson(
                            'POST',
                            this.urls['api.netscape.import'],
                            null, {
                                html: fileContent
                            },
                            true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            this.$store.alert.success('Bookmarks imported successfully!');
                            this.doFilter(() => {}, true);
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error importing bookmarks: ' + err.message);
                    } finally {
                        this.loading.callNetscapeImport = false;
                    }
                },
                async callNetscapeExport() {
                    try {
                        if (this.loading.callNetscapeExport) return;
                        this.loading.callNetscapeExport = true;

                        const filters = JSON.parse(JSON.stringify(this.filters));
                        if (this.filters.collection === null) {
                            delete filters.collection;
                        }
                        const res = await this.$store.call.callJson(
                            'GET', this.urls['api.netscape.export'], filters, null, true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            this.$store.alert.success('Bookmarks exported successfully!');
                            const blob = new Blob([resJson.data.file], {
                                type: 'text/html;charset=utf-8'
                            });
                            saveAs(blob, resJson.data.file_name);
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error exporting bookmarks: ' + err.message);
                    } finally {
                        this.loading.callNetscapeExport = false;
                    }
                },
                async callUpdateBookmark(bookmarkId, fieldName, fieldValue) {
                    try {
                        if (this.loading.callUpdateBookmark) return;
                        this.loading.callUpdateBookmark = bookmarkId + '-' + fieldName;

                        const data = {};
                        data[fieldName] = fieldValue;

                        const res = await this.$store.call.callJson(
                            'PATCH', this.urls['api.bookmarks.updateAttribute'] + '/' + bookmarkId, null, data, true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            this.$store.alert.success('Bookmarks updated successfully!');
                            this.doFilter(() => {}, true, false);
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error');
                    } finally {
                        this.loading.callUpdateBookmark = null;
                    }
                },
                async callStoreBookmark() {
                    try {
                        if (this.loading.callStoreBookmark) return;
                        this.loading.callStoreBookmark = true;

                        const res = await this.$store.call.callJson(
                            'POST', this.urls['api.bookmarks.store'], null, this.createForm, true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            this.$store.alert.success('Bookmarks created successfully!');
                            this.doFilter(() => {});
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error');
                    } finally {
                        this.loading.callStoreBookmark = false;
                    }
                },
            }
        }
    </script>
@endsection
