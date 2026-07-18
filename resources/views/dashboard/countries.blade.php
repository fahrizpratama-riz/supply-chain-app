@extends('layouts.dashboard')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #00b575 0%, #007a4d 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .page-header::after {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        right: -80px; top: -100px;
        pointer-events: none;
    }
    .page-header h2 { font-size: 26px; font-weight: 800; margin: 0 0 6px; }
    .page-header p  { margin: 0; opacity: 0.85; font-size: 14px; }

    /* Filters Bar */
    .filter-bar {
        background: #fff;
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 24px;
    }
    .filter-bar input, .filter-bar select {
        border: 1.5px solid #e8ecef;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .filter-bar input:focus, .filter-bar select:focus {
        border-color: #00b575;
        box-shadow: 0 0 0 3px rgba(0,181,117,0.12);
    }
    #searchInput { flex: 1; min-width: 220px; }
    .filter-bar .count-badge {
        margin-left: auto;
        font-size: 13px;
        color: #888;
        font-weight: 600;
    }

    /* Country Cards */
    .country-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1.5px solid #f0f0f0;
    }
    .country-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.1);
        border-color: #d0f0e4;
    }
    .card-flag-wrap {
        position: relative;
        height: 90px;
        overflow: hidden;
        background: linear-gradient(135deg, #f0fdf9, #e6f7f0);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-flag-wrap img {
        height: 60px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        object-fit: cover;
    }
    .region-ribbon {
        position: absolute;
        top: 8px; right: 8px;
        background: rgba(0,181,117,0.15);
        color: #007a4d;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }
    .card-body-inner {
        padding: 14px 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .card-body-inner h6 {
        font-size: 16px;
        font-weight: 800;
        color: #1a1f2e;
        margin: 0 0 4px;
    }
    .card-body-inner .iso-badge {
        font-size: 11px;
        background: #f4f7f6;
        color: #888;
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 12px;
    }
    .card-stat-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #555;
        padding: 5px 0;
        border-bottom: 1px dashed #f0f0f0;
    }
    .card-stat-row:last-of-type { border-bottom: none; }
    .card-stat-row .lbl { color: #aaa; font-weight: 500; }
    .card-stat-row .val { font-weight: 700; color: #1a1f2e; }

    .card-footer-inner {
        padding: 10px 16px 14px;
        display: flex;
        gap: 8px;
    }
    .btn-detail {
        flex: 1;
        padding: 7px 0;
        background: #00b575;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-detail:hover { background: #009a62; }
    .btn-watch {
        width: 36px; height: 36px;
        border: 1.5px solid #e8ecef;
        border-radius: 8px;
        background: #fff;
        color: #ccc;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-watch.active { color: #f59e0b; border-color: #f59e0b; background: #fffbeb; }
    .btn-watch:hover { border-color: #f59e0b; color: #f59e0b; }

    /* Risk badge on card */
    .risk-pill {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        display: inline-block;
    }
    .risk-low    { background: #d1fae5; color: #065f46; }
    .risk-medium { background: #fef3c7; color: #92400e; }
    .risk-high   { background: #fee2e2; color: #991b1b; }

    /* Loading skeleton */
    .skeleton-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        height: 280px;
        border: 1.5px solid #f0f0f0;
    }
    .skeleton-block {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 6px;
    }
    @keyframes shimmer { to { background-position: -200% 0; } }

    /* Modal */
    .modal-header-custom {
        background: linear-gradient(135deg, #00b575, #007a4d);
        color: #fff;
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
    }
    .modal-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
    }
    .modal-detail-row:last-child { border-bottom: none; }
    .modal-detail-row .mlbl { color: #888; }
    .modal-detail-row .mval { font-weight: 700; color: #1a1f2e; }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fa-solid fa-globe" style="font-size:36px; opacity:0.85;"></i>
        <div>
            <h2>Countries Directory</h2>
            <p>Monitor economic indicators, risk scores, and currency data for countries worldwide</p>
        </div>
    </div>
    <div class="d-flex gap-4 mt-2">
        <div><span class="fw-bold fs-5" id="totalCount">—</span><br><small style="opacity:0.75;">Total Countries</small></div>
        <div><span class="fw-bold fs-5" id="regionCount">—</span><br><small style="opacity:0.75;">Regions</small></div>
        <div><span class="fw-bold fs-5" id="watchCount">—</span><br><small style="opacity:0.75;">Watchlisted</small></div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="filter-bar">
    <i class="fa-solid fa-search" style="color:#00b575; font-size:16px;"></i>
    <input type="text" id="searchInput" placeholder="Search country by name or code...">
    <select id="regionFilter" style="min-width:160px;">
        <option value="">All Regions</option>
        <option value="Asia">Asia</option>
        <option value="Europe">Europe</option>
        <option value="Americas">Americas</option>
        <option value="Africa">Africa</option>
        <option value="Oceania">Oceania</option>
        <option value="Antarctic">Antarctic</option>
    </select>
    <select id="sortBy" style="min-width:160px;">
        <option value="name">Sort: Name A–Z</option>
        <option value="gdp_desc">Sort: GDP (High–Low)</option>
        <option value="risk_asc">Sort: Risk (Low–High)</option>
        <option value="risk_desc">Sort: Risk (High–Low)</option>
    </select>
    <span class="count-badge" id="showingCount">Showing <b id="visibleCount">0</b> countries</span>
</div>

{{-- COUNTRY GRID --}}
<div class="row g-3" id="countriesGrid">
    {{-- Skeleton Loaders --}}
    @for($i = 0; $i < 12; $i++)
    <div class="col-6 col-md-4 col-lg-3 skeleton-wrap">
        <div class="skeleton-card">
            <div style="height:90px;" class="skeleton-block"></div>
            <div style="padding:14px;">
                <div class="skeleton-block mb-2" style="height:18px; width:60%;"></div>
                <div class="skeleton-block mb-3" style="height:12px; width:35%;"></div>
                <div class="skeleton-block mb-2" style="height:10px;"></div>
                <div class="skeleton-block mb-2" style="height:10px;"></div>
                <div class="skeleton-block mb-2" style="height:10px;"></div>
            </div>
        </div>
    </div>
    @endfor
</div>

<div id="noResults" class="text-center py-5" style="display:none;">
    <i class="fa-solid fa-search" style="font-size:48px; color:#e0e0e0;"></i>
    <p class="text-muted mt-3">No countries found matching your search.</p>
</div>

{{-- COUNTRY DETAIL MODAL --}}
<div class="modal fade" id="countryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <img id="modalFlag" src="" alt="Flag" style="height:50px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.2);">
                    <div>
                        <h4 id="modalCountryName" class="mb-1 fw-bold">—</h4>
                        <span id="modalRegion" class="badge bg-white bg-opacity-25 text-white">—</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="custom-card text-center py-3 mb-0">
                            <div style="font-size:11px; color:#aaa; text-transform:uppercase; font-weight:700; letter-spacing:.5px;">GDP</div>
                            <div id="modalGDP" class="fw-bold fs-5 text-success mt-1">—</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="custom-card text-center py-3 mb-0">
                            <div style="font-size:11px; color:#aaa; text-transform:uppercase; font-weight:700; letter-spacing:.5px;">Population</div>
                            <div id="modalPop" class="fw-bold fs-5 text-primary mt-1">—</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="custom-card text-center py-3 mb-0">
                            <div style="font-size:11px; color:#aaa; text-transform:uppercase; font-weight:700; letter-spacing:.5px;">Currency</div>
                            <div id="modalCurrency" class="fw-bold fs-5 text-warning mt-1">—</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="custom-card text-center py-3 mb-0">
                            <div style="font-size:11px; color:#aaa; text-transform:uppercase; font-weight:700; letter-spacing:.5px;">Risk Score</div>
                            <div id="modalRisk" class="fw-bold fs-5 mt-1">—</div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-info-circle text-success me-2"></i>Country Details</h6>
                        <div class="modal-detail-row"><span class="mlbl">ISO Code</span><span class="mval" id="modalISO">—</span></div>
                        <div class="modal-detail-row"><span class="mlbl">Region</span><span class="mval" id="modalRegionDetail">—</span></div>
                        <div class="modal-detail-row"><span class="mlbl">Capital</span><span class="mval" id="modalCapital">—</span></div>
                        <div class="modal-detail-row"><span class="mlbl">Languages</span><span class="mval" id="modalLang">—</span></div>
                        <div class="modal-detail-row"><span class="mlbl">Timezone</span><span class="mval" id="modalTZ">—</span></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-chart-bar text-warning me-2"></i>Risk Breakdown</h6>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1"><small class="text-muted">Weather Risk</small><small class="fw-bold" id="mRiskW">—</small></div>
                            <div class="progress" style="height:6px;"><div id="mRiskWBar" class="progress-bar" style="width:0%; background:#667eea; transition:width 0.8s;"></div></div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1"><small class="text-muted">Inflation Risk</small><small class="fw-bold" id="mRiskI">—</small></div>
                            <div class="progress" style="height:6px;"><div id="mRiskIBar" class="progress-bar" style="width:0%; background:#ffb822; transition:width 0.8s;"></div></div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1"><small class="text-muted">Currency Risk</small><small class="fw-bold" id="mRiskC">—</small></div>
                            <div class="progress" style="height:6px;"><div id="mRiskCBar" class="progress-bar" style="width:0%; background:#6482ff; transition:width 0.8s;"></div></div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1"><small class="text-muted">News Risk</small><small class="fw-bold" id="mRiskN">—</small></div>
                            <div class="progress" style="height:6px;"><div id="mRiskNBar" class="progress-bar" style="width:0%; background:#ff6384; transition:width 0.8s;"></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-warning" id="modalWatchBtn" onclick="toggleWatchFromModal()">
                    <i class="fa-solid fa-star me-2"></i>Add to Watchlist
                </button>
                <a id="modalDashboardLink" href="/" class="btn btn-success">
                    <i class="fa-solid fa-chart-line me-2"></i>View on Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let allCountries   = [];
let riskCache      = {};   // iso_code → risk data
let watchlistSet   = new Set(JSON.parse(localStorage.getItem('watchlist') || '[]'));
let restDataCache  = {};   // iso_code → REST Countries data
let modalCurrentIso = null;

/* =========================================================
   INIT
   ========================================================= */
$(document).ready(function () {
    loadCountries();

    $('#searchInput').on('input', renderGrid);
    $('#regionFilter').on('change', renderGrid);
    $('#sortBy').on('change', renderGrid);
});

/* =========================================================
   LOAD COUNTRIES
   ========================================================= */
async function loadCountries() {
    const countries = await $.get('/api/countries');
    allCountries = countries;

    // Compute stats
    const regions = new Set(countries.map(c => c.region).filter(Boolean));
    $('#totalCount').text(countries.length);
    $('#regionCount').text(regions.size);
    updateWatchCount();

    // Fetch REST Countries data (flags, capital, languages) for enrichment
    await enrichWithRestData();

    renderGrid();
}

async function enrichWithRestData() {
    try {
        const resp = await $.get('https://restcountries.com/v3.1/all?fields=name,cca3,flags,capital,languages,timezones,population');
        resp.forEach(c => {
            restDataCache[c.cca3] = c;
        });
    } catch(e) {
        console.warn('REST Countries enrichment failed:', e);
    }
}

/* =========================================================
   RENDER GRID
   ========================================================= */
function renderGrid() {
    const q      = $('#searchInput').val().trim().toLowerCase();
    const region = $('#regionFilter').val();
    const sort   = $('#sortBy').val();

    let list = allCountries.filter(c => {
        const matchQ = !q || c.name.toLowerCase().includes(q) || (c.iso_code||'').toLowerCase().includes(q);
        const matchR = !region || (c.region||'').includes(region);
        return matchQ && matchR;
    });

    // Sort
    if (sort === 'name')        list.sort((a,b) => a.name.localeCompare(b.name));
    if (sort === 'risk_asc')    list.sort((a,b) => (riskCache[a.iso_code]?.total||0) - (riskCache[b.iso_code]?.total||0));
    if (sort === 'risk_desc')   list.sort((a,b) => (riskCache[b.iso_code]?.total||0) - (riskCache[a.iso_code]?.total||0));

    // Remove skeletons
    $('.skeleton-wrap').remove();

    const $grid = $('#countriesGrid');
    $grid.empty();

    if (list.length === 0) {
        $('#noResults').show();
        $('#visibleCount').text(0);
        return;
    }
    $('#noResults').hide();
    $('#visibleCount').text(list.length);

    list.forEach(c => {
        const rest    = restDataCache[c.iso_code] || {};
        const flags   = rest.flags?.png || `https://flagcdn.com/w160/${c.iso_code.slice(0,2).toLowerCase()}.png`;
        const capital = (rest.capital||[])[0] || '—';
        const pop     = rest.population ? formatPop(rest.population) : '—';
        const isWatch = watchlistSet.has(c.iso_code);
        const risk    = riskCache[c.iso_code];
        const riskPill = risk
            ? `<span class="risk-pill ${risk.level === 'Low Risk' ? 'risk-low' : risk.level === 'Medium Risk' ? 'risk-medium' : 'risk-high'}">${risk.total}/100</span>`
            : `<span class="risk-pill" style="background:#f4f7f6; color:#aaa;">Loading…</span>`;

        $grid.append(`
        <div class="col-6 col-md-4 col-lg-3 country-item"
             data-region="${c.region||''}"
             data-name="${c.name.toLowerCase()}"
             data-iso="${c.iso_code}">
            <div class="country-card">
                <div class="card-flag-wrap">
                    <img src="${flags}" alt="${c.name}" onerror="this.src='https://via.placeholder.com/80x50?text=${c.iso_code}'">
                    <span class="region-ribbon">${c.region||'—'}</span>
                </div>
                <div class="card-body-inner">
                    <h6>${c.name}</h6>
                    <span class="iso-badge">${c.iso_code} · ${c.currency_code||'—'}</span>
                    <div class="card-stat-row">
                        <span class="lbl">Capital</span>
                        <span class="val">${capital}</span>
                    </div>
                    <div class="card-stat-row">
                        <span class="lbl">Population</span>
                        <span class="val">${pop}</span>
                    </div>
                    <div class="card-stat-row">
                        <span class="lbl">Risk Score</span>
                        <span class="val" id="risk-${c.iso_code}">${riskPill}</span>
                    </div>
                </div>
                <div class="card-footer-inner">
                    <button class="btn-detail" onclick="openDetail('${c.iso_code}')">
                        <i class="fa-solid fa-chart-line me-1"></i>Details
                    </button>
                    <button class="btn-watch ${isWatch ? 'active' : ''}"
                            id="watch-${c.iso_code}"
                            onclick="toggleWatch('${c.iso_code}')"
                            title="${isWatch ? 'Remove from Watchlist' : 'Add to Watchlist'}">
                        <i class="fa-${isWatch ? 'solid' : 'regular'} fa-star"></i>
                    </button>
                </div>
            </div>
        </div>`);
    });

    // Lazy-load risk for visible countries (batch, max 6 at once)
    lazyLoadRisk(list.map(c => c.iso_code));
}

/* =========================================================
   LAZY LOAD RISK
   ========================================================= */
function lazyLoadRisk(isoCodes) {
    const todo = isoCodes.filter(iso => !riskCache[iso]);
    // Process in batches of 4 to avoid hammering
    let i = 0;
    function next() {
        if (i >= todo.length) return;
        const batch = todo.slice(i, i+4);
        i += 4;
        batch.forEach(iso => {
            $.get(`/api/risk?iso_code=${iso}`)
              .done(res => {
                riskCache[iso] = res.risk_score;
                updateRiskCell(iso, res.risk_score);
              })
              .always(() => {});
        });
        setTimeout(next, 1000);
    }
    next();
}

function updateRiskCell(iso, rs) {
    const cls  = rs.level === 'Low Risk' ? 'risk-low' : rs.level === 'Medium Risk' ? 'risk-medium' : 'risk-high';
    $(`#risk-${iso}`).html(`<span class="risk-pill ${cls}">${rs.total}/100</span>`);
}

/* =========================================================
   OPEN DETAIL MODAL
   ========================================================= */
function openDetail(iso) {
    modalCurrentIso = iso;
    const c    = allCountries.find(x => x.iso_code === iso);
    const rest = restDataCache[iso] || {};

    // Basic info
    $('#modalCountryName').text(c.name);
    $('#modalRegion').text(c.region || '—');
    $('#modalRegionDetail').text(c.region || '—');
    $('#modalISO').text(c.iso_code);
    $('#modalCurrency').text(c.currency_code || '—');

    const flags   = rest.flags?.png || `https://flagcdn.com/w160/${iso.slice(0,2).toLowerCase()}.png`;
    $('#modalFlag').attr('src', flags);
    $('#modalCapital').text((rest.capital||[])[0] || '—');
    $('#modalPop').text(rest.population ? formatPop(rest.population) : '—');
    $('#modalTZ').text((rest.timezones||[])[0] || '—');

    const langs = rest.languages ? Object.values(rest.languages).slice(0,3).join(', ') : '—';
    $('#modalLang').text(langs);

    // Watch button state
    const isWatch = watchlistSet.has(iso);
    $('#modalWatchBtn').html(isWatch
        ? '<i class="fa-solid fa-star me-2"></i>Remove from Watchlist'
        : '<i class="fa-solid fa-star me-2"></i>Add to Watchlist'
    ).toggleClass('btn-warning', !isWatch).toggleClass('btn-outline-warning', isWatch);

    // Dashboard link
    $('#modalDashboardLink').attr('href', `/?country=${iso}`);

    // Reset risk
    $('#modalGDP').text('Loading…');
    $('#modalRisk').text('Loading…');
    ['W','I','C','N'].forEach(k => {
        $(`#mRisk${k}`).text('—');
        $(`#mRisk${k}Bar`).css('width','0%');
    });

    $('#countryModal').modal('show');

    // Fetch risk
    $.get(`/api/risk?iso_code=${iso}`, res => {
        const rs = res.risk_score;
        riskCache[iso] = rs;
        updateRiskCell(iso, rs);

        const gdpFmt = res.gdp ? '$' + formatGDP(res.gdp) : 'N/A';
        $('#modalGDP').text(gdpFmt);

        const riskCls = rs.level === 'Low Risk' ? 'text-success' : rs.level === 'Medium Risk' ? 'text-warning' : 'text-danger';
        $('#modalRisk').text(`${rs.total}/100`).removeClass('text-success text-warning text-danger').addClass(riskCls);

        $('#mRiskW').text(`${rs.weather}/25`);
        $('#mRiskI').text(`${rs.inflation}/25`);
        $('#mRiskC').text(`${rs.currency}/25`);
        $('#mRiskN').text(`${rs.news}/25`);
        setTimeout(() => {
            $('#mRiskWBar').css('width', (rs.weather /25*100)+'%');
            $('#mRiskIBar').css('width', (rs.inflation/25*100)+'%');
            $('#mRiskCBar').css('width', (rs.currency /25*100)+'%');
            $('#mRiskNBar').css('width', (rs.news     /25*100)+'%');
        }, 100);
    });
}

/* =========================================================
   WATCHLIST
   ========================================================= */
function toggleWatch(iso) {
    if (watchlistSet.has(iso)) {
        watchlistSet.delete(iso);
    } else {
        watchlistSet.add(iso);
    }
    localStorage.setItem('watchlist', JSON.stringify([...watchlistSet]));

    const isNow = watchlistSet.has(iso);
    const $btn  = $(`#watch-${iso}`);
    $btn.toggleClass('active', isNow)
        .find('i').removeClass('fa-solid fa-regular')
        .addClass(isNow ? 'fa-solid' : 'fa-regular');

    updateWatchCount();
}

function toggleWatchFromModal() {
    if (!modalCurrentIso) return;
    toggleWatch(modalCurrentIso);
    const isNow = watchlistSet.has(modalCurrentIso);
    $('#modalWatchBtn').html(isNow
        ? '<i class="fa-solid fa-star me-2"></i>Remove from Watchlist'
        : '<i class="fa-solid fa-star me-2"></i>Add to Watchlist'
    ).toggleClass('btn-warning', !isNow).toggleClass('btn-outline-warning', isNow);
}

function updateWatchCount() {
    $('#watchCount').text(watchlistSet.size);
}

/* =========================================================
   HELPERS
   ========================================================= */
function formatPop(n) {
    if (n >= 1e9) return (n/1e9).toFixed(2) + 'B';
    if (n >= 1e6) return (n/1e6).toFixed(1)  + 'M';
    if (n >= 1e3) return (n/1e3).toFixed(0)  + 'K';
    return n;
}
function formatGDP(v) {
    if (v >= 1e12) return (v/1e12).toFixed(2)+'T';
    if (v >= 1e9)  return (v/1e9).toFixed(2)+'B';
    if (v >= 1e6)  return (v/1e6).toFixed(2)+'M';
    return v.toFixed(0);
}
</script>
@endsection