<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>{{ $title ?? 'تسجيل الدخول - ' . config('app.name', 'My Laravel App') }}</title>

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
      color: var(--ink);
    }
    .shadow-soft{ box-shadow: 0 6px 24px rgba(14, 42, 44, 0.06); }
    /* في حال لم تكن عرّفت .input و .btn داخل app.css */
    .input{ width:100%; border-radius:12px; border:1px solid var(--line); background:#fff; padding:.65rem .9rem; outline: none; }
    .input:focus{ box-shadow: 0 0 0 3px rgba(214,179,90,.25); border-color: var(--brand); }
    .btn{ display:inline-flex; align-items:center; justify-content:center; gap:.5rem; border-radius:12px; padding:.75rem 1rem; background: var(--brand); color:#fff; }
    .btn:hover{ opacity:.92; }
  </style>
</head>

<body class="min-h-screen flex flex-col">

  <main class="flex-1 flex items-center justify-center py-10">
    <div class="w-full max-w-md mx-4">
      <div class="rounded-2xl bg-[var(--bg-card)] border border-[var(--line)] shadow-soft p-6 sm:p-8">
        <div class="flex flex-col items-center text-center">
          <div class="h-16 w-16 rounded-full bg-[var(--brand)]/20 border border-[var(--brand)] flex items-center justify-center mb-3">
            <!-- أيقونة بسيطة -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[var(--brand-ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0Z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-[var(--brand-ink)]">تسجيل الدخول</h1>
          <p class="text-sm text-[var(--muted)] mt-1">مرحباً بك، يرجى إدخال بيانات حسابك</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="mt-6 space-y-4">
          @csrf

          {{-- البريد الإلكتروني --}}
          <div>
            <label for="email" class="block text-sm mb-1 text-[var(--ink)]">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="input"
                   placeholder="example@mail.com">
            @error('email')
              <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
          </div>

          {{-- كلمة المرور --}}
          <div>
            <div class="flex items-center justify-between">
              <label for="password" class="block text-sm mb-1 text-[var(--ink)]">كلمة المرور</label>
              @if (Route::has('password.request'))
                <a class="text-xs text-[var(--brand-ink)] hover:underline" href="{{ route('password.request') }}">
                  نسيت كلمة المرور؟
                </a>
              @endif
            </div>
            <input id="password" type="password" name="password" required
                   class="input"
                   placeholder="••••••••">
            @error('password')
              <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
          </div>

          {{-- تذكّرني --}}
          <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-[var(--line)]">
            <label for="remember_me" class="text-sm text-[var(--ink)]">تذكّرني</label>
          </div>

          {{-- زر الدخول --}}
          <button type="submit" class="w-full btn h-11 text-base font-semibold">
            دخول
          </button>

          {{-- رابط إنشاء حساب (اختياري) --}}
          @if (Route::has('register'))
            <p class="text-center text-sm text-[var(--muted)]">
              لا تملك حساباً؟
              <a href="{{ route('register') }}" class="text-[var(--brand-ink)] hover:underline">أنشئ حساباً</a>
            </p>
          @endif
        </form>
      </div>
    </div>
  </main>

  <footer class="border-t border-[var(--line)] bg-[var(--bg-card)]">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 py-6 text-center text-sm text-[var(--muted)]">
      &copy; {{ now()->year }} {{ config('app.name', 'My Laravel App') }} — جميع الحقوق محفوظة.
    </div>
  </footer>

</body>
</html>
