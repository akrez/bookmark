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
            'api.bookmarks.updateAttributes' => route('api.bookmarks.index'),
            'api.bookmarks.store' => route('api.bookmarks.index'),
            'api.bookmarks.destroy' => route('api.bookmarks.destroy', ['id' => '__ID__']),
        ],
    ];
@endphp

@section('content')
    <div class="container-fluid" x-data="data()" x-init="initData({{ json_encode($params) }})">
        <div class="row border-bottom bg-light sticky-top">
            <div class="col-lg-3 col-xl-2 border-end p-3">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <button class="btn btn-primary rounded-circle" @click="isProfileModalOpen = !isProfileModalOpen"
                        x-text="$store.auth.user().name.charAt(0).toUpperCase()">
                    </button>
                    <button
                        class="btn rounded bg-white text-secondary border border-secondary-subtle rounded-4 d-flex flex-row p-2 px-3 flex-grow-1"
                        @click="isCreateModalOpen ? closeCreateModal() : (isCreateModalOpen = true)">
                        <i class="bi bi-plus-lg"></i>
                        <span class="ms-2">Create</span>
                    </button>
                </div>
            </div>
            <div class="col-lg-9 col-xl-10 p-2 g-0 d-flex">
                <div class="w-favicon d-flex flex-column align-items-center justify-content-between">
                    <p x-text="selectedBookmarks.length > 0 ? selectedBookmarks.length : '&nbsp;'"></p>
                    <input class="form-check-input m-1" type="checkbox"
                        @click="selectAll = !selectAll; selectedBookmarks = (selectAll ? bookmarks.map(b => b.id) : [])">
                </div>
                <div class="row flex-grow-1 text-break m-0">
                    <div class="col-md-7 col-lg-8">
                    </div>
                    <div class="col-md-5 col-lg-4 d-flex align-items-end flex-column">
                        <div class="input-group input-group-sm flex-nowrap pb-2">
                            <span class="input-group-text fs-8">collection</span>
                            <select class="form-control fs-8 p-1" :disabled="selectedBookmarks.length === 0"
                                x-model="bulkActions.collection">
                                <option></option>
                                <template x-for="collection in collections" x-show="!loading.callBookmarksCollections">
                                    <option :value="collection.name" x-text="collection.name">
                                    </option>
                                </template>
                            </select>
                            <button class="btn btn-outline-secondary fs-8 p-1 px-2"
                                :disabled="selectedBookmarks.length === 0" @click="applyUpdateBookmarksCollection()">
                                <i
                                    :class="loading.callBulkCollection ?
                                        'spinner-border spinner-border-sm' : 'bi bi-check2'"></i>
                            </button>
                        </div>
                        <div class="row g-0 gap-1 flex-grow-1 w-100">
                            <div class="col">
                                <select class="form-control fs-8 p-1" :disabled="selectedBookmarks.length === 0"
                                    x-model="bulkActions.is_read">
                                    <option></option>
                                    <option value="true">Read</option>
                                    <option value="false">UnRead</option>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-control fs-8 p-1" :disabled="selectedBookmarks.length === 0"
                                    x-model="bulkActions.is_shared">
                                    <option></option>
                                    <option value="true">Share</option>
                                    <option value="false">UnShare</option>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-control fs-8 p-1" :disabled="selectedBookmarks.length === 0"
                                    x-model="bulkActions.is_favorited">
                                    <option></option>
                                    <option value="true">Favorited</option>
                                    <option value="false">UnFavorited</option>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-control fs-8 p-1" :disabled="selectedBookmarks.length === 0"
                                    x-model="bulkActions.is_archived">
                                    <option></option>
                                    <option value="true">Archive</option>
                                    <option value="false">UnArchive</option>
                                </select>
                            </div>
                            <div class="col">
                                <button class="btn btn-outline-secondary w-100 fs-8 p-1 px-2"
                                    :disabled="selectedBookmarks.length === 0 || !hasBulkActions()"
                                    @click="applyUpdateBookmarks()">
                                    <i
                                        :class="loading.callBulkUpdate ? 'spinner-border spinner-border-sm' :
                                            'bi bi-check2'"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 col-xl-2 border-end bg-light min-vh-100 p-3">
                <div class="mb-3">
                    <input type="text" class="ps-3 form-control form-control-sm rounded-pill"
                        placeholder="Search bookmarks..." x-model="filters.q">
                </div>
                <div class="input-group input-group-sm mb-3">
                    <label class="input-group-text">
                        <i class="bi-bookmark-check"></i>
                    </label>
                    <select class="form-select" x-model="filters.read">
                        <option value="ALL">All</option>
                        <option value="READ">Read</option>
                        <option value="UNREAD">UnRead</option>
                    </select>
                </div>
                <div class="input-group input-group-sm mb-3">
                    <label class="input-group-text">
                        <i class="bi-share"></i>
                    </label>
                    <select class="form-select" x-model="filters.share">
                        <option value="ALL">All</option>
                        <option value="SHARED">Shared</option>
                        <option value="UNSHARED">UnShared</option>
                    </select>
                </div>
                <div class="input-group input-group-sm mb-3">
                    <label class="input-group-text">
                        <i class="bi-star"></i>
                    </label>
                    <select class="form-select" x-model="filters.favorite">
                        <option value="ALL">All</option>
                        <option value="FAVORITED">Favorited</option>
                        <option value="UNFAVORITED">UnFavorited</option>
                    </select>
                </div>
                <div class="input-group input-group-sm mb-3">
                    <label class="input-group-text">
                        <i class="bi-archive"></i>
                    </label>
                    <select class="form-select" x-model="filters.archive">
                        <option value="ALL">All</option>
                        <option value="ARCHIVED">Archived</option>
                        <option value="UNARCHIVED">UnArchived</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div x-show="loading.callBookmarksCollections" class="spinner-border spinner-border-sm"></div>
                    <div class="form-check" x-show="!loading.callBookmarksCollections">
                        <input class="form-check-input" type="checkbox" id="collection-none" value=""
                            x-model="filters.collections">
                        <label class="form-check-label fs-7" for="collection-none"></label>
                    </div>
                    <template x-for="collection in collections" x-show="!loading.callBookmarksCollections">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" :id="'collection-' + collection.name"
                                :value="collection.name" x-model="filters.collections">
                            <label class="form-check-label fs-7" :for="'collection-' + collection.name"
                                x-text="collection.name"></label>
                        </div>
                    </template>
                </div>
                <div>
                    <button class="btn btn-sm btn-primary rounded-pill w-100" @click="applyCollectionFilter()">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10 p-0 g-0">
                <template x-if="loading.callBookmarksIndex">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </template>
                <template x-if="!loading.callBookmarksIndex && (bookmarks.length < 1)">
                    <div class="text-center py-4 text-secondary fs-6">
                        <i class="bi bi-bookmarks fs-1 d-block mb-2"></i>
                        Your search did not match any documents.
                    </div>
                </template>
                <template x-if="!loading.callBookmarksIndex && bookmarks.length > 0">
                    <div class="">
                        <template x-for="(bookmark, index) in bookmarks">
                            <div class="d-flex border-bottom p-2">
                                <div class="w-favicon d-flex flex-column align-items-center justify-content-between">
                                    <img class="img-fluid w-100"
                                        :src="bookmark.url.favicon ?? bookmark.url.base_url + '/favicon.ico'"
                                        onerror="this.style.display='none'">
                                    <input class="form-check-input m-1" type="checkbox" :value="bookmark.id"
                                        x-model="selectedBookmarks">
                                </div>
                                <div class="row flex-grow-1 text-break m-0">
                                    <div class="col-md-7 col-lg-8">
                                        <a class="d-block text-decoration-none text-break pb-2" target="_blank"
                                            :href="bookmark.url.url">
                                            <div class="fs-8 text-secondary text-break" x-text="bookmark.url.url">
                                            </div>
                                            <div class="fs-7 text-secondary text-break" x-text="bookmark.url.description"
                                                x-show="bookmark.url.description">
                                            </div>
                                            <div class="fs-6 text-primary text-break" x-text="bookmark.url.title">
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-5 col-lg-4">
                                        <div class="input-group input-group-sm flex-nowrap pb-2">
                                            <span class="input-group-text fs-8">note</span>
                                            <input type="text" class="form-control fs-8 p-1"
                                                x-model="noteForms[bookmark.id]">
                                            <button class="btn btn-outline-secondary fs-8 p-1 px-2"
                                                @click="callUpdateBookmarks([bookmark.id], {'note': noteForms[bookmark.id]}, false)">
                                                <i class="bi bi-check2"></i>
                                            </button>
                                        </div>
                                        <div class="input-group input-group-sm flex-nowrap pb-2">
                                            <span class="input-group-text fs-8">collection</span>
                                            <input type="text" class="form-control fs-8 p-1"
                                                x-model="collectionForms[bookmark.id]">
                                            <button class="btn btn-outline-secondary fs-8 p-1 px-2"
                                                @click="callUpdateBookmarks([bookmark.id], {'collection': collectionForms[bookmark.id]}, true)">
                                                <i class="bi bi-check2"></i>
                                            </button>
                                        </div>
                                        <div class="row g-0 gap-1">
                                            <div class="col">
                                                <span
                                                    class="cursor-pointer d-flex align-items-center justify-content-center gap-1 fs-8 py-1"
                                                    :class="{ 'fw-bold text-success': bookmark.read_at }"
                                                    @click="callUpdateBookmarks([bookmark.id], {'is_read': bookmark.read_at ? false : true})">
                                                    <i
                                                        :class="loading.callx?.[bookmark.id]?.is_read ?
                                                            'spinner-border spinner-border-sm' : (bookmark
                                                                .read_at ?
                                                                'bi-bookmark-check-fill' :
                                                                'bi-bookmark-check')"></i>
                                                    <span class="text-truncate"
                                                        x-text="bookmark.read_at ? 'Read' : 'Unread'"></span>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <span
                                                    class="cursor-pointer d-flex align-items-center justify-content-center gap-1 fs-8 py-1"
                                                    :class="{ 'fw-bold text-primary': bookmark.shared_at }"
                                                    @click="callUpdateBookmarks([bookmark.id], {'is_shared': bookmark.shared_at ? false : true})">
                                                    <i
                                                        :class="loading.callx?.[bookmark.id]?.is_shared ?
                                                            'spinner-border spinner-border-sm' : (bookmark
                                                                .shared_at ?
                                                                'bi-share-fill' : 'bi-share')"></i>
                                                    <span class="text-truncate"
                                                        x-text="bookmark.shared_at ? 'Shared' : 'Share'"></span>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <span
                                                    class="cursor-pointer d-flex align-items-center justify-content-center gap-1 fs-8 py-1"
                                                    :class="{ 'fw-bold text-warning': bookmark.favorited_at }"
                                                    @click="callUpdateBookmarks([bookmark.id], {'is_favorited': bookmark.favorited_at ? false : true})">
                                                    <i
                                                        :class="loading.callx?.[bookmark.id]?.is_favorited ?
                                                            'spinner-border spinner-border-sm' : (bookmark
                                                                .favorited_at ? 'bi-star-fill' : 'bi-star'
                                                            )"></i>
                                                    <span class="text-truncate"
                                                        x-text="bookmark.favorited_at ? 'Favorited' : 'Favorite'"></span>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <span
                                                    class="cursor-pointer d-flex align-items-center justify-content-center gap-1 fs-8 py-1"
                                                    :class="{ 'fw-bold text-dark': bookmark.archived_at }"
                                                    @click="callUpdateBookmarks([bookmark.id], {'is_archived': bookmark.archived_at ? false : true})">
                                                    <i
                                                        :class="loading.callx?.[bookmark.id]?.is_archived ?
                                                            'spinner-border spinner-border-sm' : (bookmark
                                                                .archived_at ?
                                                                'bi-archive-fill' : 'bi-archive')"></i>
                                                    <span class="text-truncate"
                                                        x-text="bookmark.archived_at ? 'Archived' : 'Archive'"></span>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <button class="btn btn-outline-danger fs-8 p-1 w-100"
                                                    :disabled="loading.callDestroyBookmark == bookmark.id"
                                                    @click="callDestroyBookmark(bookmark.id)">
                                                    <i
                                                        :class="loading.callDestroyBookmark == bookmark.id ?
                                                            'spinner-border spinner-border-sm' : 'bi bi-trash'"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Pagination -->
                <template
                    x-if="!loading.callBookmarksIndex && paginator && !(paginator.currentPage == 1 && paginator.onLastPage)">
                    <div class="d-flex justify-content-center gap-1 my-3">
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
                                class="text-decoration-none px-2 cursor-pointer" x-text="n"
                                :class="{
                                    'fw-bold text-primary': paginator?.currentPage == n,
                                    'text-secondary': paginator
                                        ?.currentPage != n
                                }">
                            </span>
                        </template>
                        <a x-show="!paginator?.onLastPage"
                            @click="doFilter(() => filters.page = paginator?.currentPage + 1, false, false)"
                            class="cursor-pointer px-2 text-decoration-none text-primary">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </template>
            </div>
        </div>

        <!-- Profile Modal -->
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

        <!-- Create Modal -->
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
                            <input class="form-control" x-model="createForm.url" placeholder="Enter URL...">
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
                selectAll: false,
                loading: {
                    callBookmarksCollections: false,
                    callBookmarksIndex: false,
                    callBulkCollection: false,
                    callBulkUpdate: false,
                    callAuthLogout: false,
                    callNetscapeImport: false,
                    callNetscapeExport: false,
                    callStoreBookmark: false,
                    callDestroyBookmark: null,
                    callx: null,
                },
                collections: [],
                bookmarks: [],
                selectedBookmarks: [],
                filters: {
                    q: '',
                    collections: [],
                    read: "ALL",
                    archive: "UNARCHIVED",
                    share: "ALL",
                    favorite: "ALL",
                    page: 1
                },
                bulkActions: {
                    is_read: '',
                    is_shared: '',
                    is_favorited: '',
                    is_archived: '',
                    collection: '',
                },
                scrollY: null,
                dropdown: null,
                paginator: null,
                noteForms: {},
                collectionForms: {},
                createForm: {
                    url: null,
                },
                init() {},
                async initData(initParams) {
                    this.urls = initParams.urls;
                    this.doFilter(() => {}, true);
                },
                async doFilter(func, resetCollection = false, resetPage = true) {
                    func();
                    this.scrollY = window.scrollY;
                    if (resetCollection) {
                        this.collections = [];
                        this.callBookmarksCollections();
                    }
                    if (resetPage) {
                        this.filters.page = 1;
                    }
                    this.collectionModalId = null;
                    this.closeCreateModal();
                    this.selectedBookmarks = [];
                    this.selectAll = false;
                    this.bookmarks = [];
                    await this.callBookmarksIndex();
                    if (!resetPage) {
                        window.scrollTo(0, this.scrollY);
                    }
                },
                applyCollectionFilter() {
                    this.doFilter(() => {}, false);
                },
                pageRange(sideCount = 5) {
                    const {
                        perPage,
                        currentPage,
                        total
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
                hasBulkActions() {
                    return this.bulkActions.read !== '' ||
                        this.bulkActions.share !== '' ||
                        this.bulkActions.favorite !== '' ||
                        this.bulkActions.archive !== '';
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

                        const res = await this.$store.call.callJson(
                            'GET', this.urls['api.bookmarks.index'], filters, null, true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            this.bookmarks = resJson.data.bookmarks;
                            this.paginator = resJson.paginator;
                            //
                            this.noteForms = {};
                            this.collectionForms = {};
                            this.bookmarks.forEach(bookmark => {
                                this.noteForms[bookmark.id] = bookmark.note;
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
                async applyUpdateBookmarks() {
                    if (this.selectedBookmarks.length === 0) {
                        return;
                    }

                    if (!this.hasBulkActions()) {
                        return;
                    }

                    const data = {};
                    if (this.bulkActions.is_read !== '') {
                        data['is_read'] = this.bulkActions.is_read === 'true';
                    }
                    if (this.bulkActions.is_shared !== '') {
                        data['is_shared'] = this.bulkActions.is_shared === 'true';
                    }
                    if (this.bulkActions.is_favorited !== '') {
                        data['is_favorited'] = this.bulkActions.is_favorited === 'true';
                    }
                    if (this.bulkActions.is_archived !== '') {
                        data['is_archived'] = this.bulkActions.is_archived === 'true';
                    }

                    this.loading.callBulkUpdate = true;
                    try {
                        await this.callUpdateBookmarks(this.selectedBookmarks, data);
                    } finally {
                        this.loading.callBulkUpdate = false;
                    }
                },
                async applyUpdateBookmarksCollection() {
                    if (this.selectedBookmarks.length === 0) {
                        return;
                    }

                    const data = {
                        collection: this.bulkActions.collection
                    };

                    this.loading.callBulkCollection = true;
                    try {
                        await this.callUpdateBookmarks(this.selectedBookmarks, data, false);
                    } finally {
                        this.loading.callBulkCollection = false;
                    }
                },
                async callUpdateBookmarks(ids, data, resetCollection = false) {
                    try {
                        if (this.loading.callUpdateBookmarks) return;
                        this.loading.callUpdateBookmarks = true;

                        const res = await this.$store.call.callJson(
                            'PATCH', this.urls['api.bookmarks.updateAttributes'], null, {
                                ids: ids,
                                ...data
                            }, true
                        );
                        const resJson = await res.json();

                        if (res.ok) {
                            this.$store.alert.success('Bookmark updated successfully!');

                            const raw = resJson.data?.bookamrks;
                            const updated = Array.isArray(raw) ? raw : (raw?.data || []);

                            if (updated.length) {
                                this.bookmarks = this.bookmarks.map(b => {
                                    const match = updated.find(u => u.id === b.id);
                                    return match ? { ...b, ...match } : b;
                                });
                            }

                            if (resetCollection) {
                                this.doFilter(() => {}, true, false);
                            }
                        } else {
                            this.$store.alert.error(resJson.message, resJson.errors);
                        }

                    } catch (err) {
                        console.log(err);
                        this.$store.alert.error('Error');
                    } finally {
                        this.loading.callUpdateBookmarks = false;
                    }
                },
                async callDestroyBookmark(bookmarkId) {
                    this.$store.alert.confirm('Are you sure you want to delete this bookmark?', async (result) => {
                        if (!result.isConfirmed) {
                            return;
                        }

                        try {
                            if (this.loading.callDestroyBookmark === bookmarkId) return;
                            this.loading.callDestroyBookmark = bookmarkId;

                            const res = await this.$store.call.callJson(
                                'DELETE', this.urls['api.bookmarks.destroy'].replace('__ID__',
                                    bookmarkId), null, null, true
                            );
                            const resJson = await res.json();

                            if (res.ok) {
                                this.$store.alert.success('Bookmark deleted successfully!');
                                this.doFilter(() => {});
                            } else {
                                this.$store.alert.error(resJson.message, resJson.errors);
                            }

                        } catch (err) {
                            console.log(err);
                            this.$store.alert.error('Error');
                        } finally {
                            this.loading.callDestroyBookmark = null;
                        }
                    });
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
                            this.$store.alert.success('Bookmark created successfully!');
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
                }
            }
        }
    </script>
@endsection
