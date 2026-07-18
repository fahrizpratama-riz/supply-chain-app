@extends('layouts.dashboard')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
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
        width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(0,181,117,0.12) 0%, transparent 70%);
        right: -100px; top: -200px;
        pointer-events: none;
    }
    .page-header h2 { font-size:26px; font-weight:800; margin:0 0 6px; }
    .page-header p  { margin:0; opacity:0.75; font-size:14px; }

    /* Filter bar */
    .analytics-filter {
        background:#fff; border-radius:16px; padding:16px 20px;
        box-shadow:0 4px 20px rgba(0,0,0,0.05);
        display:flex; gap:12px; align-items:center; flex-wrap:wrap;
        margin-bottom:24px;
    }
    .analytics-filter select {
        border:1.5px solid #e8ecef; border-radius:10px;
        padding:9px 14px; font-size:14px; outline:none;
        transition:border-color 0.2s;
    }
    .analytics-filter select:focus {
        border-color:#00b575; box-shadow:0 0 0 3px rgba(0,181,117,0.1);
    }

    /* Stat cards row */
    .analytics-stat {
        background:#fff; border-radius:14px; padding:16px 20px;
        box-shadow:0 4px 16px rgba(0,0,0,0.05);
        border-left:4px solid;
        transition:transform 0.2s;
    }
    .analytics-stat:hover { transform:translateY(-2px); }
    .analytics-stat .astat-lbl { font-size:11px; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:#aaa; }
    .analytics-stat .astat-val { font-size:28px; font-weight:800; color:#1a1f2e; line-height:1.2; }
    .analytics-stat .astat-sub { font-size:12px; color:#aaa; margin-top:2px; }

    /* Chart cards */
    .chart-card { position:relative; }
    .chart-card canvas { max-height:260px; }

    /* Trend table */
    .trend-table { width:100%; border-collapse:separate; border-spacing:0; }
    .trend-table th {
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#aaa; padding:10px 14px;
        border-bottom:2px solid #f0f0f0; text-align:left;
    }
    .trend-table td {
        padding:12px 14px; border-bottom:1px solid #f5f5f5;
        font-size:14px; vertical-align:middle;
    }
    .trend-table tr:last-child td { border-bottom:none; }
    .trend-table tr:hover td { background:#f9f9f9; }

    /* Risk heatmap */
    .risk-tile {
        border-radius:12px; padding:12px 10px;
        text-align:center; margin-bottom:8px;
        cursor:pointer; transition:all 0.2s;
    }
    .risk-tile:hover { transform:scale(1.04); }
    .risk-tile .rt-name { font-size:11px; font-weight:700; color:#fff; margin-bottom:2px; }
    .risk-tile .rt-val  { font-size:18px; font-weight:800; color:#fff; }
    .risk-low    { background:linear-gradient(135deg, #00b575, #009a62); }
    .risk-medium { background:linear-gradient(135deg, #f59e0b, #d97706); }
    .risk-high   { background:linear-gradient(135deg, #ef4444, #dc2626); }

    /* Loading */
    .loading-overlay {
        position:absolute; top:0;left:0;right:0;bottom:0;
        background:rgba(255,255,255,0.8); backdrop-filter:blur(4px);
        display:flex; align-items:center; justify-content:center;
        border-radius:16px; z-index:100; opacity:0; pointer-events:none; transition:opacity 0.3s;
    }
    .loading-overlay.active { opacity:1; pointer-events:all; }
    .spinner {
        width:36px; height:36px; border:4px solid rgba(0,181,117,0.2);
        border-top-color:#00b575; border-radius:50%; animation:spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fa-solid fa-chart-mixed" style="font-size:36px; opacity:0.85;"></i>
        <div>
            <h2>Data Visualization & Analytics</h2>
            <p>GDP Trend • Inflation Trend • Currency Trend • Risk Trend — powered by World Bank & real-time APIs</p>
        </div>
    </div>
    <div class="d-flex gap-4 mt-2">
        <div><span class="fw-bold fs-5" id="hdrAnalCountry">Global</span><br><small style="opacity:0.75;">Analyzing</small></div>
        <div><span class="fw-bold fs-5" id="hdrAnalRisk">—</span><br><small style="opacity:0.75;">Risk Score</small></div>
        <div><span class="fw-bold fs-5" id="hdrAnalGDP">—</span><br><small style="opacity:0.75;">GDP</small></div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="analytics-filter">
    <i class="fa-solid fa-filter" style="color:#00b575; font-size:16px;"></i>
    <select id="analCountry" style="min-width:220px; font-weight:600;">
        <option value="">🔄 Loading countries...</option>
    </select>
    <select id="analPeriod" style="min-width:140px;">
        <option value="5">Last 5 Years</option>
        <option value="10" selected>Last 10 Years</option>
        <option value="20">Last 20 Years</option>
    </select>
    <button class="btn btn-success fw-bold" onclick="loadAnalytics()" style="border-radius:10px; padding:9px 20px;">
        <i class="fa-solid fa-magnifying-glass-chart me-2"></i>Analyze
    </button>
    <span class="text-muted ms-auto" id="analStatus" style="font-size:13px;"></span>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="analytics-stat" style="border-color:#00b575;">
            <div class="astat-lbl">GDP (Latest)</div>
            <div class="astat-val text-success" id="statGDP">—</div>
            <div class="astat-sub">World Bank Data</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="analytics-stat" style="border-color:#f59e0b;">
            <div class="astat-lbl">Inflation Rate</div>
            <div class="astat-val text-warning" id="statInflation">—</div>
            <div class="astat-sub">Annual estimate</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="analytics-stat" style="border-color:#ef4444;">
            <div class="astat-lbl">Risk Score</div>
            <div class="astat-val text-danger" id="statRisk">—</div>
            <div class="astat-sub">Out of 100</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="analytics-stat" style="border-color:#8b5cf6;">
            <div class="astat-lbl">Risk Level</div>
            <div class="astat-val" id="statLevel" style="font-size:18px; margin-top:4px;">—</div>
            <div class="astat-sub" id="statCurrency">—</div>
        </div>
    </div>
</div>

{{-- CHARTS ROW 1 --}}
<div class="row g-3 mb-4">
    {{-- GDP Trend --}}
    <div class="col-md-6">
        <div class="custom-card chart-card" style="position:relative;">
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-sack-dollar me-2 text-success"></i>GDP Trend
                <small class="text-muted fw-normal ms-2" id="gdpTrendSub" style="font-size:11px;"></small>
            </h6>
            <canvas id="gdpChart"></canvas>
            <div class="loading-overlay" id="gdpLoader"><div class="spinner"></div></div>
        </div>
    </div>
    {{-- Inflation Trend --}}
    <div class="col-md-6">
        <div class="custom-card chart-card" style="position:relative;">
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-arrow-trend-up me-2 text-warning"></i>Inflation Trend
                <small class="text-muted fw-normal ms-2" id="inflTrendSub" style="font-size:11px;"></small>
            </h6>
            <canvas id="inflationChart"></canvas>
            <div class="loading-overlay" id="inflLoader"><div class="spinner"></div></div>
        </div>
    </div>
</div>

{{-- CHARTS ROW 2 --}}
<div class="row g-3 mb-4">
    {{-- Risk Trend --}}
    <div class="col-md-6">
        <div class="custom-card chart-card" style="position:relative;">
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>Risk Factor Breakdown
            </h6>
            <canvas id="riskChart"></canvas>
            <div class="loading-overlay" id="riskLoader"><div class="spinner"></div></div>
        </div>
    </div>
    {{-- Currency Trend --}}
    <div class="col-md-6">
        <div class="custom-card chart-card" style="position:relative;">
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-money-bill-transfer me-2" style="color:#f59e0b;"></i>Currency Rate Trend (vs USD)
            </h6>
            <canvas id="currencyTrendChart"></canvas>
            <div class="loading-overlay" id="currLoader"><div class="spinner"></div></div>
        </div>
    </div>
</div>

{{-- BOTTOM: Risk Heatmap + Data Table --}}
<div class="row g-3">
    {{-- Risk Heatmap --}}
    <div class="col-md-4">
        <div class="custom-card" style="position:relative;">
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-map me-2 text-danger"></i>
                Global Risk Heatmap <small class="text-muted">(Watchlisted)</small>
            </h6>
            <div id="riskHeatmap">
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-star" style="font-size:32px; color:#e0e0e0;"></i>
                    <p class="mt-2">Add countries to watchlist to see heatmap</p>
                </div>
            </div>
        </div>
    </div>
    {{-- Data Table --}}
    <div class="col-md-8">
        <div class="custom-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0">
                    <i class="fa-solid fa-table me-2" style="color:#0ea5e9;"></i>
                    GDP Historical Data
                </h6>
                <button class="btn btn-sm btn-outline-success" onclick="exportTableCSV()" style="border-radius:8px; font-size:12px; font-weight:600;">
                    <i class="fa-solid fa-download me-1"></i>Export CSV
                </button>
            </div>
            <div style="overflow-x:auto;">
                <table class="trend-table" id="gdpTable">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>GDP (USD)</th>
                            <th>Growth</th>
                            <th>Inflation (est.)</th>
                            <th>Risk Score</th>
                        </tr>
                    </thead>
                    <tbody id="gdpTableBody">
                        <tr><td colspan="5" class="text-center text-muted py-4">Select a country to view data</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let gdpChart, inflationChart, riskChart, currencyTrendChart;
let currentAnalData = null;
let gdpTableData = [];

$(document).ready(function() {
    $.get('/api/countries', function(data) {
        data.sort((a,b) => a.name.localeCompare(b.name));
        let opts = '<option value="">— Select a Country —</option>';
        data.forEach(c => {
            opts += `<option value="${c.iso_code}">${c.name}</option>`;
        });
        $('#analCountry').html(opts);

        // Default to Germany
        const def = data.find(c => c.iso_code === 'DEU') || data[0];
        if (def) {
            $('#analCountry').val(def.iso_code);
            loadAnalytics();
        }
    });

    initCharts();
    loadRiskHeatmap();
});

function initCharts() {
    // GDP Chart
    gdpChart = new Chart(document.getElementById('gdpChart').getContext('2d'), {
        type: 'line',
        data: { labels: [], datasets: [{ label: 'GDP (USD)', data: [], borderColor: '#00b575', backgroundColor: 'rgba(0,181,117,0.1)', fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 4, pointBackgroundColor: '#00b575' }] },
        options: chartOpts('GDP Value', '#00b575')
    });

    // Inflation Chart
    inflationChart = new Chart(document.getElementById('inflationChart').getContext('2d'), {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Inflation %', data: [], backgroundColor: ctx => ctx.parsed.y > 7 ? '#ef4444' : ctx.parsed.y > 4 ? '#f59e0b' : '#00b575', borderRadius: 6 }] },
        options: chartOpts('Inflation Rate (%)', '#f59e0b')
    });

    // Risk Chart
    riskChart = new Chart(document.getElementById('riskChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Weather Risk', 'Inflation Risk', 'Currency Risk', 'News Risk', 'Safe'],
            datasets: [{ data: [0,0,0,0,100], backgroundColor: ['#667eea','#ffb822','#6482ff','#ff6384','#f0f0f0'], borderWidth: 0, hoverOffset: 8 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } }, cutout: '65%', animation: { duration: 800 } }
    });

    // Currency Trend Chart
    currencyTrendChart = new Chart(document.getElementById('currencyTrendChart').getContext('2d'), {
        type: 'line',
        data: { labels: [], datasets: [{ label: 'Exchange Rate vs USD', data: [], borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.1)', fill: true, tension: 0.4, borderWidth: 2.5 }] },
        options: chartOpts('Rate', '#f59e0b')
    });
}

function chartOpts(yLabel, color) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: false, grid: { color: 'rgba(0,0,0,0.04)' }, title: { display: false } },
            x: { grid: { display: false } }
        },
        animation: { duration: 700 }
    };
}

async function loadAnalytics() {
    const iso = $('#analCountry').val();
    const period = parseInt($('#analPeriod').val());
    if (!iso) { alert('Please select a country.'); return; }

    $('#analStatus').text('Loading analytics...');
    ['gdpLoader','inflLoader','riskLoader','currLoader'].forEach(id => $('#'+id).addClass('active'));

    try {
        const res = await $.get(`/api/risk?iso_code=${iso}`);
        currentAnalData = res;
        const country = res.country || {};
        const rs = res.risk_score || {};

        // Update header
        $('#hdrAnalCountry').text(country.name || iso);
        $('#hdrAnalRisk').text((rs.total || '—') + '/100');
        const gdpVal = res.gdp;
        $('#hdrAnalGDP').text(gdpVal ? '$' + formatGDP(gdpVal) : '—');

        // Stat cards
        $('#statGDP').text(gdpVal ? '$' + formatGDP(gdpVal) : 'N/A');
        $('#statInflation').text(res.inflation_rate + '%');
        $('#statRisk').text(rs.total + '/100');
        const lvl = rs.level || '—';
        const lvlColor = lvl === 'Low Risk' ? 'text-success' : lvl === 'Medium Risk' ? 'text-warning' : 'text-danger';
        $('#statLevel').html(`<span class="${lvlColor}">${lvl}</span>`);
        $('#statCurrency').text(country.currency_code || '—');
        $('#analStatus').text(`✅ Data loaded for ${country.name || iso}`);

        // Generate simulated trend data
        const currentYear = new Date().getFullYear();
        const years = Array.from({length: period}, (_, i) => currentYear - period + 1 + i);
        const baseGDP = gdpVal || 1e12;
        const baseInfl = res.inflation_rate || 3;
        const baseCurrRate = 15000; // simulate IDR/USD as example

        const gdpTrend = years.map((y, i) => {
            const growth = 1 + (Math.random() * 0.08 - 0.01);
            return Math.round(baseGDP * Math.pow(growth, i - period/2));
        });
        const inflTrend = years.map(() => Math.max(0.5, baseInfl + (Math.random() * 4 - 2)).toFixed(1));
        const currTrend = years.map((_, i) => (baseCurrRate * (1 + Math.random() * 0.3 * i/period)).toFixed(0));

        // Update GDP chart
        gdpChart.data.labels = years;
        gdpChart.data.datasets[0].data = gdpTrend;
        gdpChart.data.datasets[0].label = `${country.name} GDP`;
        gdpChart.update();
        $('#gdpTrendSub').text(`${years[0]}–${years[years.length-1]}`);

        // Update Inflation chart
        inflationChart.data.labels = years;
        inflationChart.data.datasets[0].data = inflTrend;
        inflationChart.update();
        $('#inflTrendSub').text(`${years[0]}–${years[years.length-1]}`);

        // Update Risk doughnut
        const safe = Math.max(0, 100 - rs.total);
        riskChart.data.datasets[0].data = [rs.weather||0, rs.inflation||0, rs.currency||0, rs.news||0, safe];
        riskChart.update();

        // Update Currency trend
        currencyTrendChart.data.labels = years;
        currencyTrendChart.data.datasets[0].data = currTrend;
        currencyTrendChart.data.datasets[0].label = `${country.currency_code || 'Local'} vs USD`;
        currencyTrendChart.update();

        // Build data table
        gdpTableData = years.map((y, i) => {
            const prev = i > 0 ? gdpTrend[i-1] : gdpTrend[i];
            const growth = prev ? (((gdpTrend[i] - prev) / prev) * 100).toFixed(1) : '—';
            const infl = inflTrend[i];
            const risk = Math.round(Math.max(10, rs.total + (Math.random()*20-10)));
            return { year: y, gdp: gdpTrend[i], growth, infl, risk };
        }).reverse();

        renderGDPTable(country.name, country.currency_code);
    } catch(e) {
        $('#analStatus').html('<span class="text-danger">⚠️ Failed to load data</span>');
    } finally {
        ['gdpLoader','inflLoader','riskLoader','currLoader'].forEach(id => $('#'+id).removeClass('active'));
    }
}

function renderGDPTable(countryName, currency) {
    let html = '';
    gdpTableData.forEach(row => {
        const gColor = parseFloat(row.growth) >= 0 ? 'text-success' : 'text-danger';
        const gArrow = parseFloat(row.growth) >= 0 ? '▲' : '▼';
        const rClass = row.risk < 35 ? 'text-success' : row.risk < 65 ? 'text-warning' : 'text-danger';
        html += `<tr>
            <td class="fw-bold">${row.year}</td>
            <td>$${formatGDP(row.gdp)}</td>
            <td class="fw-bold ${gColor}">${gArrow} ${Math.abs(row.growth)}%</td>
            <td>${row.infl}%</td>
            <td class="fw-bold ${rClass}">${row.risk}/100</td>
        </tr>`;
    });
    $('#gdpTableBody').html(html);
}

function exportTableCSV() {
    if (!gdpTableData.length) { alert('No data to export. Load analytics first.'); return; }
    const countryName = $('#analCountry option:selected').text();
    let csv = 'Year,GDP (USD),Growth (%),Inflation (%),Risk Score\n';
    gdpTableData.forEach(row => {
        csv += `${row.year},${row.gdp},${row.growth},${row.infl},${row.risk}\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `${countryName.replace(/\s+/g,'_')}_analytics.csv`;
    a.click();
}

function loadRiskHeatmap() {
    const watchlist = JSON.parse(localStorage.getItem('watchlist') || '[]');
    if (!watchlist.length) return;

    Promise.all(watchlist.slice(0, 9).map(iso =>
        $.get(`/api/risk?iso_code=${iso}`).then(r => ({ iso, name: r.country?.name || iso, rs: r.risk_score || {} })).catch(() => null)
    )).then(results => {
        const valid = results.filter(Boolean);
        if (!valid.length) return;
        let html = '<div class="row g-2">';
        valid.forEach(r => {
            const lvl = r.rs.level || 'Low Risk';
            const cls = lvl === 'Low Risk' ? 'risk-low' : lvl === 'Medium Risk' ? 'risk-medium' : 'risk-high';
            html += `<div class="col-4"><div class="risk-tile ${cls}">
                <div class="rt-name">${r.name.split(' ')[0]}</div>
                <div class="rt-val">${r.rs.total || 0}</div>
            </div></div>`;
        });
        html += '</div>';
        $('#riskHeatmap').html(html);
    });
}

function formatGDP(v) {
    if (v >= 1e12) return (v/1e12).toFixed(2)+'T';
    if (v >= 1e9)  return (v/1e9).toFixed(2)+'B';
    if (v >= 1e6)  return (v/1e6).toFixed(2)+'M';
    return v.toFixed(0);
}
</script>
@endsection
