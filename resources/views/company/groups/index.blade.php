@extends('layouts.layout', ['title' => 'مجموعات الأدوية'])

@section('content')
  @if (session('status'))
    <div class="mb-4 rounded-lg border border-[var(--line)] bg-green-50 px-4 py-3 text-sm text-green-700">
      {{ session('status') }}
    </div>
  @endif

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold text-[var(--brand-ink)]">مجموعات الأدوية</h1>
    <a href="{{ route('company.groups.create') }}" class="btn px-4 py-2 rounded-xl bg-[var(--brand)] text-white">إنشاء مجموعة</a>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="text-[var(--muted)]">
        <tr class="border-b border-[var(--line)]">
          <th class="py-3 text-right">#</th>
          <th class="py-3 text-right">العنوان</th>
          <th class="py-3 text-right">الحالة</th>
          <th class="py-3 text-right">عدد الأدوية</th>
          <th class="py-3 text-right">آخر تحديث</th>
          <th class="py-3 text-right">إجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($groups as $g)
          <tr class="border-b border-[var(--line)]">
            <td class="py-3">{{ $g->id }}</td>
            <td class="py-3">{{ $g->title ?? '—' }}</td>
            <td class="py-3">
              <span class="inline-block rounded-md border px-2 py-0.5 text-xs
                @class([
                  'bg-yellow-50 border-yellow-200 text-yellow-700' => $g->status === 'draft',
                  'bg-blue-50 border-blue-200 text-blue-700'       => $g->status === 'submitted',
                  'bg-green-50 border-green-200 text-green-700'    => $g->status === 'published',
                  'bg-gray-50 border-gray-200 text-gray-600'       => $g->status === 'archived',
                ])
              ">{{ __("{$g->status}") }}</span>
            </td>
            <td class="py-3">{{ $g->drugs()->count() }}</td>
            <td class="py-3">{{ $g->updated_at?->diffForHumans() }}</td>
            <td class="py-3">
              <a href="{{ route('company.groups.show',$g) }}" class="text-[var(--brand-ink)] hover:underline">عرض</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="py-6 text-center text-[var(--muted)]">لا توجد مجموعات بعد.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $groups->links() }}</div>
@endsection
