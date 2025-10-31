@php
  $guard = $guard ?? null;
  $user  = $guard ? Auth::guard($guard)->user() : auth()->user();
  $count = $user ? $user->unreadNotifications()->count() : 0;
@endphp

@if($user)
  <div class="relative">
    <button id="notifBellBtn" class="relative inline-flex items-center gap-2 px-2 py-1">
      <span>🔔</span>
      <span id="notifBadge" class="{{ $count ? '' : 'hidden' }} inline-flex items-center justify-center min-w-5 h-5 text-xs px-1.5 rounded-full bg-red-600 text-white">
        {{ $count }}
      </span>
    </button>

    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 rounded-xl bg-white shadow-xl border p-2 z-50">
      <div class="flex items-center justify-between px-2 py-1">
        <strong>الإشعارات</strong>
        <form method="POST" action="{{ route($guard.'.notifications.readAll') }}">
          @csrf
          <button class="text-sm text-blue-600 hover:underline">تمييز الكل مقروء</button>
        </form>
      </div>
      <div id="notifList" class="max-h-80 overflow-auto divide-y">
        <div class="px-3 py-3 text-sm text-gray-500">جاري التحميل…</div>
      </div>
    </div>
  </div>

  <audio id="notifSound" src="/sounds/notify.mp3" preload="auto"></audio>

  <script>
    (function () {
      const guard    = @json($guard);
      const bellBtn  = document.getElementById('notifBellBtn');
      const dropdown = document.getElementById('notifDropdown');
      const badge    = document.getElementById('notifBadge');
      const audio    = document.getElementById('notifSound');
      const listBox  = document.getElementById('notifList');

      bellBtn?.addEventListener('click', () => dropdown.classList.toggle('hidden'));
      document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) dropdown.classList.add('hidden');
      });

      if (window.Notification && Notification.permission === 'default') Notification.requestPermission();

      refreshList();

      let lastCount = {{ (int) $count }};
      setInterval(() => {
        fetch(`{{ route($guard.'.notifications.unreadCount') }}`, {cache: 'no-store'})
          .then(r => r.json())
          .then(({count}) => {
            if (count > 0) { badge.textContent = count; badge.classList.remove('hidden'); }
            else { badge.classList.add('hidden'); }

            if (count > lastCount) {
              audio?.play().catch(()=>{});
              if (window.Notification && Notification.permission === 'granted') {
                new Notification('🔔 إشعار جديد', { body: 'وصلك إشعار جديد', icon: '/favicon.ico' });
              }
              refreshList();
            }
            lastCount = count;
          })
          .catch(()=>{});
      }, 10000);

      function refreshList() {
        fetch(`{{ route($guard.'.notifications.list') }}`, {cache: 'no-store'})
          .then(r => r.text())
          .then(html => listBox.innerHTML = html)
          .catch(() => listBox.innerHTML = '<div class="px-3 py-3 text-sm text-gray-500">تعذّر التحميل.</div>');
      }
    })();
  </script>
@endif
