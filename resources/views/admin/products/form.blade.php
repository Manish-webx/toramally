@extends('admin.layout', [
    'title' => $isEdit ? 'Edit Silhouette: ' . $product->name : 'Create New Silhouette',
    'header' => $isEdit ? 'Edit Silhouette: ' . $product->name : 'New Atelier Silhouette'
])

@section('content')
<div style="margin-bottom:20px">
    <a href="{{ route('admin.products.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">← Back to Products</a>
</div>

<form action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <!-- Left: Core Information -->
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
                        <label class="form-label-custom">Category *</label>
                        <select name="category" class="form-select-custom" required>
                            <option value="Men" {{ old('category', $product->category)==='Men'?'selected':'' }}>Men</option>
                            <option value="Women" {{ old('category', $product->category)==='Women'?'selected':'' }}>Women</option>
                            <option value="Everyday" {{ old('category', $product->category)==='Everyday'?'selected':'' }}>Everyday</option>
                            <option value="Accessories" {{ old('category', $product->category)==='Accessories'?'selected':'' }}>Accessories</option>
                            <option value="Service" {{ old('category', $product->category)==='Service'?'selected':'' }}>Service</option>
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

                <div id="coloursList">
                    @if($isEdit && $product->colours->count() > 0)
                        @foreach($product->colours as $c)
                            <div class="row g-2 mb-2 align-items-center colour-row">
                                <div class="col-md-6">
                                    <input type="text" name="colour_names[]" class="form-control-custom" value="{{ $c->name }}" placeholder="Colour Name (e.g. Oxblood, Cognac)">
                                </div>
                                <div class="col-md-4">
                                    <input type="color" name="colour_hexes[]" class="form-control-custom" value="{{ $c->hex ?: '#1f1c19' }}" style="height:38px;padding:2px">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="this.closest('.colour-row').remove()" class="btn-atelier btn-atelier-danger btn-atelier-sm">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row g-2 mb-2 align-items-center colour-row">
                            <div class="col-md-6">
                                <input type="text" name="colour_names[]" class="form-control-custom" placeholder="Colour Name (e.g. Cognac)" value="Cognac">
                            </div>
                            <div class="col-md-4">
                                <input type="color" name="colour_hexes[]" class="form-control-custom" value="#8f4b21" style="height:38px;padding:2px">
                            </div>
                            <div class="col-md-2">
                                <button type="button" onclick="this.closest('.colour-row').remove()" class="btn-atelier btn-atelier-danger btn-atelier-sm">Remove</button>
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" onclick="addColourRow()" class="btn-atelier btn-atelier-secondary btn-atelier-sm" style="margin-top:8px">+ Add Variant Colour</button>
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
                    {{ $isEdit ? 'Save Changes' : 'Create Silhouette' }}
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
        <div class="col-md-6">
            <input type="text" name="colour_names[]" class="form-control-custom" placeholder="Colour Name">
        </div>
        <div class="col-md-4">
            <input type="color" name="colour_hexes[]" class="form-control-custom" value="#4b1719" style="height:38px;padding:2px">
        </div>
        <div class="col-md-2">
            <button type="button" onclick="this.closest('.colour-row').remove()" class="btn-atelier btn-atelier-danger btn-atelier-sm">Remove</button>
        </div>
    `;
    list.appendChild(div);
}
</script>
@endpush
@endsection
