(function () {
    'use strict';

    // ── Config (injected by Blade via data-* on <script>) ──
    const scriptTag = document.getElementById('admin-notif-script');
    if (!scriptTag) return;

    const allNotificationsUrl = scriptTag.dataset.allNotificationsUrl;
    const qrNotificationsUrl = scriptTag.dataset.qrNotificationsUrl;
    const orderDetailBaseUrl = scriptTag.dataset.orderDetailBaseUrl;

    const POLL_MS = 3000;
    const STORAGE = {
        since: 'admin_order_notif_since',
        ids: 'admin_order_notified_ids',
        audioUnlocked: 'admin_qr_audio_unlocked',
        soundMuted: 'admin_notif_sound_muted',
        thermalPrint: 'admin_qr_auto_thermal_print',
        printerConnected: 'admin_bt_printer_connected',
    };

    // ── DOM refs ──
    const notifBellBtn = document.getElementById('notif-bell-btn');
    const notifBadge = document.getElementById('order-notif-badge');
    const soundToggleBtn = document.getElementById('notif-sound-toggle');
    const soundIcon = document.getElementById('notif-sound-icon');
    const printerToggleBtn = document.getElementById('printer-toggle-btn');
    const printerToggleIcon = document.getElementById('printer-toggle-icon');

    // ── Notification container ──
    const notifContainer = document.createElement('div');
    notifContainer.className = 'fixed top-24 right-4 md:right-8 z-50 space-y-3 w-[92vw] max-w-sm pointer-events-none';
    document.body.appendChild(notifContainer);

    // ── State ──
    let lastSince = localStorage.getItem(STORAGE.since);
    if (!lastSince) {
        lastSince = new Date(Date.now() - 2 * 60 * 1000).toISOString();
        localStorage.setItem(STORAGE.since, lastSince);
    }
    const savedIds = sessionStorage.getItem(STORAGE.ids);
    const notifiedIds = new Set(savedIds ? JSON.parse(savedIds) : []);
    let unseenCount = 0;
    let soundMuted = localStorage.getItem(STORAGE.soundMuted) === '1';
    const autoThermalPrintEnabled = localStorage.getItem(STORAGE.thermalPrint) !== '0';
    let audioContext = null;
    let audioUnlocked = localStorage.getItem(STORAGE.audioUnlocked) === '1';
    let printerDevice = null;
    let printerCharacteristic = null;
    let printerConnected = localStorage.getItem(STORAGE.printerConnected) === '1';

    // ── Helpers ──
    function persistNotifiedIds() {
        const ids = Array.from(notifiedIds).slice(-200);
        sessionStorage.setItem(STORAGE.ids, JSON.stringify(ids));
    }

    function updateBadge() {
        if (!notifBadge) return;
        if (unseenCount > 0) {
            notifBadge.textContent = unseenCount > 99 ? '99+' : unseenCount;
            notifBadge.classList.remove('hidden');
            notifBadge.classList.add('flex');
        } else {
            notifBadge.classList.add('hidden');
            notifBadge.classList.remove('flex');
        }
    }

    function updateSoundUi() {
        if (!soundIcon || !soundToggleBtn) return;
        soundIcon.className = soundMuted
            ? 'fas fa-volume-mute text-sm text-red-400'
            : 'fas fa-volume-up text-sm';
        soundToggleBtn.title = soundMuted ? 'Suara notifikasi mati' : 'Suara notifikasi aktif';
    }

    // ── Audio ──
    function unlockAudio() {
        if (audioUnlocked) return;
        try {
            const Ctx = window.AudioContext || window.webkitAudioContext;
            if (Ctx) {
                audioContext = audioContext || new Ctx();
                if (audioContext.state === 'suspended') audioContext.resume();
            }
            audioUnlocked = true;
            localStorage.setItem(STORAGE.audioUnlocked, '1');
        } catch (_) {}
    }

    /**
     * Play a prominent 3-note ascending chime (repeated twice).
     * Much louder and more attention-grabbing than a single beep.
     */
    function playNotificationSound() {
        if (soundMuted) return;
        try {
            const Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            const ctx = audioContext || new Ctx();
            audioContext = ctx;
            if (ctx.state === 'suspended') ctx.resume();

            // Play 2 rounds of a 3-note ascending chime
            const notes = [
                { freq: 784, time: 0.00,  dur: 0.15 },  // G5
                { freq: 988, time: 0.12,  dur: 0.15 },  // B5
                { freq: 1319, time: 0.24, dur: 0.25 },  // E6
                { freq: 784, time: 0.55,  dur: 0.15 },  // G5 (repeat)
                { freq: 988, time: 0.67,  dur: 0.15 },  // B5
                { freq: 1319, time: 0.79, dur: 0.30 },  // E6 (longer)
            ];

            notes.forEach(function (n) {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(n.freq, ctx.currentTime + n.time);
                gain.gain.setValueAtTime(0.0001, ctx.currentTime + n.time);
                gain.gain.exponentialRampToValueAtTime(0.25, ctx.currentTime + n.time + 0.03);
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + n.time + n.dur);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(ctx.currentTime + n.time);
                osc.stop(ctx.currentTime + n.time + n.dur + 0.01);
            });
        } catch (_) {}
    }

    // ── System Notification (background) ──
    async function ensureNotificationPermission() {
        if (!('Notification' in window)) return;
        if (Notification.permission === 'default') {
            try { await Notification.requestPermission(); } catch (_) {}
        }
    }

    async function showSystemNotification(order) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;

        const sourceLabel = order.source === 'walk-in' ? '🏪 Walk-in' : '📱 QR Code';
        const title = 'Pesanan Baru • ' + order.order_number;
        const options = {
            body: sourceLabel + ' • ' + (order.customer_name || 'Pelanggan') + ' • Meja ' + (order.table_number || '-') + ' • ' + order.formatted_total,
            icon: '/icons/icon-192.png',
            badge: '/icons/favicon-32.png',
            tag: 'order-' + order.id,
            renotify: true,
            requireInteraction: true,
            data: { orderId: order.id, url: orderDetailBaseUrl + '/' + order.id },
        };

        try {
            if ('serviceWorker' in navigator) {
                const reg = await navigator.serviceWorker.ready;
                await reg.showNotification(title, options);
                return;
            }
        } catch (_) {}

        try {
            var n = new Notification(title, options);
            n.onclick = function () {
                window.focus();
                window.location.href = orderDetailBaseUrl + '/' + order.id;
                n.close();
            };
        } catch (_) {}
    }

    // ── Popup card ──
    function createNotificationCard(order) {
        var isQr = order.source !== 'walk-in';
        var gradientFrom = isQr ? 'from-blue-600' : 'from-orange-500';
        var gradientTo = isQr ? 'to-blue-500' : 'to-orange-400';
        var sourceLabel = isQr ? '📱 Pesanan QR Baru' : '🏪 Pesanan Walk-in Baru';
        var borderColor = isQr ? 'border-blue-200' : 'border-orange-200';
        var btnColor = isQr ? 'bg-blue-600 hover:bg-blue-700' : 'bg-orange-500 hover:bg-orange-600';

        var card = document.createElement('div');
        card.className = 'pointer-events-auto qr-notification-enter notif-pulse rounded-2xl border ' + borderColor + ' shadow-2xl bg-white overflow-hidden';
        card.innerHTML =
            '<div class="bg-gradient-to-r ' + gradientFrom + ' ' + gradientTo + ' text-white px-4 py-2.5 text-xs font-semibold tracking-wide uppercase flex items-center justify-between">' +
                '<span>' + sourceLabel + '</span>' +
                '<span class="opacity-75 text-[10px]">' + order.created_at_label + '</span>' +
            '</div>' +
            '<div class="p-4">' +
                '<div class="flex items-start justify-between gap-3">' +
                    '<div>' +
                        '<p class="font-bold text-slate-900 text-sm">' + order.order_number + '</p>' +
                        '<p class="text-xs text-slate-500 mt-0.5">Meja ' + (order.table_number || '-') + ' • ' + (order.payment_method_label || '') + '</p>' +
                    '</div>' +
                    '<button class="notif-close-btn text-slate-300 hover:text-slate-500 transition text-sm flex-shrink-0" aria-label="Tutup notifikasi">' +
                        '<i class="fas fa-times"></i>' +
                    '</button>' +
                '</div>' +
                '<p class="text-sm text-slate-700 mt-2 font-medium">' + (order.customer_name || 'Pelanggan') + '</p>' +
                '<div class="flex items-center justify-between mt-3">' +
                    '<span class="text-base font-bold text-green-600">' + order.formatted_total + '</span>' +
                    '<div class="flex items-center gap-2">' +
                        '<a href="' + order.thermal_print_url + '?autoprint=1" target="_blank" class="text-xs px-2.5 py-1.5 rounded-lg ' + btnColor + ' text-white transition"><i class="fas fa-print mr-1"></i>Print</a>' +
                        '<a href="' + orderDetailBaseUrl + '/' + order.id + '" class="text-xs px-3 py-1.5 rounded-lg bg-slate-900 text-white hover:bg-slate-700 transition">Lihat</a>' +
                    '</div>' +
                '</div>' +
            '</div>';

        // Shake effect for attention
        setTimeout(function () { card.classList.add('notif-shake'); }, 500);
        setTimeout(function () { card.classList.remove('notif-shake'); }, 1100);

        var closeBtn = card.querySelector('.notif-close-btn');
        closeBtn.addEventListener('click', function () {
            card.classList.add('notif-exit');
            setTimeout(function () { card.remove(); }, 300);
        });

        // Auto-dismiss after 15 seconds with exit animation
        setTimeout(function () {
            if (card.parentNode) {
                card.classList.remove('notif-pulse');
                card.classList.add('notif-exit');
                setTimeout(function () { card.remove(); }, 300);
            }
        }, 15000);

        return card;
    }

    // ── Printer (unchanged from original) ──
    function updatePrinterUi(connected) {
        if (!printerToggleBtn || !printerToggleIcon) return;
        printerToggleBtn.classList.toggle('connected', connected);
        printerToggleBtn.title = connected ? 'Putuskan printer thermal' : 'Hubungkan printer thermal';
    }

    function textToEscPos(text) {
        var encoder = new TextEncoder();
        var init = new Uint8Array([0x1b, 0x40]);
        var alignLeft = new Uint8Array([0x1b, 0x61, 0x00]);
        var body = encoder.encode(text.replace(/\n/g, '\r\n'));
        var feedCut = new Uint8Array([0x0a, 0x0a, 0x1d, 0x56, 0x41, 0x10]);
        var merged = new Uint8Array(init.length + alignLeft.length + body.length + feedCut.length);
        merged.set(init, 0);
        merged.set(alignLeft, init.length);
        merged.set(body, init.length + alignLeft.length);
        merged.set(feedCut, init.length + alignLeft.length + body.length);
        return merged;
    }

    async function resolveWritableCharacteristic(device) {
        var server = await device.gatt.connect();
        var services = await server.getPrimaryServices();
        for (var si = 0; si < services.length; si++) {
            var chars = await services[si].getCharacteristics();
            for (var ci = 0; ci < chars.length; ci++) {
                if (chars[ci].properties.write || chars[ci].properties.writeWithoutResponse) return chars[ci];
            }
        }
        throw new Error('Karakteristik printer tidak ditemukan');
    }

    async function connectPrinter() {
        if (!navigator.bluetooth) { alert('Browser ini belum mendukung Web Bluetooth.'); return; }
        var device = await navigator.bluetooth.requestDevice({ acceptAllDevices: true, optionalServices: [0xFFE0, 0xFF00, 0x180F, 0x18F0] });
        device.addEventListener('gattserverdisconnected', function () {
            printerDevice = null; printerCharacteristic = null; printerConnected = false;
            localStorage.setItem(STORAGE.printerConnected, '0'); updatePrinterUi(false);
        });
        printerCharacteristic = await resolveWritableCharacteristic(device);
        printerDevice = device; printerConnected = true;
        localStorage.setItem(STORAGE.printerConnected, '1'); updatePrinterUi(true);
    }

    async function disconnectPrinter() {
        try { if (printerDevice?.gatt?.connected) printerDevice.gatt.disconnect(); } catch (_) {}
        printerDevice = null; printerCharacteristic = null; printerConnected = false;
        localStorage.setItem(STORAGE.printerConnected, '0'); updatePrinterUi(false);
    }

    async function writeToPrinter(bytes) {
        if (!printerCharacteristic) { if (!printerConnected) return; throw new Error('Printer belum terhubung'); }
        var chunkSize = 180;
        for (var i = 0; i < bytes.length; i += chunkSize) {
            var chunk = bytes.slice(i, i + chunkSize);
            if (printerCharacteristic.properties.writeWithoutResponse) {
                await printerCharacteristic.writeValueWithoutResponse(chunk);
            } else {
                await printerCharacteristic.writeValue(chunk);
            }
            await new Promise(function (r) { setTimeout(r, 20); });
        }
    }

    async function printThermalText(order) {
        if (!autoThermalPrintEnabled || !printerConnected || !order.thermal_text) return;
        try {
            if (!printerCharacteristic && printerDevice) {
                printerCharacteristic = await resolveWritableCharacteristic(printerDevice);
            }
            await writeToPrinter(textToEscPos(order.thermal_text));
        } catch (_) {}
    }

    function triggerThermalAutoPrint(order) {
        if (!autoThermalPrintEnabled || !order.thermal_print_url || printerConnected) return;
        var iframe = document.createElement('iframe');
        iframe.style.cssText = 'position:fixed;width:1px;height:1px;opacity:0;pointer-events:none;bottom:0;right:0';
        iframe.src = order.thermal_print_url + '?autoprint=1';
        document.body.appendChild(iframe);
        setTimeout(function () { iframe.remove(); }, 45000);
    }

    // ── Polling ──
    async function fetchAllNotifications() {
        try {
            var url = allNotificationsUrl + '?since=' + encodeURIComponent(lastSince);
            var response = await fetch(url, {
                cache: 'no-store',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) return;
            var payload = await response.json();
            var orders = Array.isArray(payload.orders) ? payload.orders : [];

            var newOrders = orders.filter(function (o) { return !notifiedIds.has(o.id); });

            if (newOrders.length > 0) {
                playNotificationSound();
                unseenCount += newOrders.length;
                updateBadge();

                // Change page title to alert when in background
                if (document.hidden) {
                    document.title = '(' + unseenCount + ') Pesanan Baru! | order sinom by z';
                }
            }

            newOrders.reverse().forEach(function (order) {
                notifiedIds.add(order.id);
                printThermalText(order);
                triggerThermalAutoPrint(order);
                notifContainer.appendChild(createNotificationCard(order));

                // Always show system notification when page is not focused
                if (document.hidden || !document.hasFocus()) {
                    showSystemNotification(order);
                }
            });

            if (payload.latest_created_at) {
                lastSince = payload.latest_created_at;
                localStorage.setItem(STORAGE.since, lastSince);
            }

            persistNotifiedIds();
        } catch (_) {
            // Ignore polling errors; next tick will retry.
        }
    }

    // ── Init ──
    ensureNotificationPermission();
    updatePrinterUi(printerConnected);
    updateSoundUi();

    if (soundToggleBtn) {
        soundToggleBtn.addEventListener('click', function () {
            soundMuted = !soundMuted;
            localStorage.setItem(STORAGE.soundMuted, soundMuted ? '1' : '0');
            updateSoundUi();
            if (!soundMuted) playNotificationSound(); // preview sound on unmute
        });
    }

    if (notifBellBtn) {
        notifBellBtn.addEventListener('click', function () {
            unseenCount = 0;
            updateBadge();
            document.title = document.title.replace(/^\(\d+\)\s*/, '');
            window.location.href = orderDetailBaseUrl;
        });
    }

    if (printerToggleBtn) {
        printerToggleBtn.addEventListener('click', async function () {
            try {
                if (printerConnected) { await disconnectPrinter(); }
                else { await connectPrinter(); }
            } catch (_) {
                alert('Gagal menghubungkan printer. Pastikan printer thermal bluetooth menyala dan dalam mode pairing.');
            }
        });
    }

    ['click', 'touchstart', 'keydown'].forEach(function (evt) {
        window.addEventListener(evt, unlockAudio, { once: true, passive: true });
    });

    // Start polling
    fetchAllNotifications();
    setInterval(fetchAllNotifications, POLL_MS);

    // When tab becomes visible again, poll immediately & reset title
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            fetchAllNotifications();
            if (unseenCount > 0) {
                document.title = document.title.replace(/^\(\d+\)\s*/, '');
            }
        }
    });
})();
