@extends('admin.layout', ['title' => 'Products & Silhouettes', 'header' => 'Catalogue & Silhouettes'])

@section('content')
<div class="card-custom">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <form action="{{ route('admin.products.index') }}" method="GET" class="toolbar-group" style="flex:1;max-width:520px">
            <input type="text" name="search" class="form-control-custom" placeholder="Search product name, slug, silhouette..." value="{{ request('search') }}">
            <select name="category" class="form-select-custom" style="width:auto">
                <option value="">All Categories</option>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" {{ request('category') === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                @else
                    <option value="Men" {{ request('category')==='Men'?'selected':'' }}>Men</option>
                    <option value="Women" {{ request('category')==='Women'?'selected':'' }}>Women</option>
                    <option value="Everyday" {{ request('category')==='Everyday'?'selected':'' }}>Everyday</option>
                    <option value="Accessories" {{ request('category')==='Accessories'?'selected':'' }}>Accessories</option>
                    <option value="Service" {{ request('category')==='Service'?'selected':'' }}>Service</option>
                @endif
            </select>
            <button type="submit" class="btn-atelier btn-atelier-primary btn-atelier-sm">Filter</button>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('admin.products.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">Clear</a>
            @endif
        </form>

        <a href="{{ route('admin.products.create') }}" class="btn-atelier btn-atelier-primary">
            + Add New Silhouette
        </a>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Silhouette / Product</th>
                    <th>Category &amp; Line</th>
                    <th>Craft</th>
                    <th>Base Price</th>
                    <th>Availability</th>
                    <th>Colours</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:15px;color:var(--green-900)">{{ $product->name }}</div>
                            <div style="font-size:12px;color:var(--muted)">slug: /shop/.../{{ $product->slug }}</div>
                            @if($product->featured)
                                <span class="pill" style="background:#FEF3C7;color:#92400E;font-size:10px">Featured on Home</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $product->category }}</div>
                            <div style="font-size:12px;color:var(--muted)">{{ $product->silhouette }}</div>
                        </td>
                        <td>
                            <span class="pill" style="background:var(--ivory-100)">
                                {{ $product->craft ? $product->craft->name : 'Patina' }}
                            </span>
                        </td>
                        <td>
                            <strong>₹{{ number_format($product->base_price) }}</strong>
                        </td>
                        <td>
                            @if($product->availability === 'Ready to Ship')
                                <span class="pill" style="background:#D1FAE5;color:#065F46">Ready to Ship</span>
                            @elseif($product->availability === 'Commission')
                                <span class="pill" style="background:#EDE9FE;color:#5B21B6">Commission</span>
                            @else
                                <span class="pill" style="background:var(--ivory-100)">{{ $product->availability }} ({{ $product->lead_min }}-{{ $product->lead_max }}w)</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:4px;flex-wrap:wrap;max-width:120px">
                                @foreach($product->colours as $col)
                                    <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:{{ $col->hex }};border:1px solid rgba(0,0,0,0.15)" title="{{ $col->name }}"></span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            @if($product->status === 'Published')
                                <span class="pill" style="background:#D1FAE5;color:#065F46">Published</span>
                            @else
                                <span class="pill" style="background:#FEE2E2;color:#991B1B">Draft</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:4px">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-atelier btn-atelier-primary btn-atelier-sm">Edit</a>
                                <form action="{{ route('admin.products.toggle', $product->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn-atelier btn-atelier-secondary btn-atelier-sm" title="Toggle Published/Draft">
                                        {{ $product->status === 'Published' ? 'Unpublish' : 'Publish' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:36px;color:var(--muted)">
                            No silhouettes or products found.
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
@endsection
