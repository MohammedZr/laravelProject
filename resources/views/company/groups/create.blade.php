@extends('layouts.layout', ['title' => 'إنشاء مجموعة'])

@section('content')
  <h1 class="text-xl font-bold text-[var(--brand-ink)] mb-4">إنشاء درفت/مسودة</h1>

  <form method="POST" action="{{ route('company.groups.store') }}" class="space-y-4">
    @csrf

    <div>
      <label class="block text-sm mb-1">عنوان المسودة</label>
      <input type="text" name="title" class="input" placeholder="مثال: مسودة أكتوبر" value="{{ old('title') }}">
      @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">ملاحظات</label>
      <textarea name="notes" rows="4" class="input" placeholder="ملاحظات داخلية عن الدفعة...">{{ old('notes') }}</textarea>
      @error('notes') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3">
      <button class="btn px-4 py-2 rounded-xl bg-[var(--brand)] text-white">إنشاء</button>
      <a href="{{ route('company.groups.index') }}" class="text-[var(--muted)] hover:underline">إلغاء</a>
    </div>
  </form>
@endsection
