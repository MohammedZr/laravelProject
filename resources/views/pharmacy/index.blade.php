@extends('layouts.layout', ['title' => 'طلباتي'])

@section('content')
  @if (session('status'))
    <div class="mb-4 rounded-lg border border-[var(--line)] bg-green-50 px-4 py-3 text-sm text-green-700">
      {{ session('status') }}
    </div>
  @endif

  <h1 class="text-xl font-bold text-[var(--brand-ink)] mb-4">طلباتي</h1>

  @if ($orders->count())
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-[var(--muted)]">
          <tr class="border-b border-[var(--line)]">
            <th class="py-3 text-right">#</th>
            <th class="py-3 text-right">الشركة</th>
            <th class="py-3 text-right">الحالة</th>
            <th class="py-3 text-right">الإجمالي</th>
            <th class="py-3 text-right">التاريخ</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($orders as $o)
            <tr class="border-b border-[var(--line)]">
              <td class="py-3">{{ $o->id }}</td>
              <td class="py-3">{{ $o->company?->name ?? '—' }}</td>
              <td class="py-3">{{ $o->status }}</td>
              <td class="py-3">{{ number_format($o->total_amount, 2) }}</td>
              <td class="py-3">{{ $o->created_at->format('Y-m-d H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
  @else
    <div class="text-center text-[var(--muted)] py-10">لا توجد طلبات بعد.</div>
  @endif
@endsection
