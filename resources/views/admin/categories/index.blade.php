@extends('admin.layout', ['title' => 'Categories Management', 'header' => 'Catalogue Categories'])

@section('content')
<div class="card-custom">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="toolbar-group" style="flex:1;max-width:540px">
            <input type="text" name="search" class="form-control-custom" placeholder="Search category name, slug, notes..." value="{{ request('search') }}">
            <select name="status" class="form-select-custom" style="width:auto">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn-atelier btn-atelier-primary btn-atelier-sm">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.categories.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">Clear</a>
            @endif
        </form>

        <a href="{{ route('admin.categories.create') }}" class="btn-atelier btn-atelier-primary">
            + Add New Category
        </a>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width:50px">Sort</th>
                    <th>Category Name</th>
                    <th>Slug &amp; Shop URL</th>
                    <th>Description</th>
                    <th style="text-align:center">Products</th>
                    <th>Status</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td style="color:var(--muted);font-weight:600">
                            {{ $cat->sort }}
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:15px;color:var(--green-900)">
                                {{ $cat->name }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size:13px;font-family:monospace;color:var(--charcoal)">
                                <code>{{ $cat->slug }}</code>
                            </div>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px">
                                @if($cat->name === 'Service')
                                    <a href="{{ url('shoe-shine-service') }}" target="_blank" style="color:var(--brass-600);text-decoration:none">/shoe-shine-service ↗</a>
                                @else
                                    <a href="{{ url('shop/' . $cat->slug) }}" target="_blank" style="color:var(--brass-600);text-decoration:none">/shop/{{ $cat->slug }} ↗</a>
                                @endif
                            </div>
                        </td>
                        <td style="max-width:280px;font-size:12.5px;color:var(--muted)">
                            {{ $cat->description ?: '—' }}
                        </td>
                        <td style="text-align:center">
                            <a href="{{ route('admin.products.index', ['category' => $cat->name]) }}" class="pill" style="background:var(--ivory-100);color:var(--charcoal);text-decoration:none" title="Filter products in this category">
                                {{ $cat->products_count }} {{ Str::plural('product', $cat->products_count) }}
                            </a>
                        </td>
                        <td>
                            @if($cat->active)
                                <span class="pill pill-paid">Active</span>
                            @else
                                <span class="pill pill-unpaid">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <div style="display:inline-flex;gap:6px;justify-content:flex-end">
                                <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn-atelier btn-atelier-primary btn-atelier-sm">
                                    Edit
                                </a>
                                <form action="{{ route('admin.categories.toggle', $cat->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn-atelier btn-atelier-secondary btn-atelier-sm" title="Toggle active status">
                                        {{ $cat->active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                @if($cat->products_count === 0)
                                    <form action="{{ route('admin.categories.delete', $cat->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete category \'{{ $cat->name }}\'?');">
                                        @csrf
                                        <button type="submit" class="btn-atelier btn-atelier-danger btn-atelier-sm" title="Delete Category">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:36px;color:var(--muted)">
                            No categories found matching your query.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px">
        {{ $categories->links() }}
    </div>
</div>
@endsection
