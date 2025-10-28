@extends('layouts.layout', ['title' => 'بحث الأدوية'])

@section('content')
  @if (session('status'))
    <div class="mb-4 rounded-lg border border-[var(--line)] bg-green-50 px-4 py-3 text-sm text-green-700">
      {{ session('status') }}
    </div>
  @endif

  {{-- نموذج البحث: مصغّر ومتمركز --}}
  <form method="GET" action="{{ route('pharmacy.search') }}"
        class="mx-auto mb-6 flex flex-col items-stretch gap-3"
        style="max-width: 820px" id="drug-search-form">
    <div class="flex flex-wrap items-center justify-center gap-3">
      <input type="text" name="q" value="{{ $q ?? '' }}" id="q"
             class="input w-full sm:w-[520px]"
             placeholder="ابحث بالاسم، المادة الفعّالة، الشكل، التركيز…">
      <button class="btn h-11 px-5 rounded-xl bg-[var(--brand)] text-white">بحث</button>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-5 text-sm" id="filter-group">
      @php
        $raw = request()->input('fields', 'all');
        if (is_array($raw)) {
            $arr = $raw;
        } else {
            $arr = explode(',', (string)$raw);
        }
        $selected = collect($arr)->filter()->values();
        if ($selected->isEmpty()) { $selected = collect(['all']); }
      @endphp
      <label class="inline-flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="fields[]" value="all" class="accent-[var(--brand)]"
               {{ $selected->contains('all') ? 'checked' : '' }}>
        <span>الكل</span>
      </label>
      <label class="inline-flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="fields[]" value="name" class="accent-[var(--brand)]"
               {{ $selected->contains('name') ? 'checked' : '' }}>
        <span>الاسم</span>
      </label>
      <label class="inline-flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="fields[]" value="generic" class="accent-[var(--brand)]"
               {{ $selected->contains('generic') ? 'checked' : '' }}>
        <span>التركيبة</span>
      </label>
      <label class="inline-flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="fields[]" value="form" class="accent-[var(--brand)]"
               {{ $selected->contains('form') ? 'checked' : '' }}>
        <span>الشكل</span>
      </label>
    </div>
  </form>

  <script>
    // فلترة فورية للبطاقات أثناء الكتابة وتغيير الشيك بوكس
    (function(){
      const qInput = document.getElementById('q');
      const container = document.getElementById('drug-cards');
      const form = document.getElementById('drug-search-form');
      const checkboxes = Array.from(form.querySelectorAll('input[name="fields[]"]'));

      function activeFields(){
        const vals = checkboxes.filter(cb=>cb.checked).map(cb=>cb.value);
        if (vals.includes('all') || vals.length === 0) return ['all'];
        return vals;
      }

      function matches(card, term, fields){
        if (!term) return true;
        term = term.toLowerCase();
        const data = {
          name: card.getAttribute('data-name')||'',
          generic: card.getAttribute('data-generic')||'',
          form: card.getAttribute('data-form')||'',
          strength: card.getAttribute('data-strength')||''
        };
        if (fields.includes('all')){
          return Object.values(data).some(v=>v.toLowerCase().includes(term));
        }
        return fields.some(f => (data[f]||'').toLowerCase().includes(term));
      }

      function applyFilter(){
        if (!container) return;
        const term = qInput.value.trim();
        const fields = activeFields();
        const cards = Array.from(container.querySelectorAll('[data-card="drug"]'));
        let visible = 0;
        for (const c of cards){
          const show = matches(c, term, fields);
          c.style.display = show ? '' : 'none';
          if (show) visible++;
        }
        document.getElementById('no-results')?.classList.toggle('hidden', visible !== 0);
      }

      qInput?.addEventListener('input', applyFilter);
      checkboxes.forEach(cb=>cb.addEventListener('change', (e)=>{
        if (e.target.value === 'all' && e.target.checked){
          checkboxes.forEach(x=>{ if (x.value !== 'all') x.checked = false; });
        } else if (e.target.value !== 'all' && e.target.checked){
          form.querySelector('input[value="all"]').checked = false;
        }
        if (!checkboxes.some(x=>x.checked)){
          form.querySelector('input[value="all"]').checked = true;
        }
        applyFilter();
      }));

      // فلترة أولية عند التحميل
      document.addEventListener('DOMContentLoaded', applyFilter);
    })();
  </script>

  @if ($drugs->count())
    {{-- شبكة أعرض للبطاقات --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-5" id="drug-cards">
      @foreach ($drugs as $d)
        <div data-card="drug"
             data-name="{{ Str::lower($d->name) }}"
             data-generic="{{ Str::lower($d->generic_name ?? '') }}"
             data-form="{{ Str::lower($d->dosage_form ?? '') }}"
             data-strength="{{ Str::lower($d->strength ?? '') }}"
             class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] p-4 shadow-soft">
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
    <div id="no-results" class="text-center text-[var(--muted)] py-10">لا نتائج.</div>
  @endif
@endsection
