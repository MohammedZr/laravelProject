@forelse($notes as $note)
  @php $data = $note->data ?? []; @endphp
  <a href="{{ $data['url'] ?? '#' }}" class="block px-3 py-2 hover:bg-gray-50">
    <div class="text-sm font-semibold">{{ $data['title'] ?? 'إشعار' }}</div>
    <div class="text-sm text-gray-600">{{ $data['body']  ?? '' }}</div>
    <div class="text-xs text-gray-400 mt-0.5">{{ $note->created_at->diffForHumans() }}</div>
  </a>
@empty
  <div class="px-3 py-3 text-sm text-gray-500">لا توجد إشعارات.</div>
@endforelse
