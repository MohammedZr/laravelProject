@extends('layouts.layout', ['title' => $title ?? 'لوحة المندوب'])

@section('content')
  @if (session('success'))
    <div class="mb-4 rounded-xl border-2 border-[var(--ink)] bg-[var(--bg-card)] p-3 text-[var(--brand-ink)]">
      {{ session('success') }}
    </div>
  @endif

  <form method="GET" class="mb-4 flex items-center gap-2">
    <select name="status" class="input w-56">
      <option value="">كل الحالات</option>
      @foreach (['assigned'=>'مُسنَد','picked_up'=>'تم الاستلام','delivering'=>'قيد التوصيل','delivered'=>'تم التسليم','failed'=>'فشل'] as $k=>$v)
        <option value="{{ $k }}" @selected($status===$k)>{{ $v }}</option>
      @endforeach
    </select>
    <button class="btn h-11 px-4 rounded-xl">تصفية</button>
  </form>

  @if ($orders->isEmpty())
    <div class="text-center text-[var(--muted)] py-16">لا توجد طلبيات معيّنة لك الآن.</div>
  @else
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
      @foreach ($orders as $o)
        <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] shadow-soft p-4">
          <div class="flex items-center justify-between">
            <div class="font-semibold text-[var(--brand-ink)]">طلب #{{ $o->id }}</div>
            <span class="text-xs rounded-lg border-2 border-[var(--ink)] px-2 py-0.5 bg-[var(--bg-page)]">
              {{ $o->delivery->status ?? '—' }}
            </span>
          </div>
          <div class="mt-2 text-sm text-[var(--muted)]">
            <div>الصيدلية: {{ $o->pharmacy->name ?? '—' }}</div>
            <div>العنوان: {{ $o->delivery_address ?? '—' }}</div>
          </div>
          <div class="mt-3">
            <a href="{{ route('delivery.orders.show', $o) }}" class="rounded-xl border-2 border-[var(--ink)] px-3 py-2 inline-block hover:bg-[var(--line)]/60 text-sm">فتح</a>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
  @endif
@endsection
