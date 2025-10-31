import "./bootstrap";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import toastr from "toastr";
import "toastr/build/toastr.min.css";

// إعداد Pusher
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: "pusher",
    key: "local",
    cluster: "mt1",
    wsHost: "127.0.0.1",
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ["ws", "wss"],
});

// إعداد Toastr
toastr.options = {
    positionClass: "toast-bottom-left",
    timeOut: "5000",
    closeButton: true,
    progressBar: true,
};

// طلب إذن الإشعارات
if ("Notification" in window && Notification.permission !== "granted") {
    Notification.requestPermission();
}

// تحميل الأصوات
const sounds = {
    newOrder: new Audio("/sounds/new_order.wav"),
    assigned: new Audio("/sounds/assigned.wav"),
    status: new Audio("/sounds/status.wav"),
};

// تجاوز حظر autoplay
document.addEventListener(
    "click",
    () => {
        Object.values(sounds).forEach((s) =>
            s
                .play()
                .then(() => s.pause())
                .catch(() => {})
        );
    },
    { once: true }
);

// دالة تشغيل صوت + إشعار المتصفح
function playSound(type) {
    sounds[type]?.play().catch(() => {});
}

function showNotification(title, message, type = "info", soundKey = null) {
    toastr[type](message, title);
    if (soundKey) playSound(soundKey);
    if ("Notification" in window && Notification.permission === "granted") {
        new Notification(title, { body: message, icon: "/favicon.ico" });
    }
}

// الاشتراك في قناة المستخدم
if (window.userId) {
    const channel = window.Echo.private(`App.Models.User.${window.userId}`);

    // إشعار شركة
    channel.listen(".NewOrderCreated", (e) => {
        showNotification(
            "طلب جديد",
            `📦 من ${e.order.pharmacy.name}`,
            "info",
            "newOrder"
        );
    });

    // إشعار مندوب
    channel.listen(".DeliveryAssigned", (e) => {
        showNotification(
            "طلب مُسند إليك",
            `🚚 الطلب #${e.order.id}`,
            "warning",
            "assigned"
        );
    });

    // إشعار صيدلية
    channel.listen(".OrderStatusUpdated", (e) => {
        showNotification(
            "تحديث حالة الطلب",
            `🔔 الحالة الآن: ${e.order.status}`,
            "success",
            "status"
        );
    });
}
