@extends('layouts.layout', ['title' => $title ?? 'توصيل الطلب'])

@section('content')
  @if (session('success'))
    <div class="mb-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--bg-card)] p-3 text-[var(--brand-ink)]">
      {{ session('success') }}
    </div>
  @endif

  <a href="{{ route('delivery.dashboard') }}" class="inline-block mb-4 rounded-xl border-2 border-[var(--ink)] px-3 py-2 hover:bg-[var(--line)]/60 text-sm">← رجوع</a>

  <div class="grid lg:grid-cols-2 gap-6">
    <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] shadow-soft p-4">
      <div class="flex items-center justify-between">
        <div class="text-xl text-[var(--brand-ink)] font-semibold">طلب #{{ $order->id }}</div>
        <span class="text-xs rounded-lg border-2 border-[var(--ink)] px-2 py-0.5 bg-[var(--bg-page)]">
          {{ $order->delivery->status ?? '—' }}
        </span>
      </div>

      <div class="mt-3 text-sm text-[var(--muted)]">
        <div>الصيدلية: {{ $order->pharmacy->name ?? '—' }}</div>
        <div>العنوان: {{ $order->delivery_address ?? '—' }}</div>
        <div>الهاتف: {{ $order->pharmacy->email ?? '' }}</div>
        <div>الإجمالي: {{ number_format($order->total,2) }}</div>
      </div>

      <div class="mt-4">
        <div class="text-[var(--brand-ink)] font-semibold mb-2">تحديث الحالة</div>
        <form method="POST" action="{{ route('delivery.orders.updateStatus', $order) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
          @csrf @method('PATCH')
          <select name="status" class="input sm:w-56">
            @foreach (['picked_up'=>'تم الاستلام','delivering'=>'قيد التوصيل','delivered'=>'تم التسليم','failed'=>'فشل'] as $k=>$v)
              <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
          <input type="text" name="failed_reason" class="input" placeholder="سبب الفشل (اختياري)">
          <button class="btn h-11 px-4 rounded-xl">حفظ</button>
        </form>
      </div>
    </div>

    {{-- الخريطة --}}
    <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] shadow-soft p-4">
      <div class="text-[var(--brand-ink)] font-semibold mb-2">موقع الصيدلية</div>
      <div id="map" class="h-[360px] rounded-xl border-2 border-[var(--ink)]"></div>

      @php
        $lat = $order->delivery_lat;
        $lng = $order->delivery_lng;
        $webUrl = "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}&travelmode=driving";
        $androidUrl = "google.navigation:q={$lat},{$lng}";
      @endphp

      @if($lat && $lng)
      <div class="mt-4 flex justify-center">
        <a id="gpsDirBtn"
           href="{{ $webUrl }}"
           class="btn w-full sm:w-auto text-center rounded-xl px-5 py-3 text-base bg-[var(--brand)] text-white">
           🚗 الاتجاهات إلى الموقع
        </a>
      </div>

      <script>
        (function(){
          const isAndroid = /Android/i.test(navigator.userAgent);
          const btn = document.getElementById('gpsDirBtn');
          if(isAndroid){
            btn.href = "{{ $androidUrl }}";
          }
        })();
      </script>
      @endif

    </div>
  </div>

  {{-- Leaflet --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
          integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <script>
    (function(){
      const lat = {{ $order->delivery_lat ?? 'null' }};
      const lng = {{ $order->delivery_lng ?? 'null' }};

      if (lat && lng) {
        const map = L.map('map').setView([lat, lng], 14);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19, attribution: '&copy; OpenStreetMap'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup(`{{ $order->delivery_address ?? 'العنوان' }}`).openPopup();
      } else {
        document.getElementById('map').innerHTML =
          '<div class="h-full w-full flex items-center justify-center text-[var(--muted)]">لا توجد إحداثيات لهذا الطلب.</div>';
      }
    })();
  </script>
  
@endsection
