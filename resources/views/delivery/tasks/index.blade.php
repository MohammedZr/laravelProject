@extends('layouts.layout', ['title' => $title ?? 'مهامي'])

@section('content')
  <h1 class="text-xl text-[var(--brand-ink)] mb-4">مهامي</h1>

  @if($deliveries->isEmpty())
    <div class="rounded-xl border-2 border-[var(--ink)] p-4 text-[var(--muted)]">لا توجد مهام حالياً.</div>
  @else
    <div class="grid md:grid-cols-2 gap-4">
      @foreach($deliveries as $d)
        <div class="rounded-2xl border-2 border-[var(--ink)] bg-[var(--bg-card)] p-4">
          <div class="flex items-center justify-between">
            <div class="font-semibold text-[var(--brand-ink)]">طلب #{{ $d->order->id }}</div>
            <span class="text-xs rounded-lg border-2 border-[var(--ink)] px-2 py-0.5 bg-[var(--bg-page)]">{{ $d->status }}</span>
          </div>
          <div class="mt-2 text-sm">
            <div class="text-[var(--muted)]">الصيدلية:</div>
            <div>{{ $d->order->pharmacy->name ?? '—' }}</div>
            <div class="text-[var(--muted)] mt-2">الإجمالي:</div>
            <div>{{ number_format($d->order->total_amount,2) }}</div>
          </div>

          <div class="mt-3 flex flex-wrap items-center gap-2">
            @if($d->status === 'assigned')
              <form method="POST" action="{{ route('delivery.tasks.accept', $d) }}">@csrf @method('PATCH')
                <button class="btn h-10 px-3 rounded-xl">قبول</button>
              </form>
            @endif

            @if(in_array($d->status, ['assigned','accepted'], true))
              <form method="POST" action="{{ route('delivery.tasks.start', $d) }}">@csrf @method('PATCH')
                <button class="btn h-10 px-3 rounded-xl">بدء التسليم</button>
              </form>
            @endif

            @if($d->status === 'in_transit')
              <form method="POST" action="{{ route('delivery.tasks.complete', $d) }}">@csrf @method('PATCH')
                <button class="btn h-10 px-3 rounded-xl">إتمام التسليم</button>
              </form>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $deliveries->links() }}
    </div>
  @endif
@endsection
