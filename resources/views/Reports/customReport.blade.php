@extends('Layouts.master')
@section('content')
    <section id="contact" class="contact section">
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>طلب <span class="orange-text">تقرير جديد</span></h3>
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    
                    <!-- حاوية الفورم -->
                    <div id="formSection" class="form-container">
                        <h3>نموذج طلب تقرير</h3>
                        <form id="reportForm" action="{{ route('report.generate') }}" method="post">
                            @csrf
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="report_query" name="report_query" 
                                    style="height: 150px" placeholder="مثلاً: اريد تقرير بالطلبات لهذا الشهر" 
                                    required>{{ old('report_query') }}</textarea>
                                <label for="report_query">اكتب تفاصيل التقرير المطلوب</label>
                            </div>

                            <div class="d-grid" style="direction: ltr">
                                <button type="submit" class="btn-submit" id="reportBtn">
                                    إنشاء التقرير <i class="bi bi-file-earmark-pdf ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- حاوية الـ PDF (مخفية افتراضياً) -->
                    

                </div>
            </div>
        </div>
    </section>
    <div id="pdfContainer" class="ratio ratio-16x9" style="display: none; text-align: center; background: #fff; padding-left: 50px; padding-right: 50px; padding-bottom: 20px; border-radius: 10px;">
                        <h3 class="mb-3">تم تجهيز التقرير بنجاح!</h3>
                        <iframe id="pdfFrame" src="" width="100%"  style="border: 1px solid #ccc;"></iframe>
                        <div class="mt-4">
                            <a href="#" id="downloadBtn" class="btn btn-success px-4" target="_blank">تحميل الملف PDF</a>
                            <button onclick="location.reload();" class="btn btn-secondary px-4">طلب تقرير آخر</button>
                        </div>
                    </div>

    <script>
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            let btn = document.getElementById('reportBtn');
            btn.disabled = true;
            btn.innerHTML = 'جاري المعالجة...';

            fetch("{{ route('report.generate') }}", {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(data => {
                if(data.pdf_url) {
                    // إخفاء الفورم
                    document.getElementById('formSection').style.display = 'none';
                    
                    // إظهار الـ PDF
                    document.getElementById('pdfFrame').src = data.pdf_url;
                    document.getElementById('downloadBtn').href = data.pdf_url;
                    document.getElementById('pdfContainer').style.display = 'block';
                } else {
                    alert('خطأ في توليد التقرير، يرجى المحاولة لاحقاً');
                    btn.disabled = false;
                    btn.innerHTML = 'إنشاء التقرير <i class="bi bi-file-earmark-pdf ms-2"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ في الاتصال بالسيرفر');
                btn.disabled = false;
            });
        });
    </script>
@endsection