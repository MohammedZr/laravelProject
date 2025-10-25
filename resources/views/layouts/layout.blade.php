<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>{{ $title ?? config('app.name', 'My Laravel App') }}</title>

    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">

    <style>
      :root{
        --font-ui: 'Tajawal', sans-serif;
        --bg-page: #f7f8fb;
        --bg-card: #ffffff;
        --ink: #243342;
        --muted: #6b7a90;
        --brand: #6aa5a9;   /* أخضر مزرق هادئ */
        --brand-ink: #0e2a2c;
        --line: #e8edf2;
      }
      html, body { height: 100%; }
      body{
        margin:0; padding:0;
        font-family: var(--font-ui);
        background: var(--bg-page);
        font-weight: 700; /* جعل النص عريض بشكل افتراضي */
        color: var(--ink);
      }
      .shadow-soft{ box-shadow: 0 6px 24px rgba(14, 42, 44, 0.06); }

      /* مكونات مساعدة إن لم تكن معرّفة في app.css */
      .input{
        width:100%;
        border-radius:12px;
        border:0.8px solid var(--ink);          /* ← بوردر غامق */
        background:#fff;
        padding:.65rem .9rem;
        outline: none;
      }
      .input:focus{
        border-color: var(--brand-ink);        /* لون أغمق عند التركيز */
        box-shadow: 0 0 0 3px rgba(36, 51, 66, .15); /* ظل خفيف */
      }
      .btn{
        display:inline-flex; align-items:center; justify-content:center; gap:.5rem;
        border-radius:12px; padding:.65rem 1rem; background: var(--brand); color:#fff;
      }
      .btn:hover{ opacity:.92; }
    </style>
</head>
<body class="min-h-dvh flex flex-col">

    @php
      // نستخدم الفساد لتجنّب استدعاء route() على اسم غير موجود
      use Illuminate\Support\Facades\Route as RouteFacade;
    @endphp

    <!-- Navbar -->
    <header class="bg-[var(--bg-card)] border-b border-[var(--line)] shadow-soft">
      <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
          <!-- Brand -->
          <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-[var(--brand-ink)]">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-[var(--brand)] text-[var(--brand-ink)]" aria-hidden="true">
              <!-- Bowl of Hygieia (cup & snake) -->
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                  xmlns="http://www.w3.org/2000/svg" stroke="currentColor"
                  stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 10h12c0 2.6-3.2 4.5-6 4.5S6 12.6 6 10Z"/>
                <path d="M12 14.5V20"/>
                <path d="M9 20h6"/>
                <path d="M15 5c-1.6 0-2.6.8-2.6 2s1 2 2.6 2H17c1.6 0 2.6.8 2.6 2s-1 2-2.6 2h-5.5"/>
                <path d="M17 5l2-1"/>
              </svg>
            </span>
            <span>{{ config('app.name', 'My Laravel App') }}</span>
          </a>

          <!-- Mobile toggle -->
          <input id="nav-toggle" type="checkbox" class="hidden peer" />
          <label for="nav-toggle" class="md:hidden cursor-pointer p-2 rounded-lg hover:bg-[var(--line)]" aria-label="Toggle menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[var(--ink)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </label>

          <!-- Links (Desktop) -->
          <nav class="hidden md:flex items-center gap-6 text-sm">
            <a class="text-[var(--ink)] hover:text-[var(--brand-ink)] transition" href="{{ url('/') }}">الرئيسية</a>

            @auth
              @php
                $role = auth()->user()->role ?? null;
                $mini = $miniCart ?? ['count'=>0,'total'=>0,'byCompany'=>collect()];
                $cnt  = $mini['count'] ?? 0;
              @endphp

              @if ($role === 'pharmacy')
                <!-- زر السلة للصيدلية -->
                <button id="open-cart-sidebar"
                        class="relative rounded-lg px-3 py-1.5 border border-[var(--line)] hover:bg-[var(--line)]/60 transition"
                        type="button">
                  <span class="inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[var(--ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path d="M6 6h15l-1.5 9h-12z"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M6 6l-2-2H2" />
                    </svg>
                    <span>السلة</span>
                  </span>
                  @if ($cnt > 0)
                    <span class="absolute -top-1 -left-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-[var(--brand)] text-white text-xs flex items-center justify-center">
                      {{ $cnt }}
                    </span>
                  @endif
                </button>
              @elseif ($role === 'company')
                <!-- التقارير للشركة (يظهر فقط إن كان الروت موجود) -->
                @if (RouteFacade::has('company.reports.index'))
                   <a class="text-[var(--ink)] hover:text-[var(--brand-ink)] transition"
                    href="{{ route('company.orders.index') }}">الطلبيات</a>
                    @elseif ($role === 'delivery')
  @if (\Illuminate\Support\Facades\Route::has('delivery.dashboard'))
    <a class="text-[var(--ink)] hover:text-[var(--brand-ink)] transition" href="{{ route('delivery.dashboard') }}">طلبياتي</a>
  @endif

                @endif
              @endif
                
              <!-- خروج -->
              <a class="rounded-lg bg-[var(--brand)] text-white px-3 py-1.5 hover:opacity-90 transition" href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                تسجيل الخروج
              </a>
              <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
            @else
              <a class="rounded-lg bg-[var(--brand)] text-white px-3 py-1.5 hover:opacity-90 transition" href="{{ route('login') }}">
                دخول
              </a>
            @endauth
          </nav>
        </div>

        <!-- Mobile menu -->
        <div class="peer-checked:block hidden md:hidden pb-3">
          <div class="mt-2 flex flex-col gap-2 text-sm">
            <a class="px-3 py-2 rounded-lg hover:bg-[var(--line)]" href="{{ url('/') }}">الرئيسية</a>

            @auth
              @php
                $role = auth()->user()->role ?? null;
                $mini = $miniCart ?? ['count'=>0];
                $cnt  = $mini['count'] ?? 0;
              @endphp

              @if ($role === 'pharmacy')
                <button id="open-cart-sidebar-mobile"
                        class="px-3 py-2 text-left rounded-lg border border-[var(--line)] hover:bg-[var(--line)]/60">
                  السلة
                  @if ($cnt > 0)
                    <span class="ml-2 inline-flex min-w-[1.25rem] h-5 px-1 rounded-full bg-[var(--brand)] text-white text-xs items-center justify-center">
                      {{ $cnt }}
                    </span>
                  @endif
                </button>
              @elseif ($role === 'company')
                @if (RouteFacade::has('company.reports.index'))
                  <a class="px-3 py-2 rounded-lg hover:bg-[var(--line)]"
                     href="{{ route('company.reports.index') }}">التقارير</a>
                @endif
              @endif

              <a class="px-3 py-2 rounded-lg bg-[var(--brand)] text-white hover:opacity-90"
                 href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                تسجيل الخروج
              </a>
              <form id="logout-form-mobile" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
            @else
              <a class="px-3 py-2 rounded-lg bg-[var(--brand)] text-white hover:opacity-90" href="{{ route('login') }}">
                دخول
              </a>
            @endauth
          </div>
        </div>
      </div>
    </header>

    <!-- Main -->
    <main class="flex-1">
      <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 py-8">
        <div class="bg-[var(--bg-card)] rounded-2xl p-6 border border-[var(--line)] shadow-soft">
          @yield('content')
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t-2 border-[var(--ink)] bg-[var(--bg-card)]">
      <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 py-6">
        <div class="mx-auto max-w-xl rounded-xl border-2 border-[var(--ink)] text-center text-sm text-[var(--ink)] py-3">
          &copy; {{ now()->year }} {{ config('app.name', 'My Laravel App') }} — جميع الحقوق محفوظة.
        </div>
      </div>
    </footer>

    {{-- ====== Collapsible Cart Sidebar + Overlay + Script ====== --}}
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
                            <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" class="input w-20">
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
            <a href="{{ route('pharmacy.cart.show') }}" class="w-full text-center rounded-xl border border-[var(--line)] py-2 hover:bg-[var(--line)]/60">فتح صفحة السلة</a>
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
    {{-- ====== /Sidebar ====== --}}

</body>
</html>
