@extends('Layouts.master')

@section('link')
    <style>
        /* ========== بطاقة الإدخال التلقائي الذكي ========== */
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

        /* مسح الصورة أثناء التحليل */
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

        /* ========== المقاسات ========== */
        .sizes-box {
            border-radius: 12px;
            border: 1px solid color-mix(in srgb, var(--default-color), transparent 80%);
            padding: 14px 18px;
            background-color: var(--surface-color);
        }

        .sizes-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            color: color-mix(in srgb, var(--default-color), transparent 40%);
            font-size: 14px;
        }

        .sizes-select-all {
            color: var(--accent-color);
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            user-select: none;
        }

        .sizes-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 22px;
            direction: ltr;
            justify-content: flex-end;
        }

        .custom-size-row {
            display: flex;
            gap: 8px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed color-mix(in srgb, var(--default-color), transparent 85%);
        }

        .custom-size-input {
            flex: 1;
            min-width: 0;
            height: 42px;
            padding: 0 14px;
            border-radius: 12px;
            border: 1px solid color-mix(in srgb, var(--default-color), transparent 80%);
            background-color: var(--surface-color);
            color: var(--default-color);
            transition: all 0.3s ease;
        }

        .custom-size-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent-color), transparent 85%);
            border-color: color-mix(in srgb, var(--accent-color), transparent 40%);
        }

        .custom-size-btn {
            height: 42px;
            padding: 0 16px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            white-space: nowrap;
            color: var(--contrast-color);
            background: linear-gradient(145deg, var(--accent-color), color-mix(in srgb, var(--accent-color), #1a4372 30%));
            transition: all 0.3s ease;
        }

        .custom-size-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px color-mix(in srgb, var(--accent-color), transparent 75%);
        }

        .size-remove {
            margin-left: 4px;
            color: #dc3545;
            cursor: pointer;
            font-size: 14px;
            vertical-align: middle;
        }

        .size-flash {
            animation: ai-flash 0.9s ease;
            border-radius: 6px;
        }

        /* ========== رسالة المنتج المكرر ========== */
        .dup-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            margin: 6px 0 12px;
            border-radius: 12px;
            text-align: right;
            direction: rtl;
            background: color-mix(in srgb, var(--accent-color), transparent 94%);
        }

        .dup-card img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 10px;
        }

        .dup-card .dup-name { font-weight: 700; font-size: 14px; }
        .dup-card .dup-price { font-size: 13px; color: var(--accent-color); }
        .dup-note { font-size: 14px; margin: 0; }

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
                <h3>{{ isset($editProduct) ? 'Update' : 'Add' }} <span class="orange-text">Product</span></h3>
            </div>
        </div>

        <div class="container my-5" style="direction: rtl; text-align: right">
            <div class="row justify-content-center">

                <div class="col-12 col-md-10 col-lg-8">

                    <div class="form-container">
                        <h3>{{ isset($editProduct) ? 'نموذج تعديل منتج' : 'نموذج إضافة منتج' }}</h3>

                        @if (isset($editProduct))
                            <div class="text-start">
                                <a href="{{ url()->previous() }}">
                                    <i class="bi bi-arrow-left fs-4 text-dark"></i>
                                </a>
                            </div>
                        @endif

                        <form id="dataForm"
                            action="{{ isset($editProduct) ? '/update-product/' . $editProduct->p_id : '/add-product' }}"
                            method="post" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="allow_duplicate" id="allowDuplicate" value="0">

                            {{-- الإدخال التلقائي الذكي --}}
                            <div class="ai-toggle-card mb-3" id="aiToggleCard">
                                <div class="ai-toggle-info">
                                    <span class="ai-icon"><i class="bi bi-stars"></i></span>
                                    <div>
                                        <div class="ai-toggle-title">الإدخال التلقائي الذكي</div>
                                        <div class="ai-status" id="aiStatus">
                                            يحلل أول صورة ويكتب الوصف ويحدد اللون تلقائياً
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input ai-switch" type="checkbox" role="switch"
                                        id="smartFill" aria-label="الإدخال التلقائي الذكي">
                                </div>
                            </div>

                            {{-- اسم المنتج --}}
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="productName" name="productName"
                                    placeholder="اسم المنتج" required maxlength="255"
                                    value="{{ old('productName', $editProduct->p_name ?? '') }}">
                                <label for="productName">اسم المنتج</label>
                                @error('productName')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- صور المنتج --}}
                            <div class="d-flex align-items-end gap-3">

                                <div class="form-floating mb-3 flex-grow-1">
                                    <input type="file" class="form-control" id="productImages" name="productImages[]"
                                        accept="image/jpeg,image/png" multiple
                                        onchange="onImagesChanged(event)" {{ isset($editProduct) ? '' : 'required' }}>
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

                                @isset($editProduct)
                                    <div class="image-preview mb-3" id="imagePreviewWrap">
                                        <img id="imagePreview"
                                            src="{{ asset('storage/uploads/products/' . $editProduct->p_image) }}"
                                            class="rounded" width="60" height="60" style="object-fit: cover;">
                                    </div>
                                @else
                                    <div class="image-preview mb-3" id="imagePreviewWrap">
                                        <img id="imagePreview" class="rounded" width="60" height="60"
                                            style="object-fit: cover; display:none;">
                                    </div>
                                @endisset

                            </div>

                            {{-- سعر المنتج: أرقام موجبة فقط --}}
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="productPrice" name="productPrice"
                                    placeholder="سعر المنتج" lang="en" inputmode="decimal"
                                    min="0.01" step="0.01" required
                                    onkeydown="return blockInvalidPriceKeys(event)"
                                    oninput="sanitizePrice(this)"
                                    onpaste="return blockInvalidPricePaste(event)"
                                    value="{{ old('productPrice', $editProduct->p_price ?? '') }}">
                                <label for="productPrice">سعر المنتج</label>
                                @error('productPrice')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- وصف المنتج (قابل للكتابة دائماً حتى أثناء التحليل) --}}
                            <div class="form-floating mb-3 ai-field" id="descWrap">
                                <textarea class="form-control" id="productDescription" name="productDescription"
                                    placeholder="وصف المنتج" style="height: 130px" required>{{ old('productDescription', $editProduct->p_description ?? '') }}</textarea>
                                <label for="productDescription">وصف المنتج</label>
                                <span class="ai-badge badge-analyzing"><i class="bi bi-stars"></i> جاري التحليل<span class="ai-dots"></span></span>
                                <span class="ai-badge badge-filled"><i class="bi bi-stars"></i> بالذكاء الاصطناعي</span>
                                @error('productDescription')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- اللون والمقاسات تظهر في الإضافة فقط --}}
                            @unless (isset($editProduct))
                                <div class="form-floating mb-3 ai-field" id="colorWrap">
                                    <input type="color" class="form-control" id="productColor" name="productColor"
                                        required value="{{ old('productColor', '#000000') }}">
                                    <label for="productColor">لون المنتج</label>
                                    <span class="ai-badge badge-analyzing"><i class="bi bi-stars"></i> تحديد اللون<span class="ai-dots"></span></span>
                                    <span class="ai-badge badge-filled"><i class="bi bi-stars"></i> بالذكاء الاصطناعي</span>
                                    @error('productColor')
                                        <div class="form-error">
                                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3 ai-field" id="colorNameWrap">
                                    <input type="text" class="form-control" id="colorName" name="colorName"
                                        placeholder="اسم اللون" list="colors-list" required maxlength="50"
                                        autocomplete="off" oninput="syncColorCode(this)"
                                        value="{{ old('colorName') }}">
                                    <label for="colorName">اسم اللون</label>
                                    <datalist id="colors-list">
                                        @foreach ($colors ?? [] as $colorName => $colorHex)
                                            <option value="{{ $colorName }}" data-hex="{{ $colorHex }}"></option>
                                        @endforeach
                                    </datalist>
                                    <span class="ai-badge badge-analyzing"><i class="bi bi-stars"></i> جاري التحليل<span class="ai-dots"></span></span>
                                    <span class="ai-badge badge-filled"><i class="bi bi-stars"></i> بالذكاء الاصطناعي</span>
                                    @error('colorName')
                                        <div class="form-error">
                                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- المقاسات: كل مقاس محدد يُحفظ كسجل مستقل --}}
                                <div class="mb-3">
                                    <div class="sizes-box">
                                        <div class="sizes-box-header">
                                            <span>مقاسات المنتج</span>
                                            <span class="sizes-select-all" id="sizesSelectAll" onclick="toggleAllSizes()">تحديد الكل</span>
                                        </div>
                                        <div class="sizes-grid" id="sizesGrid">
                                            @foreach ($sizes ?? [] as $size)
                                                <div class="checkbox-wrapper-46">
                                                    <input type="checkbox" class="inp-cbx size-checkbox" name="sizes[]"
                                                        id="size-{{ $size }}" value="{{ $size }}"
                                                        onchange="updateSizesState()"
                                                        {{ in_array($size, old('sizes', [])) ? 'checked' : '' }}>
                                                    <label for="size-{{ $size }}" class="cbx">
                                                        <span>
                                                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                            </svg>
                                                        </span>
                                                        <span>{{ $size }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- إدخال مقاس مخصص --}}
                                        <div class="custom-size-row">
                                            <input type="text" class="custom-size-input" id="customSizeInput"
                                                placeholder="مقاس آخر، مثال: 64" maxlength="10" autocomplete="off"
                                                onkeydown="if (event.key === 'Enter') { event.preventDefault(); addCustomSize(); }">
                                            <button type="button" class="custom-size-btn" onclick="addCustomSize()">
                                                <i class="bi bi-plus"></i> إضافة
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-error" id="sizesError" style="{{ $errors->has('sizes') || $errors->has('sizes.*') ? '' : 'display:none' }}">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span>{{ $errors->first('sizes') ?: ($errors->first('sizes.*') ?: 'الرجاء اختيار مقاس واحد على الأقل') }}</span>
                                    </div>
                                </div>
                            @endunless

                            {{-- فئة المنتج --}}
                            <div class="form-floating mb-3">
                                <select class="form-control" id="productSubcategory" name="productSubcategory" required>
                                    <option value="" disabled
                                        {{ !isset($editProduct) && !old('productSubcategory') ? 'selected' : '' }}>
                                        اختر الفئة...
                                    </option>

                                    @foreach ($subCategories as $subCategory)
                                        <option value="{{ $subCategory->subcat_id }}"
                                            {{ old('productSubcategory', $editProduct->subcat_id ?? '') == $subCategory->subcat_id ? 'selected' : '' }}>
                                            {{ $subCategory->subcat_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="productSubcategory">فئة المنتج</label>
                                @error('productSubcategory')
                                    <div class="form-error">
                                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-grid" style="direction: ltr">
                                <button type="button" class="btn-submit" id="saveBtn" onclick="submitProductForm()">
                                    @if (isset($editProduct))
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
        const PRODUCT_PAGE = {
            analyzeUrl: @json(url('/products/analyze-image')),
            checkNameUrl: @json(url('/products/check-name')),
            csrf: @json(csrf_token()),
            exceptId: @json($editProduct->p_id ?? null),
            storageKey: 'nice.smartFill'
        };

        // ===================== السعر: منع السالب =====================
        function blockInvalidPriceKeys(event) {
            return !['-', '+', 'e', 'E'].includes(event.key);
        }

        function blockInvalidPricePaste(event) {
            const text = (event.clipboardData || window.clipboardData).getData('text');
            return /^\d*\.?\d*$/.test(text.trim());
        }

        function sanitizePrice(input) {
            const value = parseFloat(input.value);
            if (!isNaN(value) && value < 0) {
                input.value = Math.abs(value);
            }
        }

        // ===================== اللون =====================
        function syncColorCode(input) {
            const option = Array.from(document.querySelectorAll('#colors-list option'))
                .find(opt => opt.value === input.value.trim());

            if (option && option.dataset.hex) {
                document.getElementById('productColor').value = option.dataset.hex.toLowerCase();
            }
        }

        // ===================== المقاسات =====================
        function sizeBoxes() {
            return Array.from(document.querySelectorAll('.size-checkbox'));
        }

        function updateSizesState() {
            const boxes = sizeBoxes();
            const checked = boxes.filter(b => b.checked).length;
            const selectAll = document.getElementById('sizesSelectAll');

            if (selectAll) {
                selectAll.textContent = checked === boxes.length ? 'إلغاء التحديد' : 'تحديد الكل';
            }
            if (checked > 0) {
                document.getElementById('sizesError').style.display = 'none';
            }
        }

        function toggleAllSizes() {
            const boxes = sizeBoxes();
            const allChecked = boxes.every(b => b.checked);
            boxes.forEach(b => b.checked = !allChecked);
            updateSizesState();
        }

        function validateSizes() {
            const boxes = sizeBoxes();
            if (boxes.length === 0) return true; // وضع التعديل

            if (!boxes.some(b => b.checked)) {
                const error = document.getElementById('sizesError');
                error.querySelector('span').textContent = 'الرجاء اختيار مقاس واحد على الأقل';
                error.style.display = 'flex';
                error.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
            return true;
        }

        // ===================== المقاس المخصص =====================
        // المقاسات المخصصة التي أُدخلت سابقاً (عند الرجوع بعد خطأ تحقق)
        const OLD_CUSTOM_SIZES = @json(array_values(array_diff(old('sizes', []), $sizes ?? [])));
        let customSizeCounter = 0;

        function normalizeSize(value) {
            // تحويل الأرقام العربية إلى إنجليزية
            return value.trim()
                .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                .replace(/\s+/g, ' ');
        }

        function showSizesError(message) {
            const error = document.getElementById('sizesError');
            error.querySelector('span').textContent = message;
            error.style.display = 'flex';
        }

        function flashSize(wrap) {
            wrap.classList.add('size-flash');
            setTimeout(() => wrap.classList.remove('size-flash'), 900);
        }

        function createSizeCheckbox(size, checked = true) {
            const id = 'size-custom-' + (++customSizeCounter);
            const wrapper = document.createElement('div');
            wrapper.className = 'checkbox-wrapper-46';
            wrapper.innerHTML = `
                <input type="checkbox" class="inp-cbx size-checkbox" name="sizes[]" id="${id}"
                    onchange="updateSizesState()" ${checked ? 'checked' : ''}>
                <label for="${id}" class="cbx">
                    <span>
                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                        </svg>
                    </span>
                    <span></span>
                </label>
                <i class="bi bi-x-circle size-remove" title="حذف المقاس"></i>`;

            // القيمة والنص بدون innerHTML لتجنب أي حقن
            wrapper.querySelector('input').value = size;
            wrapper.querySelector('label span:last-child').textContent = size;
            wrapper.querySelector('.size-remove').addEventListener('click', () => {
                wrapper.remove();
                updateSizesState();
            });

            document.getElementById('sizesGrid').appendChild(wrapper);
            return wrapper;
        }

        function addCustomSize() {
            const input = document.getElementById('customSizeInput');
            const size = normalizeSize(input.value);

            if (size === '') {
                input.focus();
                return;
            }

            if (!/^[\p{L}\p{N}\-\/ ]{1,10}$/u.test(size)) {
                showSizesError('المقاس يقبل حروفاً وأرقاماً فقط (حتى 10 خانات)');
                return;
            }

            // إذا كان المقاس موجوداً نحدده بدل تكراره
            const existing = sizeBoxes().find(b => b.value.toLowerCase() === size.toLowerCase());

            if (existing) {
                existing.checked = true;
                flashSize(existing.closest('.checkbox-wrapper-46'));
            } else {
                flashSize(createSizeCheckbox(size, true));
            }

            input.value = '';
            input.focus();
            updateSizesState();
        }

        // ===================== الإدخال التلقائي الذكي =====================
        const AI = {
            controller: null,
            analyzing: false,
            typingTimer: null,
            userEdited: { desc: false, colorName: false, color: false }
        };

        const el = (id) => document.getElementById(id);

        function setAiStatus(html, state = '') {
            const status = el('aiStatus');
            status.className = 'ai-status' + (state ? ' is-' + state : '');
            status.innerHTML = html;
        }

        function aiFieldWraps() {
            return ['descWrap', 'colorWrap', 'colorNameWrap'].map(el).filter(Boolean);
        }

        function setAnalyzingUI(on) {
            AI.analyzing = on;
            el('aiToggleCard').classList.toggle('is-busy', on);
            el('imagePreviewWrap').classList.toggle('is-scanning', on);
            aiFieldWraps().forEach(w => {
                w.classList.toggle('is-analyzing', on);
                if (on) w.classList.remove('is-filled');
            });
        }

        function markFilled(wrapId) {
            const wrap = el(wrapId);
            if (!wrap) return;
            wrap.classList.add('is-filled', 'ai-flash');
            setTimeout(() => wrap.classList.remove('ai-flash'), 900);
        }

        function stopTyping() {
            if (AI.typingTimer) {
                clearInterval(AI.typingTimer);
                AI.typingTimer = null;
            }
        }

        // كتابة الوصف بتأثير الآلة الكاتبة، ويتوقف فوراً إذا بدأ المستخدم الكتابة
        function typeDescription(text) {
            const textarea = el('productDescription');
            stopTyping();
            textarea.value = '';
            let i = 0;

            AI.typingTimer = setInterval(() => {
                textarea.value += text.slice(i, i + 3);
                i += 3;
                textarea.scrollTop = textarea.scrollHeight;

                if (i >= text.length) {
                    stopTyping();
                    textarea.value = text;
                }
            }, 18);
        }

        async function analyzeFirstImage() {
            const input = el('productImages');
            const file = input.files && input.files[0];

            if (!el('smartFill').checked || !file) return;

            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                setAiStatus('الصيغ المدعومة للتحليل: jpg و png', 'error');
                return;
            }

            // إلغاء أي تحليل سابق
            if (AI.controller) AI.controller.abort();
            AI.controller = new AbortController();

            stopTyping();
            setAnalyzingUI(true);
            setAiStatus('جاري تحليل العباية بالذكاء الاصطناعي<span class="ai-dots"></span>');

            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch(PRODUCT_PAGE.analyzeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': PRODUCT_PAGE.csrf,
                        'Accept': 'application/json'
                    },
                    body: formData,
                    signal: AI.controller.signal
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.success) {
                    const message = data.message
                        || (data.errors ? Object.values(data.errors)[0][0] : null)
                        || 'تعذر تحليل الصورة، يمكنك إدخال البيانات يدوياً';
                    throw new Error(message);
                }

                setAnalyzingUI(false);
                await applyAiResult(data);

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

        async function applyAiResult(data) {
            const desc = el('productDescription');
            const colorName = el('colorName');
            const color = el('productColor');

            // الحقول التي كتب فيها المستخدم بنفسه
            const conflicts = [];
            if (AI.userEdited.desc && desc.value.trim() !== '') conflicts.push('الوصف');
            if (colorName && data.color_name && AI.userEdited.colorName && colorName.value.trim() !== '') conflicts.push('اسم اللون');
            if (color && data.color_hex && AI.userEdited.color) conflicts.push('لون المنتج');

            let replaceUserInput = true;

            if (conflicts.length) {
                const result = await Swal.fire({
                    icon: 'question',
                    title: 'اكتمل التحليل',
                    text: 'لقد كتبت في (' + conflicts.join('، ') + ')، هل تريد استبدالها بنتيجة التحليل؟',
                    width: '350px',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، استبدل',
                    cancelButtonText: 'أبقِ ما كتبته',
                    confirmButtonColor: '#008870',
                    cancelButtonColor: '#dc3545',
                    customClass: { popup: 'compact-alert' }
                });
                replaceUserInput = result.isConfirmed;
            }

            // الوصف
            if (replaceUserInput || !AI.userEdited.desc || desc.value.trim() === '') {
                typeDescription(data.description);
                AI.userEdited.desc = false;
                markFilled('descWrap');
            }

            // اسم اللون
            if (colorName && data.color_name && (replaceUserInput || !AI.userEdited.colorName || colorName.value.trim() === '')) {
                colorName.value = data.color_name;
                AI.userEdited.colorName = false;
                markFilled('colorNameWrap');
            }

            // كود اللون في منتقي الألوان
            if (color && data.color_hex && (replaceUserInput || !AI.userEdited.color)) {
                color.value = data.color_hex.toLowerCase();
                AI.userEdited.color = false;
                markFilled('colorWrap');
            }

            setAiStatus('<i class="bi bi-check-circle"></i> تم ملء البيانات تلقائياً، يمكنك تعديلها', 'success');
        }

        function onImagesChanged(event) {
            previewImage(event);
            analyzeFirstImage();
        }

        function onSmartFillToggle() {
            const on = el('smartFill').checked;
            el('aiToggleCard').classList.toggle('is-on', on);

            try { localStorage.setItem(PRODUCT_PAGE.storageKey, on ? '1' : '0'); } catch (e) {}

            if (on) {
                const hasFile = el('productImages').files.length > 0;
                if (hasFile) {
                    analyzeFirstImage();
                } else {
                    setAiStatus('اختر صور المنتج وسيبدأ التحليل تلقائياً');
                }
            } else {
                if (AI.controller) AI.controller.abort();
                stopTyping();
                setAnalyzingUI(false);
                aiFieldWraps().forEach(w => w.classList.remove('is-filled'));
                setAiStatus('يحلل أول صورة ويكتب الوصف ويحدد اللون تلقائياً');
            }
        }

        // تتبع ما يكتبه المستخدم بنفسه
        function trackUserEdits() {
            const map = { productDescription: 'desc', colorName: 'colorName', productColor: 'color' };
            const wraps = { productDescription: 'descWrap', colorName: 'colorNameWrap', productColor: 'colorWrap' };

            Object.keys(map).forEach(id => {
                const field = el(id);
                if (!field) return;

                field.addEventListener('input', (e) => {
                    if (!e.isTrusted) return;
                    AI.userEdited[map[id]] = true;
                    el(wraps[id]).classList.remove('is-filled');
                    if (id === 'productDescription') stopTyping();
                });
            });

            // القيم الموجودة مسبقاً (مثل الرجوع بعد خطأ تحقق) تعتبر من المستخدم
            AI.userEdited.desc = el('productDescription').value.trim() !== '';
            AI.userEdited.colorName = !!(el('colorName') && el('colorName').value.trim() !== '');
        }

        // ===================== الإرسال =====================
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        async function confirmDuplicate(product) {
            const image = product.image
                ? `<img src="${escapeHtml(product.image)}" alt="">`
                : '';

            const result = await Swal.fire({
                icon: 'warning',
                title: 'المنتج موجود مسبقًا',
                html: `
                    <div class="dup-card">
                        ${image}
                        <div>
                            <div class="dup-name">${escapeHtml(product.name)}</div>
                            <div class="dup-price">${escapeHtml(String(product.price ?? ''))}</div>
                        </div>
                    </div>
                    <p class="dup-note">هل تريد المتابعة وإضافة منتج جديد بنفس الاسم؟</p>`,
                width: '350px',
                showCancelButton: true,
                confirmButtonText: 'نعم، أضف المنتج',
                cancelButtonText: 'تعديل الاسم',
                confirmButtonColor: '#008870',
                cancelButtonColor: '#dc3545',
                reverseButtons: true
            });

            if (!result.isConfirmed) {
                el('productName').focus();
                el('productName').select();
            }

            return result.isConfirmed;
        }

        async function submitProductForm() {
            const form = el('dataForm');

            if (!form.reportValidity() || !validateSizes()) {
                return;
            }

            // التحليل ما زال يعمل
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
            stopTyping();

            const allowDuplicate = el('allowDuplicate');

            if (allowDuplicate.value !== '1') {
                try {
                    const params = new URLSearchParams({ name: el('productName').value.trim() });
                    if (PRODUCT_PAGE.exceptId) {
                        params.append('except', PRODUCT_PAGE.exceptId);
                    }

                    const response = await fetch(`${PRODUCT_PAGE.checkNameUrl}?${params}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (response.ok) {
                        const data = await response.json();

                        if (data.exists) {
                            if (!(await confirmDuplicate(data.product))) return;
                            allowDuplicate.value = '1';
                        }
                    }
                } catch (e) {
                    // عند فشل الفحص يتولى السيرفر التحقق
                }
            }

            activateLoading('dataForm', 'saveBtn');
        }

        // ===================== التهيئة =====================
        document.addEventListener('DOMContentLoaded', () => {
            const smartFill = el('smartFill');

            try { smartFill.checked = localStorage.getItem(PRODUCT_PAGE.storageKey) === '1'; } catch (e) {}

            el('aiToggleCard').classList.toggle('is-on', smartFill.checked);
            smartFill.addEventListener('change', onSmartFillToggle);

            el('productName').addEventListener('input', () => el('allowDuplicate').value = '0');

            trackUserEdits();
            if (el('sizesGrid')) {
                OLD_CUSTOM_SIZES.forEach(size => createSizeCheckbox(size, true));
                updateSizesState();
            }
        });
    </script>
@endsection