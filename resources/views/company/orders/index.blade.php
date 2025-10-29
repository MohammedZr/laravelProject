@extends('layouts.layout', ['title' => $title ?? 'إدارة الطلبيات'])

@section('content')
  {{-- إشعارات --}}
  @if (session('success'))
    <div class="mb-4 rounded-xl border border-green-400 bg-green-50 p-3 text-green-800">
      {{ session('success') }}
    </div>
  @elseif (session('error'))
    <div class="mb-4 rounded-xl border border-red-400 bg-red-50 p-3 text-red-800">
      {{ session('error') }}
    </div>
  @endif

  {{-- شريط البحث --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <form id="order-search-form" class="flex items-center gap-2" method="GET" action="{{ route('company.orders.index') }}">
      <input type="text" name="q" id="order-search-input" value="{{ $search }}" placeholder="بحث برقم الطلب أو اسم الصيدلية" class="input w-64">
      <button class="btn h-11 px-4 rounded-xl">بحث</button>
    </form>
  </div>

  {{-- شبكة الطلبات --}}
  @if ($orders->isEmpty())
    <div class="text-center text-[var(--muted)] py-16">لا توجد طلبيات حالياً.</div>
  @else
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
      @foreach ($orders as $order)
        <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] shadow-soft p-4">
          <div class="flex items-center justify-between">
            <div class="text-[var(--brand-ink)] font-semibold">طلب #{{ $order->id }}</div>
            <span class="text-xs rounded-lg border px-2 py-0.5 bg-[var(--bg-page)]">
              {{ __("statuses.$order->status") ?? $order->status }}
            </span>
          </div>

          <div class="mt-2 text-sm text-[var(--muted)]">
            <div>الصيدلية: {{ $order->pharmacy->name ?? '—' }}</div>
            <div>الإجمالي: {{ number_format($order->total_amount, 2) }} د.ل</div>
            <div>بتاريخ: {{ $order->created_at?->format('Y-m-d H:i') }}</div>
          </div>

          {{-- معاينة العناصر --}}
          <div class="mt-3 space-y-2">
            @foreach ($order->items->take(3) as $item)
              <div class="flex items-center gap-2 text-sm">
                @php $img = $item->drug->image_url ?? null; @endphp
                @if ($img)
                  <img src="{{ $img }}" class="h-8 w-8 rounded-lg object-cover border" alt="">
                @else
                  <div class="h-8 w-8 rounded-lg border bg-[var(--bg-page)]"></div>
                @endif
                <div class="truncate flex-1">
                  <div class="truncate">{{ $item->drug->name ?? '—' }}</div>
                  <div class="text-[10px] text-[var(--muted)]">x{{ $item->quantity }} • {{ number_format($item->unit_price, 2) }}</div>
                </div>
              </div>
            @endforeach
          </div>

          {{-- الأزرار --}}
          <div class="mt-4 flex items-center justify-between gap-2">
            <a href="{{ route('company.orders.show', $order) }}" class="btn btn-outline h-10 px-3 rounded-xl text-sm">تفاصيل الطلب</a>

            @if ($order->status === 'pending')
              <form method="POST" action="{{ route('company.orders.updateStatus', $order) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="out_for_delivery">
                <button class="btn h-10 px-3 rounded-xl text-sm">تأكيد الطلب</button>
              </form>
            @elseif ($order->status === 'out_for_delivery')
              <span class="text-sm text-green-600 font-semibold">جارِ التسليم</span>
            @elseif ($order->status === 'completed')
              <span class="text-sm text-gray-500 font-semibold">مكتمل</span>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
  @endif
  <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'local',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

// ✅ الاستماع للقناة العامة للشركات
Echo.channel('company.orders')
    .listen('.order.created', (e) => {
        console.log('طلب جديد:', e.order);

        // 🔔 تشغيل صوت الجرس
        const audio = new Audio('/sounds/notify.mp3');
        audio.play();

        // 💬 إشعار داخل الصفحة
        const div = document.createElement('div');
        div.innerHTML = `
          <div class="fixed top-5 left-5 bg-green-600 text-white px-4 py-2 rounded-xl shadow-lg z-[9999] animate-bounce">
            🔔 طلب جديد #${e.order.id} من ${e.order.pharmacy?.name ?? 'صيدلية'}
          </div>
        `;
        document.body.appendChild(div);
        setTimeout(()=>div.remove(), 5000);

        // 🌐 إشعار المتصفح
        if (Notification.permission === 'granted') {
            new Notification('طلبية جديدة!', {
                body: `طلب رقم #${e.order.id} من ${e.order.pharmacy?.name ?? 'صيدلية'}`,
                icon: '/favicon.ico'
            });
        }
    });

// ✨ طلب إذن إشعارات المتصفح مرة واحدة
if (Notification.permission !== 'granted') {
    Notification.requestPermission();
}
</script>

@endsection
