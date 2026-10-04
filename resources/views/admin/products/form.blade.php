@extends('admin.layout', [
    'title' => $isEdit ? 'Edit Silhouette: ' . $product->name : 'Create New Silhouette',
    'header' => $isEdit ? 'Edit Silhouette: ' . $product->name : 'New Atelier Silhouette'
])

@section('content')
<div style="margin-bottom:20px">
    <a href="{{ route('admin.products.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">← Back to Products</a>
</div>

<form action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Left: Core Information & Imagery -->
        <div class="col-12 col-lg-8">
            <div class="card-custom">
                <div class="card-title">Core Silhouette Details</div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-custom">Silhouette Name *</label>
                        <input type="text" name="name" class="form-control-custom" value="{{ old('name', $product->name) }}" required placeholder="e.g. Taus, Noor, Adab">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">URL Slug *</label>
                        <input type="text" name="slug" class="form-control-custom" value="{{ old('slug', $product->slug) }}" required placeholder="e.g. taus">
                    </div>

                    <div class="col-md-4">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <label class="form-label-custom">Category *</label>
                            <a href="{{ route('admin.categories.create') }}" target="_blank" style="font-size:11px;color:var(--brass-600);text-decoration:none" title="Create a new category in a new tab">+ New Category</a>
                        </div>
                        <select name="category" class="form-select-custom" required>
                            @php
                                $currentCat = old('category', $product->category);
                                $hasCustom = $currentCat && !in_array($currentCat, $categories->pluck('name')->all());
                            @endphp
                            @if($hasCustom)
                                <option value="{{ $currentCat }}" selected>{{ $currentCat }} (Custom)</option>
                            @endif
                            @forelse($categories as $cat)
                                <option value="{{ $cat->name }}" {{ $currentCat === $cat->name ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @empty
                                <option value="Men" {{ $currentCat === 'Men' ? 'selected' : '' }}>Men</option>
                                <option value="Women" {{ $currentCat === 'Women' ? 'selected' : '' }}>Women</option>
                                <option value="Everyday" {{ $currentCat === 'Everyday' ? 'selected' : '' }}>Everyday</option>
                                <option value="Accessories" {{ $currentCat === 'Accessories' ? 'selected' : '' }}>Accessories</option>
                                <option value="Service" {{ $currentCat === 'Service' ? 'selected' : '' }}>Service</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Line / Style Tag</label>
                        <input type="text" name="line" class="form-control-custom" value="{{ old('line', $product->line) }}" placeholder="e.g. Classic, Special Occasion">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Silhouette Type *</label>
                        <input type="text" name="silhouette" class="form-control-custom" value="{{ old('silhouette', $product->silhouette) }}" required placeholder="e.g. Belgian loafer, Oxford, Mule, Belt">
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Poetic Dek / One-liner</label>
                        <input type="text" name="poetic" class="form-control-custom" value="{{ old('poetic', $product->poetic) }}" placeholder="e.g. A peacock in miniature, painted across the vamp.">
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">Craft Description &amp; House Story</label>
                        <textarea name="story" rows="4" class="form-control-custom" placeholder="Detailed provenance, atelier making notes, polish notes...">{{ old('story', $product->story) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Craft & Construction -->
            <div class="card-custom">
                <div class="card-title">Craftsmanship &amp; Construction</div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Craft Ladder Level</label>
                        <select name="craft_id" class="form-select-custom">
                            <option value="">Select Craft Level</option>
                            @foreach($crafts as $craft)
                                <option value="{{ $craft->id }}" {{ old('craft_id', $product->craft_id)==$craft->id?'selected':'' }}>
                                    {{ $craft->name }} ({{ $craft->scale_word }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Construction</label>
                        <input type="text" name="construction" class="form-control-custom" value="{{ old('construction', $product->construction) }}" placeholder="e.g. Blake stitched, Goodyear welted, Cemented">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Upper Material</label>
                        <input type="text" name="material" class="form-control-custom" value="{{ old('material', $product->material) }}" placeholder="e.g. Crust Calf, Velvet">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Last Shape Name</label>
                        <input type="text" name="last_name" class="form-control-custom" value="{{ old('last_name', $product->last_name) }}" placeholder="e.g. LOBO, RAY, 031">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Occasions Tag</label>
                        <input type="text" name="occasions" class="form-control-custom" value="{{ old('occasions', $product->occasions) }}" placeholder="Wedding, Evening, Everyday">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">HSN / Tax Code</label>
                        <input type="text" name="hsn_code" class="form-control-custom" value="{{ old('hsn_code', $product->hsn_code) }}" placeholder="6403">
                    </div>
                </div>
            </div>

            <!-- Variant Colours -->
            <div class="card-custom">
                <div class="card-title">Colour Palette Variants</div>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px">Define the available colourways for this silhouette. You can attach photos to each colour variant below.</p>

                <div id="coloursList">
                    @if($isEdit && $product->colours->count() > 0)
                        @foreach($product->colours as $c)
                            <div class="row g-2 mb-2 align-items-center colour-row">
                                <input type="hidden" name="colour_ids[]" value="{{ $c->id }}">
                                <div class="col-md-6">
                                    <input type="text" name="colour_names[]" class="form-control-custom colour-name-input" value="{{ $c->name }}" placeholder="Colour Name (e.g. Oxblood, Cognac)" oninput="syncColourDropdowns()">
                                </div>
                                <div class="col-md-4">
                                    <input type="color" name="colour_hexes[]" class="form-control-custom" value="{{ $c->hex ?: '#1f1c19' }}" style="height:38px;padding:2px">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="this.closest('.colour-row').remove(); syncColourDropdowns();" class="btn-atelier btn-atelier-danger btn-atelier-sm">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row g-2 mb-2 align-items-center colour-row">
                            <input type="hidden" name="colour_ids[]" value="">
                            <div class="col-md-6">
                                <input type="text" name="colour_names[]" class="form-control-custom colour-name-input" placeholder="Colour Name (e.g. Cognac)" value="Cognac" oninput="syncColourDropdowns()">
                            </div>
                            <div class="col-md-4">
                                <input type="color" name="colour_hexes[]" class="form-control-custom" value="#8f4b21" style="height:38px;padding:2px">
                            </div>
                            <div class="col-md-2">
                                <button type="button" onclick="this.closest('.colour-row').remove(); syncColourDropdowns();" class="btn-atelier btn-atelier-danger btn-atelier-sm">Remove</button>
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" onclick="addColourRow()" class="btn-atelier btn-atelier-secondary btn-atelier-sm" style="margin-top:8px">+ Add Variant Colour</button>
            </div>

            <!-- Product Photography & Variant Images -->
            <div class="card-custom">
                <div class="card-title">Product Photography &amp; Variant Images</div>
                <p style="font-size:12px;color:var(--muted);margin-bottom:16px">
                    Upload photos for this product and assign each photo to a specific <strong>Colour Variant</strong> (e.g. Cognac, Dark Brown, Black) or <strong>General (All Variants)</strong>. When a customer selects a colour on the product page, the gallery automatically switches to that colour variant's photo.
                </p>

                @if($isEdit && $product->images && $product->images->count() > 0)
                    <div style="margin-bottom:20px">
                        <div style="font-weight:600;font-size:13px;color:var(--green-900);margin-bottom:10px">Current Photos ({{ $product->images->count() }})</div>
                        <div class="table-responsive">
                            <table class="custom-table" style="font-size:12.5px">
                                <thead>
                                    <tr>
                                        <th style="width:70px">Photo</th>
                                        <th>Variant Colour</th>
                                        <th>View Angle</th>
                                        <th>Alt Text</th>
                                        <th style="width:60px">Sort</th>
                                        <th style="text-align:right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->images as $img)
                                        <tr style="vertical-align:middle">
                                            <td>
                                                <input type="hidden" name="existing_image_ids[]" value="{{ $img->id }}">
                                                <img src="{{ asset($img->path) }}" alt="{{ $img->alt }}" style="width:54px;height:54px;object-fit:cover;border-radius:4px;border:1px solid var(--line)">
                                            </td>
                                            <td>
                                                <select name="existing_image_colours[{{ $img->id }}]" class="form-select-custom existing-colour-select" style="font-size:12px;padding:4px 8px">
                                                    <option value="*" {{ empty($img->colour_id) ? 'selected' : '' }}>General (All Colours)</option>
                                                    @foreach($product->colours as $c)
                                                        <option value="{{ $c->id }}" {{ $img->colour_id == $c->id ? 'selected' : '' }}>
                                                            Variant: {{ $c->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="existing_image_kinds[{{ $img->id }}]" class="form-select-custom" style="font-size:12px;padding:4px 8px">
                                                    <option value="side" {{ in_array($img->kind, ['side', 'hero']) ? 'selected' : '' }}>Main Side View (Hero)</option>
                                                    <option value="angle" {{ $img->kind === 'angle' ? 'selected' : '' }}>Angle Perspective</option>
                                                    <option value="macro" {{ $img->kind === 'macro' ? 'selected' : '' }}>Macro Craft Detail</option>
                                                    <option value="box" {{ $img->kind === 'box' ? 'selected' : '' }}>Presentation Box</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="existing_image_alts[{{ $img->id }}]" value="{{ $img->alt }}" class="form-control-custom" style="font-size:12px;padding:4px 8px" placeholder="Image description">
                                            </td>
                                            <td>
                                                <input type="number" name="existing_image_sorts[{{ $img->id }}]" value="{{ $img->sort }}" class="form-control-custom" style="font-size:12px;padding:4px 8px;width:50px">
                                            </td>
                                            <td style="text-align:right">
                                                <label style="display:inline-flex;align-items:center;gap:4px;color:#C5221F;font-size:12px;cursor:pointer">
                                                    <input type="checkbox" name="delete_image_ids[]" value="{{ $img->id }}">
                                                    Delete
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Upload New Images -->
                <div style="background:var(--ivory-50);border:1px dashed var(--brass-500);border-radius:6px;padding:16px;margin-top:10px">
                    <div style="font-weight:600;font-size:13px;color:var(--green-900);margin-bottom:10px;display:flex;justify-content:space-between;align-items:center">
                        <span>+ Upload New Images / Variant Photos</span>
                        <button type="button" onclick="addNewImageRow()" class="btn-atelier btn-atelier-primary btn-atelier-sm">+ Add Image File</button>
                    </div>

                    <div id="newImagesList">
                        <div class="new-image-row" style="background:#fff;border:1px solid var(--line);border-radius:6px;padding:12px;margin-bottom:10px">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label-custom" style="font-size:11px">Select Photo File *</label>
                                    <input type="file" name="new_images[]" class="form-control-custom image-file-input" accept="image/*" onchange="previewImageFile(this)">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom" style="font-size:11px">Variant Colour</label>
                                    <select name="new_image_colours[]" class="form-select-custom variant-colour-select" style="font-size:12px">
                                        <option value="*">General (All Colours)</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label-custom" style="font-size:11px">View Angle</label>
                                    <select name="new_image_kinds[]" class="form-select-custom" style="font-size:12px">
                                        <option value="side">Main Side (Hero)</option>
                                        <option value="angle">Angle View</option>
                                        <option value="macro">Macro Craft Detail</option>
                                        <option value="box">Box View</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label-custom" style="font-size:11px">Alt Text</label>
                                    <input type="text" name="new_image_alts[]" class="form-control-custom" placeholder="e.g. Noor in Cognac" style="font-size:12px">
                                </div>
                                <div class="col-md-1 text-end" style="padding-top:16px">
                                    <button type="button" onclick="this.closest('.new-image-row').remove()" class="btn-atelier btn-atelier-danger btn-atelier-sm" title="Remove row">✕</button>
                                </div>
                                <div class="col-12 image-preview-box" style="display:none;margin-top:6px">
                                    <img src="" style="height:60px;border-radius:4px;border:1px solid var(--line)" alt="Preview">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Pricing, Availability & Status -->
        <div class="col-12 col-lg-4">
            <div class="card-custom">
                <div class="card-title">Pricing &amp; Lead Time</div>

                <div class="mb-3">
                    <label class="form-label-custom">Base Price (INR) *</label>
                    <input type="number" name="base_price" class="form-control-custom" value="{{ old('base_price', $product->base_price) }}" required placeholder="23000">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Availability Mode *</label>
                    <select name="availability" class="form-select-custom" required>
                        <option value="Ready to Ship" {{ old('availability', $product->availability)==='Ready to Ship'?'selected':'' }}>Ready to Ship</option>
                        <option value="Made to Order" {{ old('availability', $product->availability)==='Made to Order'?'selected':'' }}>Made to Order</option>
                        <option value="Commission" {{ old('availability', $product->availability)==='Commission'?'selected':'' }}>Commission (Custom quote)</option>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label-custom">Min Weeks *</label>
                        <input type="number" name="lead_min" class="form-control-custom" value="{{ old('lead_min', $product->lead_min ?? 5) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label-custom">Max Weeks *</label>
                        <input type="number" name="lead_max" class="form-control-custom" value="{{ old('lead_max', $product->lead_max ?? 7) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Status *</label>
                    <select name="status" class="form-select-custom" required>
                        <option value="Published" {{ old('status', $product->status)==='Published'?'selected':'' }}>Published (Live in Boutique)</option>
                        <option value="Draft" {{ old('status', $product->status)==='Draft'?'selected':'' }}>Draft (Hidden)</option>
                        <option value="Archived" {{ old('status', $product->status)==='Archived'?'selected':'' }}>Archived</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                        <input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured)?'checked':'' }}>
                        <strong>Feature on Homepage Gallery</strong>
                    </label>
                </div>

                <button type="submit" class="btn-atelier btn-atelier-primary full" style="width:100%;justify-content:center;padding:12px">
                    {{ $isEdit ? 'Save Changes & Photos' : 'Create Silhouette & Upload Photos' }}
                </button>
            </div>
        </div>
    </div>
</form>

@push('admin-scripts')
<script>
function addColourRow() {
    const list = document.getElementById('coloursList');
    const div = document.createElement('div');
    div.className = 'row g-2 mb-2 align-items-center colour-row';
    div.innerHTML = `
        <input type="hidden" name="colour_ids[]" value="">
        <div class="col-md-6">
            <input type="text" name="colour_names[]" class="form-control-custom colour-name-input" placeholder="Colour Name" oninput="syncColourDropdowns()">
        </div>
        <div class="col-md-4">
            <input type="color" name="colour_hexes[]" class="form-control-custom" value="#4b1719" style="height:38px;padding:2px">
        </div>
        <div class="col-md-2">
            <button type="button" onclick="this.closest('.colour-row').remove(); syncColourDropdowns();" class="btn-atelier btn-atelier-danger btn-atelier-sm">Remove</button>
        </div>
    `;
    list.appendChild(div);
    syncColourDropdowns();
}

function getActiveColourNames() {
    const inputs = document.querySelectorAll('.colour-name-input');
    const names = [];
    inputs.forEach(input => {
        const val = input.value.trim();
        if (val && !names.includes(val)) {
            names.push(val);
        }
    });
    return names;
}

function syncColourDropdowns() {
    const names = getActiveColourNames();
    const selects = document.querySelectorAll('.variant-colour-select');

    selects.forEach(select => {
        const currentVal = select.value;
        let html = '<option value="*">General (All Colours)</option>';
        names.forEach(name => {
            html += `<option value="${name}" ${currentVal === name ? 'selected' : ''}>Variant: ${name}</option>`;
        });
        select.innerHTML = html;
    });
}

function addNewImageRow() {
    const list = document.getElementById('newImagesList');
    const div = document.createElement('div');
    div.className = 'new-image-row';
    div.style = 'background:#fff;border:1px solid var(--line);border-radius:6px;padding:12px;margin-bottom:10px';
    div.innerHTML = `
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label-custom" style="font-size:11px">Select Photo File *</label>
                <input type="file" name="new_images[]" class="form-control-custom image-file-input" accept="image/*" onchange="previewImageFile(this)">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom" style="font-size:11px">Variant Colour</label>
                <select name="new_image_colours[]" class="form-select-custom variant-colour-select" style="font-size:12px">
                    <option value="*">General (All Colours)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-custom" style="font-size:11px">View Angle</label>
                <select name="new_image_kinds[]" class="form-select-custom" style="font-size:12px">
                    <option value="side">Main Side (Hero)</option>
                    <option value="angle">Angle View</option>
                    <option value="macro">Macro Craft Detail</option>
                    <option value="box">Box View</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-custom" style="font-size:11px">Alt Text</label>
                <input type="text" name="new_image_alts[]" class="form-control-custom" placeholder="e.g. In Dark Brown" style="font-size:12px">
            </div>
            <div class="col-md-1 text-end" style="padding-top:16px">
                <button type="button" onclick="this.closest('.new-image-row').remove()" class="btn-atelier btn-atelier-danger btn-atelier-sm" title="Remove row">✕</button>
            </div>
            <div class="col-12 image-preview-box" style="display:none;margin-top:6px">
                <img src="" style="height:60px;border-radius:4px;border:1px solid var(--line)" alt="Preview">
            </div>
        </div>
    `;
    list.appendChild(div);
    syncColourDropdowns();
}

function previewImageFile(input) {
    const row = input.closest('.new-image-row');
    const previewBox = row.querySelector('.image-preview-box');
    const previewImg = previewBox.querySelector('img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewBox.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        previewBox.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    syncColourDropdowns();
});
</script>
@endpush
@endsection
