<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Atelier Management' }} | Tōramally Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" rel="stylesheet">

    <style>
        :root {
            --green-900: #1F3D2B;
            --green-800: #294D37;
            --green-700: #38644A;
            --brass-500: #9C7A3C;
            --brass-600: #84652e;
            --brass-100: #F4EDE0;
            --ivory-50: #FAF8F5;
            --ivory-100: #F3EFE6;
            --ivory-200: #E7E0D2;
            --charcoal: #1E1E1E;
            --muted: #6B6862;
            --line: rgba(31, 61, 43, 0.12);
            --line-strong: rgba(31, 61, 43, 0.22);
            --sidebar-w: 260px;
            --serif: 'Cormorant Garamond', Georgia, serif;
            --sans: 'Jost', system-ui, -apple-system, sans-serif;
            --card-shadow: 0 4px 20px rgba(31, 61, 43, 0.05);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--sans);
            background: var(--ivory-50);
            color: var(--charcoal);
            font-size: 14px;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
        }

        /* Layout Structure */
        .admin-sidebar {
            width: var(--sidebar-w);
            background: var(--green-900);
            color: var(--ivory-100);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            transition: transform .3s ease;
        }

        .admin-main {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-w));
        }

        /* Sidebar Elements */
        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar-brand h1 {
            font-family: var(--serif);
            font-size: 24px;
            letter-spacing: .08em;
            color: var(--ivory-50);
            margin: 0;
            font-weight: 500;
        }
        .sidebar-brand .badge-tag {
            font-size: 10px;
            letter-spacing: .12em;
            text-transform: uppercase;
            padding: 3px 6px;
            background: rgba(156, 122, 60, 0.25);
            color: #E2C285;
            border-radius: 3px;
            border: 1px solid rgba(156, 122, 60, 0.4);
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
            overflow-y: auto;
        }
        .nav-section {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .15em;
            color: rgba(243, 239, 230, 0.45);
            padding: 12px 10px 6px;
            font-weight: 600;
        }
        .nav-link-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 12px;
            color: rgba(243, 239, 230, 0.82);
            text-decoration: none;
            border-radius: 6px;
            font-size: 13.5px;
            font-weight: 400;
            margin-bottom: 3px;
            transition: all .2s ease;
        }
        .nav-link-item:hover {
            background: rgba(255, 255, 255, 0.07);
            color: #fff;
        }
        .nav-link-item.active {
            background: rgba(156, 122, 60, 0.22);
            color: #E2C285;
            font-weight: 500;
            border: 1px solid rgba(156, 122, 60, 0.35);
        }
        .nav-link-item .icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 11px;
            width: 18px;
            height: 18px;
            opacity: .85;
            flex-shrink: 0;
        }
        .nav-link-item .icon svg {
            width: 16px !important;
            height: 16px !important;
            stroke: currentColor;
            display: block;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .user-info .name {
            font-weight: 500;
            font-size: 13px;
            color: var(--ivory-50);
        }
        .user-info .role {
            font-size: 11px;
            color: var(--brass-500);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        /* Top Bar */
        .admin-topbar {
            height: 64px;
            background: #fff;
            border-bottom: 1px solid var(--line);
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 90;
        }
        .topbar-title {
            font-family: var(--serif);
            font-size: 22px;
            font-weight: 500;
            color: var(--green-900);
            margin: 0;
        }
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Content Area */
        .admin-content {
            padding: 28px;
            flex: 1;
        }

        /* Cards & UI Elements */
        .card-custom {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
        }
        .card-custom .card-title {
            font-family: var(--serif);
            font-size: 19px;
            font-weight: 500;
            color: var(--green-900);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Metric KPI Card */
        .stat-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--brass-500);
        }
        .stat-card.green::before { background: var(--green-700); }
        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 6px;
        }
        .stat-value {
            font-family: var(--serif);
            font-size: 30px;
            font-weight: 600;
            color: var(--green-900);
            line-height: 1.1;
        }
        .stat-sub {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .custom-table th {
            background: var(--ivory-100);
            color: var(--charcoal);
            text-align: left;
            padding: 11px 14px;
            font-weight: 600;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: .08em;
            border-bottom: 1px solid var(--line);
        }
        .custom-table td {
            padding: 13px 14px;
            border-bottom: 1px solid var(--line);
            vertical-align: middle;
        }
        .custom-table tr:hover td {
            background: rgba(243, 239, 230, 0.35);
        }

        /* Badges & Pills */
        .pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .05em;
        }
        .pill-placed { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
        .pill-confirmed { background: #DBEAFE; color: #1E40AF; border: 1px solid #BFDBFE; }
        .pill-crafting { background: #EDE9FE; color: #5B21B6; border: 1px solid #DDD6FE; }
        .pill-shipped { background: #E0E7FF; color: #3730A3; border: 1px solid #C7D2FE; }
        .pill-delivered { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .pill-cancelled { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        .pill-paid { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .pill-unpaid { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        .pill-stock-ok { background: #E6F4EA; color: #137333; }
        .pill-stock-low { background: #FEF7E0; color: #B06000; }
        .pill-stock-out { background: #FCE8E6; color: #C5221F; }

        /* Buttons */
        .btn-atelier {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-family: var(--sans);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 4px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all .2s ease;
        }
        .btn-atelier-primary {
            background: var(--green-900);
            color: var(--ivory-50);
            border-color: var(--green-900);
        }
        .btn-atelier-primary:hover {
            background: var(--green-800);
            color: #fff;
        }
        .btn-atelier-secondary {
            background: var(--ivory-100);
            color: var(--charcoal);
            border-color: var(--line-strong);
        }
        .btn-atelier-secondary:hover {
            background: var(--ivory-200);
        }
        .btn-atelier-brass {
            background: var(--brass-500);
            color: #fff;
        }
        .btn-atelier-brass:hover {
            background: var(--brass-600);
        }
        .btn-atelier-danger {
            background: #fff;
            color: #C5221F;
            border-color: #FCE8E6;
        }
        .btn-atelier-danger:hover {
            background: #FCE8E6;
        }
        .btn-atelier-sm {
            padding: 4px 9px;
            font-size: 12px;
        }

        /* Form Controls */
        .form-label-custom {
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }
        .form-control-custom, .form-select-custom {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--line-strong);
            background: #fff;
            border-radius: 4px;
            font-family: var(--sans);
            font-size: 13.5px;
            color: var(--charcoal);
            outline: none;
            transition: border-color .2s;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            border-color: var(--brass-500);
            box-shadow: 0 0 0 2px rgba(156, 122, 60, 0.15);
        }

        /* Filter Toolbar */
        .toolbar-filter {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }
        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Alert Banners */
        .alert-custom {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-success-custom {
            background: #E6F4EA;
            border: 1px solid #CEEAD6;
            color: #137333;
        }
        .alert-danger-custom {
            background: #FCE8E6;
            border: 1px solid #FAD2CF;
            color: #C5221F;
        }

        /* Mobile Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: 0;
            font-size: 22px;
            color: var(--green-900);
            cursor: pointer;
        }

        /* Pagination Styling */
        .pagination-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding: 16px 0 6px;
            font-size: 13px;
        }
        .pagination-info {
            color: var(--muted);
            font-size: 12.5px;
        }
        .pagination-info span {
            font-weight: 600;
            color: var(--green-900);
        }
        .pagination-list {
            display: inline-flex;
            align-items: center;
            list-style: none;
            gap: 4px;
            margin: 0;
            padding: 0;
        }
        .pagination-list .page-item {
            margin: 0;
            padding: 0;
        }
        .pagination-list .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 4px;
            border: 1px solid var(--line-strong);
            background: #fff;
            color: var(--charcoal);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all .2s ease;
        }
        .pagination-list .page-link:hover {
            background: var(--ivory-100);
            border-color: var(--brass-500);
            color: var(--brass-600);
        }
        .pagination-list .page-item.active .page-link {
            background: var(--green-900);
            border-color: var(--green-900);
            color: #fff;
            font-weight: 600;
        }
        .pagination-list .page-item.disabled .page-link {
            background: var(--ivory-50);
            border-color: var(--line);
            color: rgba(107, 104, 98, 0.4);
            cursor: not-allowed;
            pointer-events: none;
        }
        .pagination-list .page-link.dots {
            border: none;
            background: transparent;
            min-width: 20px;
        }

        /* Prevent rogue SVG expansion */
        nav[role="navigation"] svg {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
        nav[role="navigation"] > div {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            width: 100%;
        }

        @media (max-width: 991px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.open {
                transform: translateX(0);
            }
            .admin-main {
                margin-left: 0;
                width: 100%;
            }
            .menu-toggle {
                display: block;
            }
            .admin-content {
                padding: 16px;
            }
        }
    </style>
    @stack('admin-styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <div>
                <h1>Tōramally</h1>
                <div style="font-size: 10px; opacity: .7; letter-spacing: .1em; text-transform: uppercase;">Atelier Management</div>
            </div>
            <span class="badge-tag">v2.0</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Operations</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    </span>
                    Overview
                </span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="nav-link-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    </span>
                    Orders &amp; Invoices
                </span>
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="nav-link-item {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>
                    </span>
                    Inventory &amp; Stock
                </span>
            </a>

            <div class="nav-section">Catalogue &amp; Atelier</div>
            <a href="{{ route('admin.products.index') }}" class="nav-link-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </span>
                    Products &amp; Silhouettes
                </span>
            </a>
            <a href="{{ route('admin.commissions.index') }}" class="nav-link-item {{ request()->routeIs('admin.commissions*') ? 'active' : '' }}">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.563-2.512 5.563-5.563C21.937 6.375 17.5 2 12 2Z"/></svg>
                    </span>
                    Bespoke Commissions
                </span>
            </a>

            <div class="nav-section">Patrons &amp; Inquiries</div>
            <a href="{{ route('admin.customers.index') }}" class="nav-link-item {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    Customers &amp; Patrons
                </span>
            </a>
            <a href="{{ route('admin.enquiries.index') }}" class="nav-link-item {{ request()->routeIs('admin.enquiries*') ? 'active' : '' }}">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    </span>
                    Appointments &amp; Queries
                </span>
            </a>
            <a href="{{ route('admin.subscribers.index') }}" class="nav-link-item {{ request()->routeIs('admin.subscribers*') ? 'active' : '' }}">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </span>
                    Letters &amp; Subscribers
                </span>
            </a>

            <div class="nav-section">System</div>
            <a href="{{ route('admin.settings.index') }}" class="nav-link-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                    Store Settings
                </span>
            </a>
            <a href="{{ url('/') }}" target="_blank" class="nav-link-item">
                <span style="display:flex;align-items:center;">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                    </span>
                    View Live Boutique
                </span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="name">{{ session('admin_user_name', 'Atelier Master') }}</div>
                <div class="role">{{ session('admin_user_role', 'Administrator') }}</div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-atelier btn-atelier-danger btn-atelier-sm" title="Sign Out">
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <header class="admin-topbar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <button class="menu-toggle" id="sidebarToggle" aria-label="Toggle Navigation">☰</button>
                <h2 class="topbar-title">{{ $header ?? $title ?? 'Atelier Management' }}</h2>
            </div>
            <div class="topbar-actions">
                <a href="{{ route('admin.orders.index') }}?status=Order+Placed" class="btn-atelier btn-atelier-secondary btn-atelier-sm">
                    New Orders
                </a>
                <a href="{{ route('admin.inventory.index') }}" class="btn-atelier btn-atelier-primary btn-atelier-sm">
                    Live Stock
                </a>
            </div>
        </header>

        <main class="admin-content">
            @if(session('success'))
                <div class="alert-custom alert-success-custom">
                    <span>✓ {{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-weight:bold;color:inherit">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-custom alert-danger-custom">
                    <span>⚠ {{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-weight:bold;color:inherit">✕</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-custom alert-danger-custom">
                    <div>
                        <strong>Please check the errors below:</strong>
                        <ul style="margin: 6px 0 0 16px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-weight:bold;color:inherit">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        if (sidebarToggle && adminSidebar) {
            sidebarToggle.addEventListener('click', () => {
                adminSidebar.classList.toggle('open');
            });
        }
    </script>
    @stack('admin-scripts')
</body>
</html>
