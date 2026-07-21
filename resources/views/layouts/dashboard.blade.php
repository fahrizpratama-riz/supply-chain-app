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
    
    <style>
        :root {
            --primary-color: #00b575;
            --sidebar-text: #ffffff;
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
            --text-dark: #333333;
            --text-muted: #888888;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            background-color: var(--primary-color);
            color: var(--sidebar-text);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            padding-top: 20px;
            transition: all 0.3s;
            border-top-right-radius: 30px;
            border-bottom-right-radius: 30px;
        }

        .sidebar .brand {
            font-size: 24px;
            font-weight: bold;
            padding: 0 20px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .brand i {
            font-size: 28px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            padding: 12px 20px;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 15px;
            opacity: 0.8;
        }
        
        .sidebar-menu li i {
            width: 20px;
            text-align: center;
        }

        .sidebar-menu li:hover, .sidebar-menu li.active {
            background-color: var(--sidebar-hover);
            opacity: 1;
            border-left: 4px solid white;
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
<body>

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
    </script>
</body>
</html>
