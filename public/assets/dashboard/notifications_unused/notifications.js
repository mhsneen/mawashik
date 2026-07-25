// notifications.js
// متطلبات: تم استدعاء firebase-app-compat و firebase-messaging-compat و axios و SweetAlert2 في الصفحة قبل استدعاء هذا الملف.

if ("Notification" in window && "serviceWorker" in navigator) {
    // قيم Firebase توضع من الـ Blade كـ window.FIREBASE_*
    const firebaseConfig = {
        apiKey: window.FIREBASE_API_KEY,
        authDomain: window.FIREBASE_AUTH_DOMAIN,
        projectId: window.FIREBASE_PROJECT_ID,
        storageBucket: window.FIREBASE_STORAGE_BUCKET,
        messagingSenderId: window.FIREBASE_MESSAGING_SENDER_ID,
        appId: window.FIREBASE_APP_ID,
    };

    // تهيئة Firebase (تأكد ألا يتم تهيئته مرتين)
    if (!window.firebase.apps?.length) {
        firebase.initializeApp(firebaseConfig);
    }
    const messaging = firebase.messaging();

    console.log("🔔 notifications.js loaded");

    // تسجيل Service Worker
    async function registerSW() {
        try {
            const reg = await navigator.serviceWorker.register(
                "/firebase-messaging-sw.js"
            );
            console.log("✅ SW registered:", reg.scope);
            return reg;
        } catch (err) {
            console.error("❌ SW register failed:", err);
            return null;
        }
    }

    // جلب/حفظ التوكن
    async function saveFCMToken() {
        const reg = await registerSW();
        if (!reg) return;

        try {
            const token = await messaging.getToken({
                vapidKey: window.FIREBASE_VAPID_KEY,
                serviceWorkerRegistration: reg,
            });

            if (!token) {
                console.warn("No FCM token returned");
                return;
            }

            console.log("✅ FCM Token:", token);

            // أرسل التوكن إلى السيرفر — غيّر المسار لو كان عندك Route آخر
            await axios.put(
                '{{ route("desktop.token.save") }}'.replace(
                    '{{ route("desktop.token.save") }}',
                    window.SAVE_TOKEN_URL
                ),
                {
                    desktop_token: token,
                }
            );
        } catch (err) {
            console.error("❌ Error getting/saving token:", err);
        }
    }

    // سنستدعي saveFCMToken() من الـ Blade بعد تعريف SAVE_TOKEN_URL و المتغيرات
    window.saveFCMToken = saveFCMToken;

    // Foreground notifications (Swal)
    const shown = new Set();
    messaging.onMessage((payload) => {
        console.log("📩 FCM onMessage (foreground):", payload);

        const orderId =
            payload.data?.order_id || payload.notification?.title || Date.now();
        if (shown.has(orderId)) return;
        shown.add(orderId);

        const orderUrl = payload.data?.url || "/orders"; // fallback

        swal(
            {
                title: payload.notification?.title || "إشعار جديد",
                text: payload.notification?.body || "",
                type: "info", // نوع الرسالة (success, error, warning, info)
                showCancelButton: false,
                confirmButtonText: "عرض الطلب",
                closeOnConfirm: true,
                imageUrl:
                    window.APP_ICON || "/assets/dashboard/img/brand/logo.png",
                imageWidth: 132,
                imageHeight: 204,
            },
            function (isConfirm) {
                if (isConfirm && orderUrl) {
                    window.location.href = orderUrl;
                }
            }
        );
    });
} else {
    console.warn(
        "Notifications or ServiceWorkers not supported in this browser"
    );
}
