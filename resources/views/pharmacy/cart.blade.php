@extends('layouts.layout', ['title' => 'سلة المشتريات'])

@section('content')
  @if (session('status'))
    <div class="mb-4 rounded-lg border border-[var(--line)] bg-green-50 px-4 py-3 text-sm text-green-700">
      {{ session('status') }}
    </div>
  @endif

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold text-[var(--brand-ink)]">سلة المشتريات</h1>
    <a href="{{ route('pharmacy.search') }}" class="text-[var(--brand-ink)] hover:underline">عودة للبحث</a>
  </div>

  @php
    $hasAny = false;
  @endphp

  @forelse ($byCompany as $companyId => $items)
    @php
      $company = $items->first()->company;
      $total = $items->sum(fn($i) => $i->quantity * $i->unit_price);
      $hasAny = true;
    @endphp

    <div class="mb-6 rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] p-4 shadow-soft">
      <div class="flex items-center justify-between">
        <div>
          <div class="font-semibold text-[var(--ink)]">شركة: {{ $company?->name ?? 'غير معروفة' }}</div>
          <div class="text-sm text-[var(--muted)]">الإجمالي: {{ number_format($total, 2) }}</div>
        </div>

        <form method="POST" action="{{ route('pharmacy.orders.checkout.company', $companyId) }}">
          @csrf
          <button class="btn h-10 px-4 rounded-xl bg-[var(--brand)] text-white">إرسال طلبية لهذه الشركة</button>
        </form>
      </div>

      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-[var(--muted)]">
            <tr class="border-b border-[var(--line)]">
              <th class="py-2 text-right">الدواء</th>
              <th class="py-2 text-right">السعر</th>
              <th class="py-2 text-right">الكمية</th>
              <th class="py-2 text-right">الإجمالي</th>
              <th class="py-2 text-right">إجراء</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($items as $item)
              <tr class="border-b border-[var(--line)]">
                <td class="py-2">
                  <div class="font-medium">{{ $item->drug->name }}</div>
                  <div class="text-xs text-[var(--muted)]">{{ $item->drug->generic_name ?? '' }}</div>
                </td>
                <td class="py-2">{{ number_format($item->unit_price, 2) }}</td>
                <td class="py-2">
                  <form method="POST" action="{{ route('pharmacy.cart.update', $item) }}" class="flex items-center gap-2">
                    @csrf
                    <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" class="input w-10">
                    <button class="btn h-9 px-3 rounded-xl bg-[var(--brand)] text-white">تحديث</button>
                  </form>
                </td>
                <td class="py-2">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                <td class="py-2">
                  <form method="POST" action="{{ route('pharmacy.cart.remove', $item) }}">
                    @csrf @method('DELETE')
                    <button class="btn h-9 px-3 rounded-xl bg-red-600 text-white">حذف</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @empty
  @endforelse

  @unless ($hasAny)
    <div class="text-center text-[var(--muted)] py-10">سلتك فارغة.</div>
  @endunless
@endsection
