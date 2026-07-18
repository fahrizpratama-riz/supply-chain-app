@extends('layouts.dashboard')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .page-header::after {
        content:''; position:absolute; width:300px; height:300px;
        background:rgba(255,255,255,0.05); border-radius:50%;
        right:-80px; top:-100px;
    }
    .page-header h2 { font-size:26px; font-weight:800; margin:0 0 6px; }
    .page-header p  { margin:0; opacity:0.8; font-size:14px; }

    /* Selector */
    .weather-selector {
        background:#fff; border-radius:16px; padding:16px 20px;
        box-shadow:0 4px 20px rgba(0,0,0,0.05);
        display:flex; gap:12px; align-items:center; flex-wrap:wrap;
        margin-bottom:24px;
    }
    .weather-selector select {
        flex:1; min-width:200px; border:1.5px solid #e8ecef;
        border-radius:10px; padding:10px 14px; font-size:15px;
        font-weight:600; outline:none;
        transition:border-color 0.2s, box-shadow 0.2s;
    }
    .weather-selector select:focus {
        border-color:#667eea;
        box-shadow:0 0 0 3px rgba(102,126,234,0.12);
    }

    /* Weather Map */
    #weatherFullMap {
        height: 400px;
        border-radius: 16px;
        z-index: 1;
    }

    /* Weather Cards */
    .weather-main-card {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 20px;
        padding: 24px;
        color: #fff;
        margin-bottom: 16px;
        position: relative;
        overflow: hidden;
    }
    .weather-main-card::after {
        content:''; position:absolute; width:200px; height:200px;
        background:rgba(255,255,255,0.06); border-radius:50%;
        right:-40px; bottom:-40px;
    }
    .temp-display {
        font-size: 64px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }
    .weather-condition {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 20px;
    }
    .weather-metrics {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .metric-item {
        text-align: center;
    }
    .metric-item .metric-val { font-size:20px; font-weight:800; }
    .metric-item .metric-lbl { font-size:11px; opacity:0.75; text-transform:uppercase; letter-spacing:.5px; }

    /* Risk card */
    .risk-weather-card {
        background:#fff; border-radius:16px; padding:20px;
        box-shadow:0 4px 16px rgba(0,0,0,0.06); margin-bottom:16px;
    }
    .risk-bar-wrap { margin-top:12px; }
    .risk-indicator-row {
        display:flex; justify-content:space-between; margin-bottom:4px;
        font-size:13px;
    }
    .risk-indicator-row .lbl { color:#888; }
    .risk-indicator-row .val { font-weight:700; }

    /* Forecast */
    .forecast-card {
        background:#fff; border-radius:14px;
        border:1.5px solid #f0f0f0; padding:14px;
        text-align:center; transition:all 0.2s;
    }
    .forecast-card:hover {
        border-color:#667eea;
        box-shadow:0 4px 12px rgba(102,126,234,0.12);
    }
    .forecast-icon { font-size:24px; margin-bottom:6px; }
    .forecast-temp { font-size:18px; font-weight:800; color:#1a1f2e; }
    .forecast-day  { font-size:11px; color:#aaa; font-weight:600; text-transform:uppercase; }
    .forecast-precip { font-size:11px; color:#0ea5e9; margin-top:4px; }

    /* Loading */
    .loading-overlay {
        position:absolute; top:0;left:0;right:0;bottom:0;
        background:rgba(255,255,255,0.8); backdrop-filter:blur(4px);
        display:flex; align-items:center; justify-content:center;
        border-radius:16px; z-index:100; opacity:0; pointer-events:none; transition:opacity 0.3s;
    }
    .loading-overlay.active { opacity:1; pointer-events:all; }
    .spinner {
        width:40px; height:40px; border:4px solid rgba(102,126,234,0.2);
        border-top-color:#667eea; border-radius:50%; animation:spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }

    /* Placeholder */
    #weatherPlaceholder {
        text-align:center; padding:60px 20px; color:#aaa;
    }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fa-solid fa-cloud-sun-rain" style="font-size:36px; opacity:0.85;"></i>
        <div>
            <h2>Global Weather Monitoring</h2>
            <p>Real-time weather data and logistic risk assessment powered by Open-Meteo API</p>
        </div>
    </div>
    <div class="d-flex gap-4 mt-2">
        <div><span class="fw-bold fs-5" id="hdrTemp">—</span><br><small style="opacity:0.75;">Temperature</small></div>
        <div><span class="fw-bold fs-5" id="hdrWind">—</span><br><small style="opacity:0.75;">Wind (km/h)</small></div>
        <div><span class="fw-bold fs-5" id="hdrPrecip">—</span><br><small style="opacity:0.75;">Precipitation</small></div>
        <div><span class="fw-bold fs-5" id="hdrRisk">—</span><br><small style="opacity:0.75;">Weather Risk</small></div>
    </div>
</div>

{{-- COUNTRY SELECTOR --}}
<div class="weather-selector">
    <i class="fa-solid fa-globe" style="font-size:20px; color:#667eea;"></i>
    <select id="weatherCountrySelect">
        <option value="">🔄 Loading countries...</option>
    </select>
    <button id="loadWeatherBtn" class="btn btn-primary" style="background:#667eea; border:none; border-radius:10px; padding:10px 20px; font-weight:600;">
        <i class="fa-solid fa-cloud-bolt me-2"></i>Load Weather
    </button>
</div>

<div class="row g-3">
    {{-- MAP --}}
    <div class="col-md-8">
        <div class="custom-card" style="position:relative; padding:16px;">
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-map me-2" style="color:#667eea;"></i>
                Weather Map
            </h5>
            <div id="weatherFullMap"></div>
            <div class="loading-overlay" id="weatherMapLoader">
                <div class="spinner"></div>
            </div>
        </div>

        {{-- 5-Day Forecast (simulated) --}}
        <div class="custom-card mt-0" id="forecastCard" style="display:none;">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-calendar-week me-2" style="color:#667eea;"></i>Weather Outlook</h6>
            <div class="row g-2" id="forecastRow"></div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="col-md-4">
        {{-- Placeholder --}}
        <div id="weatherPlaceholder">
            <i class="fa-solid fa-cloud-sun" style="font-size:64px; color:#e8ecef;"></i>
            <p class="mt-3 fw-semibold" style="color:#ccc;">Select a country to view weather data</p>
        </div>

        {{-- Main Weather Card --}}
        <div id="weatherMainCard" style="display:none;">
            <div class="weather-main-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="weather-condition" id="weatherCountryName">—</div>
                        <div class="temp-display" id="weatherTemp">—°</div>
                        <div style="font-size:14px; opacity:0.85;" id="weatherCondition">—</div>
                    </div>
                    <span id="weatherEmoji" style="font-size:56px;">🌤</span>
                </div>
                <div class="weather-metrics mt-4">
                    <div class="metric-item">
                        <div class="metric-val" id="weatherWind">—</div>
                        <div class="metric-lbl">💨 Wind</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-val" id="weatherPrecip">—</div>
                        <div class="metric-lbl">🌧 Precip</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-val" id="weatherHumidity">—</div>
                        <div class="metric-lbl">💧 Humidity</div>
                    </div>
                </div>
            </div>

            {{-- Risk Indicators --}}
            <div class="risk-weather-card">
                <h6 class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>Logistics Risk Indicators</h6>
                <div class="risk-bar-wrap">
                    <div class="risk-indicator-row">
                        <span class="lbl">Storm Risk</span>
                        <span class="val" id="stormRisk">—</span>
                    </div>
                    <div class="progress mb-3" style="height:8px;">
                        <div id="stormBar" class="progress-bar" style="width:0%; background:#ef4444; transition:width 0.8s;"></div>
                    </div>
                    <div class="risk-indicator-row">
                        <span class="lbl">Flooding Risk</span>
                        <span class="val" id="floodRisk">—</span>
                    </div>
                    <div class="progress mb-3" style="height:8px;">
                        <div id="floodBar" class="progress-bar" style="width:0%; background:#0ea5e9; transition:width 0.8s;"></div>
                    </div>
                    <div class="risk-indicator-row">
                        <span class="lbl">Delay Probability</span>
                        <span class="val" id="delayRisk">—</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div id="delayBar" class="progress-bar" style="width:0%; background:#f59e0b; transition:width 0.8s;"></div>
                    </div>
                </div>
                <div class="mt-3 p-2 border-top">
                    <small class="text-muted" id="weatherAdvice">—</small>
                </div>
            </div>

            {{-- API Info --}}
            <div class="custom-card">
                <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-info me-2 text-primary"></i>Data Source</h6>
                <p class="text-muted mb-0" style="font-size:13px; line-height:1.6;">
                    Weather data provided by <strong>Open-Meteo API</strong> (free, no API key required).
                    Data refreshes every hour. Coordinates sourced from country database.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let weatherMap;
let weatherMarker;
let currentWeatherData = null;

$(document).ready(function() {
    initMap();

    // Load countries
    $.get('/api/countries', function(data) {
        data.sort((a,b) => a.name.localeCompare(b.name));
        let opts = '<option value="">— Select a Country —</option>';
        data.forEach(c => {
            opts += `<option value="${c.iso_code}" data-lat="${c.latitude||0}" data-lng="${c.longitude||0}">${c.name}</option>`;
        });
        $('#weatherCountrySelect').html(opts);
    });

    $('#loadWeatherBtn').on('click', loadWeather);
    $('#weatherCountrySelect').on('change', loadWeather);
});

function initMap() {
    weatherMap = L.map('weatherFullMap').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(weatherMap);
}

function loadWeather() {
    const iso = $('#weatherCountrySelect').val();
    if (!iso) return;

    $('#weatherMapLoader').addClass('active');

    $.get(`/api/risk?iso_code=${iso}`, function(res) {
        const w = res.weather;
        const c = res.country;
        if (!w) {
            $('#weatherMapLoader').removeClass('active');
            alert('No weather data available for this country.');
            return;
        }

        currentWeatherData = w;
        const lat = parseFloat(c.latitude || 0);
        const lng = parseFloat(c.longitude || 0);

        // Header
        const temp = w.temperature_2m;
        const wind = w.wind_speed_10m;
        const precip = w.precipitation;
        $('#hdrTemp').text(temp + '°C');
        $('#hdrWind').text(wind + ' km/h');
        $('#hdrPrecip').text(precip + ' mm');

        // Risk calcs
        const stormPct  = Math.min(100, Math.round((wind/120) * 100));
        const floodPct  = Math.min(100, Math.round((precip/60) * 100));
        const delayPct  = Math.min(100, Math.round(((stormPct + floodPct)/2)));
        const riskLvl   = res.risk_score?.weather || 0;
        $('#hdrRisk').text(riskLvl + '/25');

        // Show main card
        $('#weatherPlaceholder').hide();
        $('#weatherMainCard').show();
        $('#forecastCard').show();

        // Fill main card
        $('#weatherCountryName').text('📍 ' + c.name);
        $('#weatherTemp').text(temp + '°C');

        // Weather emoji
        const emoji = getWeatherEmoji(temp, wind, precip);
        $('#weatherEmoji').text(emoji);
        const condition = getWeatherCondition(wind, precip);
        $('#weatherCondition').text(condition);

        $('#weatherWind').text(wind + ' km/h');
        $('#weatherPrecip').text(precip + ' mm');
        $('#weatherHumidity').text('~' + Math.round(55 + (precip*0.8)) + '%');

        // Risk bars
        $('#stormRisk').text(stormPct + '%');
        $('#floodRisk').text(floodPct + '%');
        $('#delayRisk').text(delayPct + '%');
        setTimeout(() => {
            $('#stormBar').css('width', stormPct + '%');
            $('#floodBar').css('width', floodPct + '%');
            $('#delayBar').css('width', delayPct + '%');
        }, 100);

        // Advice
        $('#weatherAdvice').text(getAdvice(wind, precip, temp));

        // Map
        weatherMap.flyTo([lat, lng], 5, { duration: 1 });
        if (weatherMarker) weatherMap.removeLayer(weatherMarker);

        const markerHtml = `<div style="
            background:linear-gradient(135deg,#667eea,#764ba2);
            color:#fff; padding:8px 12px; border-radius:20px;
            font-size:13px; font-weight:800; white-space:nowrap;
            box-shadow:0 4px 16px rgba(102,126,234,0.5);
        ">${emoji} ${temp}°C</div>`;

        weatherMarker = L.marker([lat, lng], {
            icon: L.divIcon({ className:'', html: markerHtml, iconAnchor:[50,15] })
        }).addTo(weatherMap)
          .bindPopup(`
            <div style="font-weight:800; font-size:15px; margin-bottom:8px;">${emoji} ${c.name} Weather</div>
            <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0;border-bottom:1px dashed #f0f0f0;">
                <span style="color:#888;">Temperature</span><span style="font-weight:700;">${temp}°C</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0;border-bottom:1px dashed #f0f0f0;">
                <span style="color:#888;">Wind Speed</span><span style="font-weight:700;">${wind} km/h</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0;">
                <span style="color:#888;">Precipitation</span><span style="font-weight:700;">${precip} mm</span>
            </div>
          `)
          .openPopup();

        // Forecast (simulated 5-day based on current data)
        renderForecast(temp, wind, precip);

        $('#weatherMapLoader').removeClass('active');
    }).fail(function() {
        $('#weatherMapLoader').removeClass('active');
        alert('Failed to load weather data. Please try again.');
    });
}

function renderForecast(baseTemp, baseWind, basePrecip) {
    const days = ['Today','Tomorrow','Day 3','Day 4','Day 5'];
    const emojis = ['☀️','🌤','⛅','🌦','🌧'];
    let html = '';
    days.forEach((day, i) => {
        const t = (baseTemp + (Math.random()*6 - 3)).toFixed(1);
        const p = Math.max(0, basePrecip + (Math.random()*10 - 5)).toFixed(1);
        const e = emojis[Math.min(i, emojis.length-1)];
        html += `
        <div class="col">
            <div class="forecast-card">
                <div class="forecast-day">${day}</div>
                <div class="forecast-icon">${e}</div>
                <div class="forecast-temp">${t}°</div>
                <div class="forecast-precip">💧 ${p}mm</div>
            </div>
        </div>`;
    });
    $('#forecastRow').html(html);
}

function getWeatherEmoji(temp, wind, precip) {
    if (wind > 80) return '🌪️';
    if (precip > 20) return '⛈️';
    if (precip > 5)  return '🌧️';
    if (temp > 35)   return '🌞';
    if (temp < 0)    return '❄️';
    return '⛅';
}

function getWeatherCondition(wind, precip) {
    if (wind > 80)   return 'Severe Storm — High Disruption Risk';
    if (wind > 50)   return 'Strong Winds — Moderate Disruption Risk';
    if (precip > 20) return 'Heavy Rain — Port Delays Possible';
    if (precip > 5)  return 'Light Rain — Minor Delays';
    return 'Clear Conditions — Normal Operations';
}

function getAdvice(wind, precip, temp) {
    if (wind > 80 || precip > 20)
        return '⚠️ Severe weather detected. Consider delaying shipments or activating contingency routes.';
    if (wind > 50 || precip > 10)
        return '⚡ Elevated weather risk. Monitor shipping schedules closely.';
    if (temp < -10)
        return '🧊 Extreme cold may affect road transport and port operations.';
    return '✅ Weather conditions are favorable for logistics operations.';
}
</script>
@endsection