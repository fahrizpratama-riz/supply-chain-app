@extends('layouts.dashboard')

@section('styles')
<style>
    /* ===== PAGE HEADER ===== */
    .page-header {
        background: linear-gradient(135deg, #1a1f2e 0%, #2d3561 50%, #1a1f2e 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .page-header::before {
        content: '';
        position: absolute;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(0,181,117,0.15) 0%, transparent 70%);
        right: -100px; top: -150px;
        pointer-events: none;
    }
    .page-header::after {
        content: '';
        position: absolute;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.03);
        border-radius: 50%;
        left: -50px; bottom: -80px;
        pointer-events: none;
    }
    .page-header h2 { font-size: 26px; font-weight: 800; margin: 0 0 6px; }
    .page-header p  { margin: 0; opacity: 0.75; font-size: 14px; }

    /* ===== SETTINGS LAYOUT ===== */
    .settings-nav {
        background: #fff;
        border-radius: 16px;
        padding: 10px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.05);
        position: sticky;
        top: 20px;
    }
    .settings-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #666;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        margin-bottom: 4px;
    }
    .settings-nav-item:hover {
        background: #f4f7f6;
        color: #1a1f2e;
    }
    .settings-nav-item.active {
        background: linear-gradient(135deg, #00b575, #009a62);
        color: #fff;
    }
    .settings-nav-item .nav-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        background: rgba(0,0,0,0.06);
        flex-shrink: 0;
    }
    .settings-nav-item.active .nav-icon {
        background: rgba(255,255,255,0.2);
    }

    /* ===== SETTINGS SECTION PANEL ===== */
    .settings-panel {
        display: none;
    }
    .settings-panel.active {
        display: block;
        animation: fadeSlideIn 0.3s ease;
    }
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .settings-section {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        margin-bottom: 20px;
        border: 1.5px solid #f0f0f0;
    }
    .settings-section-title {
        font-size: 16px;
        font-weight: 800;
        color: #1a1f2e;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .settings-section-title i {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
        background: linear-gradient(135deg, #00b575, #009a62);
        color: #fff;
        flex-shrink: 0;
    }
    .settings-section-subtitle {
        font-size: 13px;
        color: #aaa;
        margin-bottom: 20px;
        margin-left: 44px;
    }

    /* ===== FORM ELEMENTS ===== */
    .setting-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #f5f5f5;
        gap: 20px;
    }
    .setting-row:last-child { border-bottom: none; }
    .setting-row-left { flex: 1; }
    .setting-row-label {
        font-size: 14px;
        font-weight: 700;
        color: #1a1f2e;
        margin-bottom: 2px;
    }
    .setting-row-desc {
        font-size: 12px;
        color: #aaa;
        line-height: 1.5;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        flex-shrink: 0;
    }
    .toggle-switch input {
        opacity: 0; width: 0; height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background: #e0e0e0;
        border-radius: 26px;
        transition: .3s;
    }
    .toggle-slider:before {
        position: absolute;
        content: '';
        height: 20px; width: 20px;
        left: 3px; bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: .3s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .toggle-switch input:checked + .toggle-slider {
        background: #00b575;
    }
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }

    /* Form inputs */
    .setting-input {
        border: 1.5px solid #e8ecef;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        min-width: 220px;
    }
    .setting-input:focus {
        border-color: #00b575;
        box-shadow: 0 0 0 3px rgba(0,181,117,0.1);
    }
    .setting-select {
        border: 1.5px solid #e8ecef;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 14px;
        outline: none;
        background: #fff;
        min-width: 180px;
        cursor: pointer;
    }
    .setting-select:focus {
        border-color: #00b575;
        box-shadow: 0 0 0 3px rgba(0,181,117,0.1);
    }

    /* API Key Input */
    .api-key-wrap {
        position: relative;
        flex: 1;
        max-width: 340px;
    }
    .api-key-wrap input {
        width: 100%;
        padding-right: 90px;
    }
    .api-key-wrap .api-eye-btn {
        position: absolute;
        right: 8px; top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #aaa;
        font-size: 14px;
        display: flex; align-items: center; gap: 4px;
        font-weight: 600;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 6px;
        transition: color 0.2s, background 0.2s;
    }
    .api-key-wrap .api-eye-btn:hover {
        color: #00b575;
        background: rgba(0,181,117,0.08);
    }

    /* Action Buttons */
    .btn-setting-save {
        background: linear-gradient(135deg, #00b575, #009a62);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 9px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-setting-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,181,117,0.35);
    }
    .btn-setting-danger {
        background: #fff;
        color: #ef4444;
        border: 1.5px solid #ef4444;
        border-radius: 10px;
        padding: 9px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-setting-danger:hover {
        background: #ef4444;
        color: #fff;
    }
    .btn-setting-secondary {
        background: #f4f7f6;
        color: #555;
        border: 1.5px solid #e8ecef;
        border-radius: 10px;
        padding: 9px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-setting-secondary:hover {
        background: #e8ecef;
    }

    /* Color theme picker */
    .color-swatch-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .color-swatch {
        width: 34px; height: 34px;
        border-radius: 50%;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.2s;
        position: relative;
    }
    .color-swatch:hover { transform: scale(1.1); }
    .color-swatch.selected {
        border-color: #1a1f2e;
    }
    .color-swatch.selected::after {
        content: '✓';
        position: absolute;
        inset: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        font-weight: 800;
        color: #fff;
    }

    /* Watchlist items */
    .watchlist-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1.5px solid #f0f0f0;
        margin-bottom: 8px;
        background: #fff;
        transition: all 0.2s;
    }
    .watchlist-item:hover {
        border-color: #00b575;
        box-shadow: 0 4px 12px rgba(0,181,117,0.1);
    }
    .watchlist-flag {
        width: 40px; height: 28px;
        border-radius: 6px;
        object-fit: cover;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        flex-shrink: 0;
    }
    .watchlist-name {
        font-weight: 700;
        font-size: 14px;
        color: #1a1f2e;
        flex: 1;
    }
    .watchlist-iso {
        font-size: 11px;
        color: #aaa;
        font-weight: 600;
    }
    .watchlist-remove {
        background: none;
        border: 1.5px solid #fee2e2;
        color: #ef4444;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .watchlist-remove:hover {
        background: #ef4444;
        color: #fff;
    }
    .watchlist-empty {
        text-align: center;
        padding: 40px 20px;
        color: #ccc;
    }
    .watchlist-empty i {
        font-size: 48px;
        margin-bottom: 12px;
        display: block;
    }

    /* System info */
    .sys-info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 13px;
        border-bottom: 1px dashed #f0f0f0;
    }
    .sys-info-row:last-child { border-bottom: none; }
    .sys-info-row .si-label { color: #aaa; font-weight: 500; }
    .sys-info-row .si-val { font-weight: 700; color: #1a1f2e; font-family: monospace; }

    /* Status badge */
    .status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .status-dot.green { background: #00b575; box-shadow: 0 0 0 3px rgba(0,181,117,0.2); }
    .status-dot.yellow { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.2); }
    .status-dot.red { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.2); }

    /* Toast notification */
    .settings-toast {
        position: fixed;
        bottom: 28px;
        right: 28px;
        background: #1a1f2e;
        color: #fff;
        padding: 14px 22px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 8px 30px rgba(0,0,0,0.18);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateY(80px);
        opacity: 0;
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .settings-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
    .settings-toast.success .toast-icon { color: #00b575; }
    .settings-toast.error   .toast-icon { color: #ef4444; }

    /* Risk scoring weights */
    .weight-slider-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }
    .weight-slider {
        flex: 1;
        accent-color: #00b575;
        height: 4px;
        cursor: pointer;
    }
    .weight-val {
        font-weight: 800;
        color: #1a1f2e;
        font-size: 15px;
        min-width: 38px;
        text-align: right;
    }

    /* Progress bar for cache */
    .cache-bar-wrap {
        background: #f0f0f0;
        border-radius: 8px;
        height: 8px;
        overflow: hidden;
        margin-top: 8px;
    }
    .cache-bar {
        height: 100%;
        background: linear-gradient(90deg, #00b575, #0ea5e9);
        border-radius: 8px;
        transition: width 0.6s ease;
    }

    /* About card */
    .about-logo-wrap {
        background: linear-gradient(135deg, #1a1f2e, #2d3561);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        color: #fff;
        margin-bottom: 16px;
    }
    .about-logo-wrap .logo-icon {
        font-size: 48px;
        margin-bottom: 12px;
        display: block;
    }
    .about-logo-wrap h4 {
        font-weight: 800;
        margin-bottom: 4px;
        font-size: 20px;
    }
    .about-logo-wrap p {
        opacity: 0.65;
        font-size: 13px;
        margin: 0;
    }
    .version-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(0,181,117,0.15);
        color: #00b575;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        margin-top: 12px;
    }

    /* Data source card */
    .datasource-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px dashed #f0f0f0;
    }
    .datasource-item:last-child { border-bottom: none; }
    .ds-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .ds-name { font-weight: 700; font-size: 14px; color: #1a1f2e; }
    .ds-desc { font-size: 12px; color: #aaa; }
    .ds-status {
        margin-left: auto;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        flex-shrink: 0;
    }
    .ds-status.ok { background: #d1fae5; color: #065f46; }
    .ds-status.limited { background: #fef3c7; color: #92400e; }
    .ds-status.offline { background: #fee2e2; color: #991b1b; }

    /* ===== DARK MODE OVERRIDES ===== */
    :root.dark-mode .settings-nav { background: var(--card-bg); border: 1px solid #2d3748; }
    :root.dark-mode .settings-nav-item { color: var(--text-muted); }
    :root.dark-mode .settings-nav-item:hover { background: var(--bg-color); color: var(--text-dark); }
    :root.dark-mode .settings-section { background: var(--card-bg); border-color: #2d3748; }
    :root.dark-mode .settings-section-title, :root.dark-mode .setting-row-label, :root.dark-mode .watchlist-name, :root.dark-mode .sys-info-row .si-val, :root.dark-mode .weight-val, :root.dark-mode .ds-name, :root.dark-mode .about-logo-wrap h4 { color: var(--text-dark); }
    :root.dark-mode .setting-row, :root.dark-mode .sys-info-row, :root.dark-mode .datasource-item { border-bottom-color: #2d3748; }
    :root.dark-mode .setting-input, :root.dark-mode .setting-select { background: var(--bg-color); border-color: #2d3748; color: var(--text-dark); }
    :root.dark-mode .btn-setting-secondary { background: var(--bg-color); color: var(--text-dark); border-color: #2d3748; }
    :root.dark-mode .btn-setting-secondary:hover { background: #2d3748; }
    :root.dark-mode .btn-setting-danger { background: var(--bg-color); }
    :root.dark-mode .watchlist-item { background: var(--card-bg); border-color: #2d3748; }
    :root.dark-mode .api-key-wrap .api-eye-btn { color: var(--text-muted); }
    :root.dark-mode .color-swatch.selected { border-color: var(--text-dark); }
    :root.dark-mode .cache-bar-wrap { background: #2d3748; }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fa-solid fa-gear" style="font-size:36px; opacity:0.85;"></i>
        <div>
            <h2>Settings & Configuration</h2>
            <p>Manage your API keys, watchlist, risk weights, and platform preferences</p>
        </div>
    </div>
    <div class="d-flex gap-4 mt-2">
        <div><span class="fw-bold fs-5" id="hdrWatchCount">—</span><br><small style="opacity:0.75;">Watchlisted</small></div>
        <div><span class="fw-bold fs-5" id="hdrApiStatus">Active</span><br><small style="opacity:0.75;">API Status</small></div>
        <div><span class="fw-bold fs-5" id="hdrCacheSize">—</span><br><small style="opacity:0.75;">Cached Items</small></div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT NAV --}}
    <div class="col-md-3">
        <div class="settings-nav">
            <div style="font-size:11px; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:.5px; padding:4px 8px 10px;">Navigation</div>
            <button class="settings-nav-item active" onclick="switchPanel('api', this)">
                <span class="nav-icon"><i class="fa-solid fa-key"></i></span>
                API Keys
            </button>
            <button class="settings-nav-item" onclick="switchPanel('watchlist', this)">
                <span class="nav-icon"><i class="fa-solid fa-star"></i></span>
                Watchlist
            </button>
            <button class="settings-nav-item" onclick="switchPanel('risk', this)">
                <span class="nav-icon"><i class="fa-solid fa-chart-bar"></i></span>
                Risk Weights
            </button>
            <button class="settings-nav-item" onclick="switchPanel('preferences', this)">
                <span class="nav-icon"><i class="fa-solid fa-sliders"></i></span>
                Preferences
            </button>
            <button class="settings-nav-item" onclick="switchPanel('cache', this)">
                <span class="nav-icon"><i class="fa-solid fa-database"></i></span>
                Data & Cache
            </button>
            <button class="settings-nav-item" onclick="switchPanel('about', this)">
                <span class="nav-icon"><i class="fa-solid fa-circle-info"></i></span>
                About Platform
            </button>
        </div>
    </div>

    {{-- RIGHT CONTENT --}}
    <div class="col-md-9">

        {{-- =================== API KEYS =================== --}}
        <div class="settings-panel active" id="panel-api">
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-key"></i>
                    API Configuration
                </div>
                <p class="settings-section-subtitle">Configure external API keys used to fetch real-time data</p>

                {{-- GNews API --}}
                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">📰 GNews API Key</div>
                        <div class="setting-row-desc">Used for fetching real-time supply chain & trade news articles</div>
                    </div>
                    <div class="api-key-wrap">
                        <input type="password" class="setting-input" id="gnewsKey" placeholder="Enter GNews API key..." autocomplete="off">
                        <button class="api-eye-btn" onclick="toggleApiKey('gnewsKey', this)">
                            <i class="fa-regular fa-eye"></i> Show
                        </button>
                    </div>
                </div>

                {{-- Open-Meteo (no key) --}}
                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🌤 Open-Meteo (Weather)</div>
                        <div class="setting-row-desc">No API key required — free & unlimited weather data</div>
                    </div>
                    <span class="badge" style="background:#d1fae5; color:#065f46; font-size:12px; padding:6px 14px; border-radius:20px;">
                        <i class="fa-solid fa-check-circle me-1"></i> No key required
                    </span>
                </div>

                {{-- REST Countries --}}
                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🌍 REST Countries API</div>
                        <div class="setting-row-desc">Used to fetch country flags, capitals, languages, and timezones</div>
                    </div>
                    <span class="badge" style="background:#d1fae5; color:#065f46; font-size:12px; padding:6px 14px; border-radius:20px;">
                        <i class="fa-solid fa-check-circle me-1"></i> No key required
                    </span>
                </div>

                {{-- OpenExchange (optional) --}}
                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">💱 Exchange Rate API <span class="badge bg-warning text-dark ms-2" style="font-size:10px; border-radius:8px;">Optional</span></div>
                        <div class="setting-row-desc">For real-time currency exchange data (currently mocked)</div>
                    </div>
                    <div class="api-key-wrap">
                        <input type="password" class="setting-input" id="exchangeKey" placeholder="Enter Exchange Rate key..." autocomplete="off">
                        <button class="api-eye-btn" onclick="toggleApiKey('exchangeKey', this)">
                            <i class="fa-regular fa-eye"></i> Show
                        </button>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button class="btn-setting-save" onclick="saveApiKeys()">
                        <i class="fa-solid fa-floppy-disk"></i> Save API Keys
                    </button>
                    <button class="btn-setting-secondary" onclick="testApiConnection()">
                        <i class="fa-solid fa-satellite-dish"></i> Test Connection
                    </button>
                </div>
            </div>

            {{-- API Status --}}
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-signal"></i>
                    Data Source Status
                </div>
                <p class="settings-section-subtitle">Live status of all connected external APIs</p>

                <div class="datasource-item">
                    <div class="ds-icon" style="background:#dbeafe; color:#1d4ed8;">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                    <div>
                        <div class="ds-name">GNews API</div>
                        <div class="ds-desc">gnews.io — News articles & headlines</div>
                    </div>
                    <span class="ds-status limited" id="ds-gnews">⚡ Limited</span>
                </div>

                <div class="datasource-item">
                    <div class="ds-icon" style="background:#fef3c7; color:#d97706;">
                        <i class="fa-solid fa-cloud-sun"></i>
                    </div>
                    <div>
                        <div class="ds-name">Open-Meteo</div>
                        <div class="ds-desc">open-meteo.com — Weather & climate</div>
                    </div>
                    <span class="ds-status ok" id="ds-meteo">✅ Online</span>
                </div>

                <div class="datasource-item">
                    <div class="ds-icon" style="background:#d1fae5; color:#065f46;">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                    <div>
                        <div class="ds-name">REST Countries</div>
                        <div class="ds-desc">restcountries.com — Country metadata</div>
                    </div>
                    <span class="ds-status ok" id="ds-countries">✅ Online</span>
                </div>

                <div class="datasource-item">
                    <div class="ds-icon" style="background:#ede9fe; color:#7c3aed;">
                        <i class="fa-solid fa-ship"></i>
                    </div>
                    <div>
                        <div class="ds-name">Port Database</div>
                        <div class="ds-desc">Local MySQL — Maritime ports data</div>
                    </div>
                    <span class="ds-status ok" id="ds-ports">✅ Online</span>
                </div>

                <div class="datasource-item">
                    <div class="ds-icon" style="background:#fee2e2; color:#dc2626;">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div>
                        <div class="ds-name">Exchange Rate API</div>
                        <div class="ds-desc">Currency exchange rates (mocked)</div>
                    </div>
                    <span class="ds-status limited" id="ds-currency">⚡ Mocked</span>
                </div>

                <div class="mt-3">
                    <button class="btn-setting-secondary" onclick="refreshApiStatus()">
                        <i class="fa-solid fa-rotate-right"></i> Refresh Status
                    </button>
                </div>
            </div>
        </div>

        {{-- =================== WATCHLIST =================== --}}
        <div class="settings-panel" id="panel-watchlist">
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-star"></i>
                    Watchlist Manager
                </div>
                <p class="settings-section-subtitle">Countries you've starred for quick monitoring — stored locally in your browser</p>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted" style="font-size:13px;" id="watchlistCountLabel">Loading...</span>
                    <button class="btn-setting-danger" id="clearAllWatchBtn" onclick="clearAllWatchlist()" style="display:none;">
                        <i class="fa-solid fa-trash"></i> Clear All
                    </button>
                </div>

                <div id="watchlistContainer">
                    <div class="watchlist-empty">
                        <i class="fa-regular fa-star"></i>
                        <p class="fw-semibold mb-1" style="color:#999;">Loading watchlist...</p>
                    </div>
                </div>
            </div>

            {{-- Export Watchlist --}}
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-file-export"></i>
                    Export & Import
                </div>
                <p class="settings-section-subtitle">Backup or restore your watchlist data</p>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn-setting-save" onclick="exportWatchlist()">
                        <i class="fa-solid fa-download"></i> Export JSON
                    </button>
                    <button class="btn-setting-secondary" onclick="$('#importFile').click()">
                        <i class="fa-solid fa-upload"></i> Import JSON
                    </button>
                    <input type="file" id="importFile" accept=".json" style="display:none;" onchange="importWatchlist(this)">
                </div>
            </div>
        </div>

        {{-- =================== RISK WEIGHTS =================== --}}
        <div class="settings-panel" id="panel-risk">
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-chart-bar"></i>
                    Risk Scoring Weights
                </div>
                <p class="settings-section-subtitle">Adjust how much each factor contributes to the overall risk score (total must equal 100%)</p>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🌤 Weather Risk</div>
                        <div class="setting-row-desc">Impact of weather conditions on logistics operations</div>
                    </div>
                    <div class="weight-slider-wrap">
                        <input type="range" class="weight-slider" id="wWeather" min="0" max="100" value="25" oninput="updateWeightDisplay()">
                        <span class="weight-val" id="wWeatherVal">25%</span>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">📈 Inflation Risk</div>
                        <div class="setting-row-desc">Economic inflation pressure on supply chains</div>
                    </div>
                    <div class="weight-slider-wrap">
                        <input type="range" class="weight-slider" id="wInflation" min="0" max="100" value="25" oninput="updateWeightDisplay()">
                        <span class="weight-val" id="wInflationVal">25%</span>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">💱 Currency Risk</div>
                        <div class="setting-row-desc">Currency volatility and exchange rate instability</div>
                    </div>
                    <div class="weight-slider-wrap">
                        <input type="range" class="weight-slider" id="wCurrency" min="0" max="100" value="25" oninput="updateWeightDisplay()">
                        <span class="weight-val" id="wCurrencyVal">25%</span>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">📰 News Sentiment Risk</div>
                        <div class="setting-row-desc">Negative news sentiment impact on trade confidence</div>
                    </div>
                    <div class="weight-slider-wrap">
                        <input type="range" class="weight-slider" id="wNews" min="0" max="100" value="25" oninput="updateWeightDisplay()">
                        <span class="weight-val" id="wNewsVal">25%</span>
                    </div>
                </div>

                <div class="mt-3 p-3 rounded-3" id="weightTotalBox" style="background:#f4f7f6;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="font-size:14px;">Total Weight</span>
                        <span class="fw-bold fs-5" id="weightTotal">100%</span>
                    </div>
                    <div class="cache-bar-wrap mt-2">
                        <div class="cache-bar" id="weightTotalBar" style="width:100%;"></div>
                    </div>
                    <small id="weightWarning" class="text-danger mt-1 d-none"><i class="fa-solid fa-triangle-exclamation me-1"></i>Weights must total 100%</small>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn-setting-save" onclick="saveRiskWeights()">
                        <i class="fa-solid fa-floppy-disk"></i> Save Weights
                    </button>
                    <button class="btn-setting-secondary" onclick="resetRiskWeights()">
                        <i class="fa-solid fa-rotate-left"></i> Reset Default
                    </button>
                </div>
            </div>

            {{-- Risk Level Thresholds --}}
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-sliders"></i>
                    Risk Level Thresholds
                </div>
                <p class="settings-section-subtitle">Define the cutoffs for Low / Medium / High risk classification</p>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🟢 Low Risk Threshold</div>
                        <div class="setting-row-desc">Score below this = Low Risk</div>
                    </div>
                    <input type="number" class="setting-input" id="threshLow" value="35" min="0" max="100" style="max-width:120px; text-align:center;">
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🟡 Medium Risk Threshold</div>
                        <div class="setting-row-desc">Score below this (and above Low) = Medium Risk</div>
                    </div>
                    <input type="number" class="setting-input" id="threshMedium" value="65" min="0" max="100" style="max-width:120px; text-align:center;">
                </div>

                <div class="mt-4">
                    <button class="btn-setting-save" onclick="saveThresholds()">
                        <i class="fa-solid fa-floppy-disk"></i> Save Thresholds
                    </button>
                </div>
            </div>
        </div>

        {{-- =================== PREFERENCES =================== --}}
        <div class="settings-panel" id="panel-preferences">
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-palette"></i>
                    Display Preferences
                </div>
                <p class="settings-section-subtitle">Customize the look and feel of your dashboard</p>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🎨 Accent Color</div>
                        <div class="setting-row-desc">Primary color used throughout the dashboard</div>
                    </div>
                    <div class="color-swatch-row">
                        <div class="color-swatch selected" style="background:#00b575;" data-color="#00b575" onclick="selectColor(this)" title="Emerald (Default)"></div>
                        <div class="color-swatch" style="background:#0ea5e9;" data-color="#0ea5e9" onclick="selectColor(this)" title="Sky Blue"></div>
                        <div class="color-swatch" style="background:#8b5cf6;" data-color="#8b5cf6" onclick="selectColor(this)" title="Purple"></div>
                        <div class="color-swatch" style="background:#f59e0b;" data-color="#f59e0b" onclick="selectColor(this)" title="Amber"></div>
                        <div class="color-swatch" style="background:#ef4444;" data-color="#ef4444" onclick="selectColor(this)" title="Red"></div>
                        <div class="color-swatch" style="background:#ec4899;" data-color="#ec4899" onclick="selectColor(this)" title="Pink"></div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🌓 Theme Mode</div>
                        <div class="setting-row-desc">Currently only Light mode is supported</div>
                    </div>
                    <select class="setting-select" id="themeMode">
                        <option value="light" selected>☀️ Light Mode</option>
                        <option value="dark">🌙 Dark Mode</option>
                    </select>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🌐 Default Language</div>
                        <div class="setting-row-desc">Interface display language</div>
                    </div>
                    <select class="setting-select" id="prefLang">
                        <option value="en" selected>🇬🇧 English</option>
                        <option value="id">🇮🇩 Bahasa Indonesia</option>
                    </select>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">📊 Default Dashboard Country</div>
                        <div class="setting-row-desc">Country pre-selected when opening the main dashboard</div>
                    </div>
                    <select class="setting-select" id="prefDefaultCountry" style="min-width:200px;">
                        <option value="">— None (manual select) —</option>
                    </select>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🗺 Default Map Zoom Level</div>
                        <div class="setting-row-desc">Starting zoom for the world map views</div>
                    </div>
                    <input type="number" class="setting-input" id="prefMapZoom" value="2" min="1" max="10" style="max-width:100px; text-align:center;">
                </div>

                <div class="mt-4">
                    <button class="btn-setting-save" onclick="savePreferences()">
                        <i class="fa-solid fa-floppy-disk"></i> Save Preferences
                    </button>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-bell"></i>
                    Notification Settings
                </div>
                <p class="settings-section-subtitle">Control which events trigger dashboard alerts</p>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">⚠️ High Risk Alerts</div>
                        <div class="setting-row-desc">Show banner when a watchlisted country has high risk score</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="notifHighRisk" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">📰 Negative News Alert</div>
                        <div class="setting-row-desc">Alert when negative news sentiment exceeds 60%</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="notifNegNews" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🌪️ Severe Weather Alert</div>
                        <div class="setting-row-desc">Alert when wind speed exceeds 80 km/h in monitored regions</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="notifWeather">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">💡 Dashboard Tips</div>
                        <div class="setting-row-desc">Show helpful tips and feature highlights on first visit</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="notifTips" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="mt-4">
                    <button class="btn-setting-save" onclick="saveNotifications()">
                        <i class="fa-solid fa-floppy-disk"></i> Save Notifications
                    </button>
                </div>
            </div>
        </div>

        {{-- =================== DATA & CACHE =================== --}}
        <div class="settings-panel" id="panel-cache">
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-database"></i>
                    Browser Cache & Storage
                </div>
                <p class="settings-section-subtitle">Manage locally stored data in your browser (localStorage)</p>

                <div class="sys-info-row">
                    <span class="si-label">Watchlist entries</span>
                    <span class="si-val" id="cacheWatchCount">—</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">Saved preferences</span>
                    <span class="si-val" id="cachePrefKeys">—</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">Estimated storage used</span>
                    <span class="si-val" id="cacheSize">—</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">localStorage capacity</span>
                    <span class="si-val">~5 MB</span>
                </div>

                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Storage usage</small>
                        <small class="fw-bold" id="cacheUsagePct">0%</small>
                    </div>
                    <div class="cache-bar-wrap">
                        <div class="cache-bar" id="cacheUsageBar" style="width:0%;"></div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button class="btn-setting-secondary" onclick="refreshCacheInfo()">
                        <i class="fa-solid fa-rotate-right"></i> Refresh Info
                    </button>
                    <button class="btn-setting-danger" onclick="clearLocalStorage()">
                        <i class="fa-solid fa-trash"></i> Clear Browser Storage
                    </button>
                </div>
            </div>

            {{-- Data Refresh --}}
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    Data Refresh Settings
                </div>
                <p class="settings-section-subtitle">Control how often data is fetched and refreshed</p>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">⏱ Auto-Refresh Interval</div>
                        <div class="setting-row-desc">Automatically reload dashboard data periodically</div>
                    </div>
                    <select class="setting-select" id="refreshInterval">
                        <option value="0">Disabled</option>
                        <option value="60">Every 1 minute</option>
                        <option value="300" selected>Every 5 minutes</option>
                        <option value="600">Every 10 minutes</option>
                        <option value="1800">Every 30 minutes</option>
                    </select>
                </div>

                <div class="setting-row">
                    <div class="setting-row-left">
                        <div class="setting-row-label">🗓 News Cache Duration</div>
                        <div class="setting-row-desc">How long fetched news articles are stored in browser</div>
                    </div>
                    <select class="setting-select" id="newsCacheDur">
                        <option value="0">No caching</option>
                        <option value="30" selected>30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="360">6 hours</option>
                    </select>
                </div>

                <div class="mt-4">
                    <button class="btn-setting-save" onclick="saveDataSettings()">
                        <i class="fa-solid fa-floppy-disk"></i> Save Settings
                    </button>
                </div>
            </div>
        </div>

        {{-- =================== ABOUT =================== --}}
        <div class="settings-panel" id="panel-about">
            <div class="settings-section">
                <div class="about-logo-wrap">
                    <i class="fa-solid fa-earth-americas logo-icon"></i>
                    <h4>SupplyChain Intelligence Platform</h4>
                    <p>Global risk monitoring for logistics & trade professionals</p>
                    <div class="version-badge">
                        <i class="fa-solid fa-tag"></i> Version 1.0.0
                    </div>
                </div>

                <div class="sys-info-row">
                    <span class="si-label">Platform</span>
                    <span class="si-val">Laravel 11 + Bootstrap 5</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">Environment</span>
                    <span class="si-val">{{ app()->environment() }}</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">PHP Version</span>
                    <span class="si-val">{{ phpversion() }}</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">Laravel Version</span>
                    <span class="si-val">{{ app()->version() }}</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">Database</span>
                    <span class="si-val">MySQL (supply_chain_db)</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">Map Library</span>
                    <span class="si-val">Leaflet.js v1.9.4</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">Charts</span>
                    <span class="si-val">Chart.js</span>
                </div>
                <div class="sys-info-row">
                    <span class="si-label">Current Time (Server)</span>
                    <span class="si-val">{{ now()->format('Y-m-d H:i:s T') }}</span>
                </div>
            </div>

            {{-- Features List --}}
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-rocket"></i>
                    Platform Features
                </div>
                <p class="settings-section-subtitle">Overview of all available modules and capabilities</p>

                @php
                $features = [
                    ['icon' => 'fa-chart-line', 'color' => '#00b575', 'name' => 'Risk Dashboard', 'desc' => 'Real-time country risk scoring with weather, inflation, currency & news factors'],
                    ['icon' => 'fa-globe', 'color' => '#0ea5e9', 'name' => 'Countries Directory', 'desc' => '195+ countries with flags, capital, population, GDP & risk breakdown'],
                    ['icon' => 'fa-cloud-sun-rain', 'color' => '#8b5cf6', 'name' => 'Weather Monitoring', 'desc' => 'Live weather data with logistics delay risk indicators via Open-Meteo'],
                    ['icon' => 'fa-ship', 'color' => '#f59e0b', 'name' => 'Port Locator', 'desc' => 'Interactive global maritime port map with search & country filter'],
                    ['icon' => 'fa-newspaper', 'color' => '#ef4444', 'name' => 'News Intelligence', 'desc' => 'AI-powered lexicon sentiment analysis on supply chain news'],
                    ['icon' => 'fa-star', 'color' => '#ec4899', 'name' => 'Watchlist System', 'desc' => 'Star and monitor countries with persistent local storage'],
                ];
                @endphp

                @foreach($features as $f)
                <div class="datasource-item">
                    <div class="ds-icon" style="background:{{ $f['color'] }}22; color:{{ $f['color'] }};">
                        <i class="fa-solid {{ $f['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="ds-name">{{ $f['name'] }}</div>
                        <div class="ds-desc">{{ $f['desc'] }}</div>
                    </div>
                    <span class="ds-status ok">✅ Active</span>
                </div>
                @endforeach
            </div>

            {{-- Links --}}
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fa-solid fa-link"></i>
                    Quick Links
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="/" class="btn-setting-save" style="text-decoration:none;">
                        <i class="fa-solid fa-chart-line"></i> Dashboard
                    </a>
                    <a href="/countries" class="btn-setting-secondary" style="text-decoration:none;">
                        <i class="fa-solid fa-globe"></i> Countries
                    </a>
                    <a href="/weather" class="btn-setting-secondary" style="text-decoration:none;">
                        <i class="fa-solid fa-cloud-sun"></i> Weather
                    </a>
                    <a href="/ports" class="btn-setting-secondary" style="text-decoration:none;">
                        <i class="fa-solid fa-ship"></i> Ports
                    </a>
                    <a href="/news" class="btn-setting-secondary" style="text-decoration:none;">
                        <i class="fa-solid fa-newspaper"></i> News
                    </a>
                </div>
            </div>
        </div>

    </div>{{-- end col-md-9 --}}
</div>{{-- end row --}}

{{-- Toast Notification --}}
<div class="settings-toast" id="settingsToast">
    <span class="toast-icon" id="toastIcon">✅</span>
    <span id="toastMessage">Saved successfully!</span>
</div>

@endsection

@section('scripts')
<script>
/* ===================================================
   PANEL NAVIGATION
   =================================================== */
function switchPanel(name, el) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.settings-nav-item').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + name).classList.add('active');
    if (el) el.classList.add('active');
}

/* ===================================================
   TOAST
   =================================================== */
function showToast(msg, type = 'success') {
    const t = $('#settingsToast');
    $('#toastMessage').text(msg);
    $('#toastIcon').text(type === 'success' ? '✅' : '❌');
    t.removeClass('success error').addClass(type).addClass('show');
    setTimeout(() => t.removeClass('show'), 3200);
}

/* ===================================================
   API KEYS
   =================================================== */
function toggleApiKey(id, btn) {
    const inp = document.getElementById(id);
    const isHidden = inp.type === 'password';
    inp.type = isHidden ? 'text' : 'password';
    btn.innerHTML = isHidden
        ? '<i class="fa-regular fa-eye-slash"></i> Hide'
        : '<i class="fa-regular fa-eye"></i> Show';
}

function saveApiKeys() {
    const gnews    = $('#gnewsKey').val().trim();
    const exchange = $('#exchangeKey').val().trim();
    if (gnews)    localStorage.setItem('gnews_api_key', gnews);
    if (exchange) localStorage.setItem('exchange_api_key', exchange);
    showToast('API keys saved to browser storage!', 'success');
}

function testApiConnection() {
    showToast('Testing connections...', 'success');
    // Test Open-Meteo (always free)
    $.get('https://api.open-meteo.com/v1/forecast?latitude=0&longitude=0&current=temperature_2m')
        .done(() => {
            $('#ds-meteo').text('✅ Online').removeClass('limited offline').addClass('ok');
        })
        .fail(() => {
            $('#ds-meteo').text('❌ Offline').removeClass('ok limited').addClass('offline');
        });

    // Test REST Countries
    $.get('https://restcountries.com/v3.1/alpha/DEU?fields=name')
        .done(() => {
            $('#ds-countries').text('✅ Online').removeClass('limited offline').addClass('ok');
        })
        .fail(() => {
            $('#ds-countries').text('❌ Offline').removeClass('ok limited').addClass('offline');
        });

    // Test GNews via our own endpoint
    $.get('/api/news?q=logistics')
        .done(() => {
            $('#ds-gnews').text('✅ Online').removeClass('limited offline').addClass('ok');
        })
        .fail(() => {
            $('#ds-gnews').text('❌ Offline').removeClass('ok limited').addClass('offline');
        });

    setTimeout(() => showToast('Connection test complete!', 'success'), 2200);
}

function refreshApiStatus() {
    testApiConnection();
}

/* ===================================================
   WATCHLIST
   =================================================== */
let allCountriesData = [];
let watchlistSet = new Set(JSON.parse(localStorage.getItem('watchlist') || '[]'));

function loadWatchlist() {
    const count = watchlistSet.size;
    $('#hdrWatchCount').text(count);
    $('#cacheWatchCount').text(count + ' countries');
    $('#watchlistCountLabel').text(count + ' countr' + (count === 1 ? 'y' : 'ies') + ' in your watchlist');

    if (count === 0) {
        $('#watchlistContainer').html(`
            <div class="watchlist-empty">
                <i class="fa-regular fa-star"></i>
                <p class="fw-semibold mb-1" style="color:#999;">No countries in watchlist</p>
                <small class="text-muted">Visit the <a href="/countries">Countries page</a> and click ⭐ to add some.</small>
            </div>`);
        $('#clearAllWatchBtn').hide();
        return;
    }

    $('#clearAllWatchBtn').show();

    if (allCountriesData.length === 0) {
        $.get('/api/countries', function(data) {
            allCountriesData = data;
            renderWatchlistItems();
        });
    } else {
        renderWatchlistItems();
    }
}

function renderWatchlistItems() {
    let html = '';
    watchlistSet.forEach(iso => {
        const c = allCountriesData.find(x => x.iso_code === iso);
        if (!c) return;
        const flagUrl = `https://flagcdn.com/w80/${iso.slice(0,2).toLowerCase()}.png`;
        html += `
        <div class="watchlist-item" id="watch-item-${iso}">
            <img src="${flagUrl}" class="watchlist-flag" alt="${c.name}" onerror="this.src='https://via.placeholder.com/40x28?text=${iso}'">
            <div>
                <div class="watchlist-name">${c.name}</div>
                <div class="watchlist-iso">${iso} · ${c.region || '—'}</div>
            </div>
            <a href="/countries" class="btn-setting-secondary" style="font-size:12px; padding:5px 10px; text-decoration:none;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
            <button class="watchlist-remove" onclick="removeFromWatchlist('${iso}')">
                <i class="fa-solid fa-xmark me-1"></i>Remove
            </button>
        </div>`;
    });
    $('#watchlistContainer').html(html || '<div class="watchlist-empty"><i class="fa-regular fa-star"></i><p>No countries found</p></div>');
}

function removeFromWatchlist(iso) {
    watchlistSet.delete(iso);
    localStorage.setItem('watchlist', JSON.stringify([...watchlistSet]));
    $(`#watch-item-${iso}`).fadeOut(250, function() { $(this).remove(); });
    loadWatchlist();
    showToast(`Removed from watchlist.`, 'success');
}

function clearAllWatchlist() {
    if (!confirm('Are you sure you want to clear your entire watchlist?')) return;
    watchlistSet.clear();
    localStorage.setItem('watchlist', JSON.stringify([]));
    loadWatchlist();
    showToast('Watchlist cleared.', 'success');
}

function exportWatchlist() {
    const data = {
        exported_at: new Date().toISOString(),
        watchlist: [...watchlistSet]
    };
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'watchlist_export.json';
    a.click();
    showToast('Watchlist exported!', 'success');
}

function importWatchlist(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = JSON.parse(e.target.result);
            const items = data.watchlist || data;
            if (Array.isArray(items)) {
                items.forEach(iso => watchlistSet.add(iso));
                localStorage.setItem('watchlist', JSON.stringify([...watchlistSet]));
                loadWatchlist();
                showToast(`Imported ${items.length} items!`, 'success');
            }
        } catch(err) {
            showToast('Invalid JSON file.', 'error');
        }
    };
    reader.readAsText(file);
    input.value = '';
}

/* ===================================================
   RISK WEIGHTS
   =================================================== */
function updateWeightDisplay() {
    const w = parseInt($('#wWeather').val());
    const i = parseInt($('#wInflation').val());
    const c = parseInt($('#wCurrency').val());
    const n = parseInt($('#wNews').val());
    const total = w + i + c + n;

    $('#wWeatherVal').text(w + '%');
    $('#wInflationVal').text(i + '%');
    $('#wCurrencyVal').text(c + '%');
    $('#wNewsVal').text(n + '%');
    $('#weightTotal').text(total + '%');

    const pct = Math.min(100, (total / 100) * 100);
    $('#weightTotalBar').css('width', pct + '%');

    if (total !== 100) {
        $('#weightWarning').removeClass('d-none');
        $('#weightTotalBox').css('background', total > 100 ? '#fef2f2' : '#fffbeb');
        $('#weightTotal').css('color', '#ef4444');
    } else {
        $('#weightWarning').addClass('d-none');
        $('#weightTotalBox').css('background', '#f0fdf4');
        $('#weightTotal').css('color', '#00b575');
    }
}

function saveRiskWeights() {
    const w = parseInt($('#wWeather').val());
    const i = parseInt($('#wInflation').val());
    const c = parseInt($('#wCurrency').val());
    const n = parseInt($('#wNews').val());
    if (w + i + c + n !== 100) {
        showToast('Total weights must equal 100%!', 'error');
        return;
    }
    localStorage.setItem('risk_weights', JSON.stringify({ weather: w, inflation: i, currency: c, news: n }));
    showToast('Risk weights saved!', 'success');
}

function resetRiskWeights() {
    $('#wWeather, #wInflation, #wCurrency, #wNews').val(25);
    updateWeightDisplay();
    showToast('Reset to default (25% each)', 'success');
}

function saveThresholds() {
    const low = parseInt($('#threshLow').val());
    const med = parseInt($('#threshMedium').val());
    if (low >= med) {
        showToast('Low threshold must be less than Medium!', 'error');
        return;
    }
    localStorage.setItem('risk_thresholds', JSON.stringify({ low, medium: med }));
    showToast('Thresholds saved!', 'success');
}

/* ===================================================
   PREFERENCES
   =================================================== */
function selectColor(el) {
    document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('selected'));
    el.classList.add('selected');
    const color = el.dataset.color;
    document.documentElement.style.setProperty('--primary-color', color);
    localStorage.setItem('accent_color', color);
    showToast('Accent color updated!', 'success');
}

function savePreferences() {
    const prefs = {
        theme: $('#themeMode').val(),
        lang: $('#prefLang').val(),
        defaultCountry: $('#prefDefaultCountry').val(),
        mapZoom: $('#prefMapZoom').val(),
    };
    
    const oldPrefs = JSON.parse(localStorage.getItem('dashboard_prefs') || '{}');
    localStorage.setItem('dashboard_prefs', JSON.stringify(prefs));
    
    if (prefs.theme === 'dark') {
        document.documentElement.classList.add('dark-mode');
    } else {
        document.documentElement.classList.remove('dark-mode');
    }

    if (oldPrefs.lang !== prefs.lang) {
        window.location.reload();
        return;
    }

    showToast('Preferences saved!', 'success');
}

function saveNotifications() {
    const notifs = {
        highRisk: $('#notifHighRisk').is(':checked'),
        negNews: $('#notifNegNews').is(':checked'),
        weather: $('#notifWeather').is(':checked'),
        tips: $('#notifTips').is(':checked'),
    };
    localStorage.setItem('notif_settings', JSON.stringify(notifs));
    showToast('Notification preferences saved!', 'success');
}

/* ===================================================
   CACHE INFO
   =================================================== */
function refreshCacheInfo() {
    let total = 0;
    let prefKeys = 0;
    const prefKeyNames = ['dashboard_prefs', 'notif_settings', 'risk_weights', 'risk_thresholds', 'accent_color', 'gnews_api_key', 'exchange_api_key'];
    prefKeyNames.forEach(k => {
        if (localStorage.getItem(k)) {
            prefKeys++;
            total += localStorage.getItem(k).length;
        }
    });

    // watchlist
    const wl = localStorage.getItem('watchlist') || '[]';
    total += wl.length;

    const sizeKB = (total / 1024).toFixed(2);
    const sizeMB = (total / (1024 * 1024)).toFixed(3);
    const pct = Math.min(100, (total / (5 * 1024 * 1024)) * 100).toFixed(1);

    $('#cachePrefKeys').text(prefKeys + ' keys');
    $('#cacheSize').text(sizeKB + ' KB (' + sizeMB + ' MB)');
    $('#cacheUsagePct').text(pct + '%');
    $('#cacheUsageBar').css('width', pct + '%');
    $('#hdrCacheSize').text(prefKeys + watchlistSet.size + ' items');
}

function clearLocalStorage() {
    if (!confirm('This will clear all browser-stored settings, watchlist, and preferences. Are you sure?')) return;
    localStorage.clear();
    watchlistSet = new Set();
    showToast('Browser storage cleared!', 'success');
    refreshCacheInfo();
    loadWatchlist();
}

function saveDataSettings() {
    const s = {
        refreshInterval: $('#refreshInterval').val(),
        newsCacheDur: $('#newsCacheDur').val()
    };
    localStorage.setItem('data_settings', JSON.stringify(s));
    showToast('Data settings saved!', 'success');
}

/* ===================================================
   LOAD SAVED SETTINGS
   =================================================== */
function loadSavedSettings() {
    // Risk weights
    const rw = JSON.parse(localStorage.getItem('risk_weights') || '{"weather":25,"inflation":25,"currency":25,"news":25}');
    $('#wWeather').val(rw.weather);
    $('#wInflation').val(rw.inflation);
    $('#wCurrency').val(rw.currency);
    $('#wNews').val(rw.news);
    updateWeightDisplay();

    // Thresholds
    const rt = JSON.parse(localStorage.getItem('risk_thresholds') || '{"low":35,"medium":65}');
    $('#threshLow').val(rt.low);
    $('#threshMedium').val(rt.medium);

    // Prefs
    const prefs = JSON.parse(localStorage.getItem('dashboard_prefs') || '{}');
    if (prefs.lang)  $('#prefLang').val(prefs.lang);
    if (prefs.theme) $('#themeMode').val(prefs.theme);
    if (prefs.mapZoom) $('#prefMapZoom').val(prefs.mapZoom);

    // Notifications
    const notifs = JSON.parse(localStorage.getItem('notif_settings') || '{}');
    if (notifs.highRisk !== undefined) $('#notifHighRisk').prop('checked', notifs.highRisk);
    if (notifs.negNews  !== undefined) $('#notifNegNews').prop('checked', notifs.negNews);
    if (notifs.weather  !== undefined) $('#notifWeather').prop('checked', notifs.weather);
    if (notifs.tips     !== undefined) $('#notifTips').prop('checked', notifs.tips);

    // Accent color
    const color = localStorage.getItem('accent_color');
    if (color) {
        document.documentElement.style.setProperty('--primary-color', color);
        document.querySelectorAll('.color-swatch').forEach(s => {
            s.classList.toggle('selected', s.dataset.color === color);
        });
    }

    // Data settings
    const ds = JSON.parse(localStorage.getItem('data_settings') || '{}');
    if (ds.refreshInterval) $('#refreshInterval').val(ds.refreshInterval);
    if (ds.newsCacheDur)    $('#newsCacheDur').val(ds.newsCacheDur);

    // API keys (masked)
    const gnews = localStorage.getItem('gnews_api_key');
    if (gnews) $('#gnewsKey').val(gnews);

    const exc = localStorage.getItem('exchange_api_key');
    if (exc) $('#exchangeKey').val(exc);
}

/* ===================================================
   INIT
   =================================================== */
$(document).ready(function() {
    // Load countries for default country dropdown
    $.get('/api/countries', function(data) {
        allCountriesData = data;
        data.sort((a,b) => a.name.localeCompare(b.name)).forEach(c => {
            $('#prefDefaultCountry').append(`<option value="${c.iso_code}">${c.name}</option>`);
        });

        // Set saved default country
        const prefs = JSON.parse(localStorage.getItem('dashboard_prefs') || '{}');
        if (prefs.defaultCountry) $('#prefDefaultCountry').val(prefs.defaultCountry);

        // Load watchlist now that countries are loaded
        loadWatchlist();
    });

    loadSavedSettings();
    refreshCacheInfo();

    // Live theme mode switcher
    $('#themeMode').on('change', function() {
        const theme = $(this).val();
        const prefs = JSON.parse(localStorage.getItem('dashboard_prefs') || '{}');
        prefs.theme = theme;
        localStorage.setItem('dashboard_prefs', JSON.stringify(prefs));
        if (theme === 'dark') {
            document.documentElement.classList.add('dark-mode');
        } else {
            document.documentElement.classList.remove('dark-mode');
        }
        showToast('Theme updated to ' + theme + ' mode!', 'success');
    });

    // Live language switcher
    $('#prefLang').on('change', function() {
        const lang = $(this).val();
        const prefs = JSON.parse(localStorage.getItem('dashboard_prefs') || '{}');
        prefs.lang = lang;
        localStorage.setItem('dashboard_prefs', JSON.stringify(prefs));
        window.location.reload();
    });
});
</script>
@endsection