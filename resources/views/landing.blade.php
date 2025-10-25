@extends('layouts.layout', ['title' => 'منصّة Pharma — اشترِ الأدوية بذكاء'])

@section('content')
  <style>
    /* حركات بسيطة */
    @keyframes floatY { 0%{ transform: translateY(0) } 50%{ transform: translateY(-8px) } 100%{ transform: translateY(0) } }
    @keyframes fadeUp { 0%{ opacity:0; transform: translateY(16px) } 100%{ opacity:1; transform: translateY(0) } }
    .animate-float { animation: floatY 3.2s ease-in-out infinite; }
    .reveal { opacity: 0; transform: translateY(16px); transition: all .6s ease; }
    .reveal.in { opacity: 1; transform: translateY(0); }

    /* بطاقات الإنفوغرافيك */
    .step-card { border: 2px dashed var(--ink); }
    .connector:before{
      content:''; position:absolute; inset-inline-start: -24px; top: 1.25rem;
      width: 12px; height: 12px; border-radius: 999px; background: var(--brand);
      box-shadow: 0 0 0 4px rgba(106,165,169,.15);
    }
    .connector:after{
      content:''; position:absolute; inset-inline-start: -18px; top: 2rem; bottom: -1.5rem; width:2px; background: var(--line);
    }
    .connector:last-child:after{ display:none }
  </style>

  {{-- HERO --}}
  <section class="relative overflow-hidden">
    <div class="flex flex-col lg:flex-row items-center gap-8">
      <div class="flex-1 reveal">
        <h1 class="text-3xl md:text-4xl text-[var(--brand-ink)] leading-snug">
          منصّة ذكية تربط <span class="text-[var(--brand)]">الصيدليات</span> بالشركات المورّدة
        </h1>
        <p class="mt-3 text-[var(--muted)]">
          ابحث عن الدواء بالاسم أو التركيبة، أضِف للسلة، وأرسل طلباتك لكل شركة بضغطة واحدة. تتبّع الطلبات بسهولة.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
          @guest
            <a href="{{ route('login') }}" class="btn rounded-xl px-5 h-12 text-base">ابدأ الآن</a>
          @endguest

          @auth
            @php $role = auth()->user()->role ?? null; @endphp
            @if($role === 'pharmacy')
              <a href="{{ route('pharmacy.search') }}" class="btn rounded-xl px-5 h-12 text-base">ابحث عن الأدوية</a>
            @elseif($role === 'company')
              @if (Illuminate\Support\Facades\Route::has('company.groups.index'))
                <a href="{{ route('company.groups.index') }}" class="btn rounded-xl px-5 h-12 text-base">إدارة الكتالوج</a>
              @endif
            @endif
          @endauth

          <a href="#how-it-works" class="rounded-xl px-5 h-12 inline-flex items-center border-2 border-[var(--ink)] hover:bg-[var(--line)]/50 transition">
            كيف يعمل؟
          </a>
        </div>
      </div>

      <div class="flex-1 flex justify-center reveal">
        <!-- Illustration: Bowl of Hygieia + عناصر طافية -->
        <div class="relative w-[320px] h-[320px]">
          <div class="absolute inset-0 rounded-3xl bg-[var(--bg-page)] border-2 border-[var(--ink)] shadow-soft"></div>

          <div class="absolute inset-0 flex items-center justify-center animate-float">
            <span class="inline-flex h-28 w-28 items-center justify-center rounded-2xl bg-[var(--brand)]/15 text-[var(--brand-ink)] border-2 border-[var(--brand)]">
              <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   xmlns="http://www.w3.org/2000/svg" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 10h12c0 2.6-3.2 4.5-6 4.5S6 12.6 6 10Z"/>
                <path d="M12 14.5V20"/><path d="M9 20h6"/>
                <path d="M15 5c-1.6 0-2.6.8-2.6 2s1 2 2.6 2H17c1.6 0 2.6.8 2.6 2s-1 2-2.6 2h-5.5"/>
                <path d="M17 5l2-1"/>
              </svg>
            </span>
          </div>

          <!-- فقاعات صغيرة -->
          <div class="absolute -top-2 start-6 animate-float" style="animation-delay:.2s">
            <div class="h-6 w-6 rounded-full bg-[var(--brand)]/25 border border-[var(--brand)]"></div>
          </div>
          <div class="absolute bottom-4 end-6 animate-float" style="animation-delay:.6s">
            <div class="h-4 w-4 rounded-full bg-[var(--brand)]/25 border border-[var(--brand)]"></div>
          </div>
          <div class="absolute top-10 end-1 animate-float" style="animation-delay:1s">
            <div class="h-5 w-5 rounded-full bg-[var(--brand)]/25 border border-[var(--brand)]"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- كيف يعمل التطبيق --}}
  <section id="how-it-works" class="mt-10">
    <h2 class="text-2xl text-[var(--brand-ink)] mb-4 reveal">كيف يعمل التطبيق؟</h2>

    <div class="grid md:grid-cols-3 gap-4">
      <div class="rounded-2xl bg-[var(--bg-card)] step-card p-5 reveal">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-lg bg-[var(--brand)]/20 border border-[var(--brand)] flex items-center justify-center">
            <!-- بحث -->
            <svg class="h-6 w-6 text-[var(--brand-ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>
            </svg>
          </div>
          <div class="text-lg">بحث ذكي عن الأدوية</div>
        </div>
        <p class="mt-2 text-[var(--muted)]">
          بالاسم، التركيبة الكيميائية، أو الشكل الصيدلاني. نتائج مرتّبة وسريعة.
        </p>
      </div>

      <div class="rounded-2xl bg-[var(--bg-card)] step-card p-5 reveal">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-lg bg-[var(--brand)]/20 border border-[var(--brand)] flex items-center justify-center">
            <!-- سلة -->
            <svg class="h-6 w-6 text-[var(--brand-ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M6 6h15l-1.5 9h-12z"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
            </svg>
          </div>
          <div class="text-lg">إضافة للسلة حسب الشركة</div>
        </div>
        <p class="mt-2 text-[var(--muted)]">
          نجمع العناصر تلقائيًا لكل شركة، وتسليم طلب منفصل لكل مورد.
        </p>
      </div>

      <div class="rounded-2xl bg-[var(--bg-card)] step-card p-5 reveal">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-lg bg-[var(--brand)]/20 border border-[var(--brand)] flex items-center justify-center">
            <!-- تتبع -->
            <svg class="h-6 w-6 text-[var(--brand-ink)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M12 2C7 2 3 6 3 11c0 6 9 11 9 11s9-5 9-11c0-5-4-9-9-9z"/><circle cx="12" cy="11" r="3"/>
            </svg>
          </div>
          <div class="text-lg">تتبّع الطلب حتى الوصول</div>
        </div>
        <p class="mt-2 text-[var(--muted)]">
          حالات فورية وخريطة تقديرية لوقت التسليم (لاحقًا).
        </p>
      </div>
    </div>
  </section>

  {{-- إنفوغرافيك خطّي (Timeline) --}}
  <section class="mt-10">
    <h2 class="text-2xl text-[var(--brand-ink)] mb-4 reveal">خط سير العملية</h2>

    <div class="relative ps-8">
      <div class="rounded-2xl bg-[var(--bg-card)] border-2 border-[var(--ink)] p-4 mb-4 connector reveal">
        <div class="font-semibold">الشركة</div>
        <p class="text-[var(--muted)] mt-1">تنشر الكتالوج وتُحدّث الأسعار والتوفر.</p>
      </div>
      <div class="rounded-2xl bg-[var(--bg-card)] border-2 border-[var(--ink)] p-4 mb-4 connector reveal">
        <div class="font-semibold">الصيدلية</div>
        <p class="text-[var(--muted)] mt-1">بحث → إضافة للسلة → إرسال طلبية لكل شركة.</p>
      </div>
      <div class="rounded-2xl bg-[var(--bg-card)] border-2 border-[var(--ink)] p-4 mb-4 connector reveal">
        <div class="font-semibold">المندوب</div>
        <p class="text-[var(--muted)] mt-1">يتسلّم الطلب من الشركة ويبدأ التوصيل.</p>
      </div>
      <div class="rounded-2xl bg-[var(--bg-card)] border-2 border-[var(--ink)] p-4 reveal">
        <div class="font-semibold">إتمام</div>
        <p class="text-[var(--muted)] mt-1">التسليم والفوترة — مع تقارير دورية للشركة.</p>
      </div>
    </div>
  </section>

  {{-- أرقام صغيرة (Stats) --}}
  <section class="mt-10">
    <h2 class="text-2xl text-[var(--brand-ink)] mb-4 reveal">لماذا منصّتنا؟</h2>
    <div class="grid sm:grid-cols-3 gap-4">
      <div class="rounded-2xl bg-[var(--bg-card)] border-2 border-[var(--ink)] p-5 text-center reveal">
        <div class="text-3xl text-[var(--brand-ink)]">+10K</div>
        <div class="text-[var(--muted)] mt-1">أدوية ضمن كتالوجات الشركات</div>
      </div>
      <div class="rounded-2xl bg-[var(--bg-card)] border-2 border-[var(--ink)] p-5 text-center reveal">
        <div class="text-3xl text-[var(--brand-ink)]">+500</div>
        <div class="text-[var(--muted)] mt-1">صيدلية تعتمد منصّتنا</div>
      </div>
      <div class="rounded-2xl bg-[var(--bg-card)] border-2 border-[var(--ink)] p-5 text-center reveal">
        <div class="text-3xl text-[var(--brand-ink)]">24/7</div>
        <div class="text-[var(--muted)] mt-1">بحث وطلبات على مدار الساعة</div>
      </div>
    </div>
  </section>

  {{-- CTA --}}
  <section class="mt-10 reveal">
    <div class="rounded-2xl bg-[var(--bg-card)] border-2 border-[var(--ink)] p-6 flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <div class="text-xl text-[var(--brand-ink)]">ابدأ رحلتك الآن</div>
        <p class="text-[var(--muted)] mt-1">دقائق قليلة تفصلك عن إدارة مشترياتك أو كتالوج شركتك.</p>
      </div>
      <div class="flex gap-3">
        @guest
          <a href="{{ route('login') }}" class="btn rounded-xl px-5 h-11 text-base">تسجيل الدخول</a>
        @endguest
        @auth
          @php $role = auth()->user()->role ?? null; @endphp
          @if($role === 'pharmacy')
            <a href="{{ route('pharmacy.search') }}" class="btn rounded-xl px-5 h-11 text-base">ابحث عن الأدوية</a>
          @elseif($role === 'company' && Illuminate\Support\Facades\Route::has('company.groups.index'))
            <a href="{{ route('company.groups.index') }}" class="btn rounded-xl px-5 h-11 text-base">إدارة الكتالوج</a>
          @endif
        @endauth
      </div>
    </div>
  </section>

  {{-- Scroll Reveal --}}
  <script>
    (function () {
      const els = document.querySelectorAll('.reveal');
      const obs = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add('in');
            obs.unobserve(e.target);
          }
        });
      }, { threshold: 0.15 });

      els.forEach(el => obs.observe(el));
    })();
  </script>
@endsection
