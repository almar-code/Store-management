@extends('Layouts.master')

@section('link')
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
    @php
        // وضع الخصومات: تأتي من DiscountController@index
        $discountMode = $discountMode ?? false;

        // تنسيق السعر: 150 بدل 150.00 و 150.5 بدل 150.50
        $formatPrice = fn ($value) => rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
        $discountedCount = $products->filter(fn ($p) => $p->discount)->count();
    @endphp

    <style>
        /* ===== شريط الإحصائيات ===== */
        .pp-stats { display: flex; flex-wrap: wrap; gap: 10px; margin: 6px 8px 18px; }
        .pp-stat { display: inline-flex; align-items: center; gap: 8px; background: #fff; border-radius: 10px; padding: 8px 14px; box-shadow: 0 2px 12px rgba(0, 0, 0, .05); font-size: .85rem; color: #555; }
        .pp-stat b { color: #00695c; font-size: 1rem; }
        .pp-stat i { color: #008870; }

        /* ===== بطاقة المنتج (عرضية) ===== */
        .pp-card { position: relative; background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, .05); padding: 12px; height: 100%; display: flex; gap: 14px; align-items: stretch; transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; text-align: right; border: 1px solid #e6eeec; }
        .pp-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0, 105, 92, .13); border-color: #b2dfdb; }

        /* البطاقة التي قائمتها مفتوحة تبقى فوق باقي البطاقات */
        .card-item { position: relative; }
        .card-item.pp-menu-open { z-index: 1060; }
        .card-item.pp-menu-open .pp-card { transform: none; }

        .pp-media { position: relative; flex: 0 0 112px; width: 112px; aspect-ratio: 1 / 1; align-self: flex-start; border-radius: 14px; overflow: hidden; background: linear-gradient(135deg, #f3f8f7, #e0f2f1); }
        .pp-media img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
        .pp-card:hover .pp-media img { transform: scale(1.07); }
        .pp-noimg { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9cc9c3; font-size: 2rem; }
        .pp-badge-discount { position: absolute; top: 6px; right: 6px; z-index: 2; background: #d32f2f; color: #fff; font-size: .7rem; font-weight: 700; padding: 3px 7px; border-radius: 7px; box-shadow: 0 3px 10px rgba(211, 47, 47, .35); }

        .pp-info { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .pp-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 6px; }
        .pp-cat { display: inline-flex; align-items: center; gap: 4px; max-width: 45%; margin-inline-start: auto; background: #f3f8f7; color: #00695c; font-size: .7rem; font-weight: 600; padding: 3px 8px; border-radius: 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; align-self: center; }
        .pp-name { font-size: .98rem; font-weight: 700; color: #1f2d2b; margin: 2px 0 3px; line-height: 1.4; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .pp-desc { font-size: .78rem; color: #8a9593; margin: 0 0 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .pp-price { display: flex; align-items: baseline; flex-wrap: wrap; gap: 6px; }
        .pp-price-now { font-size: 1.12rem; font-weight: 800; color: #00695c; }
        .pp-price-now.sale { color: #d32f2f; }
        .pp-currency { font-size: .72rem; color: #008870; font-weight: 600; }
        .pp-price-old { font-size: .8rem; color: #9e9e9e; text-decoration: line-through; }

        .pp-ends { display: inline-flex; align-items: center; gap: 4px; font-size: .74rem; color: #ef6c00; margin-top: 4px; }

        .pp-actions { display: flex; align-items: center; gap: 6px; margin-top: auto; padding-top: 8px; }
        .pp-btn { display: inline-flex; align-items: center; justify-content: center; gap: 5px; border-radius: 9px; padding: 6px 12px; font-size: .78rem; font-weight: 600; text-decoration: none; transition: .2s; border: 1px solid transparent; }
        .pp-btn-primary { background: #00695c; color: #fff; }
        .pp-btn-primary:hover { background: #004d40; color: #fff; }
        .pp-btn-danger { background: #fff; color: #c62828; border-color: #f3c7c7; }
        .pp-btn-danger:hover { background: #c62828; color: #fff; border-color: #c62828; }
        .pp-icon-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 9px; background: #f3f8f7; color: #00695c; border: none; text-decoration: none; transition: .2s; flex-shrink: 0; }
        .pp-icon-btn:hover { background: #e0f2f1; color: #004d40; }

        .pp-menu .dropdown-menu { border-radius: 12px; border: 1px solid #eef1f1; padding: 6px; min-width: 220px; }
        .pp-menu .dropdown-item { border-radius: 8px; font-size: .88rem; }
        .pp-menu .dropdown-item:hover { background: #f3f8f7; }

        .pp-empty { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, .06); padding: 60px 20px; text-align: center; margin: 0 8px; }
        .pp-empty-icon { font-size: 3rem; color: #b2dfdb; display: block; line-height: 1; }
        .pp-empty h5 { color: #1f2d2b; font-weight: 700; }
        .pp-btn-icon { margin-inline-start: 6px; font-size: 1rem; }

        @media (max-width: 576px) {
            .pp-card { padding: 10px; gap: 10px; }
            .pp-media { flex-basis: 92px; width: 92px; }
            .pp-name { font-size: .9rem; }
            .pp-price-now { font-size: 1rem; }
        }
    </style>

    <div class="container my-4" dir="rtl">
        <div class="container">

            <div class="controls-container">
                <div class="section-ar">
                    <div class="section-title" style="margin-bottom: 0px;">
                        @if ($discountMode)
                            <h3 style="font-size: 20px">قائمة <span class="orange-text">المنتجات المخفضة</span></h3>
                        @else
                            <h3 style="font-size: 20px">قائمة <span class="orange-text">المنتجات</span></h3>
                        @endif
                    </div>
                </div>
                <div class="search-input-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search" id="sectionSearch">
                </div>

                @if ($discountMode)
                    <a href="/products" class="btn-add-product shadow-sm">
                        كل المنتجات
                        <i class="bi bi-box-seam pp-btn-icon"></i>
                    </a>
                @else
                    <a href="/addproduct" class="btn-add-product shadow-sm">
                        إضافة منتج
                        <i class="bi bi-plus-lg pp-btn-icon"></i>
                    </a>

                    <i class="bi bi-question-circle text-turquoise fs-4"
                        onclick="startTour()"
                        style="cursor: pointer; margin-right: 10px;"
                        title="مساعدة - Help">
                    </i>
                @endif
            </div>
        </div>

        @if ($products->isNotEmpty())
            <div class="pp-stats">
                @if ($discountMode)
                    <span class="pp-stat"><i class="bi bi-percent"></i> منتجات عليها خصم <b>{{ $products->count() }}</b></span>
                @else
                    <span class="pp-stat"><i class="bi bi-box-seam"></i> عدد المنتجات <b>{{ $products->count() }}</b></span>
                    <span class="pp-stat"><i class="bi bi-percent"></i> عليها خصم <b>{{ $discountedCount }}</b></span>
                @endif
            </div>
        @endif

        <div class="row g-3 px-2">
            @forelse ($products as $product)
                @php
                    $price = (float) $product->p_price;
                    $discount = $product->discount;
                    $hasDiscount = (bool) $discount;
                    $discountAmount = $hasDiscount ? (float) $discount->discount_perce : 0;
                    $finalPrice = $price - $discountAmount;
                    $discountPercent = ($hasDiscount && $price > 0) ? round(($discountAmount / $price) * 100) : 0;
                    $categoryName = optional($product->subcategory)->subcat_name;
                    $daysLeft = ($hasDiscount && $discount->end_date)
                        ? (int) \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($discount->end_date)->startOfDay(), false)
                        : null;
                @endphp

                <div class="col-12 col-md-6 col-xl-4 card-item"
                    @if ($loop->first && !$discountMode)
                        data-intro="بطاقة المنتج: تظهر لك هنا صورة المنتج، اسمه، وسعره بشكل سريع ومختصر."
                        data-step="1"
                    @endif>
                    <div class="pp-card">

                        {{-- الصورة --}}
                        <div class="pp-media">
                            @if (!empty($product->p_image))
                                <img src="{{ asset('storage/uploads/products/' . $product->p_image) }}"
                                    alt="{{ $product->p_name }}" loading="lazy">
                            @else
                                <div class="pp-noimg"><i class="bi bi-image"></i></div>
                            @endif

                            @if ($hasDiscount)
                                <span class="pp-badge-discount">-{{ $discountPercent }}%</span>
                            @endif
                        </div>

                        {{-- البيانات --}}
                        <div class="pp-info">
                            <div class="pp-top">
                                <h5 class="pp-name card-title" style="min-width: 0;" title="{{ $product->p_name }}">{{ $product->p_name ?? '' }}</h5>

                                {{-- قائمة الخيارات (في صفحة المنتجات فقط) --}}
                                @unless ($discountMode)
                                    <div class="dropdown pp-menu"
                                        @if ($loop->first)
                                            data-step="2"
                                            data-position="buttom"
                                            data-intro='
                                            <div class="tour-header">
                                                <i class="bi bi-gear-fill spin-icon"></i>
                                                <span>مركز التحكم الاحترافي</span>
                                            </div>
                                            <div class="tour-content">
                                                <p class="tour-desc text-end mb-3" style="line-height: 1.6; color: #555;">
                                                   لقد صممنا لك هذه القائمة لتسهل عليك إدارة منتجاتك في خطوات بسيطة.
                                                </p>
                                                <ul class="tour-list">
                                                    <li><i class="bi bi-pencil-square text-primary"></i> <span><b>تعديل المنتج:</b> صفحة لتغيير البيانات الأساسية ورفع <b>صورة الغلاف</b>.</span></li>
                                                    <li><i class="bi bi-palette text-success"></i> <span><b>ألوان المنتج:</b> صفحة لإضافة الألوان ورفع <b>ألبوم صور كامل</b> لكل لون.</span></li>
                                                    <li><i class="bi bi-rulers text-info"></i> <span><b>مقاسات المنتج:</b> صفحة مستقلة لضبط جدول المقاسات المتاحة.</span></li>
                                                    <li><i class="bi bi-tags text-warning"></i> <span><b>العروض:</b> صفحة التحكم في الخصومات وتحديد سعر العرض.</span></li>
                                                </ul>
                                            </div>'
                                        @endif>
                                        <button class="pp-icon-btn" type="button" data-bs-toggle="dropdown"
                                            data-bs-boundary="viewport" aria-expanded="false" title="المزيد">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-start shadow">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/edit-product/{{ $product->p_id }}">
                                                    <span class="ms-2">تعديل</span>
                                                    <i class="bi bi-pencil-square text-turquoise"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/addsize/{{ $product->p_id }}">
                                                    <span class="ms-2">اضافة مقاس</span>
                                                    <i class="bi bi-plus-circle text-turquoise"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/sizeManagement/{{ $product->p_id }}">
                                                    <span class="ms-2">مقاسات المنتج</span>
                                                    <i class="bi bi-rulers text-turquoise"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/addColor/{{ $product->p_id }}">
                                                    <span class="ms-2">اضافة لون</span>
                                                    <i class="bi bi-plus-circle text-turquoise"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/colors/{{ $product->p_id }}">
                                                    <span class="ms-2">الوان المنتج</span>
                                                    <i class="bi bi-palette text-turquoise"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/add_videoToProduct/{{ $product->p_id }}">
                                                    <span class="ms-2">اضافة فيديو</span>
                                                    <i class="bi bi-camera-video text-turquoise"></i>
                                                </a>
                                            </li>
                                            @if (!$hasDiscount)
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                        href="/addDiscount/{{ $product->p_id }}">
                                                        <span class="ms-2">اضافة خصم</span>
                                                        <i class="bi bi-percent text-turquoise"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                        href="/edit-discount/{{ $discount->discount_id }}">
                                                        <span class="ms-2">تعديل الخصم</span>
                                                        <i class="bi bi-pencil text-turquoise"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                        href="#"
                                                        onclick="confirmDelete('/delete-discount/{{ $discount->discount_id }}', 'حذف الخصم', 'هل أنت متأكد من حذف هذا الخصم؟'); return false;">
                                                        <span class="ms-2">حذف الخصم</span>
                                                        <i class="bi bi-x-circle text-danger"></i>
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/create-ad/{{ $product->p_id }}">
                                                    <span class="ms-2">إضافة إعلان للمنتج</span>
                                                    <i class="bi bi-megaphone text-turquoise"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                    href="/assign-general-ad/{{ $product->p_id }}">
                                                    <span class="ms-2">إضافة المنتج لعرض عام</span>
                                                    <i class="bi bi-tag text-turquoise"></i>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-danger fw-bold text-end"
                                                    href="#"
                                                    onclick="confirmDelete('/delete-product/{{ $product->p_id }}', 'حذف منتج؟', 'هل أنت متأكد من حذف هذا المنتج؟'); return false;">
                                                    <span class="ms-2">حذف المنتج</span>
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endunless
                            </div>

                            <p class="pp-desc card-text">{{ $product->p_description ?? '' }}</p>

                            <div class="pp-price">
                                @if ($hasDiscount)
                                    <span class="pp-price-now sale">{{ $formatPrice($finalPrice) }}</span>
                                    <span class="pp-currency">ر.س</span>
                                    <span class="pp-price-old">{{ $formatPrice($price) }}</span>
                                @else
                                    <span class="pp-price-now">{{ $formatPrice($price) }}</span>
                                    <span class="pp-currency">ر.س</span>
                                @endif

                                @if ($categoryName)
                                    <span class="pp-cat" title="{{ $categoryName }}"><i class="bi bi-tag"></i> {{ $categoryName }}</span>
                                @endif
                            </div>

                            @if ($discountMode && $daysLeft !== null)
                                <span class="pp-ends">
                                    <i class="bi bi-clock-history"></i>
                                    @if ($daysLeft <= 0)
                                        ينتهي الخصم اليوم
                                    @elseif ($daysLeft == 1)
                                        ينتهي الخصم غداً
                                    @else
                                        متبقٍ {{ $daysLeft }} يوم على انتهاء الخصم
                                    @endif
                                </span>
                            @endif

                            {{-- الأزرار --}}
                            <div class="pp-actions">
                                @if ($discountMode)
                                    <a href="/edit-discount/{{ $discount->discount_id }}" class="pp-btn pp-btn-primary">
                                        <i class="bi bi-pencil"></i> تعديل الخصم
                                    </a>
                                    <a href="#" class="pp-btn pp-btn-danger"
                                        onclick="confirmDelete('/delete-discount/{{ $discount->discount_id }}', 'حذف الخصم', 'هل أنت متأكد من حذف هذا الخصم؟'); return false;">
                                        <i class="bi bi-x-circle"></i> حذف الخصم
                                    </a>
                                @else
                                    <a href="/edit-product/{{ $product->p_id }}" class="pp-btn pp-btn-primary">
                                        <i class="bi bi-pencil-square"></i> تعديل
                                    </a>
                                    <a href="/colors/{{ $product->p_id }}" class="pp-icon-btn" title="ألوان المنتج">
                                        <i class="bi bi-palette"></i>
                                    </a>
                                    <a href="/sizeManagement/{{ $product->p_id }}" class="pp-icon-btn" title="مقاسات المنتج">
                                        <i class="bi bi-rulers"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="pp-empty">
                        @if ($discountMode)
                            <i class="bi bi-percent pp-empty-icon"></i>
                            <h5 class="mt-3">لا توجد منتجات عليها خصم حالياً</h5>
                            <p class="text-muted">يمكنك إضافة خصم من قائمة خيارات أي منتج</p>
                            <a href="/products" class="btn-add-product shadow-sm d-inline-flex mt-2">
                                كل المنتجات <i class="bi bi-box-seam pp-btn-icon"></i>
                            </a>
                        @else
                            <i class="bi bi-box-seam pp-empty-icon"></i>
                            <h5 class="mt-3">لا توجد منتجات بعد</h5>
                            <p class="text-muted">ابدأ بإضافة أول منتج لمتجرك</p>
                            <a href="/addproduct" class="btn-add-product shadow-sm d-inline-flex mt-2">
                                إضافة منتج <i class="bi bi-plus-lg pp-btn-icon"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        // رفع البطاقة التي قائمتها مفتوحة فوق البطاقات المجاورة
        document.querySelectorAll('.pp-menu').forEach(function (menu) {
            const item = menu.closest('.card-item');
            menu.addEventListener('show.bs.dropdown', () => item.classList.add('pp-menu-open'));
            menu.addEventListener('hidden.bs.dropdown', () => item.classList.remove('pp-menu-open'));
        });
    </script>
@endsection