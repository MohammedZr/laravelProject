@extends('layouts.layout', ['title' => $group->title ?? "مجموعة #{$group->id}"])

@section('content')
  @if (session('status'))
    <div class="mb-4 rounded-lg border border-[var(--line)] bg-green-50 px-4 py-3 text-sm text-green-700">
      {{ session('status') }}
    </div>
  @endif

  <div class="flex flex-col gap-4">
    <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] p-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h1 class="text-xl font-bold text-[var(--brand-ink)]">
            {{ $group->title ?? "مجموعة #{$group->id}" }}
          </h1>
          <p class="text-sm text-[var(--muted)]">الحالة: <b>{{ $group->status }}</b> — تم الإنشاء {{ $group->created_at->diffForHumans() }}</p>
        </div>

        <div class="flex items-center gap-2">
          @if($group->status === 'draft')
            <form method="POST" action="{{ route('company.groups.submit',$group) }}">
              @csrf
              <button class="btn h-10 px-4 rounded-xl bg-blue-600 text-white">إرسال للمراجعة</button>
            </form>
          @endif

          @if(in_array($group->status, ['draft','submitted']))
            <form method="POST" action="{{ route('company.groups.publish',$group) }}">
              @csrf
              <button class="btn h-10 px-4 rounded-xl bg-green-600 text-white">نشر وتفعيل الأدوية</button>
            </form>
          @endif

          @if($group->status !== 'archived')
            <form method="POST" action="{{ route('company.groups.archive',$group) }}">
              @csrf
              <button class="btn h-10 px-4 rounded-xl bg-gray-600 text-white">أرشفة</button>
            </form>
          @endif
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-[var(--line)] bg-[var(--bg-card)] p-4">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-[var(--ink)]">الأدوية في هذه المجموعة</h2>
        <a href="{{ route('company.drugs.create',$group) }}" class="btn px-3 py-2 rounded-xl bg-[var(--brand)] text-white">إضافة دواء</a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-[var(--muted)]">
            <tr class="border-b border-[var(--line)]">
              <th class="py-3 text-right">#</th>
              <th class="py-3 text-right">الاسم التجاري</th>
              <th class="py-3 text-right">المادة الفعّالة</th>
              <th class="py-3 text-right">الشكل/التركيز</th>
              <th class="py-3 text-right">العبوة</th>
              <th class="py-3 text-right">SKU</th>
              <th class="py-3 text-right">السعر</th>
              <th class="py-3 text-right">المخزون</th>
              <th class="py-3 text-right">الحالة</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($group->drugs as $d)
              <tr class="border-b border-[var(--line)]">
                <td class="py-3">{{ $d->id }}</td>
                <td class="py-3 font-medium">{{ $d->name }}</td>
                <td class="py-3">{{ $d->generic_name ?? '—' }}</td>
                <td class="py-3">{{ $d->dosage_form ?? '—' }} — {{ $d->strength ?? '—' }}</td>
                <td class="py-3">{{ $d->pack_size }} {{ $d->unit ?? '' }}</td>
                <td class="py-3">{{ $d->sku ?? '—' }}</td>
                <td class="py-3">{{ number_format($d->price, 2) }}</td>
                <td class="py-3">{{ $d->stock }}</td>
                <td class="py-3">
                  <span class="inline-block rounded-md border px-2 py-0.5 text-xs
                    @class([
                      'bg-green-50 border-green-200 text-green-700' => $d->is_active,
                      'bg-gray-50 border-gray-200 text-gray-600'   => !$d->is_active,
                    ])
                  ">{{ $d->is_active ? 'مفعل' : 'غير مفعل' }}</span>
                </td>
              </tr>
            @empty
              <tr><td colspan="9" class="py-6 text-center text-[var(--muted)]">لا توجد أدوية بعد.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
