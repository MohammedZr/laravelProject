@extends('layouts.layout', ['title' => $title ?? "طلب #{$order->id}"])

@section('content')
  @if (session('success'))
    <div class="mb-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--bg-card)] p-3 text-[var(--brand-ink)]">
      {{ session('success') }}
    </div>
  @endif
  @if (session('error'))
    <div class="mb-4 rounded-xl border-2 border-red-700 bg-red-50 p-3 text-red-800">
      {{ session('error') }}
    </div>
  @endif

  <div class="flex items-center justify-between mb-4">
    <div class="text-lg font-bold text-[var(--brand-ink)]">تفاصيل الطلب #{{ $order->id }}</div>
    <div class="flex gap-2">
      <a href="{{ route('delivery.orders.print', $order) }}" target="_blank" class="btn btn-secondary">
        🖨️ طباعة فواتير A5
      </a>
      <a href="{{ route('delivery.dashboard') }}" class="btn btn-outline">العودة للمهام</a>
    </div>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] p-4 shadow-soft">
      <div class="font-semibold mb-2">معلومات عامة</div>
      <div class="text-sm text-[var(--muted)] space-y-1">
        <div>الحالة الحالية: <b>{{ __("statuses.$order->status") ?? $order->status }}</b></div>
        <div>الصيدلية: <b>{{ $order->pharmacy->name ?? '—' }}</b></div>
        <div>الشركة: <b>{{ $order->company->name ?? '—' }}</b></div>
        <div>الإجمالي: <b>{{ number_format($order->total_amount, 2) }}</b></div>
        <div>التاريخ: <b>{{ $order->created_at?->format('Y-m-d H:i') }}</b></div>
      </div>

      <div class="mt-4 font-semibold">العناصر</div>
      <div class="mt-2 space-y-2">
        @foreach($order->items as $item)
          <div class="flex items-center gap-2 text-sm">
            @php $img = $item->drug->image_url ?? null; @endphp
            @if ($img)
              <img src="{{ $img }}" class="h-10 w-10 rounded-lg object-cover border border-[var(--line)]" alt="">
            @else
              <div class="h-10 w-10 rounded-lg border border-[var(--line)] bg-[var(--bg-page)]"></div>
            @endif
            <div class="flex-1">
              <div class="font-medium">{{ $item->drug->name }}</div>
              <div class="text-[10px] text-[var(--muted)]">
                x{{ $item->quantity }} • {{ number_format($item->unit_price, 2) }} • الإجمالي: {{ number_format($item->line_total ?? $item->quantity * $item->unit_price, 2) }}
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] p-4 shadow-soft">
      <div class="font-semibold mb-2">موقع التسليم على الخريطة</div>

      @if(!$targetLat || !$targetLng)
        <div class="text-sm text-[var(--muted)]">
          لا توجد إحداثيات للتسليم. رجاءً تأكد من حفظ إحداثيات الصيدلية أو الطلب.
        </div>
      @else
        <div id="map" class="w-full h-72 rounded-xl border"></div>
        <div class="mt-3 text-sm">
          <div>المسافة حتى الهدف: <b id="dist-label">—</b> متر</div>
          <div class="text-[var(--muted)]">سيُفعَّل زر "تم التسليم" تلقائيًا عند الاقتراب ≤ 30 متر.</div>
        </div>
      @endif

      <div class="mt-4 grid grid-cols-2 gap-2">
        {{-- ابدأ التسليم (إن لم يكن Out For Delivery) --}}
        @if($order->status !== 'out_for_delivery' && $order->status !== 'completed')
          <form method="POST" action="{{ route('delivery.orders.updateStatus', $order) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="out_for_delivery">
            <button class="btn w-full">🚚 ابدأ التوصيل</button>
          </form>
        @endif

        {{-- تم التسليم (يتفعل فقط عند القرب) --}}
        <form method="POST" action="{{ route('delivery.orders.updateStatus', $order) }}">
          @csrf @method('PATCH')
          <input type="hidden" name="status" value="completed">
          <button id="deliver-btn" class="btn btn-danger w-full" {{ ($order->status === 'completed') ? 'disabled' : '' }}>
            {{ $order->status === 'completed' ? '✅ تم التسليم' : '🚫 اقترب أولًا' }}
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Leaflet CDN --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  @if($targetLat && $targetLng)
  <script>
    (function () {
      const target = [{{ $targetLat }}, {{ $targetLng }}];
      const map = L.map('map').setView(target, 17);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19}).addTo(map);
      L.marker(target).addTo(map).bindPopup('📍 موقع التسليم').openPopup();

      const btn = document.getElementById('deliver-btn');
      const distLabel = document.getElementById('dist-label');
      let myMarker;

      function updateDistance(lat, lng) {
        const d = map.distance([lat, lng], target); // بالأمتار
        distLabel.textContent = Math.round(d);

        if (btn) {
          if (d <= 30 && '{{ $order->status }}' !== 'completed') {
            btn.disabled = false;
            btn.textContent = '✅ تم الوصول - تأكيد التسليم';
          } else if ('{{ $order->status }}' === 'completed') {
            btn.disabled = true;
            btn.textContent = '✅ تم التسليم';
          } else {
            btn.disabled = true;
            btn.textContent = `🚫 اقترب أكثر (${Math.round(d)} م)`;
          }
        }
      }

      if (navigator.geolocation) {
        navigator.geolocation.watchPosition(function (pos) {
          const { latitude, longitude } = pos.coords;
          if (myMarker) map.removeLayer(myMarker);
          myMarker = L.marker([latitude, longitude], {
            title: '🚴 موقعي'
          }).addTo(map);
          updateDistance(latitude, longitude);
        }, function (err) {
          console.warn('Geolocation error', err);
        }, { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 });
      }
    })();
  </script>
  @endif
@endsection
