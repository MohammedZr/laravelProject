@extends('layouts.layout', ['title' => $title ?? 'تفاصيل المهمة'])

@push('head')
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
@endpush

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl text-[var(--brand-ink)]">تفاصيل التوصيل لطلب #{{ $delivery->order_id }}</h1>
    <a href="{{ route('delivery.tasks.index') }}" class="text-sm underline">رجوع للمهام</a>
  </div>

  @php $order = $delivery->order; @endphp

  <div class="grid gap-4">
    <div class="rounded-2xl border border-[var(--ink)] bg-[var(--bg-card)] p-4">
      <div class="grid sm:grid-cols-2 gap-3 text-sm">
        <div>
          <div class="text-[var(--muted)]">الصيدلية</div>
          <div>{{ $order->pharmacy->name ?? '—' }}</div>
          <div class="text-[var(--muted)]">{{ $order->pharmacy->email ?? '' }}</div>
        </div>
        <div>
          <div class="text-[var(--muted)]">بيانات التسليم</div>
          <div>العنوان: {{ $order->delivery_address_line ?? '—' }}</div>
          <div>المدينة: {{ $order->delivery_city ?? '—' }}</div>
          <div>الهاتف: {{ $order->delivery_phone ?? '—' }}</div>
        </div>
      </div>

      @if ($order?->delivery_lat && $order?->delivery_lng)
        <div class="mt-3">
          <div id="map" class="w-full h-56 rounded-xl border border-[var(--ink)]"></div>
        </div>
      @endif

      <div class="mt-4 flex items-center gap-2">
        <span class="text-xs rounded-lg border-2 border-[var(--ink)] px-2 py-0.5">حالة المهمة: {{ $delivery->status }}</span>
        <span class="text-xs rounded-lg border-2 border-[var(--ink)] px-2 py-0.5">حالة الطلب: {{ $order->status }}</span>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        @if(in_array($delivery->status, ['assigned']))
          <form method="POST" action="{{ route('delivery.tasks.accept', $delivery) }}">@csrf
            <button class="btn h-10 px-4 rounded-xl bg-[var(--brand)] text-white">قبول المهمة</button>
          </form>
        @endif

        @if(in_array($delivery->status, ['assigned','accepted']))
          <form method="POST" action="{{ route('delivery.tasks.pickup', $delivery) }}">@csrf
            <button class="btn h-10 px-4 rounded-xl bg-[var(--brand-ink)] text-white">تم الاستلام</button>
          </form>
        @endif

        @if(in_array($delivery->status, ['accepted','picked_up']))
          <form method="POST" action="{{ route('delivery.tasks.complete', $delivery) }}">@csrf
            <button class="btn h-10 px-4 rounded-xl bg-green-600 text-white">تم التسليم</button>
          </form>
        @endif

        @if(!in_array($delivery->status, ['delivered','cancelled']))
          <form method="POST" action="{{ route('delivery.tasks.cancel', $delivery) }}">@csrf
            <button class="btn h-10 px-4 rounded-xl bg-red-600 text-white">إلغاء المهمة</button>
          </form>
        @endif

        @if ($order?->delivery_lat && $order?->delivery_lng)
          <a class="btn h-10 px-4 rounded-xl bg-slate-600 text-white" target="_blank"
             href="https://www.google.com/maps?q={{ $order->delivery_lat }},{{ $order->delivery_lng }}">فتح في Google Maps</a>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  @if ($order?->delivery_lat && $order?->delivery_lng)
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
      (function(){
        const lat = {{ $order->delivery_lat }};
        const lng = {{ $order->delivery_lng }};
        const map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
        L.marker([lat,lng]).addTo(map);
      })();
    </script>
  @endif
@endpush
