<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>{{ $title ?? config('app.name', 'Pharma App') }}</title>

    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">

    <style>
  :root{
    --font-ui: 'Tajawal', sans-serif;
    --bg-page: #f7f8fb;
    --bg-card: #ffffff;
    --ink: #243342;
    --muted: #6b7a90;
    --brand: #6aa5a9;
    --brand-ink: #0e2a2c;
    --line: #e8edf2;
  }

  html, body { height:100%; max-width:100%; overflow-x:hidden; }
  body { margin:0; background:var(--bg-page); font-family:var(--font-ui); color:var(--ink); }
  .shadow-soft { box-shadow:0 6px 24px rgba(14, 42, 44, 0.06); }

  /* =========================
     حقول الإدخال
     ========================= */
  :where(
    input[type="text"],
    input[type="password"],
    input[type="email"],
    input[type="number"],
    input[type="search"],
    input[type="tel"],
    input[type="url"],
    input[type="datetime-local"],
    input[type="date"],
    input[type="time"],
    select,
    textarea
  ){
    appearance: none;
    width: 100%;
    border: 1.2px solid var(--ink);
    background: #fff;
    color: var(--ink);
    border-radius: 12px;
    padding: .65rem .9rem;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
  }

  :where(
    input[type="text"],
    input[type="password"],
    input[type="email"],
    input[type="number"],
    input[type="search"],
    input[type="tel"],
    input[type="url"],
    input[type="datetime-local"],
    input[type="date"],
    input[type="time"],
    select,
    textarea
  ):focus{
    border-color: var(--brand-ink);
    box-shadow: 0 0 0 3px rgba(14, 42, 44, .12);
  }

  :where(
    input[type="text"],
    input[type="password"],
    input[type="email"],
    input[type="number"],
    input[type="search"],
    input[type="tel"],
    input[type="url"],
    input[type="datetime-local"],
    input[type="date"],
    input[type="time"],
    select,
    textarea
  ):disabled,
  :where(
    input[type="text"],
    input[type="password"],
    input[type="email"],
    input[type="number"],
    input[type="search"],
    input[type="tel"],
    input[type="url"],
    input[type="datetime-local"],
    input[type="date"],
    input[type="time"],
    select,
    textarea
  )[readonly]{
    background: #f2f4f7;
    color: #768499;
    cursor: not-allowed;
  }

  .input{
    width:100%;
    border-radius:12px;
    border:1.2px solid var(--ink);
    background:#fff;
    padding:.65rem .9rem;
    outline:none;
  }
  .input:focus{
    border-color: var(--brand-ink);
    box-shadow:0 0 0 3px rgba(14, 42, 44, .12);
  }

  /* ===========================
   الأزرار
   =========================== */
  .btn,
  button,
  a.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    font-size: .9rem;
    font-weight: 600;
    color: #fff;
    background: var(--brand);
    padding: .65rem 1rem;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s ease, opacity .2s ease, transform .1s ease;
  }
  .btn:hover,
  button:hover,
  a.btn:hover { opacity: .9; }

  .btn-secondary { background: var(--muted); }
  .btn-danger { background: #e04747; }
  .btn-danger:hover { background: #d03939; }
  .btn-outline { background: transparent; color: var(--brand-ink); border: 1.5px solid var(--brand); }
  .btn-outline:hover { background: var(--brand); color: #fff; }

  .btn-sm { padding: .4rem .75rem; font-size: .8rem; border-radius: 10px; }
  .btn-icon { width: 2.4rem; height: 2.4rem; padding: 0; justify-content: center; }
  </style>
</head>

<body class="min-h-dvh flex flex-col">
@php
    use Illuminate\Support\Facades\Route as RouteFacade;
    $role = auth()->check() ? auth()->user()->role : null;

    // ✅ اجعل الرئيسية ثابتة إلى landing/
    $homeUrl = RouteFacade::has('landing') ? route('landing') : url('/landing');
@endphp

<header class="bg-[var(--bg-card)] border-b border-[var(--line)] shadow-soft">
  <div class="mx-auto max-w-7xl w-full px-4 sm:px-6">

    <!-- ✅ toggle as peer -->
    <input id="nav-toggle" type="checkbox" class="hidden peer" />

    <div class="flex items-center justify-between h-16">
      <!-- ✅ Logo: يوجّه إلى /landing -->
      <a href="{{ $homeUrl }}" class="flex items-center gap-2 font-bold text-[var(--brand-ink)]">
        <span class="h-10 w-10 rounded-lg bg-[var(--brand)] inline-flex items-center justify-center text-[var(--brand-ink)]">
          🧪
        </span>
        <span>منصة صيدلية</span>
      </a>

      <!-- Burger -->
      <label for="nav-toggle" class="md:hidden cursor-pointer p-2 rounded hover:bg-[var(--line)]">
        <svg class="h-6 w-6 text-[var(--ink)]" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </label>

      <!-- Desktop Links -->
      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a href="{{ $homeUrl }}" class="hover:text-[var(--brand-ink)]">الرئيسية</a>

        @auth
          @php $role = auth()->user()->role; @endphp

          @if ($role === 'pharmacy')
            {{-- السلة --}}
            <button id="open-cart-sidebar"
                    class="relative px-3 py-1.5 border rounded-lg border-[var(--line)] hover:bg-[var(--line)]/60">
              السلة
              @if(($miniCart['count'] ?? 0) > 0)
                <span class="absolute -top-1 -left-1 text-xs bg-[var(--brand)] text-white rounded-full px-1">
                  {{ $miniCart['count'] }}
                </span>
              @endif
            </button>

            {{-- زر بحث الأدوية --}}
            <a href="{{ RouteFacade::has('pharmacy.search') ? route('pharmacy.search') : url('/pharmacy/search') }}"
               class="px-3 py-1.5 rounded-lg border border-[var(--line)] hover:bg-[var(--line)]/60">
              بحث الأدوية
            </a>

          @elseif ($role === 'company')
            <a class="hover:text-[var(--brand-ink)]"
               href="{{ route('company.orders.index') }}">الطلبيات</a>

          @elseif ($role === 'delivery')
            @if(RouteFacade::has('delivery.dashboard'))
              <a class="hover:text-[var(--brand-ink)]"
                 href="{{ route('delivery.dashboard') }}">طلبياتي</a>
            @endif
          @endif

          <a class="bg-[var(--brand)] text-white px-3 py-1.5 rounded-lg hover:opacity-90"
             href="{{ route('logout') }}"
             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            تسجيل الخروج
          </a>
          <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
        @else
          <a class="bg-[var(--brand)] text-white px-3 py-1.5 rounded-lg hover:opacity-90"
             href="{{ route('login') }}">دخول</a>
        @endauth
      </nav>
    </div>

    <!-- ✅ Mobile Menu -->
    <div class="hidden peer-checked:flex flex-col gap-2 md:hidden pb-4 rounded-lg bg-[var(--bg-card)] shadow-soft">
      <a href="{{ $homeUrl }}" class="px-3 py-2 rounded hover:bg-[var(--line)]">الرئيسية</a>

      @auth
        @if ($role === 'pharmacy')
          <button id="open-cart-sidebar-mobile"
                  class="px-3 py-2 text-start border rounded-lg hover:bg-[var(--line)]/60">
            السلة
          </button>

          <a href="{{ RouteFacade::has('pharmacy.search') ? route('pharmacy.search') : url('/pharmacy/search') }}"
             class="px-3 py-2 rounded hover:bg-[var(--line)]">
            بحث الأدوية
          </a>

        @elseif ($role === 'company')
          <a href="{{ route('company.orders.index') }}"
             class="px-3 py-2 rounded hover:bg-[var(--line)]">الطلبيات</a>

        @elseif ($role === 'delivery')
          @if(RouteFacade::has('delivery.dashboard'))
            <a href="{{ route('delivery.dashboard') }}"
               class="px-3 py-2 rounded hover:bg-[var(--line)]">طلبياتي</a>
          @endif
        @endif

        <a href="{{ route('logout') }}"
           class="px-3 py-2 rounded bg-[var(--brand)] text-white hover:opacity-90"
           onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
          تسجيل الخروج
        </a>
        <form id="logout-form-mobile" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>

      @else
        <a href="{{ route('login') }}"
           class="px-3 py-2 rounded bg-[var(--brand)] text-white hover:opacity-90">دخول</a>
      @endauth
    </div>

  </div>
</header>


<main class="flex-1">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 py-8">
    <div class="bg-[var(--bg-card)] rounded-2xl p-6 border shadow-soft">
      @yield('content')
    </div>
  </div>
</main>


<footer class="mt-auto border-t border-[var(--line)] bg-[var(--bg-card)]">
  <div class="text-center py-4 text-sm text-[var(--ink)]">
    &copy; {{ now()->year }} {{ config('app.name', 'Pharma App') }} — جميع الحقوق محفوظة.
  </div>
</footer>


{{-- ✅ Cart Sidebar for Pharmacy فقط --}}
@includeWhen(auth()->check() && auth()->user()->role === 'pharmacy', 'components.cart-sidebar')


</body>
</html>
