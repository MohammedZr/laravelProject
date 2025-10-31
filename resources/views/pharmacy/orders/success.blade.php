@extends('layouts.layout', ['title' => $title])

@section('content')
<div class="text-center py-10">
  <div class="text-5xl mb-4">✅</div>
  <h1 class="text-2xl font-bold text-[var(--brand-ink)] mb-2">
    تم إرسال الطلب بنجاح!
  </h1>
  <p class="text-[var(--muted)] mb-6">
    شكراً لك، تم استلام طلبك بنجاح وسيتم مراجعته من قبل الشركة قريباً.
  </p>

  <audio id="success-sound" src="/sounds/notify.wav" preload="auto"></audio>

  <div class="flex justify-center gap-3">
    <a href="{{ route('pharmacy.orders.index') }}" class="btn btn-outline">📋 عرض كل الطلبات</a>
    <a href="{{ route('pharmacy.orders.show', $order) }}" class="btn">🔍 عرض هذا الطلب</a>
  </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', () => {
  const audio = document.getElementById('success-sound');
  // محاولة تشغيل الصوت تلقائيًا بعد تفاعل المستخدم الأول
  const playSound = () => {
    audio.play().catch(() => {});
    window.removeEventListener('click', playSound);
  };
  window.addEventListener('click', playSound);
  setTimeout(() => audio.play().catch(()=>{}), 300);
});
</script>
@endsection
