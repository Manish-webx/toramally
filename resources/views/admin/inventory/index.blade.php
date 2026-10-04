@extends('admin.layout', ['title' => 'Inventory & Stock Management', 'header' => 'Inventory & Workshop Stock'])

@section('content')
<div class="card-custom">
    <!-- Toolbar Filters -->
    <form action="{{ route('admin.inventory.index') }}" method="GET" class="toolbar-filter">
        <div class="toolbar-group" style="flex:1;max-width:440px">
            <input type="text" name="search" class="form-control-custom" placeholder="Search product name, silhouette, slug..." value="{{ request('search') }}">
        </div>

        <div class="toolbar-group">
            <select name="category" class="form-select-custom" style="width:auto">
                <option value="">All Categories</option>
                <option value="Men" {{ request('category')==='Men'?'selected':'' }}>Men</option>
                <option value="Women" {{ request('category')==='Women'?'selected':'' }}>Women</option>
                <option value="Everyday" {{ request('category')==='Everyday'?'selected':'' }}>Everyday</option>
                <option value="Accessories" {{ request('category')==='Accessories'?'selected':'' }}>Accessories</option>
                <option value="Service" {{ request('category')==='Service'?'selected':'' }}>Service</option>
            </select>

            <select name="availability" class="form-select-custom" style="width:auto">
                <option value="">All Modes</option>
                <option value="Ready to Ship" {{ request('availability')==='Ready to Ship'?'selected':'' }}>Ready to Ship</option>
                <option value="Made to Order" {{ request('availability')==='Made to Order'?'selected':'' }}>Made to Order</option>
                <option value="Commission" {{ request('availability')==='Commission'?'selected':'' }}>Commission</option>
            </select>

            <button type="submit" class="btn-atelier btn-atelier-primary btn-atelier-sm">Filter</button>
            @if(request()->hasAny(['search', 'category', 'availability']))
                <a href="{{ route('admin.inventory.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">Clear</a>
            @endif
        </div>
    </form>

    <!-- Stock Manager Matrix -->
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="min-width:180px">Product &amp; Craft</th>
                    <th>Category</th>
                    <th>Availability</th>
                    <th style="min-width:460px">Size Stock Quantities (Live Adjust)</th>
                    <th>Total Units</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $stocks = $product->stocks->keyBy('size');
                        $sizes = $product->category === 'Women' ? $womenSizes : ($product->silhouette === 'Belt' ? $beltSizes : $standardSizes);
                        if ($product->size_type === 'none') {
                            $sizes = ['One size'];
                        }
                    @endphp
                    <tr id="product-row-{{ $product->id }}">
                        <td>
                            <div style="font-weight:600;font-size:15px;color:var(--green-900)">{{ $product->name }}</div>
                            <div style="font-size:12px;color:var(--muted)">
                                {{ $product->silhouette }} · {{ $product->craft ? $product->craft->name : 'Patina' }}
                            </div>
                            <div style="font-size:11.5px;color:var(--brass-500)">
                                ₹{{ number_format($product->base_price) }}
                            </div>
                        </td>
                        <td>{{ $product->category }}</td>
                        <td>
                            @if($product->availability === 'Ready to Ship')
                                <span class="pill" style="background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0">Ready to Ship</span>
                            @else
                                <span class="pill" style="background:var(--ivory-100)">{{ $product->availability }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;flex-wrap:wrap;gap:8px">
                                @foreach($sizes as $sz)
                                    @php
                                        $curQty = isset($stocks[$sz]) ? (int)$stocks[$sz]->qty : 0;
                                    @endphp
                                    <div class="stock-pill" style="background:var(--ivory-50);border:1px solid var(--line);border-radius:4px;padding:4px 8px;display:inline-flex;align-items:center;gap:6px">
                                        <span style="font-size:11px;font-weight:600;color:var(--muted)">{{ $sz }}:</span>
                                        <span class="qty-val" style="font-weight:600;min-width:18px;text-align:center;{{ $curQty == 0 ? 'color:#C5221F' : ($curQty <= 2 ? 'color:#B06000' : 'color:#137333') }}">
                                            {{ $curQty }}
                                        </span>
                                        <div style="display:inline-flex;gap:2px">
                                            <button type="button" onclick="adjustStock(this, {{ $product->id }}, '{{ addslashes($sz) }}', -1)" style="width:20px;height:20px;line-height:16px;background:#fff;border:1px solid var(--line);border-radius:3px;cursor:pointer;font-size:11px">-</button>
                                            <button type="button" onclick="adjustStock(this, {{ $product->id }}, '{{ addslashes($sz) }}', 1)" style="width:20px;height:20px;line-height:16px;background:var(--green-900);color:#fff;border:0;border-radius:3px;cursor:pointer;font-size:11px">+</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <strong class="total-units-val">{{ $product->stocks->sum('qty') }}</strong>
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">Edit Product</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:36px;color:var(--muted)">
                            No products found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px">
        {{ $products->links() }}
    </div>
</div>

@push('admin-scripts')
<script>
async function adjustStock(btn, productId, size, delta) {
    const pill = btn.closest('.stock-pill');
    if (!pill) return;
    const qtySpan = pill.querySelector('.qty-val');
    if (!qtySpan) return;

    let current = parseInt(qtySpan.textContent.trim()) || 0;
    let newQty = Math.max(0, current + delta);

    // Disable button during request
    btn.disabled = true;

    try {
        const res = await fetch("{{ route('admin.inventory.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                size: size,
                qty: newQty
            })
        });
        const data = await res.json();
        if (data.ok) {
            qtySpan.textContent = data.qty;
            if (data.qty === 0) qtySpan.style.color = "#C5221F";
            else if (data.qty <= 2) qtySpan.style.color = "#B06000";
            else qtySpan.style.color = "#137333";

            // Update row total units in real time
            const row = btn.closest('tr');
            if (row) {
                const totalUnitsSpan = row.querySelector('.total-units-val');
                if (totalUnitsSpan) {
                    let total = 0;
                    row.querySelectorAll('.qty-val').forEach(el => {
                        total += (parseInt(el.textContent.trim()) || 0);
                    });
                    totalUnitsSpan.textContent = total;
                }
            }
        }
    } catch (e) {
        console.error("Failed to update stock", e);
    } finally {
        btn.disabled = false;
    }
}
</script>
@endpush
@endsection
