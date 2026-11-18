@extends('Layouts.master')
@section('link')
    <link href="{{ secure_asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ secure_asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
    <style>
        .renew-ad-box { border-radius: 16px; }
        .renew-ad-submit { background: var(--accent-color, #008870); color: #fff; }
        .renew-ad-submit:hover { background: #006f5b; color: #fff; }
    </style>
@endsection

@section('content')
<div class="container my-4 px-2 px-md-2" dir="rtl">
    <div class="container">
                <div class="controls-container">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">قائمة <span class="orange-text">الاعلانات</span></h3>
                </div>
            </div>
            <div class="search-input-container">
                <i class="bi bi-search search-icon"></i>
                   <input type="text" class="search-input" placeholder="Search" id="sectionSearch">
            </div>

            <a href="#" onclick="startTour()" class="btn-add-product shadow-sm" style="justify-content: center;" title="تحديث الصفحة">
                <i class="bi bi-arrow-clockwise"  ></i>

            </a>

            <i class="bi bi-question-circle text-turquoise fs-4" 
            onclick="startTour()" 
            style="cursor: pointer; margin-right: 10px;" 
            title="مساعدة - Help">
            </i>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>

                        <th>متسلسل</th >
                        <th> الاعلان </th>
                        <th>الصوره </th>
                        <th>النوع</th>
                        <th data-intro="رابط التوجيه: هنا تضع الرابط الذي تريد نقل العميل إليه؛ بمجرد أن يضغط العميل على صورة الإعلان في التطبيق، سيقوم النظام بفتحه فوراً ونقله إلى الصفحة المحددة (سواء كانت صفحة منتج معين، قسم خاص، أو عرض خارجي). تأكد من وضع الرابط كاملاً لضمان انتقال العميل بنجاح." data-step="1">الرابط </th>
                        <th data-intro="حالة الظهور والتحكم: يمكنك الضغط مباشرة على الدائرة لتفعيل الإعلان (🟢) أو إيقافه (🔴)؛ الأخضر يظهر الاعلان للعملاء  والأحمر يخفيه من الواجهة." data-step="2">النشاط </th>
                        <th data-intro="تاريخ الانتهاء: يوضح هذا التاريخ موعد توقف الإعلان تلقائياً عن الظهور، مما يساعدك على تنظيم حملاتك الإعلانية بدقة." data-step="3"> الانتهاء </th>
                        <th> العمليات  </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($ads as $adss)
                    <tr class="card-item">
                        <td class="fs-6 fs-md-5">{{ $loop->iteration }}</td>
                        <td class="fw-bold lh-1 fs-md-5 card-title">{{ $adss->AdsName }}</td>
                        <td class="card-title">
                            <img src="{{ asset('storage/uploads/Advertisement/'.$adss->AdsImage) }}" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                        </td>
                        <td >
                            @if($adss->type == 'image') <span class="badge bg-secondary">إعلان خاص</span>
                            @elseif($adss->type == 'general') <span class="badge bg-success">إعلان عام</span>
                            @elseif($adss->type == 'link') <span class="badge bg-warning">إعلان برابط</span>
                            @elseif($adss->product_id) <span class="badge bg-info">مرتبط بمنتج</span>
                            @else <span class="badge bg-dark">عادي</span>
                            @endif
                        </td>
                        <td class="text-muted lh-1 fs-md-5">
                            @if ($adss->type == 'link')
                                {{-- عرض بداية الرابط والباقي نقاط --}}
                                <span title="{{ $adss->AdsLink }}">
                                   {{ $adss->type == 'link' ? str_repeat('.', 5) . substr(preg_replace('#^(https?://)?(www\.)?#', '', $adss->AdsLink), 0, 5) : '-' }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @php $isExpired = $adss->expires_at && $adss->expires_at->lt(\Carbon\Carbon::today()); @endphp
                            {{-- الإعلان المنتهي يطلب مدة جديدة قبل التفعيل --}}
                            <a href="{{ !$adss->is_active && $isExpired ? '#' : '/update-ads/' . $adss->ads_id }}"
                               @if(!$adss->is_active && $isExpired)
                               class="renew-ad" data-id="{{ $adss->ads_id }}" data-name="{{ $adss->AdsName }}"
                               @endif>
                                <div style="
                                    margin-top: 5px;
                                    width: 16px;
                                    height: 16px;
                                    border-radius: 50%;
                                    background-color: {{ $adss->is_active ? '#008870' : '#ff0000' }};
                                    display: inline-block;
                                " title="{{ $adss->is_active ? 'الاعلان نشط' : ($isExpired ? 'الاعلان منتهي - اضغط لتجديد المدة' : 'الاعلان غير نشط') }}"></div>
                            </a>
                        </td>
                        <td class="lh-1 fs-md-5">
                            {{ is_object($adss->expires_at) ? $adss->expires_at->toDateString() : $adss->expires_at }}
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn text-dark p-0 border-0 shadow-none" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical fs-4"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow border-light">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-danger font-weight-bold text-end"
                                            href="#" onclick="confirmDelete('/delete-ads/{{$adss->ads_id}}','حذف الاعلان','هل أنت متأكد من حذف هذا الاعلان')">
                                            <span class="ms-2">حذف الاعلان</span>
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </li>

                                    @if($adss->type == 'general')
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 text-end"
                                                href="/ads/{{ $adss->ads_id }}/products" title="عرض المنتجات المرتبطة">
                                                <span class="ms-2">عرض المنتجات</span>
                                                <i class="bi bi-eye fs-5 text-turquoise"></i>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- نافذة تجديد مدة الإعلان المنتهي --}}
<div class="modal fade" id="renewAdModal" tabindex="-1" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content renew-ad-box" id="renewAdForm" method="post">
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
                <button type="submit" class="btn renew-ad-submit w-100 w-sm-auto flex-sm-fill">تفعيل الإعلان</button>
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
