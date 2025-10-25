@extends('layouts.layout', ['title' => $title ?? 'إدارة الطلبيات'])

@section('content')
  {{-- فلاش --}}
  @if (session('success'))
    <div class="mb-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--bg-card)] p-3 text-[var(--brand-ink)]">
      {{ session('success') }}
    </div>
  @endif

  {{-- فلاتر سريعة --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <form class="flex items-center gap-2" method="GET" action="{{ route('company.orders.index') }}">
      <input type="text" name="q" value="{{ $search }}" placeholder="بحث برقم الطلب أو اسم الصيدلية" class="input w-64">
      <select name="status" class="input w-48">
        <option value="">كل الحالات</option>
        @foreach (['pending'=>'قيد المراجعة','confirmed'=>'تم التأكيد','preparing'=>'جارِ التجهيز','out_for_delivery'=>'خرج للتسليم','completed'=>'مكتمل','cancelled'=>'ملغي'] as $key=>$label)
          <option value="{{ $key }}" @selected($status===$key)>{{ $label }}</option>
        @endforeach
      </select>
      <button class="btn h-11 px-4 rounded-xl">تصفية</button>
    </form>

    {{-- عدادات مختصرة --}}
    <div class="flex flex-wrap gap-2 text-xs">
      @foreach (['pending'=>'قيد المراجعة','confirmed'=>'تم التأكيد','preparing'=>'جارِ التجهيز','out_for_delivery'=>'خرج للتسليم','completed'=>'مكتمل','cancelled'=>'ملغي'] as $k=>$label)
        <span class="rounded-xl border-2 border-[var(--ink)] px-3 py-1 bg-[var(--bg-card)]">
          {{ $label }}: {{ $counts[$k] ?? 0 }}
        </span>
      @endforeach
    </div>
  </div>

  {{-- شبكة الطلبيات --}}
  @if ($orders->isEmpty())
    <div class="text-center text-[var(--muted)] py-16">لا توجد طلبيات حالياً.</div>
  @else
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
      @foreach ($orders as $order)
        <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] shadow-soft p-4">
          <div class="flex items-center justify-between">
            <div class="text-[var(--brand-ink)] font-semibold">طلب #{{ $order->id }}</div>
            <span class="text-xs rounded-lg border-2 border-[var(--ink)] px-2 py-0.5 bg-[var(--bg-page)]">
              {{ __("statuses.$order->status") ?? $order->status }}
            </span>
          </div>

          <div class="mt-2 text-sm text-[var(--muted)]">
            <div>الصيدلية: {{ $order->pharmacy->name ?? '—' }}</div>
            <div>الإجمالي: {{ number_format($order->total_amount, 2) }}</div>
            <div>بتاريخ: {{ $order->created_at?->format('Y-m-d H:i') }}</div>
          </div>

          {{-- معاينة مختصرة للمواد --}}
          <div class="mt-3 space-y-2">
            @foreach ($order->items->take(3) as $item)
              <div class="flex items-center gap-2 text-sm">
                @php $img = $item->drug->image_url ?? null; @endphp
                @if ($img)
                  <img src="{{ $img }}" class="h-8 w-8 rounded-lg object-cover border border-[var(--line)]" alt="">
                @else
                  <div class="h-8 w-8 rounded-lg border border-[var(--line)] bg-[var(--bg-page)]"></div>
                @endif
                <div class="truncate flex-1">
                  <div class="truncate">{{ $item->drug->name ?? '—' }}</div>
                  <div class="text-[10px] text-[var(--muted)]">x{{ $item->quantity }} • {{ number_format($item->unit_price,2) }}</div>
                </div>
              </div>
            @endforeach
            @if ($order->items->count() > 3)
              <div class="text-xs text-[var(--muted)]">+ {{ $order->items->count() - 3 }} عناصر أخرى</div>
            @endif
          </div>

          <div class="mt-4 flex items-center justify-between gap-2">
            <a href="{{ route('company.orders.show', $order) }}" class="rounded-xl border-2 border-[var(--ink)] px-3 py-2 hover:bg-[var(--line)]/60 text-sm">
              تفاصيل الطلب
            </a>

            <form method="POST" action="{{ route('company.orders.updateStatus', $order) }}" class="flex items-center gap-2">
              @csrf @method('PATCH')
              <select name="status" class="input w-44">
                @foreach (['pending'=>'قيد المراجعة','confirmed'=>'تم التأكيد','preparing'=>'جارِ التجهيز','out_for_delivery'=>'خرج للتسليم','completed'=>'مكتمل','cancelled'=>'ملغي'] as $key=>$label)
                  <option value="{{ $key }}" @selected($order->status===$key)>{{ $label }}</option>
                @endforeach
              </select>
              <button class="btn h-10 px-3 rounded-xl text-sm">تحديث</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $orders->links() }}
    </div>
  @endif
@endsection
