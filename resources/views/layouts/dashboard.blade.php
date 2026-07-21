<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk Intelligence Platform</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS (loaded per-page) -->
    <script>
        (function() {
            try {
                const prefs = JSON.parse(localStorage.getItem('dashboard_prefs') || '{}');
                if (prefs.theme === 'dark') {
                    document.documentElement.classList.add('dark-mode');
                }
                const color = localStorage.getItem('accent_color');
                if (color) {
                    document.documentElement.style.setProperty('--primary-color', color);
                }
            } catch (e) {}
        })();
    </script>
    <style>
        :root {
            --primary-color: #00b575;
            --sidebar-bg: #00b575;
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.15);
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
            --text-dark: #333333;
            --text-muted: #888888;
        }

        :root.dark-mode {
            --primary-color: #00b575;
            --sidebar-bg: #1c2331;
            --sidebar-text: rgba(255, 255, 255, 0.7);
            --sidebar-hover: rgba(255, 255, 255, 0.08);
            --bg-color: #121721;
            --card-bg: #1c2331;
            --text-dark: #f0f4f8;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Sidebar */
        .sidebar {
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            padding-top: 20px;
            transition: background-color 0.3s ease;
        }

        .sidebar .brand {
            font-size: 24px;
            font-weight: bold;
            padding: 0 24px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffffff;
        }

        .sidebar .brand i {
            font-size: 28px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu a {
            color: inherit !important;
            text-decoration: none !important;
            display: block;
        }

        .sidebar-menu li {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 15px;
            font-weight: 500;
            color: var(--sidebar-text);
            margin: 4px 0 4px 15px;
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
            opacity: 0.85;
            position: relative;
        }
        
        .sidebar-menu li i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar-menu li:hover {
            background-color: var(--sidebar-hover);
            opacity: 1;
            color: #ffffff;
        }

        /* Active tab connecting seamlessly with main content */
        .sidebar-menu li.active {
            background-color: var(--bg-color) !important;
            color: var(--primary-color) !important;
            opacity: 1;
            font-weight: 700;
            border-left: none !important;
        }

        .sidebar-menu li.active i {
            color: var(--primary-color) !important;
        }

        /* Concave curved corners top and bottom of active tab */
        .sidebar-menu li.active::before {
            content: '';
            position: absolute;
            top: -20px;
            right: 0;
            width: 20px;
            height: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 6px 6px 0 6px var(--bg-color);
            pointer-events: none;
        }

        .sidebar-menu li.active::after {
            content: '';
            position: absolute;
            bottom: -20px;
            right: 0;
            width: 20px;
            height: 20px;
            border-top-right-radius: 20px;
            box-shadow: 6px -6px 0 6px var(--bg-color);
            pointer-events: none;
        }

        /* Dark mode overrides */
        :root.dark-mode body {
            background-color: var(--bg-color) !important;
            color: var(--text-dark) !important;
        }

        :root.dark-mode .sidebar {
            background-color: var(--sidebar-bg);
            /* border-right removed to allow seamless active tab connection */
        }

        :root.dark-mode .custom-card,
        :root.dark-mode .card,
        :root.dark-mode .settings-section,
        :root.dark-mode .settings-nav,
        :root.dark-mode .country-card,
        :root.dark-mode .news-card,
        :root.dark-mode .weather-card,
        :root.dark-mode .filter-box,
        :root.dark-mode .table-card,
        :root.dark-mode .port-card,
        :root.dark-mode .stat-card-custom,
        :root.dark-mode .watchlist-item,
        :root.dark-mode .datasource-item,
        :root.dark-mode .selector-bar {
            background-color: var(--card-bg) !important;
            color: var(--text-dark) !important;
            border-color: #2d3748 !important;
        }

        :root.dark-mode input,
        :root.dark-mode select,
        :root.dark-mode textarea,
        :root.dark-mode .form-control,
        :root.dark-mode .form-select,
        :root.dark-mode .setting-input,
        :root.dark-mode .setting-select,
        :root.dark-mode .search-bar input {
            background-color: var(--card-bg) !important;
            color: #f0f4f8 !important;
            border-color: #2d3748 !important;
        }
        
        /* Select2 Dark Mode Fixes */
        :root.dark-mode .select2-container--default .select2-selection--single {
            background-color: var(--card-bg) !important;
            border-color: #2d3748 !important;
        }
        :root.dark-mode .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-dark) !important;
        }
        :root.dark-mode .select2-dropdown {
            background-color: var(--card-bg) !important;
            border-color: #2d3748 !important;
        }
        :root.dark-mode .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #121721 !important;
            color: var(--text-dark) !important;
            border-color: #2d3748 !important;
        }
        :root.dark-mode .select2-container--default .select2-results__option {
            background-color: var(--card-bg);
            color: var(--text-dark);
        }
        :root.dark-mode .select2-container--default .select2-results__option--highlighted[aria-selected],
        :root.dark-mode .select2-container--default .select2-results__option:hover {
            background-color: rgba(0, 181, 117, 0.2) !important;
            color: #00b575 !important;
        }

        :root.dark-mode h1,
        :root.dark-mode h2,
        :root.dark-mode h3,
        :root.dark-mode h4,
        :root.dark-mode h5,
        :root.dark-mode h6,
        :root.dark-mode .fw-bold,
        :root.dark-mode .card-title,
        :root.dark-mode .setting-row-label,
        :root.dark-mode .settings-section-title,
        :root.dark-mode .watchlist-name,
        :root.dark-mode .ds-name {
            color: var(--text-dark) !important;
        }

        :root.dark-mode p,
        :root.dark-mode small,
        :root.dark-mode .text-muted,
        :root.dark-mode .setting-row-desc,
        :root.dark-mode .settings-section-subtitle,
        :root.dark-mode .ds-desc,
        :root.dark-mode .card-label,
        :root.dark-mode .card-sub,
        :root.dark-mode .map-popup-row .label {
            color: var(--text-muted) !important;
        }

        /* SVG Overrides for dark mode */
        :root.dark-mode .risk-needle {
            stroke: var(--text-dark) !important;
        }
        :root.dark-mode circle[fill="#1a1f2e"] {
            fill: var(--text-dark) !important;
        }

        /* Override all hardcoded dark font colors in dark mode */
        :root.dark-mode *[style*="color:#1a1f2e"],
        :root.dark-mode *[style*="color: #1a1f2e"],
        :root.dark-mode *[style*="color:#333"],
        :root.dark-mode *[style*="color: #333"],
        :root.dark-mode *[style*="color:#555"],
        :root.dark-mode *[style*="color: #555"],
        :root.dark-mode *[style*="color:#666"],
        :root.dark-mode *[style*="color: #666"],
        :root.dark-mode *[style*="color:#444"],
        :root.dark-mode *[style*="color: #444"],
        :root.dark-mode *[style*="color:#000"],
        :root.dark-mode *[style*="color: #000"],
        :root.dark-mode *[style*="color: black"],
        :root.dark-mode .forecast-temp,
        :root.dark-mode .port-name,
        :root.dark-mode .port-popup-row .val,
        :root.dark-mode .rate-code,
        :root.dark-mode .curr-mini-code,
        :root.dark-mode .card-stat-row .val,
        :root.dark-mode .modal-detail-row .mval,
        :root.dark-mode .analytics-stat .astat-val,
        :root.dark-mode .admin-stat-val,
        :root.dark-mode .map-popup-row .value,
        :root.dark-mode .stat-value,
        :root.dark-mode option,
        :root.dark-mode select option,
        :root.dark-mode .card-value,
        :root.dark-mode #countryNameDisplay,
        :root.dark-mode .c-val,
        :root.dark-mode .map-popup-title {
            color: #f0f4f8 !important;
        }

        /* Ensure dark banners and headers always keep crisp bright white text in both light and dark mode */
        .page-header,
        .page-header h2,
        .page-header p,
        .page-header small,
        .page-header span,
        .page-header i,
        .page-header div,
        :root.dark-mode .page-header,
        :root.dark-mode .page-header h2,
        :root.dark-mode .page-header p,
        :root.dark-mode .page-header small,
        :root.dark-mode .page-header span,
        :root.dark-mode .page-header i,
        :root.dark-mode .page-header div,
        .about-logo-wrap,
        .about-logo-wrap *,
        :root.dark-mode .about-logo-wrap,
        :root.dark-mode .about-logo-wrap *,
        .btn-setting-save,
        .btn-setting-save *,
        :root.dark-mode .btn-setting-save,
        :root.dark-mode .btn-setting-save * {
            color: #ffffff !important;
        }

        .page-header p {
            opacity: 0.9 !important;
        }

        .page-header small {
            opacity: 0.8 !important;
        }

        /* Leaflet popups in dark mode */
        :root.dark-mode .leaflet-popup-content-wrapper,
        :root.dark-mode .leaflet-popup-tip {
            background-color: #1c2331 !important;
            color: #f0f4f8 !important;
            border: 1px solid #2d3748 !important;
        }
        :root.dark-mode .leaflet-popup-content,
        :root.dark-mode .leaflet-popup-content * {
            color: #f0f4f8 !important;
        }

        :root.dark-mode #searchResults {
            background-color: #1c2331;
            border-color: #2d3748;
        }

        :root.dark-mode #searchResults .result-item {
            border-bottom-color: #2d3748;
            color: #f0f0f0;
        }

        :root.dark-mode #searchResults .result-item:hover {
            background-color: #2d3748;
        }

        :root.dark-mode #searchResults .result-item .region-tag {
            background-color: #2d3748;
            color: #ccc;
        }

        :root.dark-mode .table {
            color: var(--text-dark);
            --bs-table-color: var(--text-dark);
            --bs-table-bg: var(--card-bg);
            --bs-table-border-color: #2d3748;
            --bs-table-hover-bg: rgba(255, 255, 255, 0.05);
        }

        :root.dark-mode .modal-content {
            background-color: var(--card-bg);
            color: var(--text-dark);
            border-color: #2d3748;
        }

        :root.dark-mode .modal-header, :root.dark-mode .modal-footer {
            border-color: #2d3748;
        }
        
        :root.dark-mode .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        :root.dark-mode .stat-card .icon-box {
            background-color: rgba(0, 181, 117, 0.15);
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px 30px;
            transition: all 0.3s;
        }

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: transparent;
        }

        .search-bar {
            position: relative;
            width: 300px;
        }
        
        .search-bar input {
            border-radius: 20px;
            padding-left: 38px;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            outline: none;
        }
        .search-bar input:focus {
            box-shadow: 0 0 0 3px rgba(0,181,117,0.15);
        }
        .search-bar i {
            position: absolute;
            left: 12px;
            top: 10px;
            color: var(--text-muted);
            z-index: 2;
        }
        /* Search Autocomplete Dropdown */
        #searchResults {
            position: absolute;
            top: calc(100% + 6px);
            left: 0; right: 0;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            z-index: 9999;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            border: 1px solid #f0f0f0;
        }
        #searchResults .result-item {
            padding: 10px 16px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.15s;
            border-bottom: 1px solid #f8f8f8;
        }
        #searchResults .result-item:last-child { border-bottom: none; }
        #searchResults .result-item:hover { background: #f0fdf8; color: #00b575; }
        #searchResults .result-item .region-tag {
            margin-left: auto;
            font-size: 11px;
            color: #aaa;
            background: #f5f5f5;
            padding: 2px 8px;
            border-radius: 10px;
        }
        #searchResults .no-result {
            padding: 14px 16px;
            color: #aaa;
            font-size: 13px;
            text-align: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        /* Cards */
        .custom-card {
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            border: none;
            margin-bottom: 20px;
        }
        
        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .stat-card .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background-color: rgba(0, 181, 117, 0.1);
            color: var(--primary-color);
        }

        h5.card-title {
            font-size: 16px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        h3.stat-value {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }

        /* Maps & Charts height */
        #weatherMap, #portMap {
            height: 350px;
            border-radius: 12px;
            z-index: 1;
        }
        
        canvas {
            max-height: 300px;
        }
    </style>
    @yield('styles')
</head>
<body class="transition-theme">

    <!-- Sidebar -->
    <div class="sidebar" style="display:flex; flex-direction:column;">
        <div class="brand">
            <i class="fa-solid fa-earth-americas"></i>
            <span>SupplyChain</span>
        </div>

        <ul class="sidebar-menu" style="flex:1; overflow-y:auto;">
            <a href="/" class="text-decoration-none text-white"><li class="{{ request()->is('/') ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i> Dashboard</li></a>
            <a href="/countries" class="text-decoration-none text-white"><li class="{{ request()->is('countries') ? 'active' : '' }}"><i class="fa-solid fa-globe"></i> Countries</li></a>
            <a href="/weather" class="text-decoration-none text-white"><li class="{{ request()->is('weather') ? 'active' : '' }}"><i class="fa-solid fa-cloud-sun-rain"></i> Weather</li></a>
            <a href="/ports" class="text-decoration-none text-white"><li class="{{ request()->is('ports') ? 'active' : '' }}"><i class="fa-solid fa-ship"></i> Ports</li></a>
            <a href="/news" class="text-decoration-none text-white"><li class="{{ request()->is('news') ? 'active' : '' }}"><i class="fa-solid fa-newspaper"></i> News</li></a>

            {{-- Divider --}}
            <li style="padding:6px 20px; opacity:0.35; font-size:10px; text-transform:uppercase; letter-spacing:1px; cursor:default; border-left:none !important; background:none !important;">Analytics</li>

            <a href="/currency" class="text-decoration-none text-white"><li class="{{ request()->is('currency') ? 'active' : '' }}"><i class="fa-solid fa-money-bill-transfer"></i> Currency</li></a>
            <a href="/compare" class="text-decoration-none text-white"><li class="{{ request()->is('compare') ? 'active' : '' }}"><i class="fa-solid fa-code-compare"></i> Compare</li></a>
            <a href="/analytics" class="text-decoration-none text-white"><li class="{{ request()->is('analytics') ? 'active' : '' }}"><i class="fa-solid fa-chart-mixed"></i> Analytics</li></a>

            {{-- Divider --}}
            <li style="padding:6px 20px; opacity:0.35; font-size:10px; text-transform:uppercase; letter-spacing:1px; cursor:default; border-left:none !important; background:none !important;">System</li>

            <a href="/settings" class="text-decoration-none text-white"><li class="{{ request()->is('settings') ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> Settings</li></a>
            @if(auth()->user()?->role === 'admin')
            <a href="/admin" class="text-decoration-none text-white"><li class="{{ request()->is('admin') ? 'active' : '' }}"><i class="fa-solid fa-shield-halved"></i> Admin</li></a>
            @endif
        </ul>

        {{-- User Info + Logout --}}
        <div style="padding:16px; border-top:1px solid rgba(255,255,255,0.1); margin-top:auto;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'User') }}&background=009a62&color=fff&size=36"
                    alt="Avatar" style="width:36px;height:36px;border-radius:50%;flex-shrink:0;">
                <div style="overflow:hidden;">
                    <div style="font-size:13px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">{{ auth()->user()?->name ?? 'Guest' }}</div>
                    <div style="font-size:10px;color:rgba(255,255,255,0.55);text-transform:uppercase;letter-spacing:.5px;">{{ auth()->user()?->role ?? 'user' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:100%;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.8);border-radius:10px;padding:8px 12px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .2s;" onmouseover="this.style.background='rgba(239,68,68,0.25)';this.style.borderColor='rgba(239,68,68,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.1)';this.style.borderColor='rgba(255,255,255,0.15)'">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="search-bar">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="globalSearch" class="form-control" placeholder="Search country..." autocomplete="off">
                <div id="searchResults"></div>
            </div>
            <div class="user-profile">
                @if(session('success'))
                <div style="background:#d1fae5;border:1px solid #a7f3d0;border-radius:10px;padding:8px 14px;font-size:13px;color:#065f46;font-weight:600;display:flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-circle-check text-success"></i> {{ session('success') }}
                </div>
                @endif
                <div class="d-flex align-items-center gap-2 ms-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'User') }}&background=00b575&color=fff" alt="User" style="width:36px;height:36px;border-radius:50%;">
                    <div>
                        <div class="fw-bold" style="font-size:14px;">{{ auth()->user()?->name ?? 'Guest' }}</div>
                        <div style="font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:.5px;">{{ auth()->user()?->role ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Leaflet JS (loaded per-page) -->
    @yield('scripts')
    <script>
    /* ===== GLOBAL SEARCH BAR (works on all pages) ===== */
    (function() {
        let allCountriesGlobal = [];

        // Fetch countries once
        $.get('/api/countries', function(data) {
            allCountriesGlobal = data.sort((a, b) => a.name.localeCompare(b.name));
        });

        $('#globalSearch').on('input', function() {
            const q = $(this).val().trim().toLowerCase();
            const $results = $('#searchResults');

            if (q.length < 1) { $results.hide(); return; }

            const matches = allCountriesGlobal.filter(c =>
                c.name.toLowerCase().includes(q) ||
                (c.iso_code && c.iso_code.toLowerCase().includes(q)) ||
                (c.region && c.region.toLowerCase().includes(q))
            ).slice(0, 8);

            if (matches.length === 0) {
                $results.html('<div class="no-result">No country found</div>').show();
                return;
            }

            let html = '';
            matches.forEach(c => {
                html += `<div class="result-item" data-iso="${c.iso_code}" data-currency="${c.currency_code || ''}">
                    <i class="fa-solid fa-location-dot" style="color:#00b575;"></i>
                    <span>${c.name}</span>
                    <span class="region-tag">${c.region || ''}</span>
                </div>`;
            });
            $results.html(html).show();
        });

        // Click result → trigger fetchData via selector change
        $(document).on('click', '.result-item', function() {
            const iso = $(this).data('iso');
            const name = $(this).find('span').first().text();
            $('#globalSearch').val(name);
            $('#searchResults').hide();

            // If on dashboard page (countrySelector exists), update it
            const $sel = $('#countrySelector');
            if ($sel.length) {
                $sel.val(iso).trigger('change');
            }
            
            // If on news page (countryFilter exists), update it with name
            const $newsSel = $('#countryFilter');
            if ($newsSel.length) {
                $newsSel.val(name).trigger('change');
            }

            // If on weather page (weatherCountrySelect exists), update it with iso
            const $weatherSel = $('#weatherCountrySelect');
            if ($weatherSel.length) {
                $weatherSel.val(iso).trigger('change');
            }

            // If on analytics page (analCountry exists), update it with iso
            const $analSel = $('#analCountry');
            if ($analSel.length) {
                $analSel.val(iso).trigger('change');
            }

            // If on currency page (currencySearch exists), update it with the currency code
            const currCode = $(this).data('currency');
            const $currSearch = $('#currencySearch');
            if ($currSearch.length && currCode) {
                $currSearch.val(currCode).trigger('input');
            }
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-bar').length) {
                $('#searchResults').hide();
            }
        });

        // Keyboard navigation: Enter = pick first result
        $('#globalSearch').on('keydown', function(e) {
            if (e.key === 'Enter') {
                const $first = $('#searchResults .result-item').first();
                if ($first.length) $first.trigger('click');
            }
            if (e.key === 'Escape') $('#searchResults').hide();
        });
    })();

    /* ===== LANGUAGE TRANSLATION ===== */
    (function() {
        try {
            const prefs = JSON.parse(localStorage.getItem('dashboard_prefs') || '{}');
            if (prefs.lang === 'id') {
                const dictionary = {
                    'Dashboard': 'Dasbor',
                    'Countries': 'Negara',
                    'Weather': 'Cuaca',
                    'Ports': 'Pelabuhan',
                    'News': 'Berita',
                    'Analytics': 'Analitik',
                    'Currency': 'Mata Uang',
                    'Compare': 'Bandingkan',
                    'Settings': 'Pengaturan',
                    'Admin': 'Admin',
                    'Logout': 'Keluar',
                    'Search country...': 'Cari negara...',
                    'System': 'Sistem',
                    'Guest': 'Tamu',
                    'Settings & Configuration': 'Pengaturan & Konfigurasi',
                    'Manage your API keys, watchlist, risk weights, and platform preferences': 'Kelola kunci API, daftar pantau, bobot risiko, dan preferensi platform Anda',
                    'Navigation': 'Navigasi',
                    'API Keys': 'Kunci API',
                    'Watchlist': 'Daftar Pantau',
                    'Risk Weights': 'Bobot Risiko',
                    'Preferences': 'Preferensi',
                    'Data & Cache': 'Data & Cache',
                    'About Platform': 'Tentang Platform',
                    'Display Preferences': 'Preferensi Tampilan',
                    'Customize the look and feel of your dashboard': 'Sesuaikan tampilan dan nuansa dasbor Anda',
                    '🎨 Accent Color': '🎨 Warna Aksen',
                    'Primary color used throughout the dashboard': 'Warna utama yang digunakan di seluruh dasbor',
                    '🌓 Theme Mode': '🌓 Mode Tema',
                    'Currently only Light mode is supported': 'Pilih tema tampilan dasbor',
                    '🌐 Default Language': '🌐 Bahasa Default',
                    'Interface display language': 'Bahasa tampilan antarmuka',
                    '📊 Default Dashboard Country': '📊 Negara Dasbor Default',
                    'Country pre-selected when opening the main dashboard': 'Negara yang dipilih sebelumnya saat membuka dasbor utama',
                    '🗺 Default Map Zoom Level': '🗺 Tingkat Zoom Peta Default',
                    'Starting zoom for the world map views': 'Zoom awal untuk tampilan peta dunia',
                    'Save Preferences': 'Simpan Preferensi',
                    'Notification Settings': 'Pengaturan Notifikasi',
                    'Control which events trigger dashboard alerts': 'Kontrol peristiwa yang memicu peringatan dasbor',
                    '⚠️ High Risk Alerts': '⚠️ Peringatan Risiko Tinggi',
                    '📰 Negative News Alert': '📰 Peringatan Berita Negatif',
                    '🌪️ Severe Weather Alert': '🌪️ Peringatan Cuaca Buruk',
                    '💡 Dashboard Tips': '💡 Tips Dasbor',
                    'Save Notifications': 'Simpan Notifikasi',
                    'Browser Cache & Storage': 'Cache & Penyimpanan Browser',
                    'Manage locally stored data in your browser (localStorage)': 'Kelola data lokal yang tersimpan di browser Anda',
                    'Watchlist entries': 'Entri daftar pantau',
                    'Saved preferences': 'Preferensi tersimpan',
                    'Estimated storage used': 'Penyimpanan terpakai',
                    'Storage usage': 'Penggunaan penyimpanan',
                    'Refresh Info': 'Segarkan Info',
                    'Clear Browser Storage': 'Hapus Penyimpanan Browser',
                    'Data Refresh Settings': 'Pengaturan Penyegaran Data',
                    'Control how often data is fetched and refreshed': 'Kontrol seberapa sering data diambil dan diperbarui',
                    '⏱ Auto-Refresh Interval': '⏱ Interval Auto-Refresh',
                    'Automatically reload dashboard data periodically': 'Muat ulang data dasbor secara berkala',
                    '🗓 News Cache Duration': '🗓 Durasi Cache Berita',
                    'How long fetched news articles are stored in browser': 'Berapa lama artikel berita tersimpan di browser',
                    'Save Settings': 'Simpan Pengaturan',
                    'Platform Features': 'Fitur Platform',
                    'Overview of all available modules and capabilities': 'Ikhtisar semua modul dan kemampuan yang tersedia',
                    'Quick Links': 'Tautan Cepat'
                };

                // Universal DOM TreeWalker translation for text nodes
                const walker = document.createTreeWalker(
                    document.body,
                    NodeFilter.SHOW_TEXT,
                    null,
                    false
                );

                let node;
                while (node = walker.nextNode()) {
                    const text = node.nodeValue.trim();
                    if (dictionary[text]) {
                        node.nodeValue = node.nodeValue.replace(text, dictionary[text]);
                    }
                }

                // Translate search placeholders
                document.querySelectorAll('input[placeholder]').forEach(inp => {
                    const ph = inp.placeholder.trim();
                    if (dictionary[ph]) {
                        inp.placeholder = dictionary[ph];
                    }
                });
            }
        } catch (e) {}
    })();
    </script>
</body>
</html>
