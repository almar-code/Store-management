{{-- جرس إشعارات لوحة الإدارة --}}
{{-- مكان الجرس في الهيدر للجوال، وفي الشاشات الكبيرة يُنقل للصفحة كزر عائم --}}
<div class="header-social-links notify-slot" id="notifySlot">
<div class="admin-notify" id="adminNotify"
     data-feed="{{ url('/notifications/feed') }}"
     data-read-all="{{ url('/notifications/read-all') }}"
     data-csrf="{{ csrf_token() }}">

    <button type="button" class="notify-bell" id="notifyBell" aria-label="الإشعارات">
        <i class="bi bi-bell"></i>
        <span class="notify-badge d-none" id="notifyBadge">0</span>
    </button>

    <div class="notify-panel" id="notifyPanel" dir="rtl">
        <div class="notify-head">
            <span class="notify-title">الإشعارات</span>
            <button type="button" class="notify-read-all" id="notifyReadAll">تحديد الكل كمقروء</button>
        </div>

        <div class="notify-list" id="notifyList">
            <div class="notify-empty">
                <i class="bi bi-bell-slash"></i>
                <span>لا توجد إشعارات</span>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    .admin-notify {
        position: relative;
        display: flex;
        align-items: center;
    }

    .notify-bell {
        position: relative;
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        background: color-mix(in srgb, var(--accent-color), transparent 90%);
        color: var(--accent-color);
        font-size: 19px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }

    .notify-bell:hover,
    .notify-bell.active {
        background: var(--accent-color);
        color: #fff;
    }

    .notify-bell.ring i {
        animation: notify-ring 0.9s ease;
    }

    .notify-badge {
        position: absolute;
        top: -3px;
        right: -3px;
        min-width: 19px;
        height: 19px;
        padding: 0 5px;
        border-radius: 10px;
        background: #dc3545;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        line-height: 19px;
        text-align: center;
        border: 2px solid #fff;
    }

    .notify-panel {
        display: none;
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 360px;
        max-height: 460px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        z-index: 1050;
        flex-direction: column;
    }

    .notify-panel.show {
        display: flex;
    }

    .notify-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
    }

    .notify-title {
        font-weight: 700;
        font-size: 15px;
        color: #000;
    }

    .notify-read-all {
        border: none;
        background: none;
        color: var(--accent-color);
        font-size: 12px;
        padding: 0;
    }

    .notify-list {
        overflow-y: auto;
    }

    .notify-item {
        display: flex;
        gap: 10px;
        padding: 12px 16px;
        border-bottom: 1px solid #f3f3f3;
        color: #000;
        text-decoration: none;
        transition: 0.2s;
    }

    .notify-item:hover {
        background: #f7f7f7;
        color: #000;
    }

    .notify-item.unread {
        background: color-mix(in srgb, var(--accent-color), transparent 93%);
    }

    .notify-icon {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: color-mix(in srgb, var(--accent-color), transparent 85%);
        color: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .notify-text {
        flex: 1;
        min-width: 0;
    }

    .notify-text strong {
        display: block;
        font-size: 13px;
    }

    .notify-text p {
        margin: 2px 0;
        font-size: 12px;
        color: #666;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .notify-text small {
        font-size: 11px;
        color: #999;
    }

    .notify-dot {
        flex-shrink: 0;
        width: 8px;
        height: 8px;
        margin-top: 6px;
        border-radius: 50%;
        background: var(--accent-color);
    }

    .notify-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 36px 16px;
        color: #999;
        font-size: 13px;
    }

    .notify-empty i {
        font-size: 28px;
    }

    /* الجوال والآيباد: القائمة بعرض الشاشة تحت الهيدر */
    @media (max-width: 1199px) {
        .admin-notify {
            position: static;
            margin-left: 10px;
        }

        .notify-panel {
            position: fixed;
            top: 70px;
            left: 10px;
            right: 10px;
            width: auto;
            max-height: 70vh;
        }
    }

    /* الشاشات الكبيرة: زر عائم أسفل يسار الصفحة فوق الفوتر */
    @media (min-width: 1200px) {
        .notify-slot {
            display: none;
        }

        .admin-notify {
            position: fixed;
            left: 24px;
            bottom: calc(var(--footer-height, 60px) + 20px);
            z-index: 1040;
            padding: 0;
        }

        .notify-bell {
            width: 54px;
            height: 54px;
            font-size: 23px;
            background: var(--accent-color);
            color: #fff;
            box-shadow: 0 8px 22px color-mix(in srgb, var(--accent-color), transparent 55%);
        }

        .notify-bell:hover,
        .notify-bell.active {
            transform: scale(1.06);
            background: var(--accent-color);
        }

        .notify-badge {
            top: -2px;
            right: -2px;
        }

        /* القائمة تفتح للأعلى */
        .notify-panel {
            top: auto;
            bottom: calc(100% + 12px);
            right: auto;
            left: 0;
            max-height: 60vh;
        }
    }

    @keyframes notify-ring {
        0%, 100% { transform: rotate(0); }
        20% { transform: rotate(15deg); }
        40% { transform: rotate(-15deg); }
        60% { transform: rotate(8deg); }
        80% { transform: rotate(-8deg); }
    }
</style>

<script>
    (function () {
        const root = document.getElementById('adminNotify');
        const slot = document.getElementById('notifySlot');
        if (!root || !slot) return;

        // نقل الجرس خارج الهيدر في الشاشات الكبيرة حتى يبقى عائماً
        const desktop = window.matchMedia('(min-width: 1200px)');

        function place() {
            const target = desktop.matches ? document.body : slot;
            if (root.parentElement !== target) target.appendChild(root);
        }

        place();
        desktop.addEventListener('change', place);

        const bell = document.getElementById('notifyBell');
        const badge = document.getElementById('notifyBadge');
        const panel = document.getElementById('notifyPanel');
        const list = document.getElementById('notifyList');
        const readAllBtn = document.getElementById('notifyReadAll');

        const icons = { new_order: 'bi-bag-check' };
        let lastUnread = null;

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        function render(items) {
            if (!items.length) {
                list.innerHTML = '<div class="notify-empty"><i class="bi bi-bell-slash"></i><span>لا توجد إشعارات</span></div>';
                return;
            }

            list.innerHTML = items.map(n => `
                <a href="${n.open_url}" class="notify-item ${n.read ? '' : 'unread'}">
                    <span class="notify-icon"><i class="bi ${icons[n.type] || 'bi-bell'}"></i></span>
                    <span class="notify-text">
                        <strong>${escapeHtml(n.title)}</strong>
                        <p>${escapeHtml(n.body)}</p>
                        <small>${escapeHtml(n.time)}</small>
                    </span>
                    ${n.read ? '' : '<span class="notify-dot"></span>'}
                </a>
            `).join('');
        }

        function setBadge(count) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.toggle('d-none', count === 0);

            // هز الجرس عند وصول إشعار جديد
            if (lastUnread !== null && count > lastUnread) {
                bell.classList.remove('ring');
                void bell.offsetWidth;
                bell.classList.add('ring');
            }

            lastUnread = count;
        }

        function load() {
            fetch(root.dataset.feed, { headers: { 'Accept': 'application/json' } })
                .then(r => r.ok ? r.json() : null)
                .then(data => {
                    if (!data) return;
                    setBadge(data.unread);
                    render(data.items);
                })
                .catch(() => {});
        }

        bell.addEventListener('click', function (e) {
            e.stopPropagation();
            panel.classList.toggle('show');
            bell.classList.toggle('active', panel.classList.contains('show'));
        });

        document.addEventListener('click', function (e) {
            if (!root.contains(e.target)) {
                panel.classList.remove('show');
                bell.classList.remove('active');
            }
        });

        readAllBtn.addEventListener('click', function () {
            fetch(root.dataset.readAll, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': root.dataset.csrf,
                    'Accept': 'application/json'
                }
            }).then(load);
        });

        load();
        setInterval(load, 30000);
    })();
</script>