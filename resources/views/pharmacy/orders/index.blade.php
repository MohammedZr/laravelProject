@extends('layouts.layout', ['title' => 'طلباتي'])

@section('content')
  @if (session('status'))
    <div class="mb-4 rounded-lg border border-[var(--line)] bg-green-50 px-4 py-3 text-sm text-green-700">
      {{ session('status') }}
    </div>
  @endif

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold text-[var(--brand-ink)]">طلباتي</h1>
    <div class="flex items-center gap-2 text-sm">
      <a href="{{ route('pharmacy.cart.show') }}" class="px-3 py-1.5 rounded-lg border border-[var(--line)] hover:bg-[var(--line)]/50">
        السلة
      </a>
      <a href="{{ route('pharmacy.search') }}" class="px-3 py-1.5 rounded-lg bg-[var(--brand)] text-white hover:opacity-90">
        البحث عن دواء
      </a>
    </div>
  </div>

  @if ($orders->count())
    <div class="overflow-x-auto rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] shadow-soft">
      <table class="min-w-full text-sm">
        <thead class="text-[var(--muted)]">
          <tr class="border-b border-[var(--line)]">
            <th class="py-3 px-4 text-right">#</th>
            <th class="py-3 px-4 text-right">الشركة</th>
            <th class="py-3 px-4 text-right">الحالة</th>
            <th class="py-3 px-4 text-right">الإجمالي</th>
            <th class="py-3 px-4 text-right">التاريخ</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($orders as $o)
            <tr class="border-b border-[var(--line)]">
              <td class="py-3 px-4">{{ $o->id }}</td>
              <td class="py-3 px-4">{{ $o->company?->name ?? '—' }}</td>
              <td class="py-3 px-4">
                @php
                  $map = [
                    'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'shipped'   => 'bg-purple-50 text-purple-700 border-purple-200',
                    'delivered' => 'bg-green-50 text-green-700 border-green-200',
                    'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                  ];
                  $cls = $map[$o->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                @endphp
                <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-xs {{ $cls }}">
                  {{ $o->status }}
                </span>
              </td>
              <td class="py-3 px-4">{{ number_format($o->total_amount, 2) }}</td>
              <td class="py-3 px-4">{{ $o->created_at?->format('Y-m-d H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
  @else
    <div class="text-center text-[var(--muted)] py-10">
      لا توجد طلبات بعد. <a href="{{ route('pharmacy.search') }}" class="text-[var(--brand-ink)] hover:underline">ابدأ بإضافة أدوية للسلة</a>.
    </div>
  @endif
@endsection
