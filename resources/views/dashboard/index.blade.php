@extends('layouts.dashboard')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* ===== LOADING SPINNER ===== */
    .loading-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        z-index: 100;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
    }
    .loading-overlay.active { opacity: 1; pointer-events: all; }

    .spinner {
        width: 40px; height: 40px;
        border: 4px solid rgba(0,181,117,0.2);
        border-top-color: #00b575;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ===== COUNTRY SELECTOR ROW ===== */
    .selector-bar {
        background: #fff;
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .selector-bar .select2-flag {
        width: 28px; height: 20px;
        border-radius: 3px;
        object-fit: cover;
    }
    #countrySelector {
        flex: 1;
        min-width: 220px;
        border: 1.5px solid #e8ecef;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 15px;
        font-weight: 500;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }
    #countrySelector:focus {
        border-color: #00b575;
        box-shadow: 0 0 0 3px rgba(0,181,117,0.12);
    }
    .country-display {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }
    #countryNameDisplay {
        font-size: 20px;
        font-weight: 700;
        color: #1a1f2e;
        margin: 0;
    }

    /* ===== STAT CARDS ===== */
    .stat-card {
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
    }
    .stat-card .card-label {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #888;
        margin-bottom: 8px;
    }
    .stat-card .card-value {
        font-size: 26px;
        font-weight: 800;
        color: #1a1f2e;
        line-height: 1;
        margin-bottom: 4px;
        transition: all 0.3s;
    }
    .stat-card .card-sub {
        font-size: 12px;
        color: #aaa;
    }
    .stat-card .icon-box {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
    }

    /* ===== INTERACTIVE WORLD MAP ===== */
    #worldMap {
        height: 430px;
        border-radius: 14px;
        z-index: 1;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
    }
    .map-popup-title {
        font-weight: 700;
        font-size: 15px;
        color: #1a1f2e;
        margin-bottom: 8px;
    }
    .map-popup-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        padding: 3px 0;
        border-bottom: 1px dashed #f0f0f0;
    }
    .map-popup-row:last-child { border-bottom: none; }
    .map-popup-row .label { color: #888; }
    .map-popup-row .value { font-weight: 600; color: #1a1f2e; }

    /* ===== WEATHER / PORT MAPS ===== */
    #weatherMap, #portMap {
        height: 300px;
        border-radius: 12px;
        z-index: 1;
    }

    /* ===== DETAIL PANEL ===== */
    .detail-panel {
        background: linear-gradient(135deg, #00b575 0%, #008f5c 100%);
        color: #fff;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .detail-panel::after {
        content: '';
        position: absolute;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
        right: -40px; top: -60px;
    }
    .detail-panel .country-name {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 4px;
    }
    .detail-panel .region-badge {
        background: rgba(255,255,255,0.2);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        display: inline-block;
        margin-bottom: 16px;
    }
    .detail-panel .meta-row {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }
    .detail-panel .meta-item {
        text-align: center;
    }
    .detail-panel .meta-item .meta-val {
        font-size: 20px;
        font-weight: 700;
    }
    .detail-panel .meta-item .meta-lbl {
        font-size: 11px;
        opacity: 0.75;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ===== RISK GAUGE ===== */
    .risk-gauge-wrap {
        text-align: center;
        padding: 10px 0;
    }
    .risk-gauge-arc {
        position: relative;
        width: 130px;
        height: 70px;
        margin: 0 auto 8px;
        overflow: hidden;
    }
    .risk-gauge-arc svg { width: 130px; height: 130px; margin-top: -60px; }
    .risk-needle {
        transform-origin: 65px 65px;
        transition: transform 1s cubic-bezier(.4,2,.55,.9);
    }
    .risk-label {
        font-size: 22px;
        font-weight: 800;
        color: #1a1f2e;
    }
    .risk-level-text {
        font-size: 13px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 6px;
    }

    /* ===== CHART TABS ===== */
    .tab-nav {
        display: flex;
        gap: 6px;
        margin-bottom: 16px;
    }
    .tab-btn {
        padding: 6px 14px;
        border: 1.5px solid #e8ecef;
        background: transparent;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #888;
        cursor: pointer;
        transition: 0.2s;
    }
    .tab-btn.active {
        background: #00b575;
        border-color: #00b575;
        color: #fff;
    }

    /* ===== WEATHER WIDGET ===== */
    .weather-badge {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .weather-badge .temp { font-size: 28px; font-weight: 800; }
    .weather-badge .detail { font-size: 12px; opacity: 0.85; line-height: 1.6; }

    /* ===== PULSE MARKER ===== */
    .pulse-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pulse-icon-inner {
        width: 14px; height: 14px;
        background: #00b575;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(0,181,117,0.4);
        animation: pulse-anim 1.5s infinite;
    }
    @keyframes pulse-anim {
        0% { box-shadow: 0 0 0 0 rgba(0,181,117,0.5); }
        70% { box-shadow: 0 0 0 14px rgba(0,181,117,0); }
        100% { box-shadow: 0 0 0 0 rgba(0,181,117,0); }
    }

    /* ===== ANIMATE VALUE CHANGE ===== */
    .flash-update {
        animation: flash 0.4s ease;
    }
    @keyframes flash {
        0%   { opacity: 0.2; transform: scale(0.95); }
        100% { opacity: 1;   transform: scale(1); }
    }
</style>
@endsection

@section('content')
{{-- ========== COUNTRY SELECTOR BAR ========== --}}
<div class="selector-bar">
    <i class="fa-solid fa-globe" style="font-size:22px; color:#00b575;"></i>
    <select id="countrySelector">
        <option value="">🔄 Loading countries...</option>
    </select>
    <div class="country-display">
        <h4 id="countryNameDisplay" class="m-0">Select a country</h4>
        <span id="riskLevelBadge" class="badge bg-secondary px-3 py-2" style="border-radius:20px; font-size:13px;">--</span>
    </div>
</div>

{{-- ========== STAT CARDS ========== --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="custom-card stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="card-label">GDP (Current USD)</div>
                <div class="card-value" id="gdpVal">—</div>
                <div class="card-sub" id="gdpYear">World Bank data</div>
            </div>
            <div class="icon-box" style="background:rgba(0,181,117,0.1); color:#00b575;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="custom-card stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="card-label">Inflation Rate</div>
                <div class="card-value" id="inflationVal">—</div>
                <div class="card-sub">Simulated estimate</div>
            </div>
            <div class="icon-box" style="background:rgba(255,184,34,0.12); color:#ffb822;">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="custom-card stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="card-label">Risk Score</div>
                <div class="card-value" id="riskScoreVal">—</div>
                <div class="card-sub" id="riskLevelSub">out of 100</div>
            </div>
            <div class="icon-box" style="background:rgba(255,99,132,0.1); color:#ff6384;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="custom-card stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="card-label">Local Currency</div>
                <div class="card-value" id="currencyVal">—</div>
                <div class="card-sub" id="currencyRegion">—</div>
            </div>
            <div class="icon-box" style="background:rgba(100,130,255,0.1); color:#6482ff;">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
        </div>
    </div>
</div>

{{-- ========== WORLD MAP + DETAIL PANEL ========== --}}
<div class="row g-3 mb-4">
    {{-- World Map --}}
    <div class="col-md-8">
        <div class="custom-card" style="position:relative; padding:16px;">
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-earth-americas me-2 text-success"></i>
                Interactive World Map
                <small class="text-muted fw-normal" style="font-size:12px; margin-left:8px;">Click any country marker to load data</small>
            </h5>
            <div id="worldMap"></div>
            <div class="loading-overlay" id="mapLoader">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
    {{-- Detail Panel --}}
    <div class="col-md-4">
        <div class="detail-panel" id="detailPanel">
            <div class="country-name" id="panelCountry">Select a Country</div>
            <div class="region-badge" id="panelRegion">—</div>
            <div class="meta-row" id="panelMeta">
                <div class="meta-item">
                    <div class="meta-val" id="panelGDP">—</div>
                    <div class="meta-lbl">GDP</div>
                </div>
                <div class="meta-item">
                    <div class="meta-val" id="panelCurrency">—</div>
                    <div class="meta-lbl">Currency</div>
                </div>
                <div class="meta-item">
                    <div class="meta-val" id="panelInflation">—</div>
                    <div class="meta-lbl">Inflation</div>
                </div>
            </div>
        </div>

        {{-- Risk Gauge --}}
        <div class="custom-card text-center">
            <h6 class="fw-bold mb-3">Overall Risk Score</h6>
            <div class="risk-gauge-wrap">
                <div class="risk-gauge-arc">
                    <svg viewBox="0 0 130 130">
                        <!-- background arc -->
                        <path d="M 15 65 A 50 50 0 0 1 115 65" fill="none" stroke="#f0f0f0" stroke-width="12" stroke-linecap="round"/>
                        <!-- green zone -->
                        <path d="M 15 65 A 50 50 0 0 1 48 25" fill="none" stroke="#00b575" stroke-width="12" stroke-linecap="round"/>
                        <!-- yellow zone -->
                        <path d="M 48 25 A 50 50 0 0 1 82 25" fill="none" stroke="#ffb822" stroke-width="12" stroke-linecap="round"/>
                        <!-- red zone -->
                        <path d="M 82 25 A 50 50 0 0 1 115 65" fill="none" stroke="#ff6384" stroke-width="12" stroke-linecap="round"/>
                        <!-- needle -->
                        <line id="gaugeNeedle" x1="65" y1="65" x2="65" y2="22" stroke="#1a1f2e" stroke-width="3" stroke-linecap="round" class="risk-needle"/>
                        <circle cx="65" cy="65" r="5" fill="#1a1f2e"/>
                    </svg>
                </div>
                <div class="risk-label" id="gaugeScore">—</div>
                <div class="risk-level-text bg-secondary text-white" id="gaugeLevelText">Awaiting Data</div>
            </div>

            {{-- Risk Breakdown --}}
            <div class="mt-3" style="text-align:left;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted">Weather</small>
                    <small class="fw-bold" id="riskWeather">—</small>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div id="riskWeatherBar" class="progress-bar" style="width:0%; background:#667eea;"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted">Inflation</small>
                    <small class="fw-bold" id="riskInflation">—</small>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div id="riskInflationBar" class="progress-bar" style="width:0%; background:#ffb822;"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted">News Sentiment</small>
                    <small class="fw-bold" id="riskNews">—</small>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div id="riskNewsBar" class="progress-bar" style="width:0%; background:#ff6384;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========== CHARTS + NEWS SENTIMENT ========== --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="custom-card" style="position:relative;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold m-0">Risk & Trend Analysis</h5>
                <div class="tab-nav">
                    <button class="tab-btn active" onclick="switchChart('bar', this)">Bar</button>
                    <button class="tab-btn" onclick="switchChart('line', this)">Line</button>
                </div>
            </div>
            <canvas id="mainChart" style="max-height:280px;"></canvas>
            <div class="loading-overlay" id="chartLoader">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="custom-card" style="height:100%; position:relative;">
            <h5 class="fw-bold mb-4">News Sentiment</h5>
            <div class="text-center mb-3">
                <h2 id="sentimentText" class="fw-bold text-muted">—</h2>
                <p class="text-muted" style="font-size:13px;">Overall Sentiment</p>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <small>Positive</small>
                <small class="fw-bold text-success" id="posPct">0%</small>
            </div>
            <div class="progress mb-3" style="height:8px;">
                <div id="posBar" class="progress-bar bg-success" style="width:0%; transition:width 0.8s;"></div>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <small>Neutral</small>
                <small class="fw-bold text-warning" id="neuPct">0%</small>
            </div>
            <div class="progress mb-3" style="height:8px;">
                <div id="neuBar" class="progress-bar bg-warning" style="width:0%; transition:width 0.8s;"></div>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <small>Negative</small>
                <small class="fw-bold text-danger" id="negPct">0%</small>
            </div>
            <div class="progress mb-3" style="height:8px;">
                <div id="negBar" class="progress-bar bg-danger" style="width:0%; transition:width 0.8s;"></div>
            </div>
            <div class="loading-overlay" id="sentimentLoader">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

{{-- ========== WEATHER + PORT MAPS ========== --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="custom-card" style="position:relative;">
            <h5 class="fw-bold mb-2">
                <i class="fa-solid fa-cloud-sun-rain me-2 text-primary"></i>Weather Monitoring
            </h5>
            {{-- Weather inline widget --}}
            <div id="weatherWidget" class="weather-badge" style="display:none;">
                <i class="fa-solid fa-temperature-half" style="font-size:28px; opacity:0.8;"></i>
                <div>
                    <div class="temp" id="weatherTemp">—°C</div>
                    <div class="detail">
                        💨 Wind: <span id="weatherWind">—</span> km/h &nbsp;|&nbsp;
                        🌧 Precip: <span id="weatherPrecip">—</span> mm
                    </div>
                </div>
            </div>
            <div id="weatherMap"></div>
            <div class="loading-overlay" id="weatherLoader">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="custom-card" style="position:relative;">
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-ship me-2 text-success"></i>Port Location Dashboard
            </h5>
            <div id="portMap"></div>
            <p class="text-muted text-center mt-2 mb-0" id="portCount" style="font-size:12px;"></p>
            <div class="loading-overlay" id="portLoader">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
/* ============================================================
   GLOBAL STATE
   ============================================================ */
let weatherMap, portMap, worldMap;
let weatherMarker, selectedWorldMarker;
let portMarkers = [];
let allCountries  = [];   // full list from /api/countries
let mainChart;
let currentIso   = null;

/* ============================================================
   INIT
   ============================================================ */
$(document).ready(function() {
    initWorldMap();
    initWeatherMap();
    initPortMap();
    initChart();

    // Load countries from backend
    $.get('/api/countries', function(countries) {
        allCountries = countries.sort((a, b) => a.name.localeCompare(b.name));
        
        // Populate dropdown
        let opts = '';
        allCountries.forEach(c => {
            opts += `<option value="${c.iso_code}">${c.name}</option>`;
        });
        $('#countrySelector').html(opts);

        // Drop markers on world map for countries that have coordinates
        addCountryMarkersToWorldMap(allCountries);

        // Default: Indonesia or first country
        const defaultIso = allCountries.find(c => c.iso_code === 'IDN')?.iso_code
                        || allCountries[0]?.iso_code;
        if (defaultIso) {
            $('#countrySelector').val(defaultIso);
            fetchData(defaultIso);
        }
    });

    // Selector change
    $('#countrySelector').change(function() {
        fetchData($(this).val());
    });
});

/* ============================================================
   MAPS INIT
   ============================================================ */
function initWorldMap() {
    worldMap = L.map('worldMap', { zoomControl: true }).setView([20, 10], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(worldMap);
}

function initWeatherMap() {
    weatherMap = L.map('weatherMap').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(weatherMap);
}

function initPortMap() {
    portMap = L.map('portMap').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(portMap);
}

/* Add clickable country dot markers on world map */
function addCountryMarkersToWorldMap(countries) {
    const pulseIcon = L.divIcon({
        className: '',
        html: '<div class="pulse-icon"><div class="pulse-icon-inner"></div></div>',
        iconSize: [14, 14],
        iconAnchor: [7, 7]
    });

    countries.forEach(c => {
        if (!c.latitude || !c.longitude) return;
        const m = L.marker([c.latitude, c.longitude], { icon: pulseIcon })
            .addTo(worldMap)
            .bindTooltip(c.name, { permanent: false, direction: 'top', offset: [0, -8] });
        
        m.on('click', function() {
            $('#countrySelector').val(c.iso_code);
            fetchData(c.iso_code);
        });
    });
}

/* ============================================================
   CHART INIT
   ============================================================ */
function initChart() {
    const ctx = document.getElementById('mainChart').getContext('2d');
    mainChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Weather Risk', 'Inflation Risk', 'Currency Risk', 'News Risk'],
            datasets: [{
                label: 'Risk Breakdown',
                data: [0, 0, 0, 0],
                backgroundColor: ['#667eea', '#ffb822', '#6482ff', '#ff6384'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 800, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` Risk: ${ctx.parsed.y} / 25`
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, max: 25, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

function switchChart(type, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    mainChart.config.type = type;
    mainChart.update();
}

/* ============================================================
   FETCH DATA — main function
   ============================================================ */
function fetchData(isoCode) {
    if (!isoCode || isoCode === currentIso) return;
    currentIso = isoCode;

    // Show loading overlays
    showLoader(['mapLoader', 'chartLoader', 'sentimentLoader', 'weatherLoader', 'portLoader']);

    // Reset cards
    setCardLoading();

    $.ajax({
        url: `/api/risk?iso_code=${isoCode}`,
        success: function(res) {
            updateUI(res);
            hideLoader(['mapLoader', 'chartLoader', 'sentimentLoader', 'weatherLoader', 'portLoader']);
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.error || 'Failed to load data';
            alert('Error: ' + msg);
            currentIso = null;
            hideLoader(['mapLoader', 'chartLoader', 'sentimentLoader', 'weatherLoader', 'portLoader']);
        }
    });
}

/* ============================================================
   UPDATE UI
   ============================================================ */
function updateUI(res) {
    const c = res.country;
    const rs = res.risk_score;
    const sentiment = res.news_sentiment;

    /* --- Country name & badge --- */
    animateText('#countryNameDisplay', c.name);
    animateText('#panelCountry', c.name);

    const lvl = rs.level;
    let bdgCls = 'bg-success';
    let textCol = 'text-success';
    if (lvl === 'Medium Risk') { bdgCls = 'bg-warning text-dark'; textCol = 'text-warning'; }
    if (lvl === 'High Risk')   { bdgCls = 'bg-danger'; textCol = 'text-danger'; }
    $('#riskLevelBadge').removeClass().addClass(`badge ${bdgCls} px-3 py-2`).css('border-radius','20px').text(lvl);

    /* --- Detail panel --- */
    $('#panelRegion').text(c.region || '—');
    animateText('#panelGDP', res.gdp ? '$' + (res.gdp / 1e9).toFixed(1) + 'B' : 'N/A');
    animateText('#panelCurrency', c.currency_code || '—');
    animateText('#panelInflation', res.inflation_rate + '%');

    /* --- Stat cards --- */
    animateText('#gdpVal',       res.gdp ? '$' + formatGDP(res.gdp) : 'N/A');
    animateText('#inflationVal', res.inflation_rate + '%');
    animateText('#riskScoreVal', rs.total + ' / 100');
    animateText('#currencyVal',  c.currency_code || '—');
    animateText('#currencyRegion', c.region || '—');
    animateText('#riskLevelSub', lvl);

    /* --- Risk gauge --- */
    updateGauge(rs.total, lvl, textCol);

    /* --- Risk breakdown bars --- */
    animateText('#riskWeather',    rs.weather   + ' / 25');
    animateText('#riskInflation',  rs.inflation + ' / 25');
    animateText('#riskNews',       rs.news      + ' / 25');
    setTimeout(() => {
        $('#riskWeatherBar').css('width', (rs.weather   / 25 * 100) + '%');
        $('#riskInflationBar').css('width', (rs.inflation / 25 * 100) + '%');
        $('#riskNewsBar').css('width', (rs.news      / 25 * 100) + '%');
    }, 100);

    /* --- News Sentiment --- */
    const sp = sentiment.percentages;
    const neutralPct = Math.max(0, 100 - sp.positive - sp.negative);
    animateText('#sentimentText', sentiment.sentiment);
    const sentCls = sentiment.sentiment === 'Positive' ? 'text-success'
                  : sentiment.sentiment === 'Negative' ? 'text-danger' : 'text-warning';
    $('#sentimentText').removeClass('text-success text-danger text-warning text-muted').addClass(sentCls);
    $('#posPct').text(sp.positive + '%');
    $('#negPct').text(sp.negative + '%');
    $('#neuPct').text(neutralPct + '%');
    setTimeout(() => {
        $('#posBar').css('width', sp.positive   + '%');
        $('#negBar').css('width', sp.negative   + '%');
        $('#neuBar').css('width', neutralPct    + '%');
    }, 100);

    /* --- Chart --- */
    mainChart.data.datasets[0].data = [rs.weather, rs.inflation, rs.currency, rs.news];
    mainChart.options.plugins.title = { display: true, text: c.name + ' — Risk Breakdown', font: { size: 14 } };
    mainChart.update();

    /* --- Weather --- */
    updateWeatherMap(res.weather, c);

    /* --- Panning world map to selected country --- */
    if (c.latitude && c.longitude) {
        worldMap.flyTo([parseFloat(c.latitude), parseFloat(c.longitude)], 4, { duration: 1.2 });
    }

    /* --- Ports --- */
    updatePortMap(c.name);
}

/* ============================================================
   WEATHER MAP UPDATE
   ============================================================ */
function updateWeatherMap(weather, country) {
    if (weather && country.latitude && country.longitude) {
        const lat = parseFloat(country.latitude);
        const lng = parseFloat(country.longitude);
        
        // Show inline widget
        $('#weatherTemp').text(weather.temperature_2m + '°C');
        $('#weatherWind').text(weather.wind_speed_10m);
        $('#weatherPrecip').text(weather.precipitation);
        $('#weatherWidget').show();

        weatherMap.flyTo([lat, lng], 5, { duration: 1 });
        if (weatherMarker) weatherMap.removeLayer(weatherMarker);
        
        const weatherIcon = L.divIcon({
            className: '',
            html: `<div style="background:#667eea;color:#fff;padding:6px 10px;border-radius:20px;font-size:12px;font-weight:700;white-space:nowrap;box-shadow:0 2px 8px rgba(0,0,0,0.2);">🌡 ${weather.temperature_2m}°C</div>`,
            iconAnchor: [40, 15]
        });
        weatherMarker = L.marker([lat, lng], { icon: weatherIcon }).addTo(weatherMap)
            .bindPopup(`
                <div class="map-popup-title">🌤 ${country.name} Weather</div>
                <div class="map-popup-row"><span class="label">Temperature</span><span class="value">${weather.temperature_2m}°C</span></div>
                <div class="map-popup-row"><span class="label">Wind Speed</span><span class="value">${weather.wind_speed_10m} km/h</span></div>
                <div class="map-popup-row"><span class="label">Precipitation</span><span class="value">${weather.precipitation} mm</span></div>
            `)
            .openPopup();
    }
}

/* ============================================================
   PORT MAP UPDATE
   ============================================================ */
function updatePortMap(countryName) {
    $.get(`/api/ports?country=${encodeURIComponent(countryName)}`, function(ports) {
        portMarkers.forEach(m => portMap.removeLayer(m));
        portMarkers = [];

        if (ports.length === 0) {
            $('#portCount').text('No ports recorded for this country.');
            portMap.setView([20, 0], 2);
            return;
        }

        $('#portCount').text(`${ports.length} port(s) found`);
        const bounds = L.latLngBounds();

        ports.forEach(port => {
            const portIcon = L.divIcon({
                className: '',
                html: `<div style="background:#00b575;color:#fff;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 2px 8px rgba(0,181,117,0.5);">⚓</div>`,
                iconSize: [28, 28], iconAnchor: [14, 14]
            });
            const m = L.marker([port.lat, port.lng], { icon: portIcon })
                .addTo(portMap)
                .bindPopup(`<div class="map-popup-title">⚓ ${port.name}</div><div class="map-popup-row"><span class="label">Country</span><span class="value">${port.country}</span></div>`);
            portMarkers.push(m);
            bounds.extend([port.lat, port.lng]);
        });

        portMap.fitBounds(bounds, { padding: [40, 40] });
        hideLoader(['portLoader']);
    }).fail(function() {
        hideLoader(['portLoader']);
    });
}

/* ============================================================
   GAUGE
   ============================================================ */
function updateGauge(score, level, textCol) {
    // Needle rotation: -90° = 0, +90° = 100
    const angle = (score / 100) * 180 - 90;
    $('#gaugeNeedle').css('transform', `rotate(${angle}deg)`);
    animateText('#gaugeScore', score);

    const levelColors = {
        'Low Risk':    '#00b575',
        'Medium Risk': '#ffb822',
        'High Risk':   '#ff6384'
    };
    const col = levelColors[level] || '#888';
    $('#gaugeLevelText')
        .text(level)
        .css({ background: col, color: '#fff', borderRadius: '20px', padding: '4px 14px' });
}

/* ============================================================
   HELPERS
   ============================================================ */
function formatGDP(val) {
    if (val >= 1e12) return (val / 1e12).toFixed(2) + 'T';
    if (val >= 1e9)  return (val / 1e9).toFixed(2) + 'B';
    if (val >= 1e6)  return (val / 1e6).toFixed(2) + 'M';
    return val.toFixed(0);
}

function animateText(selector, text) {
    $(selector).addClass('flash-update').text(text);
    setTimeout(() => $(selector).removeClass('flash-update'), 500);
}

function setCardLoading() {
    ['#gdpVal','#inflationVal','#riskScoreVal','#currencyVal',
     '#panelGDP','#panelCurrency','#panelInflation',
     '#sentimentText','#gaugeScore'].forEach(id => {
        $(id).text('...');
    });
    $('#riskWeatherBar, #riskInflationBar, #riskNewsBar').css('width', '0%');
    $('#posBar, #negBar, #neuBar').css('width', '0%');
}

function showLoader(ids) {
    ids.forEach(id => $('#' + id).addClass('active'));
}
function hideLoader(ids) {
    ids.forEach(id => $('#' + id).removeClass('active'));
}
</script>
@endsection
