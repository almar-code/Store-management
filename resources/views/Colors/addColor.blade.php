@extends('Layouts.master')

@section('link')
    <style>
        /* ========== بطاقة الإدخال التلقائي الذكي (نفس تصميم إضافة منتج) ========== */
        .ai-toggle-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            border: 1px solid color-mix(in srgb, var(--accent-color), transparent 75%);
            background: color-mix(in srgb, var(--accent-color), transparent 94%);
            transition: all 0.3s ease;
        }

        .ai-toggle-card.is-on {
            border-color: color-mix(in srgb, var(--accent-color), transparent 40%);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent-color), transparent 88%);
        }

        .ai-toggle-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ai-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            background: linear-gradient(145deg, var(--accent-color), color-mix(in srgb, var(--accent-color), #1a4372 30%));
        }

        .ai-toggle-card.is-busy .ai-icon i {
            animation: ai-pulse 1s ease-in-out infinite;
        }

        .ai-toggle-title {
            font-weight: 700;
            font-size: 15px;
            color: color-mix(in srgb, var(--default-color), transparent 10%);
        }

        .ai-status {
            font-size: 13px;
            color: color-mix(in srgb, var(--default-color), transparent 40%);
        }

        .ai-status.is-success { color: var(--accent-color); }
        .ai-status.is-error { color: #b42318; }

        .ai-status .ai-retry {
            color: var(--accent-color);
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            margin-right: 4px;
        }

        .ai-dots::after {
            content: '';
            animation: ai-dots 1.2s steps(4, end) infinite;
        }

        .ai-switch.form-check-input {
            width: 3em;
            height: 1.5em;
            cursor: pointer;
            float: none;
            margin: 0;
        }

        .ai-switch.form-check-input:checked {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .ai-switch.form-check-input:focus {
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent-color), transparent 85%);
            border-color: color-mix(in srgb, var(--accent-color), transparent 40%);
        }

        /* ========== تأثيرات الحقول أثناء التحليل ========== */
        .ai-field {
            position: relative;
        }

        .ai-field.is-analyzing .form-control {
            border-color: transparent !important;
            background:
                linear-gradient(var(--surface-color), var(--surface-color)) padding-box,
                linear-gradient(90deg, #008870, #3fd1b5, #7b61ff, #3fd1b5, #008870) border-box !important;
            background-size: 100% 100%, 300% 100% !important;
            border: 2px solid transparent !important;
            animation: ai-border 2s linear infinite;
        }

        .ai-field.is-analyzing::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 12px;
            pointer-events: none;
            background: linear-gradient(100deg, transparent 20%, color-mix(in srgb, var(--accent-color), transparent 88%) 50%, transparent 80%);
            background-size: 200% 100%;
            animation: ai-shimmer 1.4s linear infinite;
        }

        .ai-badge {
            position: absolute;
            left: 12px;
            bottom: 10px;
            z-index: 5;
            display: none;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            pointer-events: none;
            color: var(--accent-color);
            background: color-mix(in srgb, var(--accent-color), transparent 90%);
        }

        .ai-field.is-analyzing .ai-badge.badge-analyzing,
        .ai-field.is-filled .ai-badge.badge-filled {
            display: inline-flex;
        }

        .ai-field.is-analyzing .ai-badge.badge-analyzing i {
            animation: ai-spin 1.2s linear infinite;
        }

        .ai-field.ai-flash .form-control {
            animation: ai-flash 0.9s ease;
        }

        .image-preview {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
        }

        .image-preview.is-scanning::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 3px;
            top: 0;
            background: linear-gradient(90deg, transparent, #3fd1b5, transparent);
            box-shadow: 0 0 10px #3fd1b5;
            animation: ai-scan 1.2s ease-in-out infinite alternate;
        }

        @keyframes ai-border { to { background-position: 0 0, 300% 0; } }
        @keyframes ai-shimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }
        @keyframes ai-spin { to { transform: rotate(360deg); } }
        @keyframes ai-pulse { 50% { transform: scale(1.2); opacity: .7; } }
        @keyframes ai-scan { from { top: 0; } to { top: calc(100% - 3px); } }
        @keyframes ai-dots { 0% { content: ''; } 25% { content: '.'; } 50% { content: '..'; } 75% { content: '...'; } }
        @keyframes ai-flash {
            0% { box-shadow: 0 0 0 0 color-mix(in srgb, var(--accent-color), transparent 40%); }
            100% { box-shadow: 0 0 0 10px transparent; }
        }
    </style>
@endsection

@section('content')
    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="col-lg-8 offset-lg-2 text-center">
            <div class="section-title">
                <h3>{{ isset($editColor) ? 'Update' : 'Add' }} <span class="orange-text">Color</span></h3>
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-12 col-md-10 col-lg-8">

                    <div class="form-container">
                        <h3>{{ isset($editColor) ? 'نموذج تعديل لون المنتج' : 'نموذج إضافة لون للمنتج' }}</h3>
                        <form id="dataForm"
                            action="{{ isset($editColor) ? '/update-color/' . $editColor->color_id : '/add-color/' . $productID }}"
                            method="post" enctype="multipart/form-data">
                            @csrf

                            {{-- الإدخال التلقائي الذكي: يحدد اللون واسمه فقط --}}
                            <div class="ai-toggle-card mb-3" id="aiToggleCard">
                                <div class="ai-toggle-info">
                                    <span class="ai-icon"><i class="bi bi-stars"></i></span>
                                    <div>
                                        <div class="ai-toggle-title">الإدخال التلقائي الذكي</div>
                                        <div class="ai-status" id="aiStatus">
                                            يحلل أول صورة ويحدد لون العباية واسمه تلقائياً
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input ai-switch" type="checkbox" role="switch"
                                        id="smartFill" aria-label="الإدخال التلقائي الذكي">
                                </div>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" readonly required=""
                                    value="{{ $productName ?? '' }}">
                                <label for="productName">اسم المنتج</label>
                            </div>

                            {{-- صور اللون --}}
                            <div class="d-flex align-items-end gap-3">
                                <div class="form-floating mb-3 flex-grow-1">
                                    <input type="file" class="form-control" id="productImages" name="productImages[]"
                                        accept="image/jpeg,image/png" multiple
                                        onchange="onImagesChanged(event)" {{ isset($editColor) ? '' : 'required' }}>
                                    <label for="productImages">صور المنتج</label>
                                    @error('productImages')
                                        <div class="form-error">
                                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                    @error('productImages.*')
                                        <div class="form-error">
                                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="image-preview mb-3" id="imagePreviewWrap">
                                    <img id="imagePreview" class="rounded" width="60" height="60"
                                        style="object-fit: cover; display:none;">
                                </div>
                            </div>

                            <div class="form-floating mb-3 ai-field" id="colorWrap">
                                <input type="color" class="form-control" id="productColor" name="productColor"
                                    required value="{{ old('productColor', $editColor->color_code ?? '#000000') }}">
                                <label for="productColor">لون المنتج</label>
                                <span class="ai-badge badge-analyzing"><i class="bi bi-stars"></i> تحديد اللون<span class="ai-dots"></span></span>
                                <span class="ai-badge badge-filled"><i class="bi bi-stars"></i> بالذكاء الاصطناعي</span>
                            </div>

                            <div class="form-floating mb-3 ai-field" id="colorNameWrap">
                                <input type="text" class="form-control" id="colorName" name="colorName"
                                    placeholder="اسم اللون" list="colors-list" maxlength="50" autocomplete="off"
                                    oninput="syncColorCode(this)"
                                    value="{{ old('colorName', $editColor->color_name ?? '') }}">
                                <label for="colorName">اسم اللون</label>
                                <datalist id="colors-list">
                                    @foreach ($colors ?? [] as $colorName => $colorHex)
                                        <option value="{{ $colorName }}" data-hex="{{ $colorHex }}"></option>
                                    @endforeach
                                </datalist>
                                <span class="ai-badge badge-analyzing"><i class="bi bi-stars"></i> جاري التحليل<span class="ai-dots"></span></span>
                                <span class="ai-badge badge-filled"><i class="bi bi-stars"></i> بالذكاء الاصطناعي</span>
                            </div>

                            @isset($editColor)
                                <!-- From Uiverse.io by vishnupprajapat -->
                                <div class="checkbox-wrapper-46 mb-3">
                                    <input type="checkbox" name="keep_old_images" id="cbx-46" class="inp-cbx" />
                                    <label for="cbx-46" class="cbx"><span>
                                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </svg></span> <span>الاحتفاظ بالصور القديمة</span>
                                    </label>
                                </div>
                            @endisset

                            <div class="d-grid" style="direction: ltr">
                                <button type="button" class="btn-submit" id="saveBtn" onclick="submitColorForm()">
                                    @if (isset($editColor))
                                        تعديل <i class="bi bi-pencil-square" style="font-size:13px; margin: 3px"></i>
                                    @else
                                        إضافة <i class="bi bi-plus ms-2"></i>
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- /Contact Section -->
@endsection

@section('jsfile')
    <script>
        const COLOR_PAGE = {
            analyzeUrl: @json(url('/products/analyze-image')),
            csrf: @json(csrf_token()),
            storageKey: 'nice.smartFill'
        };

        const AI = {
            controller: null,
            analyzing: false,
            userEdited: { colorName: false, color: false }
        };

        const el = (id) => document.getElementById(id);

        // ===================== اللون =====================
        function syncColorCode(input) {
            const option = Array.from(document.querySelectorAll('#colors-list option'))
                .find(opt => opt.value === input.value.trim());

            if (option && option.dataset.hex) {
                el('productColor').value = option.dataset.hex.toLowerCase();
            }
        }

        // ===================== الإدخال التلقائي الذكي =====================
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        function setAiStatus(html, state = '') {
            const status = el('aiStatus');
            status.className = 'ai-status' + (state ? ' is-' + state : '');
            status.innerHTML = html;
        }

        function setAnalyzingUI(on) {
            AI.analyzing = on;
            el('aiToggleCard').classList.toggle('is-busy', on);
            el('imagePreviewWrap').classList.toggle('is-scanning', on);
            ['colorWrap', 'colorNameWrap'].forEach(id => {
                el(id).classList.toggle('is-analyzing', on);
                if (on) el(id).classList.remove('is-filled');
            });
        }

        function markFilled(wrapId) {
            const wrap = el(wrapId);
            wrap.classList.add('is-filled', 'ai-flash');
            setTimeout(() => wrap.classList.remove('ai-flash'), 900);
        }

        async function analyzeFirstImage() {
            const file = el('productImages').files[0];

            if (!el('smartFill').checked || !file) return;

            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                setAiStatus('الصيغ المدعومة للتحليل: jpg و png', 'error');
                return;
            }

            if (AI.controller) AI.controller.abort();
            AI.controller = new AbortController();

            setAnalyzingUI(true);
            setAiStatus('جاري تحديد لون العباية بالذكاء الاصطناعي<span class="ai-dots"></span>');

            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch(COLOR_PAGE.analyzeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': COLOR_PAGE.csrf,
                        'Accept': 'application/json'
                    },
                    body: formData,
                    signal: AI.controller.signal
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.success) {
                    const message = data.message
                        || (data.errors ? Object.values(data.errors)[0][0] : null)
                        || 'تعذر تحليل الصورة، يمكنك إدخال اللون يدوياً';
                    throw new Error(message);
                }

                setAnalyzingUI(false);

                if (!data.color_hex && !data.color_name) {
                    setAiStatus('لم يتمكن الذكاء الاصطناعي من تحديد اللون بثقة، أدخله يدوياً', 'error');
                    return;
                }

                await applyColorResult(data);

            } catch (error) {
                if (error.name === 'AbortError') return;

                setAnalyzingUI(false);
                setAiStatus(
                    escapeHtml(error.message) +
                    ' <span class="ai-retry" onclick="analyzeFirstImage()">إعادة المحاولة</span>',
                    'error'
                );
            }
        }

        async function applyColorResult(data) {
            const colorName = el('colorName');
            const color = el('productColor');

            const conflicts = [];
            if (data.color_name && AI.userEdited.colorName && colorName.value.trim() !== '') conflicts.push('اسم اللون');
            if (data.color_hex && AI.userEdited.color) conflicts.push('لون المنتج');

            let replace = true;

            if (conflicts.length) {
                const result = await Swal.fire({
                    icon: 'question',
                    title: 'اكتمل التحليل',
                    text: 'لقد أدخلت (' + conflicts.join('، ') + ')، هل تريد استبدالها بنتيجة التحليل؟',
                    width: '350px',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، استبدل',
                    cancelButtonText: 'أبقِ ما أدخلته',
                    confirmButtonColor: '#008870',
                    cancelButtonColor: '#dc3545',
                    customClass: { popup: 'compact-alert' }
                });
                replace = result.isConfirmed;
            }

            if (data.color_name && (replace || !AI.userEdited.colorName || colorName.value.trim() === '')) {
                colorName.value = data.color_name;
                AI.userEdited.colorName = false;
                markFilled('colorNameWrap');
            }

            if (data.color_hex && (replace || !AI.userEdited.color)) {
                color.value = data.color_hex.toLowerCase();
                AI.userEdited.color = false;
                markFilled('colorWrap');
            }

            setAiStatus('<i class="bi bi-check-circle"></i> تم تحديد اللون تلقائياً، يمكنك تعديله', 'success');
        }

        function onImagesChanged(event) {
            previewImage(event);
            analyzeFirstImage();
        }

        function onSmartFillToggle() {
            const on = el('smartFill').checked;
            el('aiToggleCard').classList.toggle('is-on', on);

            try { localStorage.setItem(COLOR_PAGE.storageKey, on ? '1' : '0'); } catch (e) {}

            if (on) {
                if (el('productImages').files.length > 0) {
                    analyzeFirstImage();
                } else {
                    setAiStatus('اختر صور اللون وسيبدأ التحليل تلقائياً');
                }
            } else {
                if (AI.controller) AI.controller.abort();
                setAnalyzingUI(false);
                ['colorWrap', 'colorNameWrap'].forEach(id => el(id).classList.remove('is-filled'));
                setAiStatus('يحلل أول صورة ويحدد لون العباية واسمه تلقائياً');
            }
        }

        // ===================== الإرسال =====================
        async function submitColorForm() {
            if (AI.analyzing) {
                const wait = await Swal.fire({
                    icon: 'info',
                    title: 'التحليل لم ينتهِ بعد',
                    text: 'هل تريد الحفظ الآن بدون انتظار نتيجة الذكاء الاصطناعي؟',
                    width: '350px',
                    showCancelButton: true,
                    confirmButtonText: 'احفظ الآن',
                    cancelButtonText: 'انتظر',
                    confirmButtonColor: '#008870',
                    cancelButtonColor: '#dc3545',
                    customClass: { popup: 'compact-alert' }
                });
                if (!wait.isConfirmed) return;

                if (AI.controller) AI.controller.abort();
                setAnalyzingUI(false);
            }

            activateLoading('dataForm', 'saveBtn');
        }

        // ===================== التهيئة =====================
        document.addEventListener('DOMContentLoaded', () => {
            const smartFill = el('smartFill');

            try { smartFill.checked = localStorage.getItem(COLOR_PAGE.storageKey) === '1'; } catch (e) {}

            el('aiToggleCard').classList.toggle('is-on', smartFill.checked);
            smartFill.addEventListener('change', onSmartFillToggle);

            // تتبع ما يدخله المستخدم بنفسه
            el('colorName').addEventListener('input', (e) => {
                if (!e.isTrusted) return;
                AI.userEdited.colorName = true;
                el('colorNameWrap').classList.remove('is-filled');
            });
            el('productColor').addEventListener('input', (e) => {
                if (!e.isTrusted) return;
                AI.userEdited.color = true;
                el('colorWrap').classList.remove('is-filled');
            });

            // في التعديل: القيم الحالية تعتبر من المستخدم
            AI.userEdited.colorName = el('colorName').value.trim() !== '';
            AI.userEdited.color = @json(isset($editColor));
        });
    </script>
@endsection