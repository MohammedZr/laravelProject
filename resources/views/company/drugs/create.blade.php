@extends('layouts.layout', ['title' => 'إضافة دواء'])

@section('content')
  <h1 class="text-xl font-bold text-[var(--brand-ink)] mb-4">إضافة دواء للمجموعة: {{ $group->title ?? "مجموعة #{$group->id}" }}</h1>

  <form method="POST" action="{{ route('company.drugs.store', $group) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf

    <div>
      <label class="block text-sm mb-1">الاسم التجاري *</label>
      <input type="text" name="name" class="input" value="{{ old('name') }}" required>
      @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">المادة الفعّالة</label>
      <input type="text" name="generic_name" class="input" value="{{ old('generic_name') }}">
      @error('generic_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">الشكل الصيدلاني</label>
      <input type="text" name="dosage_form" class="input" placeholder="Tablet/Capsule/Syrup..." value="{{ old('dosage_form') }}">
      @error('dosage_form') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">التركيز</label>
      <input type="text" name="strength" class="input" placeholder="500mg , 1g/5ml ..." value="{{ old('strength') }}">
      @error('strength') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">حجم العبوة</label>
      <input type="number" min="1" name="pack_size" class="input" value="{{ old('pack_size', 20) }}">
      @error('pack_size') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">الوحدة</label>
      <input type="text" name="unit" class="input" placeholder="tabs/caps/ml..." value="{{ old('unit','tabs') }}">
      @error('unit') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">SKU</label>
      <input type="text" name="sku" class="input" value="{{ old('sku') }}">
      @error('sku') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">الباركود</label>
      <input type="text" name="barcode" class="input" value="{{ old('barcode') }}">
      @error('barcode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">السعر</label>
      <input type="number" step="0.01" name="price" class="input" value="{{ old('price', 10) }}">
      @error('price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">المخزون</label>
      <input type="number" min="0" name="stock" class="input" value="{{ old('stock', 100) }}">
      @error('stock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 flex items-center gap-3">
      <button class="btn px-4 py-2 rounded-xl bg-[var(--brand)] text-white">حفظ الدواء</button>
      <a href="{{ route('company.groups.show', $group) }}" class="text-[var(--muted)] hover:underline">رجوع للمجموعة</a>
    </div>
    <div>
  <label class="block text-sm mb-1">رابط الصورة</label>
  <input type="url" name="image_url" class="input" placeholder="https://...">
  @error('image_url') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

  </form>
@endsection
