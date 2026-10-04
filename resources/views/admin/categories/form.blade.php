@extends('admin.layout', [
    'title' => $isEdit ? 'Edit Category: ' . $category->name : 'Create New Category',
    'header' => $isEdit ? 'Edit Category: ' . $category->name : 'New Atelier Category'
])

@section('content')
<div style="margin-bottom:20px">
    <a href="{{ route('admin.categories.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">← Back to Categories</a>
</div>

<form action="{{ $isEdit ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <!-- Left: Category Core Details -->
        <div class="col-12 col-lg-8">
            <div class="card-custom">
                <div class="card-title">Category Information</div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-custom">Category Name *</label>
                        <input type="text" id="categoryName" name="name" class="form-control-custom" value="{{ old('name', $category->name) }}" required placeholder="e.g. Men, Women, Boots, Belts, Leather Goods">
                        <small style="font-size:11px;color:var(--muted)">Display name shown across catalogue filters and boutique headers.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">URL Slug</label>
                        <input type="text" id="categorySlug" name="slug" class="form-control-custom" value="{{ old('slug', $category->slug) }}" placeholder="e.g. boots, men, leather-goods">
                        <small style="font-size:11px;color:var(--muted)">URL segment for the shop (e.g. /shop/<code>men</code>). Auto-generated if left blank.</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Description &amp; Atelier Tagline</label>
                        <textarea name="description" rows="4" class="form-control-custom" placeholder="Short description of this category, crafting notes, or collection summary...">{{ old('description', $category->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Settings & Status -->
        <div class="col-12 col-lg-4">
            <div class="card-custom">
                <div class="card-title">Status &amp; Ordering</div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-custom">Sort Priority</label>
                        <input type="number" name="sort" class="form-control-custom" value="{{ old('sort', $category->sort ?? 0) }}" placeholder="0">
                        <small style="font-size:11px;color:var(--muted)">Lower numbers appear first in menu dropdowns and shop filters.</small>
                    </div>

                    <div class="col-12" style="margin-top:10px">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;user-select:none">
                            <input type="checkbox" name="active" value="1" {{ old('active', $category->active ?? true) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--green-900)">
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--green-900)">Active in Boutique</div>
                                <div style="font-size:11.5px;color:var(--muted)">When checked, this category is visible in product assignments and navigation.</div>
                            </div>
                        </label>
                    </div>

                    @if($isEdit)
                        <div class="col-12" style="padding-top:14px;border-top:1px solid var(--line)">
                            <div style="font-size:11.5px;color:var(--muted)">
                                <strong>Attached Products:</strong> {{ $category->products_count ?? $category->products()->count() }} silhouettes
                            </div>
                        </div>
                    @endif
                </div>

                <div style="margin-top:24px;display:flex;gap:8px">
                    <button type="submit" class="btn-atelier btn-atelier-primary" style="flex:1;justify-content:center">
                        {{ $isEdit ? 'Update Category' : 'Save Category' }}
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn-atelier btn-atelier-secondary">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('admin-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('categoryName');
        const slugInput = document.getElementById('categorySlug');
        const isEdit = {{ $isEdit ? 'true' : 'false' }};

        if (!isEdit && nameInput && slugInput) {
            nameInput.addEventListener('input', function() {
                if (!slugInput.dataset.touched) {
                    slugInput.value = this.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                }
            });

            slugInput.addEventListener('input', function() {
                slugInput.dataset.touched = 'true';
            });
        }
    });
</script>
@endpush
@endsection
