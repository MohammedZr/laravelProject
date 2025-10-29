@extends('layouts.layout', ['title' => $title ?? 'مهام التوصيل'])

@section('content')
  <h1 class="text-lg font-bold mb-4">📦 مهام التوصيل الحالية</h1>

  @if($orders->isEmpty())
    <div class="p-4 border rounded-xl text-center text-[var(--muted)] bg-[var(--bg-card)]">
      لا توجد طلبيات حالياً.
    </div>
  @else
    <div class="space-y-4">
      @foreach($orders as $order)
        <div class="rounded-xl border border-[var(--line)] bg-[var(--bg-card)] p-4 shadow-soft">
          <div class="flex justify-between items-start">
            <div>
              <div class="font-semibold text-[var(--brand-ink)]">
                الطلب رقم #{{ $order->id }}
              </div>
              <div class="text-sm text-[var(--muted)] leading-relaxed">
                الصيدلية: {{ $order->pharmacy->name ?? '—' }} <br>
                الحالة: {{ __("statuses.$order->status") ?? $order->status }} <br>
                المبلغ الإجمالي: {{ number_format($order->total_amount, 2) }} د.ل <br>
                التاريخ: {{ $order->created_at?->format('Y-m-d H:i') }}
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 text-sm mt-2 sm:mt-0">
              {{-- 📍 زر الخريطة --}}
              <a href="{{ route('delivery.orders.show', $order) }}#map"
                 class="btn btn-secondary">
                 📍 خريطة
              </a>

              {{-- 📄 زر التفاصيل --}}
              <a href="{{ route('delivery.orders.show', $order) }}"
                 class="btn btn-outline">
                 📄 التفاصيل
              </a>

              {{-- ✅ زر تم التسليم --}}
              <form method="POST" action="{{ route('delivery.orders.updateStatus', $order) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="completed">
                <button class="btn btn-danger"
                  {{ $order->status === 'completed' ? 'disabled' : '' }}>
                  {{ $order->status === 'completed' ? '✅ تم التسليم' : '✔️ تأكيد التسليم' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $orders->links() }}
    </div>
  @endif
@endsection
