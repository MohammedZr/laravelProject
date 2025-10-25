@extends('layouts.layout', ['title' => 'بحث الأدوية'])

@section('content')
  @if (session('status'))
    <div class="mb-4 rounded-lg border border-[var(--line)] bg-green-50 px-4 py-3 text-sm text-green-700">
      {{ session('status') }}
    </div>
  @endif

  {{-- نموذج البحث: مصغّر ومتمركز --}}
  <form method="GET" action="{{ route('pharmacy.search') }}"
        class="mx-auto mb-6 flex flex-wrap items-center justify-center gap-3"
        style="max-width: 720px">
    <input type="text" name="q" value="{{ $q ?? '' }}"
           class="input w-full sm:w-[420px]"
           placeholder="ابحث بالاسم، المادة الفعّالة، الشكل، التركيز…">
    <select name="type" class="input w-36 sm:w-40">
      <option value="all"    @selected(($type ?? 'all')==='all')>الكل</option>
      <option value="name"   @selected(($type ?? '')==='name')>الاسم</option>
      <option value="generic"@selected(($type ?? '')==='generic')>التركيبة</option>
      <option value="form"   @selected(($type ?? '')==='form')>الشكل</option>
    </select>
    <button class="btn h-11 px-5 rounded-xl bg-[var(--brand)] text-white">بحث</button>
  </form>

  @if ($drugs->count())
    {{-- شبكة أعرض للبطاقات --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-5">
      @foreach ($drugs as $d)
        <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] p-4 shadow-soft">
          <div class="flex items-start gap-4">
            {{-- الصورة --}}
            <div class="shrink-0">
              @php
                $img = $d->image_url ?? null;
              @endphp
              @if ($img)
                <img src="{{ $img }}" alt="صورة {{ $d->name }}"
                     class="h-24 w-24 rounded-xl object-cover border border-[var(--line)]">
              @else
                {{-- Placeholder SVG لو مافيش صورة --}}
                <div class="h-24 w-24 rounded-xl border border-[var(--line)] bg-[var(--bg-page)] flex items-center justify-center">
                  <svg viewBox="0 0 24 24" class="h-10 w-10 text-[var(--muted)]" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M3 17l4-4 3 3 5-5 6 6" />
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                  </svg>
                </div>
              @endif
            </div>

            {{-- تفاصيل الدواء --}}
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-3">
                <div class="truncate">
                  <div class="text-base font-semibold text-[var(--brand-ink)] truncate">{{ $d->name }}</div>
                  <div class="text-sm text-[var(--muted)] truncate">{{ $d->generic_name ?? '—' }}</div>
                </div>
                <div class="text-xs text-[var(--muted)] text-right whitespace-nowrap">
                  {{ $d->dosage_form ?? '' }} {{ $d->strength ? '— '.$d->strength : '' }}
                </div>
              </div>

              <div class="mt-2 flex items-center justify-between">
                <div class="text-sm">
                  <span class="text-[var(--muted)]">السعر:</span>
                  <b>{{ number_format($d->price, 2) }}</b>
                </div>
                <div class="text-xs text-[var(--muted)]">المخزون: {{ $d->stock }}</div>
              </div>

              {{-- إضافة للسلة: زر أعرض + حقل كمية --}}
              <form method="POST" action="{{ route('pharmacy.cart.add', $d) }}" class="mt-3 flex items-center gap-3">
                @csrf
                <input type="number" name="quantity" min="1" value="1" class="input w-24">
                <button class="btn h-11 px-5 rounded-xl bg-[var(--brand)] text-white w-full sm:w-40">
                  أضف للسلة
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">{{ $drugs->links() }}</div>
  @else
    <div class="text-center text-[var(--muted)] py-10">لا نتائج.</div>
  @endif
@endsection
