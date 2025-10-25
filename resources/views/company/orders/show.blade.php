@extends('layouts.layout', ['title' => $title ?? 'تفاصيل الطلب'])

@section('content')
  <a href="{{ route('company.orders.index') }}"
     class="inline-block mb-4 rounded-xl border-2 border-[var(--ink)] px-3 py-2 hover:bg-[var(--line)]/60 text-sm">
    ← رجوع للطلبات
  </a>

  <div class="rounded-2xl border-2 border-[var(--ink)] bg-[var(--bg-card)] shadow-soft p-4">
    {{-- رأس الطلب --}}
    <div class="flex items-center justify-between">
      <div class="text-xl text-[var(--brand-ink)] font-semibold">
        طلب #{{ $order->id }}
      </div>
      <span class="text-xs rounded-lg border-2 border-[var(--ink)] px-2 py-0.5 bg-[var(--bg-page)]">
        {{ __("statuses.$order->status") ?? $order->status }}
      </span>
    </div>

    {{-- معلومات أساسية --}}
    <div class="grid sm:grid-cols-2 gap-4 mt-4 text-sm">
      <div>
        <div class="text-[var(--muted)]">الصيدلية</div>
        <div>{{ optional($order->pharmacy)->name ?? '—' }}</div>
        <div class="text-[var(--muted)]">{{ optional($order->pharmacy)->email ?? '' }}</div>
      </div>
      <div>
        <div class="text-[var(--muted)]">تاريخ الإنشاء</div>
        <div>{{ $order->created_at?->format('Y-m-d H:i') }}</div>
        <div class="text-[var(--muted)] mt-2">الإجمالي</div>
        <div>{{ number_format($order->total_amount, 2) }}</div>
      </div>
    </div>

    {{-- عناصر الطلب --}}
    <div class="mt-6">
      <div class="text-[var(--brand-ink)] font-semibold mb-2">عناصر الطلب</div>
      <div class="divide-y divide-[var(--line)]">
        @foreach ($order->items as $item)
          <div class="py-3 flex items-center gap-3">
            @php $img = $item->drug->image_url ?? null; @endphp
            @if ($img)
              <img src="{{ $img }}" class="h-14 w-14 rounded-xl object-cover border-2 border-[var(--ink)]" alt="">
            @else
              <div class="h-14 w-14 rounded-xl border-2 border-[var(--ink)] bg-[var(--bg-page)]"></div>
            @endif

            <div class="flex-1 min-w-0">
              <div class="truncate text-[var(--ink)]">
                {{ $item->drug->name ?? '—' }}
              </div>
              <div class="text-xs text-[var(--muted)] truncate">
                {{ $item->drug->generic_name ?? '' }}
              </div>
            </div>

            <div class="text-sm text-[var(--muted)] whitespace-nowrap">
              x{{ $item->quantity }}
            </div>
            <div class="text-sm text-[var(--muted)] whitespace-nowrap">
              {{ number_format($item->unit_price, 2) }}
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- تحديث الحالة --}}
    <div class="mt-6 border-t border-[var(--line)] pt-4 flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
      <form method="POST" action="{{ route('company.orders.updateStatus', $order) }}" class="flex items-center gap-2">
        @csrf @method('PATCH')
        <select name="status" class="input w-56 border-2 border-[var(--ink)]">
          @foreach ([
              'pending'          => 'قيد المراجعة',
              'confirmed'        => 'تم التأكيد',
              'preparing'        => 'جارِ التجهيز',
              'out_for_delivery' => 'خرج للتسليم',
              'completed'        => 'مكتمل',
              'cancelled'        => 'ملغي',
            ] as $key=>$label)
            <option value="{{ $key }}" @selected($order->status===$key)>{{ $label }}</option>
          @endforeach
        </select>
        <button class="btn h-11 px-4 rounded-xl">تحديث الحالة</button>
      </form>

      <div class="text-lg text-[var(--brand-ink)] font-semibold">
        الإجمالي: {{ number_format($order->total_amount, 2) }}
      </div>
    </div>

    {{-- إسناد الطلب لمنذوب --}}
    @php
      $couriers = \App\Models\User::where('role','delivery')
                  ->where('company_id', auth()->id())
                  ->orderBy('name')
                  ->get();
    @endphp

    <div class="mt-6 rounded-2xl border-2 border-[var(--ink)] p-4">
      <div class="text-[var(--brand-ink)] font-semibold mb-3">إسناد الطلب لمنذوب</div>

      @if(session('ok'))
        <div class="mb-3 rounded-xl border-2 border-[var(--ink)] bg-[var(--bg-page)] px-3 py-2 text-sm">
          {{ session('ok') }}
        </div>
      @endif

      @if($couriers->isEmpty())
        <div class="text-[var(--muted)] text-sm">
          لا يوجد مناديب مضافة لهذه الشركة.
          أضف مستخدمين بدور <b>delivery</b> واجعل <code>company_id</code> لهم = {{ auth()->id() }}.
        </div>
      @else
        <form method="POST" action="{{ route('company.orders.assign', $order) }}"
              class="flex flex-col sm:flex-row gap-2">
          @csrf
          <select name="delivery_user_id" class="input w-full sm:w-64 border-2 border-[var(--ink)]" required>
            <option value="">اختر المنذوب...</option>
            @foreach($couriers as $c)
              <option value="{{ $c->id }}" @selected(optional($order->delivery)->delivery_user_id === $c->id)>
                {{ $c->name }}
              </option>
            @endforeach
          </select>

          <input type="text" name="notes" class="input flex-1 border-2 border-[var(--ink)]"
                 value="{{ old('notes', optional($order->delivery)->notes) }}"
                 placeholder="ملاحظات للمنذوب (اختياري)">

          <button class="btn h-11 px-4 rounded-xl">إسناد</button>
        </form>

        @error('delivery_user_id')
          <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
        @enderror
      @endif
    </div>

    {{-- ملخص مهمة التوصيل إن وُجدت --}}
    @if($order->delivery)
      <div class="mt-6 rounded-2xl border-2 border-[var(--ink)] p-4">
        <div class="text-[var(--brand-ink)] font-semibold mb-2">تفاصيل مهمة التوصيل</div>
        <div class="grid sm:grid-cols-2 gap-4 text-sm">
          <div>
            <div class="text-[var(--muted)]">المنذوب</div>
            <div>{{ optional($order->delivery->courier)->name ?? '—' }}</div>
          </div>
          <div>
            <div class="text-[var(--muted)]">حالة التسليم</div>
            <div>{{ $order->delivery->status }}</div>
          </div>
          <div>
            <div class="text-[var(--muted)]">بداية التسليم</div>
            <div>{{ $order->delivery->picked_up_at?->format('Y-m-d H:i') ?? '—' }}</div>
          </div>
          <div>
            <div class="text-[var(--muted)]">وقت التسليم</div>
            <div>{{ $order->delivery->delivered_at?->format('Y-m-d H:i') ?? '—' }}</div>
          </div>
        </div>
        @if($order->delivery->notes)
          <div class="mt-3 text-sm">
            <div class="text-[var(--muted)]">ملاحظات:</div>
            <div>{{ $order->delivery->notes }}</div>
          </div>
        @endif
      </div>
    @endif
  </div>
@endsection
