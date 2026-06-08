@extends('layouts.app')

@section('title', 'بوکمارک‌ها')

@section('content')

    <div class="container-fluid">
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
                <div class="fs-7 d-inline-block px-0 py-2 me-3 border-3 border-bottom border-dark">All</div>
                <div class="fs-7 d-inline-block px-0 py-2 me-3">Images</div>
                <div class="fs-7 d-inline-block px-0 py-2 me-3">Videos</div>
                <div class="fs-7 d-inline-block px-0 py-2 me-3">Forums</div>
                <div class="fs-7 d-inline-block px-0 py-2 me-3">News</div>
                <div class="fs-7 d-inline-block px-0 py-2 me-3">Short</div>
                <div class="fs-7 d-inline-block px-0 py-2 me-3">Web</div>
                <div class="fs-7 d-inline-block px-0 py-2 me-3">More</div>
            </div>
        </div>
        <div class="row py-4">
            <div class="col-lg-10 offset-lg-1">
                <div class="d-flex flex-wrap gap-2">
                    <div class="fs-7 rounded-pill px-3 py-2 bg-white text-dark border border-secondary">Css</div>
                    <div class="fs-7 rounded-pill px-3 py-2 bg-white text-dark border border-secondary">Bootstrap</div>
                    <div class="fs-7 rounded-pill px-3 py-2 bg-white text-dark border border-secondary">Navbar</div>
                    <div class="fs-7 rounded-pill px-3 py-2 bg-white text-dark border border-secondary">Carousel</div>
                    <div class="fs-7 rounded-pill px-3 py-2 bg-white text-dark border border-secondary">Modal</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-7 offset-lg-1">

                <div class="d-flex flex-column pb-4">
                    <div class="d-flex">
                        <div class="d-flex flex-grow-0 me-2 justify-content-center align-items-center">
                            <img class="w-32" src="https://lendo.ir/favicon.ico">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <div class="fs-7">
                                https://lendo.ir
                                <i class="bi bi-three-dots-vertical"></i>
                            </div>
                            <div class="fs-8">
                                https://lendo.ir/landing/bank-validation
                            </div>
                        </div>
                    </div>
                    <div class="fs-5 text-primary">
                        لندو: خرید قسطی کالا و خدمات از فروشگاه‌های آنلاین
                    </div>
                    <div class="fs-7">
                        آرمان گستر آریا (لندو) است.Copyrights - Lendo Co. - 1401. همین الان گردونه رو بچرخون، جایزه بگیر!
                        start. ثبت‌نام | ورود به حساب کاربری. شماره
                    </div>
                    <div class="d-flex gap-3 fs-7">
                        <span class="text-dark">
                            #tag1
                        </span>
                        <span class="text-dark">
                            #tag2
                        </span>
                        <span class="text-dark">
                            #tag3
                        </span>
                        <span class="text-dark">
                            #tag4
                        </span>
                        <span class="text-dark">
                            #tag5
                        </span>
                        <span class="text-dark">
                            #tag6
                        </span>
                    </div>
                    <div class="d-flex gap-3 fs-7">
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Read
                        </button>
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Archive
                        </button>
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Share
                        </button>
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Favorite
                        </button>
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Collection
                        </button>
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Tags
                        </button>
                    </div>
                </div>

                <div class="d-flex flex-column pb-4">
                    <div class="d-flex">
                        <div class="d-flex flex-grow-0 w-32 me-2 justify-content-center align-items-center">
                            <img class="w-32" src="https://www.mydigipay.com/favicon.ico">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <div class="fs-7">
                                https://www.mydigipay.com
                                <i class="bi bi-three-dots-vertical"></i>
                            </div>
                            <div class="fs-8">
                                https://www.mydigipay.com/merchants-seller/
                            </div>
                        </div>
                    </div>
                    <div class="fs-5 text-primary">
                        صفحه‌اصلی | دیجی‌پی
                    </div>
                    <div class="fs-7">
                        مانیتور 27 اینچ ای او سی مدل 27B30H با گارانتی 18 ماهه شرکتی · شلف دیواری زیر تی وی DT 136 ·
                        پاوربانک Solar Flex ظرفیت 20000 میلی آمپری گرین لاین Green Lion Solar
                    </div>
                    <div class="d-flex gap-3 fs-7">
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Read
                        </button>
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Archive
                        </button>
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Share
                        </button>
                        <button class="btn btn-sm btn-link text-decoration-underline p-0 text-dark">
                            Favorite
                        </button>
                    </div>
                </div>

            </div>
            <div class="col-lg-3">
                <div class="card sticky-top">
                    <div class="card-body">
                        Notes Notes Notes Notes Notes Notes Notes Notes Notes Notes Notes Notes Notes Notes Notes Notes
                        Notes Notes Notes Notes Notes Notes Notes Notes
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-badge"></i> پروفایل کاربر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>نام:</strong> احسان محمدی</p>
                    <p><strong>ایمیل:</strong> ehsan@bookmark.com</p>
                    <p><strong>تعداد کل بوکمارک‌ها:</strong> 6</p>
                    <p><strong>تعداد کالکشن‌ها:</strong> 6</p>
                </div>
            </div>
        </div>
    </div>
@endsection
