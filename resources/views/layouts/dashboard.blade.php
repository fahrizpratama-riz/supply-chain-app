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
    <div class="sidebar">
        <div class="brand">
            <i class="fa-solid fa-earth-americas"></i>
            <span>SupplyChain</span>
        </div>
        <ul class="sidebar-menu">
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
            <a href="/admin" class="text-decoration-none text-white"><li class="{{ request()->is('admin') ? 'active' : '' }}"><i class="fa-solid fa-shield-halved"></i> Admin</li></a>
        </ul>
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
                <span class="text-muted text-sm fw-bold">ENG <i class="fa-solid fa-chevron-down ms-1"></i></span>
                <div class="d-flex align-items-center gap-2 ms-3">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=00b575&color=fff" alt="User">
                    <span class="fw-bold">Admin <i class="fa-solid fa-chevron-down ms-1 text-muted"></i></span>
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
                html += `<div class="result-item" data-iso="${c.iso_code}">
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
