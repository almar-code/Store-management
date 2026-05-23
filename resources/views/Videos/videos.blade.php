@extends('Layouts.master')

@section('link')
<style>
    /* تنسيقات المعاينة الدائرية */
    .video-preview-container:hover .bi-play-circle-fill {
        color: #008870 !important;
        transform: scale(1.1);
        transition: 0.3s ease;
    }

    .video-preview-container .bi-play-circle-fill {
        color: rgba(255, 255, 255, 0.9);
        text-shadow: 0px 4px 8px rgba(0,0,0,0.5);
        filter: drop-shadow(0 0 5px rgba(0,0,0,0.3));
    }

    /* تنسيق المودال الاحترافي */
    #videoPlayModal .modal-content {
        background-color: #000 !important; /* خلفية سوداء سينمائية */
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 50px rgba(0,0,0,0.8);
    }

    /* ضمان تناسق الفيديو مع طول الشاشة */
    #mainVideoPlayer {
        max-height: 85vh; /* يمنع الفيديو من تجاوز 85% من طول الشاشة */
        max-width: 100%;
        display: block;
        margin: 0 auto;
        outline: none;
    }

    /* زر الإغلاق العائم */
    .close-video-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 1060;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        padding: 8px;
        line-height: 0;
        transition: 0.3s;
    }

    .close-video-btn:hover {
        background-color: #008870;
    }

    /* تعديلات للجوال */
    @media (max-width: 576px) {
        .modal-fullscreen-sm-down .modal-content {
            border-radius: 0; /* ملء الشاشة تماماً على الجوال */
        }
        #mainVideoPlayer {
            max-height: 80vh;
        }
    }
</style>
@endsection

@section('content')
<div class="main-content-wrapper" style="min-height: 100vh; direction: rtl;">
    <div class="container">

        <div class="controls-container">
            <div class="section-ar">
                <div class="section-title" style="margin-bottom: 0px;">
                    <h3 style="font-size: 20px">قائمة <span class="orange-text">الفيديوهات</span></h3>
                </div>
            </div>
            <div class="search-input-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" placeholder="بحث عن فيديو..." id="sectionSearch">
            </div>

            <a href="/add_video" class="btn-add-product shadow-sm">
                إضافة فيديو 
                <i class="bi bi-plus-lg ms-2"></i>
            </a>
        </div>

        <div class="row g-4 px-2">
            @forelse($videos as $video)
                <div class="card card-item border-0 shadow-sm rounded-4 overflow-hidden position-relative bg-white mb-3">
                    <div class="card-body p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            
                            <!-- فيديو دائري -->
                            <div class="video-preview-container ms-3 position-relative" 
                                 onclick="openVideoModal('{{ asset('storage/uploads/videos/'.$video->video_path) }}')" 
                                 style="cursor: pointer;">
                                <div class="p-1 bg-white rounded-circle border shadow-sm overflow-hidden" style="width: 65px; height: 65px;">
                                    <video width="100%" height="100%" style="object-fit: cover;">
                                        <source src="{{ asset('storage/uploads/videos/'.$video->video_path) }}#t=0.5" type="video/mp4">
                                    </video>
                                    <div class="position-absolute top-50 start-50 translate-middle text-white">
                                        <i class="bi bi-play-circle-fill fs-4 shadow-sm"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end flex-grow-1 overflow-hidden">
                                <h6 class="card-title mb-1 fw-bold text-truncate" style="font-size:13px; color: #000;">
                                    رقم الفيديو: {{ $video->video_id }}
                                </h6>
                                <span class="card-text badge rounded-pill bg-light text-secondary border px-2 py-1 fw-normal" style="font-size:10px;">
                                    <i class="bi bi-box-seam me-1"></i>
                                    المنتج: {{ $video->product->p_name ?? 'فيديو عام' }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2 ms-lg-3">
                                <a href="#" onclick="confirmDelete('{{ route('video.delete', $video->video_id) }}','حذف الفيديو؟','هل أنت متأكد من حذف هذا الفيديو نهائياً؟')"
                                    class="btn btn-sm btn-light text-danger border-0 rounded-3 px-2 py-lg-2">
                                    <i class="bi bi-trash"></i>
                                    حذف
                                </a>
                            </div>

                        </div>
                    </div>
                    <div class="position-absolute start-0 top-0 h-100" style="width:4px; background:#008870;"></div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-camera-video text-muted fs-1"></i>
                    <p class="text-muted mt-2">لا توجد فيديوهات حالياً</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal النافذة المنبثقة -->
<div class="modal fade" id="videoPlayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down modal-lg">
        <div class="modal-content border-0">
            <!-- زر الإغلاق المخصص -->
            <button type="button" class="btn-close-white close-video-btn border-0 shadow-none" data-bs-dismiss="modal">
                <i class="bi bi-x-lg text-white"></i>
            </button>
            
            <div class="modal-body p-0 d-flex align-items-center justify-content-center bg-black" style="min-height: 300px;">
                <video id="mainVideoPlayer" controls playsinline>
                    <source src="" type="video/mp4">
                    متصفحك لا يدعم تشغيل الفيديو.
                </video>
            </div>
        </div>
    </div>
</div>

<script>
    function openVideoModal(videoSrc) {
        const modalElement = document.getElementById('videoPlayModal');
        const modal = new bootstrap.Modal(modalElement);
        const player = document.getElementById('mainVideoPlayer');
        
        // تعيين المصدر والتحميل
        player.src = videoSrc;
        player.load();
        
        modal.show();

        // التشغيل التلقائي فور فتح المودال
        modalElement.addEventListener('shown.bs.modal', function () {
            // كتم الصوت برمجياً لضمان التشغيل التلقائي في أغلب المتصفحات، ثم يمكن للمستخدم رفعه
            player.muted = true; 
            player.play().catch(error => console.log("Autoplay blocked: ", error));
        });

        // تنظيف عند الإغلاق لمنع استمرار الصوت في الخلفية
        modalElement.addEventListener('hidden.bs.modal', function () {
            player.pause();
            player.src = "";
        });
    }
</script>
@endsection