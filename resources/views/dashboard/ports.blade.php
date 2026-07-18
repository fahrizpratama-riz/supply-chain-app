@extends('layouts.dashboard')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .page-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
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

    /* Search bar */
    .port-search-bar {
        background:#fff; border-radius:16px;
        padding:16px 20px;
        box-shadow:0 4px 20px rgba(0,0,0,0.05);
        display:flex; gap:12px; flex-wrap:wrap; align-items:center;
        margin-bottom:20px;
    }
    .port-search-bar input, .port-search-bar select {
        border:1.5px solid #e8ecef; border-radius:10px;
        padding:9px 14px; font-size:14px; outline:none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .port-search-bar input:focus, .port-search-bar select:focus {
        border-color:#0ea5e9;
        box-shadow:0 0 0 3px rgba(14,165,233,0.12);
    }
    #portSearchInput { flex:1; min-width:200px; }

    /* Map */
    #fullPortMap {
        height: 520px;
        border-radius: 16px;
        z-index: 1;
        border: 2px solid #f0f0f0;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15) !important;
    }
    .port-popup-title {
        font-weight: 800; font-size:15px; color:#1a1f2e; margin-bottom:8px;
    }
    .port-popup-row {
        display:flex; justify-content:space-between; font-size:13px;
        padding:4px 0; border-bottom:1px dashed #f0f0f0;
    }
    .port-popup-row:last-child { border-bottom:none; }
    .port-popup-row .lbl { color:#888; }
    .port-popup-row .val { font-weight:700; color:#1a1f2e; }

    /* Stats */
    .port-stat {
        background:#fff; border-radius:14px; padding:18px 20px;
        box-shadow:0 4px 16px rgba(0,0,0,0.05); margin-bottom:16px;
    }
    .port-stat .lbl { font-size:12px; color:#aaa; text-transform:uppercase; letter-spacing:.5px; font-weight:700; }
    .port-stat .val { font-size:26px; font-weight:800; color:#0ea5e9; margin:4px 0 0; }

    /* Port list */
    .port-list-item {
        display:flex; align-items:center; gap:12px;
        padding:12px 14px; border-radius:12px;
        border:1.5px solid #f0f0f0; margin-bottom:8px;
        background:#fff; cursor:pointer;
        transition:all 0.2s;
    }
    .port-list-item:hover {
        border-color:#0ea5e9;
        box-shadow:0 4px 12px rgba(14,165,233,0.12);
    }
    .port-icon {
        width:36px; height:36px; border-radius:50%;
        background:linear-gradient(135deg,#0ea5e9,#0369a1);
        display:flex; align-items:center; justify-content:center;
        font-size:16px; flex-shrink:0;
    }
    .port-name { font-size:14px; font-weight:700; color:#1a1f2e; }
    .port-country { font-size:12px; color:#aaa; }

    .loading-overlay {
        position:absolute; top:0;left:0;right:0;bottom:0;
        background:rgba(255,255,255,0.8); backdrop-filter:blur(4px);
        display:flex; align-items:center; justify-content:center;
        border-radius:16px; z-index:100; opacity:0; pointer-events:none; transition:opacity 0.3s;
    }
    .loading-overlay.active { opacity:1; pointer-events:all; }
    .spinner {
        width:40px; height:40px; border:4px solid rgba(14,165,233,0.2);
        border-top-color:#0ea5e9; border-radius:50%; animation:spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fa-solid fa-ship" style="font-size:36px; opacity:0.85;"></i>
        <div>
            <h2>Port Location Dashboard</h2>
            <p>Interactive global port map — search, filter, and explore maritime hubs worldwide</p>
        </div>
    </div>
    <div class="d-flex gap-4 mt-2">
        <div><span class="fw-bold fs-5" id="totalPorts">—</span><br><small style="opacity:0.75;">Total Ports</small></div>
        <div><span class="fw-bold fs-5" id="totalPortCountries">—</span><br><small style="opacity:0.75;">Countries</small></div>
        <div><span class="fw-bold fs-5" id="filteredPorts">—</span><br><small style="opacity:0.75;">Showing</small></div>
    </div>
</div>

{{-- SEARCH BAR --}}
<div class="port-search-bar">
    <i class="fa-solid fa-search" style="color:#0ea5e9; font-size:16px;"></i>
    <input type="text" id="portSearchInput" placeholder="Search port by name...">
    <select id="portCountryFilter" style="min-width:180px;">
        <option value="">All Countries</option>
    </select>
    <button class="btn btn-sm" id="resetBtn" style="background:#f4f7f6; border-radius:10px; border:1.5px solid #e8ecef; font-size:13px; font-weight:600; color:#888; padding:9px 16px;">
        <i class="fa-solid fa-rotate-left me-1"></i>Reset
    </button>
    <span id="portMapStatus" class="text-muted ms-auto" style="font-size:13px;"></span>
</div>

<div class="row g-3">
    {{-- MAP --}}
    <div class="col-md-8">
        <div class="custom-card" style="position:relative; padding:16px;">
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-map-location-dot me-2 text-primary"></i>
                Global Port Map
            </h5>
            <div id="fullPortMap"></div>
            <div class="loading-overlay" id="portMapLoader">
                <div class="spinner"></div>
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="col-md-4">
        {{-- Stats --}}
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="port-stat mb-0">
                    <div class="lbl">Total Ports</div>
                    <div class="val" id="statTotal">—</div>
                </div>
            </div>
            <div class="col-6">
                <div class="port-stat mb-0">
                    <div class="lbl">Countries</div>
                    <div class="val" id="statCountries">—</div>
                </div>
            </div>
        </div>

        {{-- Port List --}}
        <div class="custom-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-list me-2 text-primary"></i>Port List</h6>
                <small class="text-muted" id="portListCount"></small>
            </div>
            <div id="portList" style="max-height:440px; overflow-y:auto;">
                <div class="text-center py-3 text-muted">
                    <div class="spinner mx-auto mb-2" style="border-top-color:#0ea5e9;"></div>
                    Loading ports...
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let portMap;
let allPorts      = [];
let portMarkers   = [];
let markerGroup;

$(document).ready(function() {
    initMap();
    loadAllPorts();

    $('#portSearchInput').on('input', filterPorts);
    $('#portCountryFilter').on('change', filterPorts);
    $('#resetBtn').on('click', function() {
        $('#portSearchInput').val('');
        $('#portCountryFilter').val('');
        filterPorts();
    });
});

function initMap() {
    portMap = L.map('fullPortMap').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(portMap);
    markerGroup = L.layerGroup().addTo(portMap);
}

function loadAllPorts() {
    $('#portMapLoader').addClass('active');
    $.get('/api/ports', function(ports) {
        allPorts = ports;

        // Populate country filter
        const countries = [...new Set(ports.map(p => p.country).filter(Boolean))].sort();
        countries.forEach(c => {
            $('#portCountryFilter').append(`<option value="${c}">${c}</option>`);
        });

        const cCount = countries.length;
        $('#totalPorts').text(ports.length);
        $('#totalPortCountries').text(cCount);
        $('#statTotal').text(ports.length);
        $('#statCountries').text(cCount);

        renderPorts(ports);
        $('#portMapLoader').removeClass('active');
    }).fail(function() {
        $('#portMapLoader').removeClass('active');
        $('#portMapStatus').text('Failed to load port data.');
    });
}

function filterPorts() {
    const q = $('#portSearchInput').val().trim().toLowerCase();
    const country = $('#portCountryFilter').val();

    const filtered = allPorts.filter(p => {
        const matchQ = !q || (p.name||'').toLowerCase().includes(q) || (p.country||'').toLowerCase().includes(q);
        const matchC = !country || p.country === country;
        return matchQ && matchC;
    });

    renderPorts(filtered);
}

function renderPorts(ports) {
    // Clear markers
    markerGroup.clearLayers();
    portMarkers = [];

    $('#filteredPorts').text(ports.length);
    $('#portListCount').text(`${ports.length} ports`);
    $('#portMapStatus').text(`Showing ${ports.length} port(s)`);

    const bounds = L.latLngBounds();
    let hasValid = false;

    ports.forEach((port, idx) => {
        if (!port.lat || !port.lng) return;
        hasValid = true;

        const portIcon = L.divIcon({
            className: '',
            html: `<div style="
                background: linear-gradient(135deg,#0ea5e9,#0369a1);
                color:#fff; width:30px; height:30px; border-radius:50%;
                display:flex; align-items:center; justify-content:center;
                font-size:14px; box-shadow:0 3px 10px rgba(14,165,233,0.5);
                border:2px solid #fff;
            ">⚓</div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const m = L.marker([port.lat, port.lng], { icon: portIcon })
            .addTo(markerGroup)
            .bindPopup(`
                <div class="port-popup-title">⚓ ${port.name}</div>
                <div class="port-popup-row"><span class="lbl">Country</span><span class="val">${port.country || '—'}</span></div>
                <div class="port-popup-row"><span class="lbl">Latitude</span><span class="val">${parseFloat(port.lat).toFixed(4)}°</span></div>
                <div class="port-popup-row"><span class="lbl">Longitude</span><span class="val">${parseFloat(port.lng).toFixed(4)}°</span></div>
            `, { maxWidth: 220 });

        portMarkers.push({ marker: m, data: port });
        bounds.extend([port.lat, port.lng]);
    });

    if (hasValid && ports.length > 0) {
        portMap.fitBounds(bounds, { padding: [40, 40], maxZoom: ports.length === 1 ? 10 : 6 });
    }

    // Render list
    let listHtml = '';
    ports.slice(0, 100).forEach((port, i) => {
        listHtml += `
        <div class="port-list-item" onclick="focusPort(${port.lat}, ${port.lng}, '${(port.name||'').replace(/'/g,"\\'")}')">
            <div class="port-icon">⚓</div>
            <div>
                <div class="port-name">${port.name || '—'}</div>
                <div class="port-country"><i class="fa-solid fa-location-dot me-1"></i>${port.country || '—'}</div>
            </div>
        </div>`;
    });
    if (ports.length > 100) {
        listHtml += `<div class="text-center text-muted py-2" style="font-size:13px;">+ ${ports.length - 100} more ports</div>`;
    }
    if (ports.length === 0) {
        listHtml = '<div class="text-center text-muted py-4"><i class="fa-solid fa-ship" style="font-size:32px;color:#e0e0e0;"></i><p class="mt-2">No ports found</p></div>';
    }
    $('#portList').html(listHtml);
}

function focusPort(lat, lng, name) {
    portMap.flyTo([lat, lng], 10, { duration: 1 });
    // Find and open popup
    portMarkers.forEach(pm => {
        if (Math.abs(pm.data.lat - lat) < 0.001 && Math.abs(pm.data.lng - lng) < 0.001) {
            pm.marker.openPopup();
        }
    });
}
</script>
@endsection