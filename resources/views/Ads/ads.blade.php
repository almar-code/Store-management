@extends('Layouts.master')
@section('link')
<link href="assets/css/order.css" rel="stylesheet">
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
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>متسلسل</th>
                        <th> الاعلان </th>
                        <th>الصوره </th>
                        <th>الرابط </th>
                        <th>النشاط </th>
                        <th> الانتهاء </th>
                        <th> العمليات  </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($ads as $adss)
                    <tr class="card-item">
                        <td style="font-size: 10px">{{ $loop->iteration }}</td>
                        <td class="card-title" style="font-size: 10px">{{ $adss->AdsName }}</td>
                        <td class="card-text">
                                    <img src="{{ asset('storage/uploads/Advertisement/'.$adss->AdsImage) }}" class="rounded-circle" width="50"
                                            height="50" style="object-fit: cover;">
                        </td>
                        <td style="font-size: 10px">{{str_repeat('.', 5) . substr($adss->AdsLink, 0, 3)}}</td>

                        <!-- النشاط -->
                       <td data-intro="من هنا يمكنك التحكم في حاله الاعلان مفعل او غير مفعل من خلال الضغط على الدائر  " data-step="2">
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
                           
                            <td style="font-size: 10px">{{$adss->expires_at}}</td>
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
