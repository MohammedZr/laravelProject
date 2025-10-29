@extends('layouts.layout', ['title' => 'تفاصيل الطلب'])

@section('content')

<div class="flex flex-col gap-6">

  {{-- ✅ معلومات الطلب --}}
  <div class="border border-[var(--line)] rounded-2xl bg-[var(--bg-card)] p-5 shadow-soft">
    <h2 class="text-lg font-semibold text-[var(--brand-ink)] mb-2">تفاصيل الطلب</h2>
    <div class="text-sm text-[var(--muted)] space-y-1">
      <div>رقم الطلب: <b>#{{ $order->id }}</b></div>
      <div>الصيدلية: <b>{{ $order->pharmacy->name ?? '—' }}</b></div>
      <div>الإجمالي: <b>{{ number_format($order->total_amount, 2) }}</b></div>
      <div>الحالة الحالية:
        <span class="rounded-lg border border-[var(--ink)] px-2 py-1 text-xs bg-[var(--bg-page)]">
          {{ __("statuses.$order->status") ?? $order->status }}
        </span>
      </div>
      <div>تاريخ الإنشاء: {{ $order->created_at->format('Y-m-d H:i') }}</div>
    </div>
  </div>

  {{-- ✅ العناصر داخل الطلب --}}
  <div class="border border-[var(--line)] rounded-2xl bg-[var(--bg-card)] p-5 shadow-soft">
    <h2 class="text-lg font-semibold text-[var(--brand-ink)] mb-3">الأدوية المطلوبة</h2>

    @foreach ($order->items as $item)
      <div class="flex items-center gap-3 py-2 border-b border-[var(--line)] last:border-none">
        @php $img = $item->drug->image_url ?? null; @endphp
        @if ($img)
          <img src="{{ $img }}" alt="صورة الدواء" class="h-12 w-12 rounded-lg object-cover border border-[var(--line)]">
        @else
          <div class="h-12 w-12 bg-[var(--bg-page)] border border-[var(--line)] rounded-lg flex items-center justify-center">
            🧴
          </div>
        @endif

        <div class="flex-1">
          <div class="font-semibold text-[var(--brand-ink)]">{{ $item->drug->name }}</div>
          <div class="text-xs text-[var(--muted)]">{{ $item->drug->generic_name ?? '' }}</div>
        </div>

        <div class="text-sm text-[var(--ink)]">
          × {{ $item->quantity }}<br>
          {{ number_format($item->unit_price, 2) }}
        </div>
      </div>
    @endforeach
  </div>

  {{-- ✅ إسناد الطلب إلى المندوب --}}
  <div class="border border-[var(--line)] rounded-2xl bg-[var(--bg-card)] p-5 shadow-soft">
    <h2 class="text-lg font-semibold text-[var(--brand-ink)] mb-3">إسناد الطلب لمندوب</h2>

    @if ($order->delivery)
      <div class="mb-3 text-sm text-[var(--muted)]">
        هذا الطلب مسند حالياً إلى:
        <b>{{ $order->delivery->courier?->name ?? 'مجهول' }}</b>
        <span class="text-xs">({{ $order->delivery->status }})</span>
      </div>
      <form method="POST" action="{{ route('company.orders.unassign', $order) }}">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm">إلغاء الإسناد</button>
      </form>
    @else
      <form method="POST" action="{{ route('company.orders.assign', $order) }}" class="flex items-center gap-3">
        @csrf
        <select name="courier_id" class="input w-64" required>
          <option value="">اختر المندوب</option>
          @foreach ($couriers as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
          @endforeach
        </select>
        <button class="btn h-10 px-4 rounded-xl">إسناد الطلب</button>
      </form>
    @endif
  </div>

  {{-- ✅ تحديث حالة الطلب --}}
  <div class="border border-[var(--line)] rounded-2xl bg-[var(--bg-card)] p-5 shadow-soft">
    <h2 class="text-lg font-semibold text-[var(--brand-ink)] mb-3">تحديث حالة الطلب</h2>
    <form method="POST" action="{{ route('company.orders.updateStatus', $order) }}" class="flex items-center gap-3">
      @csrf
      @method('PATCH')
      <select name="status" class="input w-48">
        @foreach (['pending'=>'قيد المراجعة','confirmed'=>'تم التأكيد','preparing'=>'جارِ التجهيز','out_for_delivery'=>'خرج للتسليم','completed'=>'مكتمل','cancelled'=>'ملغي'] as $key=>$label)
          <option value="{{ $key }}" @selected($order->status===$key)>{{ $label }}</option>
        @endforeach
      </select>
      <button class="btn h-10 px-4 rounded-xl">تحديث</button>
    </form>
  </div>

</div>

@endsection
