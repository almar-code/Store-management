@extends('Layouts.master')
@section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
<div class="container my-4 px-2 px-md-2 at-page" dir="rtl">
    <div class="container">
        {{-- رأس الصفحة --}}
        <div class="controls-container">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">
                        قائمة <span class="orange-text">الاعلانات</span>
                        <span class="at-count">{{ $ads->count() }}</span>
                    </h3>
                </div>
            </div>

            <div class="search-input-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" placeholder="ابحث عن إعلان..." id="sectionSearch">
            </div>

            <a href="{{ url()->current() }}" class="at-btn" title="تحديث الصفحة">
                <i class="bi bi-arrow-clockwise"></i>
            </a>

            <i class="bi bi-question-circle text-turquoise fs-4"
               onclick="startTour()"
               style="cursor: pointer; margin-right: 10px;"
               title="مساعدة - Help"></i>
        </div>

        {{-- جدول الإعلانات --}}
        <div class="table-container table-responsive">
            <table class="table align-middle at-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>الإعلان</th>
                        <th>النوع</th>
                        <th data-intro="رابط التوجيه: هنا تضع الرابط الذي تريد نقل العميل إليه؛ بمجرد أن يضغط العميل على صورة الإعلان في التطبيق، سيقوم النظام بفتحه فوراً ونقله إلى الصفحة المحددة (سواء كانت صفحة منتج معين، قسم خاص، أو عرض خارجي). تأكد من وضع الرابط كاملاً لضمان انتقال العميل بنجاح." data-step="1">الرابط</th>
                        <th data-intro="حالة الظهور والتحكم: اضغط على الحالة لتفعيل الإعلان أو إيقافه؛ الأخضر يظهر الإعلان للعملاء والأحمر يخفيه، والإعلان المنتهي يطلب منك مدة جديدة قبل تفعيله." data-step="2">الحالة</th>
                        <th data-intro="تاريخ الانتهاء: يوضح هذا التاريخ موعد توقف الإعلان تلقائياً عن الظهور، مما يساعدك على تنظيم حملاتك الإعلانية بدقة." data-step="3">الانتهاء</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ads as $adss)
                    @php
                        $isExpired = $adss->expires_at && $adss->expires_at->lt(\Carbon\Carbon::today());
                        $typeKey = $adss->type === 'link' ? 'link' : ($adss->type === 'general' ? 'general' : ($adss->type === 'product' || $adss->product_id ? 'product' : 'image'));
                        $typeClass = ['image' => 'gray', 'general' => 'green', 'link' => 'amber', 'product' => 'blue'][$typeKey];
                        $typeLabel = ['image' => 'إعلان خاص', 'general' => 'إعلان عام', 'link' => 'إعلان برابط', 'product' => 'إعلان منتج'][$typeKey];
                        $statusClass = $adss->is_active ? 'green' : ($isExpired ? 'amber' : 'red');
                        $statusLabel = $adss->is_active ? 'نشط' : ($isExpired ? 'منتهي' : 'غير نشط');
                    @endphp
                    <tr class="card-item">
                        <td><span class="at-num">{{ $loop->iteration }}</span></td>
                        <td>
                            <img src="{{ asset('storage/uploads/Advertisement/' . $adss->AdsImage) }}" class="at-thumb" alt="" loading="lazy">
                        </td>
                        <td class="fw-bold lh-1 fs-md-5 card-title">{{ $adss->AdsName }}</td>
                        <td><span class="at-pill {{ $typeClass }}">{{ $typeLabel }}</span></td>
                        <td>
                            @if ($adss->type == 'link' && $adss->AdsLink)
                                <a href="{{ $adss->AdsLink }}" target="_blank" rel="noopener" class="at-link" title="{{ $adss->AdsLink }}">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                    <span>{{ \Illuminate\Support\Str::limit(preg_replace('#^(https?://)?(www\.)?#', '', $adss->AdsLink), 4, '...') }}</span>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {{-- الإعلان المنتهي يطلب مدة جديدة قبل التفعيل --}}
                            <a href="{{ !$adss->is_active && $isExpired ? '#' : '/update-ads/' . $adss->ads_id }}"
                               class="at-pill {{ $statusClass }} {{ !$adss->is_active && $isExpired ? 'renew-ad' : '' }}"
                               @if(!$adss->is_active && $isExpired)
                               data-id="{{ $adss->ads_id }}" data-name="{{ $adss->AdsName }}"
                               @endif
                               title="{{ $adss->is_active ? 'اضغط لإيقاف الإعلان' : ($isExpired ? 'اضغط لتجديد المدة وتفعيل الإعلان' : 'اضغط لتفعيل الإعلان') }}">
                                <span class="at-dot"></span>{{ $statusLabel }}
                            </a>
                        </td>
                        <td>
                            <span class="at-nowrap {{ $isExpired ? 'at-danger' : '' }}">
                                {{ optional($adss->expires_at)->toDateString() ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="at-actions-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="العمليات">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow border-light at-menu">
                                    @if($adss->type == 'general')
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center py-2 text-end" href="/ads/{{ $adss->ads_id }}/products">
                                                <i class="bi bi-eye text-turquoise"></i>
                                                <span>عرض المنتجات</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2 text-danger text-end" href="#"
                                           onclick="confirmDelete('/delete-ads/{{ $adss->ads_id }}','حذف الاعلان','هل أنت متأكد من حذف هذا الاعلان')">
                                            <i class="bi bi-trash3"></i>
                                            <span>حذف الإعلان</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="at-empty">
                            <i class="bi bi-megaphone"></i>
                            لا توجد إعلانات حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- نافذة تجديد مدة الإعلان المنتهي --}}
<div class="modal fade" id="renewAdModal" tabindex="-1" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content at-modal" id="renewAdForm" method="post">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">تجديد مدة الإعلان</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">
                    انتهت مدة الإعلان <strong id="renewAdName"></strong>، حدد مدة جديدة تبدأ من اليوم لتفعيله.
                </p>
                <div class="form-floating">
                    <input type="number" class="form-control" id="renewDuration" name="offerDuration" min="1" placeholder="المدة" required>
                    <label for="renewDuration">المدة بالأيام</label>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-light w-100 w-sm-auto flex-sm-fill" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="at-btn-solid w-100 w-sm-auto flex-sm-fill">تفعيل الإعلان</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('jsfile')
<script>
    // فتح نافذة التجديد بمسار الإعلان المختار
    document.querySelectorAll('.renew-ad').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            document.getElementById('renewAdForm').action = '/update-ads/' + link.dataset.id;
            document.getElementById('renewAdName').textContent = link.dataset.name;
            document.getElementById('renewDuration').value = '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('renewAdModal')).show();
        });
    });

    // منع الإرسال المزدوج للتجديد
    document.getElementById('renewAdForm').addEventListener('submit', e => {
        e.target.querySelector('button[type="submit"]').disabled = true;
    });
</script>
@endsection
