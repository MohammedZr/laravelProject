{{-- ====== Cart Sidebar (Pharmacy only) ====== --}}
@auth
@if ((auth()->user()->role ?? null) === 'pharmacy')
  {{-- Overlay --}}
  <div id="cart-overlay"
       class="fixed inset-0 bg-black/30 backdrop-blur-[1px] z-40 hidden"></div>

  {{-- Sidebar --}}
  <aside id="cart-sidebar"
         class="fixed top-0 right-0 h-screen w-full max-w-md z-50
                bg-[var(--bg-card)] border-l border-[var(--line)] shadow-soft
                translate-x-full transition-transform duration-300 ease-out
                flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 sm:px-6 h-14 border-b border-[var(--line)]">
      <div class="font-bold text-[var(--brand-ink)]">سلة المشتريات</div>
      <button id="close-cart-sidebar" class="p-2 rounded-lg hover:bg-[var(--line)]" type="button" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[var(--ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M6 6l12 12M18 6l-12 12"/>
        </svg>
      </button>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4">
      @php
        $byCompany = ($miniCart['byCompany'] ?? collect());
      @endphp

      @if ($byCompany->isEmpty())
        <div class="text-center text-[var(--muted)] py-10">
          سلتك فارغة. <a href="{{ route('pharmacy.search') }}" class="text-[var(--brand-ink)] hover:underline">ابحث عن الأدوية</a>.
        </div>
      @else
        @foreach ($byCompany as $companyId => $items)
          @php
            $company = optional($items->first()->company);
            $total = $items->sum(fn($i) => $i->quantity * $i->unit_price);
          @endphp

          <div class="mb-5 rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] shadow-soft">
            <div class="flex items-center justify-between px-4 py-3 border-b border-[var(--line)]">
              <div>
                <div class="font-semibold text-[var(--ink)] truncate">شركة: {{ $company->name ?? '—' }}</div>
                <div class="text-xs text-[var(--muted)]">إجمالي هذه الشركة: {{ number_format($total,2) }}</div>
              </div>
              <form method="POST" action="{{ route('pharmacy.orders.checkout.company', $companyId) }}">
                @csrf
                <button class="btn h-9 px-3 rounded-xl bg-[var(--brand)] text-white">إرسال الطلبية</button>
              </form>
            </div>

            <div class="divide-y divide-[var(--line)]">
              @foreach ($items as $item)
                <div class="p-3 flex items-start gap-3">
                  {{-- صورة مصغّرة --}}
                  <div class="shrink-0">
                    @php $img = $item->drug->image_url ?? null; @endphp
                    @if ($img)
                      <img src="{{ $img }}" alt="صورة {{ $item->drug->name }}"
                           class="h-14 w-14 rounded-xl object-cover border border-[var(--line)]">
                    @else
                      <div class="h-14 w-14 rounded-xl border border-[var(--line)] bg-[var(--bg-page)] flex items-center justify-center">
                        <svg viewBox="0 0 24 24" class="h-7 w-7 text-[var(--muted)]" fill="none" stroke="currentColor" stroke-width="1.6">
                          <path d="M3 17l4-4 3 3 5-5 6 6" /><circle cx="8.5" cy="8.5" r="1.5"/>
                        </svg>
                      </div>
                    @endif
                  </div>

                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-3">
                      <div class="truncate">
                        <div class="font-semibold text-[var(--brand-ink)] truncate">{{ $item->drug->name }}</div>
                        <div class="text-xs text-[var(--muted)] truncate">{{ $item->drug->generic_name ?? '' }}</div>
                      </div>
                      <div class="text-xs text-[var(--muted)] whitespace-nowrap">
                        {{ number_format($item->unit_price,2) }}
                      </div>
                    </div>

                    <div class="mt-2 flex items-center justify-between gap-2">
                      <form method="POST" action="{{ route('pharmacy.cart.update', $item) }}" class="flex items-center gap-2">
                        @csrf
                        <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" class="input w-20 border-2 border-[var(--ink)]">
                        <button class="btn h-9 px-3 rounded-xl bg-[var(--brand)] text-white">تحديث</button>
                      </form>

                      <form method="POST" action="{{ route('pharmacy.cart.remove', $item) }}">
                        @csrf @method('DELETE')
                        <button class="btn h-9 px-3 rounded-xl bg-red-600 text-white">حذف</button>
                      </form>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      @endif
    </div>

    {{-- Footer --}}
    <div class="border-t border-[var(--line)] px-4 sm:px-6 py-3">
      <div class="flex items-center justify-between">
        <div class="text-sm text-[var(--muted)]">المجموع الكلي</div>
        <div class="font-bold text-[var(--brand-ink)]">{{ number_format($miniCart['total'] ?? 0, 2) }}</div>
      </div>
      <div class="mt-3 flex items-center gap-2 text-sm">
        <a href="{{ route('pharmacy.cart.show') }}" class="w-full text-center rounded-xl border-2 border-[var(--ink)] py-2 hover:bg-[var(--line)]/60">فتح صفحة السلة</a>
        <a href="{{ route('pharmacy.search') }}" class="w-full text-center rounded-xl bg-[var(--brand)] text-white py-2 hover:opacity-90">متابعة التسوق</a>
      </div>
    </div>
  </aside>

  {{-- Script: فتح/إغلاق + ربط زر الموبايل --}}
  <script>
    (function() {
      const openBtn  = document.getElementById('open-cart-sidebar');
      const openBtnMobile = document.getElementById('open-cart-sidebar-mobile');
      const closeBtn = document.getElementById('close-cart-sidebar');
      const sidebar  = document.getElementById('cart-sidebar');
      const overlay  = document.getElementById('cart-overlay');

      function openSidebar() {
        if (!sidebar || !overlay) return;
        sidebar.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
      }
      function closeSidebar() {
        if (!sidebar || !overlay) return;
        sidebar.classList.add('translate-x-full');
        overlay.classList.add('hidden');
      }

      openBtn && openBtn.addEventListener('click', openSidebar);
      openBtnMobile && openBtnMobile.addEventListener('click', openSidebar);
      closeBtn && closeBtn.addEventListener('click', closeSidebar);
      overlay && overlay.addEventListener('click', closeSidebar);
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
      });
    })();
  </script>
@endif
@endauth
{{-- ====== /Cart Sidebar ====== --}}
