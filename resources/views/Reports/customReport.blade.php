@extends('Layouts.master')
@section('content')
    <style>
        .rpt-page { direction: rtl; text-align: right; }
        .rpt-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0, 0, 0, .06); padding: 28px; }
        .rpt-label { display: block; font-weight: 600; margin-bottom: 10px; color: #333; }
        .rpt-textarea { min-height: 150px; resize: vertical; border-radius: 12px; padding: 14px; }
        .rpt-textarea:focus { border-color: #00695c; box-shadow: 0 0 0 .2rem rgba(0, 105, 92, .15); }
        .rpt-hint { font-size: .85rem; color: #888; margin: 18px 0 8px; }

        /* الاقتراحات */
        .rpt-chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .rpt-chip { border: 1px solid #cfe3e0; background: #f3f8f7; color: #00695c; border-radius: 6px; padding: 7px 14px; font-size: .85rem; cursor: pointer; transition: .2s; }
        .rpt-chip:hover { background: #00695c; color: #fff; }

        /* الأزرار */
        .rpt-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 10px; padding: 10px 18px; font-weight: 600; font-size: .95rem; border: 1px solid transparent; transition: .2s; text-decoration: none; cursor: pointer; }
        .rpt-btn-primary { background: #00695c; color: #fff; }
        .rpt-btn-primary:hover { background: #004d40; color: #fff; }
        .rpt-btn-outline { background: #fff; color: #00695c; border-color: #00695c; }
        .rpt-btn-outline:hover { background: #f3f8f7; color: #00695c; }
        .rpt-btn-light { background: #f1f3f5; color: #333; }
        .rpt-btn-light:hover { background: #e2e6ea; color: #333; }
        .rpt-btn-saved, .rpt-btn-saved:disabled { background: #2e7d32; color: #fff; opacity: 1; }
        .rpt-btn:disabled { opacity: .7; cursor: not-allowed; }

        /* الطلبات السابقة */
        .rpt-history { margin-top: 24px; border-top: 1px solid #eef1f1; padding-top: 18px; }
        .rpt-history-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .rpt-history-title { font-weight: 600; color: #333; font-size: .95rem; }
        .rpt-clear-all { background: none; border: none; color: #c62828; font-size: .85rem; cursor: pointer; padding: 4px 6px; border-radius: 6px; }
        .rpt-clear-all:hover { background: #fdecea; }
        .rpt-history-list { display: flex; flex-direction: column; gap: 6px; max-height: 260px; overflow-y: auto; }
        .rpt-history-item { display: flex; align-items: center; gap: 6px; border: 1px solid #eef1f1; border-radius: 8px; background: #fafbfb; transition: opacity .25s, transform .25s; }
        .rpt-history-item.removing { opacity: 0; transform: translateX(-12px); }
        .rpt-history-text { flex: 1; min-width: 0; display: flex; align-items: center; gap: 8px; background: none; border: none; text-align: right; padding: 9px 12px; color: #444; font-size: .88rem; cursor: pointer; }
        .rpt-history-text span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .rpt-history-text i { color: #00695c; }
        .rpt-history-text:hover { color: #00695c; }
        .rpt-history-del { background: none; border: none; color: #999; padding: 8px 12px; cursor: pointer; border-radius: 8px; }
        .rpt-history-del:hover { color: #c62828; background: #fdecea; }

        /* أثناء الإنشاء */
        .rpt-busy .rpt-chip,
        .rpt-busy .rpt-history-item,
        .rpt-busy .rpt-clear-all { pointer-events: none; opacity: .55; }

        /* عرض التقرير */
        .rpt-toolbar { display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .rpt-toolbar-group { display: flex; flex-wrap: wrap; gap: 8px; }
        .rpt-query { background: #f8f9fa; border-right: 4px solid #00695c; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; color: #555; font-size: .9rem; white-space: pre-wrap; }
        .rpt-viewer { display: block; width: 100%; height: 80vh; min-height: 560px; border: 1px solid #e0e0e0; border-radius: 12px; background: #525659; }

        .rpt-hidden { display: none !important; }

        @media (max-width: 576px) {
            .rpt-card { padding: 18px; }
            .rpt-viewer { height: 70vh; min-height: 420px; }
            .rpt-toolbar-group { width: 100%; }
            .rpt-toolbar-group .rpt-btn { flex: 1 1 auto; }
        }
    </style>

    <section class="section rpt-page">
        <div class="container">

            <div class="section-title text-center">
                <h3 id="pageTitle">طلب <span class="orange-text">تقرير جديد</span></h3>
                <p id="pageSubtitle" class="text-muted mb-0">اكتب ما تريد معرفته وسيقوم النظام بتحليل البيانات وتجهيز التقرير</p>
            </div>

            {{-- نموذج الطلب --}}
            <div id="formSection" class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="rpt-card" id="formCard">
                        <form id="reportForm">
                            <label for="report_query" class="rpt-label">ما التقرير الذي تحتاجه؟</label>
                            <textarea class="form-control rpt-textarea" id="report_query" name="report_query"
                                maxlength="2000" required
                                placeholder="مثلاً: أريد تقريراً بالطلبات المكتملة لهذا الشهر مع إجمالي المبيعات"></textarea>

                            <div class="rpt-hint">أو اختر أحد الاقتراحات لإنشائه مباشرة:</div>
                            <div class="rpt-chips">
                                <button type="button" class="rpt-chip">تقرير بالطلبات لهذا الشهر</button>
                                <button type="button" class="rpt-chip">أكثر 10 منتجات مبيعاً</button>
                                <button type="button" class="rpt-chip">المنتجات التي عليها خصم</button>
                                <button type="button" class="rpt-chip">العملاء الجدد هذا الشهر</button>
                                <button type="button" class="rpt-chip">الفئات وعدد المنتجات في كل فئة</button>
                            </div>

                            <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                                <button type="button" id="cancelEditBtn" class="rpt-btn rpt-btn-light rpt-hidden">
                                    <i class="bi bi-arrow-right"></i> العودة للتقرير
                                </button>
                                <button type="submit" id="reportBtn" class="rpt-btn rpt-btn-primary">
                                    <i class="bi bi-file-earmark-pdf"></i> إنشاء التقرير
                                </button>
                            </div>
                        </form>

                        {{-- الطلبات السابقة --}}
                        <div id="historySection" class="rpt-history {{ $recentRequests->isEmpty() ? 'rpt-hidden' : '' }}">
                            <div class="rpt-history-head">
                                <span class="rpt-history-title"><i class="bi bi-clock-history"></i> طلباتك السابقة</span>
                                <button type="button" id="clearAllBtn" class="rpt-clear-all">
                                    <i class="bi bi-trash"></i> حذف الكل
                                </button>
                            </div>
                            <div id="historyList" class="rpt-history-list">
                                @foreach ($recentRequests as $item)
                                    <div class="rpt-history-item" data-id="{{ $item->request_id }}">
                                        <button type="button" class="rpt-history-text" title="{{ $item->report_query }}">
                                            <i class="bi bi-arrow-repeat"></i>
                                            <span>{{ $item->report_query }}</span>
                                        </button>
                                        <button type="button" class="rpt-history-del" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- عرض التقرير --}}
            <div id="resultSection" class="rpt-hidden">
                <div class="rpt-card">
                    <div class="rpt-toolbar">
                        <div class="rpt-toolbar-group">
                            <button type="button" id="editBtn" class="rpt-btn rpt-btn-outline">
                                <i class="bi bi-pencil-square"></i> تعديل الطلب
                            </button>
                            <button type="button" id="newBtn" class="rpt-btn rpt-btn-light">
                                <i class="bi bi-plus-lg"></i> تقرير جديد
                            </button>
                        </div>
                        <div class="rpt-toolbar-group">
                            <a id="openBtn" href="#" target="_blank" class="rpt-btn rpt-btn-light">
                                <i class="bi bi-box-arrow-up-left"></i> فتح في نافذة
                            </a>
                            <a id="downloadBtn" href="#" download class="rpt-btn rpt-btn-outline">
                                <i class="bi bi-download"></i> تحميل
                            </a>
                            <button type="button" id="saveBtn" class="rpt-btn rpt-btn-primary">
                                <i class="bi bi-bookmark-plus"></i> حفظ التقرير
                            </button>
                            <a id="savedLink" href="{{ route('reports.index') }}" class="rpt-btn rpt-btn-light rpt-hidden">
                                <i class="bi bi-folder2-open"></i> التقارير المحفوظة
                            </a>
                        </div>
                    </div>

                    <div class="rpt-query"><strong>الطلب:</strong> <span id="queryText"></span></div>

                    <iframe id="pdfFrame" class="rpt-viewer" title="معاينة التقرير"></iframe>
                </div>
            </div>

        </div>
    </section>

    <script>
        (function () {
            const routes = {
                generate: "{{ route('report.generate') }}",
                save: "{{ route('reports.save') }}",
                requests: "{{ url('reports/requests') }}"
            };
            const csrf = "{{ csrf_token() }}";
            const el = id => document.getElementById(id);

            const sections = {
                form: el('formSection'),
                result: el('resultSection')
            };

            const defaultTitle = el('pageTitle').innerHTML;
            const defaultSubtitle = el('pageSubtitle').textContent;
            const saveBtnHtml = el('saveBtn').innerHTML;
            const reportBtnHtml = el('reportBtn').innerHTML;
            const MAX_HISTORY = 15;

            // التقرير الحالي: { title, file, url, query, saved }
            let current = null;
            let busy = false;

            /* ---------- أدوات عامة ---------- */

            function show(name) {
                Object.entries(sections).forEach(([key, section]) => {
                    section.classList.toggle('rpt-hidden', key !== name);
                });
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            // نفس رسائل النظام (SweetAlert) المستخدمة في باقي الصفحات
            function showMessage(type, text) {
                Swal.fire({
                    icon: type,
                    title: type === 'success' ? 'نجاح' : 'خطأ',
                    timer: 3000,
                    width: '250px',
                    timerProgressBar: true,
                    showConfirmButton: false,
                    showCloseButton: true,
                    text: text,
                    customClass: {
                        popup: 'compact-alert'
                    }
                });
            }

            function confirmAction(title, text, confirmText) {
                return Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: text,
                    width: '350px',
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#008870',
                    cancelButtonColor: '#dc3545'
                }).then(result => result.isConfirmed);
            }

            async function request(method, url, payload) {
                const options = {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    }
                };
                if (payload) {
                    options.headers['Content-Type'] = 'application/json';
                    options.body = JSON.stringify(payload);
                }

                const res = await fetch(url, options);

                let data = {};
                try { data = await res.json(); } catch (e) {}

                if (!res.ok || data.success === false) {
                    throw new Error(data.message || 'حدث خطأ غير متوقع، حاول مرة أخرى');
                }
                return data;
            }

            /* ---------- العنوان والروابط ---------- */

            function setHeading(prefix, highlight, subtitle) {
                const h = el('pageTitle');
                h.textContent = prefix ? prefix + ' ' : '';
                const span = document.createElement('span');
                span.className = 'orange-text';
                span.textContent = highlight;
                h.appendChild(span);
                el('pageSubtitle').textContent = subtitle;
            }

            function resetHeading() {
                el('pageTitle').innerHTML = defaultTitle;
                el('pageSubtitle').textContent = defaultSubtitle;
            }

            function setLinks(url) {
                el('openBtn').href = url;
                el('downloadBtn').href = url;
            }

            function updateSaveState() {
                const btn = el('saveBtn');
                if (current && current.saved) {
                    btn.disabled = true;
                    btn.classList.add('rpt-btn-saved');
                    btn.innerHTML = '<i class="bi bi-bookmark-check"></i> تم الحفظ';
                    el('savedLink').classList.remove('rpt-hidden');
                } else {
                    btn.disabled = false;
                    btn.classList.remove('rpt-btn-saved');
                    btn.innerHTML = saveBtnHtml;
                    el('savedLink').classList.add('rpt-hidden');
                }
            }

            function renderResult(reloadPdf) {
                setHeading('', current.title, 'تم تجهيز التقرير بنجاح');
                el('queryText').textContent = current.query;
                setLinks(current.url);
                if (reloadPdf) {
                    el('pdfFrame').src = current.url + '#view=FitH';
                }
                updateSaveState();
                el('cancelEditBtn').classList.add('rpt-hidden');
                show('result');
            }

            /* ---------- حالة الإنشاء على الزر ---------- */

            function setBusy(state) {
                busy = state;
                const btn = el('reportBtn');
                btn.disabled = state;
                btn.innerHTML = state
                    ? '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الإنشاء...'
                    : reportBtnHtml;
                el('report_query').readOnly = state;
                el('formCard').classList.toggle('rpt-busy', state);
            }

            /* ---------- إنشاء التقرير ---------- */

            async function generate(query) {
                query = (query || '').trim();
                if (!query || busy) return;

                setBusy(true);

                try {
                    const data = await request('POST', routes.generate, {
                        report_query: query,
                        remember: true
                    });

                    current = { title: data.title, file: data.file, url: data.pdf_url, query: query, saved: false };

                    if (data.request) addHistoryItem(data.request.id, data.request.query);

                    renderResult(true);
                } catch (err) {
                    showMessage('error', err.message);
                } finally {
                    setBusy(false);
                }
            }

            el('reportForm').addEventListener('submit', function (e) {
                e.preventDefault();
                generate(el('report_query').value);
            });

            // الاقتراحات: إنشاء مباشر
            document.querySelectorAll('.rpt-chip').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    const text = chip.textContent.trim();
                    el('report_query').value = text;
                    generate(text);
                });
            });

            /* ---------- الطلبات السابقة ---------- */

            const historyList = el('historyList');

            function refreshHistoryVisibility() {
                el('historySection').classList.toggle('rpt-hidden', historyList.children.length === 0);
            }

            function addHistoryItem(id, query) {
                const existing = historyList.querySelector('[data-id="' + id + '"]');
                if (existing) existing.remove();

                const item = document.createElement('div');
                item.className = 'rpt-history-item';
                item.dataset.id = id;

                const textBtn = document.createElement('button');
                textBtn.type = 'button';
                textBtn.className = 'rpt-history-text';
                textBtn.title = query;
                textBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i>';
                const span = document.createElement('span');
                span.textContent = query;
                textBtn.appendChild(span);

                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'rpt-history-del';
                delBtn.title = 'حذف';
                delBtn.innerHTML = '<i class="bi bi-trash"></i>';

                item.appendChild(textBtn);
                item.appendChild(delBtn);
                historyList.prepend(item);

                while (historyList.children.length > MAX_HISTORY) {
                    historyList.lastElementChild.remove();
                }
                refreshHistoryVisibility();
            }

            function removeWithFade(item) {
                item.classList.add('removing');
                setTimeout(function () {
                    item.remove();
                    refreshHistoryVisibility();
                }, 250);
            }

            historyList.addEventListener('click', async function (e) {
                const item = e.target.closest('.rpt-history-item');
                if (!item || busy) return;

                // إعادة إنشاء الطلب
                if (e.target.closest('.rpt-history-text')) {
                    const query = item.querySelector('.rpt-history-text span').textContent;
                    el('report_query').value = query;
                    generate(query);
                    return;
                }

                // حذف طلب واحد
                if (e.target.closest('.rpt-history-del')) {
                    const ok = await confirmAction('حذف الطلب', 'هل تريد حذف هذا الطلب من القائمة؟', 'نعم، احذف');
                    if (!ok) return;

                    try {
                        await request('DELETE', routes.requests + '/' + item.dataset.id);
                        removeWithFade(item);
                    } catch (err) {
                        showMessage('error', err.message);
                    }
                }
            });

            // حذف الكل
            el('clearAllBtn').addEventListener('click', async function () {
                if (busy) return;

                const ok = await confirmAction('حذف كل الطلبات', 'هل تريد حذف جميع طلباتك السابقة؟', 'نعم، احذف الكل');
                if (!ok) return;

                try {
                    await request('DELETE', routes.requests);
                    Array.from(historyList.children).forEach(removeWithFade);
                } catch (err) {
                    showMessage('error', err.message);
                }
            });

            /* ---------- أزرار التقرير ---------- */

            el('editBtn').addEventListener('click', function () {
                el('report_query').value = current ? current.query : '';
                setHeading('تعديل', 'طلب التقرير', 'عدّل نص الطلب ثم أعد إنشاء التقرير');
                el('cancelEditBtn').classList.remove('rpt-hidden');
                show('form');
                el('report_query').focus();
            });

            el('cancelEditBtn').addEventListener('click', function () {
                if (current) renderResult(false);
            });

            el('newBtn').addEventListener('click', function () {
                current = null;
                el('report_query').value = '';
                el('pdfFrame').src = 'about:blank';
                el('cancelEditBtn').classList.add('rpt-hidden');
                resetHeading();
                show('form');
                el('report_query').focus();
            });

            el('saveBtn').addEventListener('click', async function () {
                if (!current || current.saved) return;

                const btn = el('saveBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الحفظ...';

                try {
                    const data = await request('POST', routes.save, {
                        file: current.file,
                        title: current.title,
                        report_query: current.query
                    });
                    current.saved = true;
                    current.url = data.pdf_url;
                    setLinks(current.url);
                    showMessage('success', 'تم حفظ التقرير بنجاح');
                } catch (err) {
                    showMessage('error', err.message);
                }
                updateSaveState();
            });

            /* ---------- تقرير قادم من صفحة التقارير الجاهزة ---------- */

            try {
                const pending = JSON.parse(sessionStorage.getItem('pendingReport') || 'null');
                if (pending && pending.url) {
                    sessionStorage.removeItem('pendingReport');
                    current = {
                        title: pending.title,
                        file: pending.file,
                        url: pending.url,
                        query: pending.query,
                        saved: false
                    };
                    renderResult(true);
                }
            } catch (e) {}
        })();
    </script>
@endsection