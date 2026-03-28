@extends('Layouts.master')
@section('link')
    <link href="{{ asset('assets/css/order.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}?v={{ time() }}" rel="stylesheet">
@endsection

@section('content')
<div class="container my-4 px-2 px-md-2" dir="rtl" data-intro="تمكنك هذه الصفحه من استعلام الاعلانات وحاله الاعلان معا امكانيه حذف هذا الاعلان " data-step="1">
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
                        <th>متسلسل</th>
                        <th> الاعلان </th>
                        <th>الصوره </th>
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
                        <td class="fw-bold lh-1 fs-md-5">{{ $adss->AdsName }}</td>
                        <td class="card-text">
                                    <img src="{{ asset('storage/uploads/Advertisement/'.$adss->AdsImage) }}" class="rounded-circle" width="50"
                                            height="50" style="object-fit: cover;">
                        </td>
                        <td class="text-muted lh-1 fs-md-5">{{str_repeat('.', 5) . substr($adss->AdsLink, 0, 3)}}</td>

                        <!-- النشاط -->
                       <td >
                        <a href="/update-ads/{{ $adss->ads_id }}">
                        <div style="
                        margin-top: 5px;
                            width: 16px;
                            height: 16px;
                            border-radius: 50%;
                            background-color: {{ $adss->is_active ? '#008870' : '#ff0000' }};
                            display: inline-block;
                        " title="{{ $adss->is_active ? 'الاعلان نشط' : 'الاعلان غير نشط' }}"></div>
                        </a>
                    </td>   
                           
                            <td class="lh-1 fs-md-5">{{$adss->expires_at}}</td>
                            <td>
                                <a class="text-danger "
                                    href="#"
                                onclick="confirmDelete('/delete-ads/{{$adss->ads_id}}','حذف الاعلان','هل أنت متأكد من حذف هذا الاعلان')">
                                    
                                    <i class="bi bi-trash"></i>
                                </a>
                                
                            </td>  
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
