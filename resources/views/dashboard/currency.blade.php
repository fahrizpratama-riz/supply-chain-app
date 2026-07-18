@extends('layouts.dashboard')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .page-header::after {
        content:''; position:absolute; width:300px; height:300px;
        background:rgba(255,255,255,0.07); border-radius:50%;
        right:-80px; top:-100px; pointer-events:none;
    }
    .page-header h2 { font-size:26px; font-weight:800; margin:0 0 6px; }
    .page-header p  { margin:0; opacity:0.8; font-size:14px; }

    /* Selector */
    .currency-selector {
        background:#fff; border-radius:16px; padding:16px 20px;
        box-shadow:0 4px 20px rgba(0,0,0,0.05);
        display:flex; gap:12px; align-items:center; flex-wrap:wrap;
        margin-bottom:24px;
    }
    .currency-selector select, .currency-selector input {
        border:1.5px solid #e8ecef; border-radius:10px;
        padding:10px 14px; font-size:14px; outline:none;
        transition:border-color 0.2s, box-shadow 0.2s;
    }
    .currency-selector select:focus, .currency-selector input:focus {
        border-color:#f59e0b;
        box-shadow:0 0 0 3px rgba(245,158,11,0.12);
    }

    /* Rate cards */
    .rate-card {
        background:#fff; border-radius:16px; padding:20px;
        box-shadow:0 4px 16px rgba(0,0,0,0.05);
        border:1.5px solid #f0f0f0; margin-bottom:12px;
        transition:all 0.2s; cursor:pointer;
    }
    .rate-card:hover {
        border-color:#f59e0b;
        box-shadow:0 6px 20px rgba(245,158,11,0.15);
        transform:translateY(-2px);
    }
    .rate-card.selected {
        border-color:#f59e0b;
        background:linear-gradient(135deg, #fffbeb, #fff);
    }
    .rate-flag {
        width:36px; height:25px; border-radius:4px;
        object-fit:cover; box-shadow:0 2px 6px rgba(0,0,0,0.12);
    }
    .rate-code { font-size:16px; font-weight:800; color:#1a1f2e; }
    .rate-name { font-size:11px; color:#aaa; }
    .rate-value { font-size:20px; font-weight:800; color:#f59e0b; }
    .rate-change { font-size:12px; font-weight:700; }
    .rate-change.up   { color:#00b575; }
    .rate-change.down { color:#ef4444; }

    /* Chart area */
    #currencyChart { max-height:320px; }
    #miniChart { max-height:80px; }

    /* Converter */
    .converter-box {
        background:linear-gradient(135deg, #f59e0b, #d97706);
        border-radius:16px; padding:24px; color:#fff;
    }
    .converter-box input {
        background:rgba(255,255,255,0.2);
        border:none; border-radius:10px;
        padding:10px 14px; color:#fff;
        font-size:18px; font-weight:700;
        width:100%; outline:none;
    }
    .converter-box input::placeholder { color:rgba(255,255,255,0.6); }
    .converter-result {
        font-size:32px; font-weight:800;
        text-shadow:0 2px 8px rgba(0,0,0,0.1);
    }

    /* Loading */
    .loading-overlay {
        position:absolute; top:0;left:0;right:0;bottom:0;
        background:rgba(255,255,255,0.8); backdrop-filter:blur(4px);
        display:flex; align-items:center; justify-content:center;
        border-radius:16px; z-index:100; opacity:0; pointer-events:none; transition:opacity 0.3s;
    }
    .loading-overlay.active { opacity:1; pointer-events:all; }
    .spinner {
        width:40px; height:40px; border:4px solid rgba(245,158,11,0.2);
        border-top-color:#f59e0b; border-radius:50%; animation:spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }

    /* Popular currencies grid */
    .curr-grid {
        display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:10px;
    }
    .curr-mini-card {
        background:#fff; border-radius:12px; padding:12px;
        border:1.5px solid #f0f0f0; text-align:center;
        transition:all 0.2s; cursor:pointer;
    }
    .curr-mini-card:hover {
        border-color:#f59e0b;
        box-shadow:0 4px 12px rgba(245,158,11,0.15);
    }
    .curr-mini-code { font-weight:800; font-size:14px; color:#1a1f2e; }
    .curr-mini-rate { font-size:13px; color:#f59e0b; font-weight:700; }
    .curr-mini-name { font-size:10px; color:#bbb; }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fa-solid fa-money-bill-transfer" style="font-size:36px; opacity:0.85;"></i>
        <div>
            <h2>Currency Impact Dashboard</h2>
            <p>Real-time exchange rates, trend analysis, and currency converter — powered by ExchangeRate API</p>
        </div>
    </div>
    <div class="d-flex gap-4 mt-2">
        <div><span class="fw-bold fs-5" id="hdrBase">USD</span><br><small style="opacity:0.75;">Base Currency</small></div>
        <div><span class="fw-bold fs-5" id="hdrRateCount">—</span><br><small style="opacity:0.75;">Currencies</small></div>
        <div><span class="fw-bold fs-5" id="hdrLastUpdate">—</span><br><small style="opacity:0.75;">Last Updated</small></div>
    </div>
</div>

{{-- SELECTOR --}}
<div class="currency-selector">
    <i class="fa-solid fa-dollar-sign" style="color:#f59e0b; font-size:18px;"></i>
    <div>
        <small class="text-muted fw-bold d-block mb-1">Base Currency</small>
        <select id="baseCurrency" style="min-width:160px;">
            <option value="USD" selected>🇺🇸 USD — US Dollar</option>
            <option value="EUR">🇪🇺 EUR — Euro</option>
            <option value="GBP">🇬🇧 GBP — British Pound</option>
            <option value="JPY">🇯🇵 JPY — Japanese Yen</option>
            <option value="CNY">🇨🇳 CNY — Chinese Yuan</option>
            <option value="IDR">🇮🇩 IDR — Indonesian Rupiah</option>
            <option value="AUD">🇦🇺 AUD — Australian Dollar</option>
            <option value="SGD">🇸🇬 SGD — Singapore Dollar</option>
        </select>
    </div>
    <div>
        <small class="text-muted fw-bold d-block mb-1">Search Currency</small>
        <input type="text" id="currencySearch" placeholder="e.g. EUR, JPY..." style="min-width:160px;">
    </div>
    <button class="btn btn-warning fw-bold" id="loadRatesBtn" style="border-radius:10px; padding:10px 20px;" onclick="loadRates()">
        <i class="fa-solid fa-rotate me-2"></i>Refresh Rates
    </button>
    <span class="text-muted ms-auto" id="ratesStatus" style="font-size:13px;"></span>
</div>

<div class="row g-3">
    {{-- CHART + CONVERTER --}}
    <div class="col-md-8">
        {{-- Main Chart --}}
        <div class="custom-card mb-3" style="position:relative;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-chart-line me-2" style="color:#f59e0b;"></i>
                        Exchange Rate Chart
                    </h5>
                    <small class="text-muted" id="chartSubtitle">Select a currency to view trend</small>
                </div>
                <div class="d-flex gap-2">
                    <select id="compareSelect" class="form-select form-select-sm" style="border-radius:8px; min-width:100px; font-weight:600;">
                        <option value="bar">Bar</option>
                        <option value="line" selected>Line</option>
                    </select>
                </div>
            </div>
            <canvas id="currencyChart"></canvas>
            <div class="loading-overlay" id="chartLoader">
                <div class="spinner"></div>
            </div>
        </div>

        {{-- Top Currencies Grid --}}
        <div class="custom-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0">
                    <i class="fa-solid fa-globe me-2 text-warning"></i>
                    Major Currency Rates (vs <span id="gridBase">USD</span>)
                </h6>
                <small class="text-muted" id="gridCount"></small>
            </div>
            <div class="curr-grid" id="currencyGrid">
                <div class="text-center text-muted py-4 col-span-all">
                    <div class="spinner mx-auto mb-2" style="border-top-color:#f59e0b;"></div>
                    Loading rates...
                </div>
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="col-md-4">
        {{-- Currency Converter --}}
        <div class="converter-box mb-3">
            <h6 class="fw-bold mb-3" style="opacity:0.9;">
                <i class="fa-solid fa-arrows-rotate me-2"></i>Currency Converter
            </h6>
            <div class="mb-3">
                <label class="fw-bold mb-1 d-block" style="font-size:13px; opacity:0.85;">Amount (<span id="convFromLabel">USD</span>)</label>
                <input type="number" id="convAmount" value="100" min="0" oninput="convertCurrency()">
            </div>
            <div class="mb-3">
                <label class="fw-bold mb-1 d-block" style="font-size:13px; opacity:0.85;">To Currency</label>
                <select id="convTo" onchange="convertCurrency()" style="background:rgba(255,255,255,0.2); border:none; border-radius:10px; padding:10px 14px; color:#fff; font-size:14px; font-weight:700; width:100%; outline:none;">
                    <option value="EUR">EUR — Euro</option>
                    <option value="IDR">IDR — Indonesian Rupiah</option>
                    <option value="JPY">JPY — Japanese Yen</option>
                    <option value="GBP">GBP — British Pound</option>
                    <option value="CNY">CNY — Chinese Yuan</option>
                    <option value="SGD">SGD — Singapore Dollar</option>
                </select>
            </div>
            <div class="text-center mt-3">
                <div style="font-size:13px; opacity:0.75; margin-bottom:4px;">Result</div>
                <div class="converter-result" id="convResult">—</div>
                <div style="font-size:12px; opacity:0.7; margin-top:4px;" id="convRate">1 USD = ? —</div>
            </div>
        </div>

        {{-- Top Rate List --}}
        <div class="custom-card" style="position:relative;">
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-list-ol me-2 text-warning"></i>
                Supply Chain Key Currencies
            </h6>
            <div id="keyRatesList">
                <div class="text-center text-muted py-3">
                    <div class="spinner mx-auto mb-2" style="border-top-color:#f59e0b;"></div>
                </div>
            </div>
            <div class="loading-overlay" id="ratesLoader">
                <div class="spinner"></div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="custom-card mt-0">
            <h6 class="fw-bold mb-2">
                <i class="fa-solid fa-circle-info me-2 text-warning"></i>Supply Chain Impact
            </h6>
            <p class="text-muted mb-0" style="font-size:13px; line-height:1.7;">
                Currency fluctuations directly affect import/export costs. A <strong>weakening local currency</strong> increases import costs and can signal supply chain risk. Monitor <strong>IDR, CNY, EUR</strong> closely for ASEAN trade routes.
            </p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let allRates = {};
let baseCurr = 'USD';
let chartInstance = null;

// Supply chain key currencies
const KEY_CURRENCIES = [
    { code: 'EUR', name: 'Euro',            flag: '🇪🇺' },
    { code: 'CNY', name: 'Chinese Yuan',    flag: '🇨🇳' },
    { code: 'JPY', name: 'Japanese Yen',    flag: '🇯🇵' },
    { code: 'GBP', name: 'British Pound',   flag: '🇬🇧' },
    { code: 'IDR', name: 'Indonesian Rupiah', flag: '🇮🇩' },
    { code: 'SGD', name: 'Singapore Dollar', flag: '🇸🇬' },
    { code: 'MYR', name: 'Malaysian Ringgit', flag: '🇲🇾' },
    { code: 'INR', name: 'Indian Rupee',    flag: '🇮🇳' },
    { code: 'KRW', name: 'South Korean Won', flag: '🇰🇷' },
    { code: 'AUD', name: 'Aust. Dollar',    flag: '🇦🇺' },
    { code: 'BRL', name: 'Brazilian Real',  flag: '🇧🇷' },
    { code: 'THB', name: 'Thai Baht',       flag: '🇹🇭' },
];

$(document).ready(function() {
    loadRates();
    initChart();

    $('#baseCurrency').on('change', function() {
        baseCurr = $(this).val();
        loadRates();
    });

    $('#currencySearch').on('input', function() {
        filterCurrencyGrid($(this).val().trim().toUpperCase());
    });

    $('#compareSelect').on('change', function() {
        updateChartType($(this).val());
    });
});

function loadRates() {
    baseCurr = $('#baseCurrency').val();
    $('#ratesLoader').addClass('active');
    $('#chartLoader').addClass('active');
    $('#ratesStatus').text('Fetching rates...');
    $('#hdrBase').text(baseCurr);
    $('#gridBase').text(baseCurr);
    $('#convFromLabel').text(baseCurr);

    $.get(`/api/currency?base=${baseCurr}`, function(data) {
        if (data && data.rates) {
            allRates = data.rates;
            const count = Object.keys(allRates).length;
            $('#hdrRateCount').text(count);
            const updated = data.date || new Date().toLocaleDateString();
            $('#hdrLastUpdate').text(updated);
            $('#ratesStatus').text(`✅ ${count} currencies loaded`);

            renderKeyRates();
            renderCurrencyGrid();
            renderChart(KEY_CURRENCIES.map(c => c.code).filter(c => allRates[c]));
            convertCurrency();
        } else {
            $('#ratesStatus').html('<span class="text-danger">⚠️ API unavailable — using demo data</span>');
            useDemoRates();
        }
    }).fail(function() {
        $('#ratesStatus').html('<span class="text-danger">⚠️ API error — using demo data</span>');
        useDemoRates();
    }).always(function() {
        $('#ratesLoader').removeClass('active');
        $('#chartLoader').removeClass('active');
    });
}

function useDemoRates() {
    // Demo rates vs USD
    allRates = {
        EUR: 0.92, GBP: 0.79, JPY: 149.5, CNY: 7.24, IDR: 15750, SGD: 1.34,
        MYR: 4.72, INR: 83.2, KRW: 1325, AUD: 1.53, BRL: 4.97, THB: 35.1,
        CAD: 1.36, CHF: 0.88, HKD: 7.82, MXN: 17.2, NOK: 10.5, SEK: 10.4,
        NZD: 1.63, ZAR: 18.6
    };
    $('#hdrRateCount').text(Object.keys(allRates).length);
    $('#hdrLastUpdate').text('Demo');
    renderKeyRates();
    renderCurrencyGrid();
    renderChart(KEY_CURRENCIES.map(c => c.code).filter(c => allRates[c]));
    convertCurrency();
}

function renderKeyRates() {
    let html = '';
    KEY_CURRENCIES.forEach(c => {
        if (!allRates[c.code]) return;
        const rate = allRates[c.code];
        // Simulate small change ±2%
        const change = ((Math.random() * 4) - 2).toFixed(2);
        const isUp = change >= 0;
        html += `
        <div class="rate-card d-flex align-items-center gap-3" onclick="selectCurrencyForChart('${c.code}', '${c.name}')">
            <span style="font-size:24px;">${c.flag}</span>
            <div class="flex-grow-1">
                <div class="rate-code">${c.code}</div>
                <div class="rate-name">${c.name}</div>
            </div>
            <div class="text-end">
                <div class="rate-value">${formatRate(rate, c.code)}</div>
                <div class="rate-change ${isUp ? 'up' : 'down'}">
                    ${isUp ? '▲' : '▼'} ${Math.abs(change)}%
                </div>
            </div>
        </div>`;
    });
    $('#keyRatesList').html(html);
}

function renderCurrencyGrid() {
    const entries = Object.entries(allRates).slice(0, 24);
    let html = '';
    entries.forEach(([code, rate]) => {
        const kc = KEY_CURRENCIES.find(k => k.code === code);
        const flag = kc ? kc.flag : '🌐';
        html += `
        <div class="curr-mini-card" onclick="selectCurrencyForChart('${code}', '${code}')">
            <div style="font-size:20px;">${flag}</div>
            <div class="curr-mini-code">${code}</div>
            <div class="curr-mini-rate">${formatRate(rate, code)}</div>
        </div>`;
    });
    $('#currencyGrid').html(html);
    $('#gridCount').text(`Showing ${entries.length} currencies`);
}

function filterCurrencyGrid(q) {
    if (!q) { renderCurrencyGrid(); return; }
    const entries = Object.entries(allRates).filter(([code]) => code.includes(q)).slice(0, 24);
    let html = '';
    entries.forEach(([code, rate]) => {
        const kc = KEY_CURRENCIES.find(k => k.code === code);
        const flag = kc ? kc.flag : '🌐';
        html += `
        <div class="curr-mini-card" onclick="selectCurrencyForChart('${code}', '${code}')">
            <div style="font-size:20px;">${flag}</div>
            <div class="curr-mini-code">${code}</div>
            <div class="curr-mini-rate">${formatRate(rate, code)}</div>
        </div>`;
    });
    $('#currencyGrid').html(html || '<div class="text-muted col-span-all">No currencies found</div>');
}

function initChart() {
    const ctx = document.getElementById('currencyChart').getContext('2d');
    chartInstance = new Chart(ctx, {
        type: 'line',
        data: { labels: [], datasets: [] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: { beginAtZero: false, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { grid: { display: false } }
            },
            animation: { duration: 600 }
        }
    });
}

function renderChart(codes) {
    // Simulate 7-day trend based on current rate
    const labels = ['7d ago','6d ago','5d ago','4d ago','3d ago','2d ago','Today'];
    const colors = ['#f59e0b','#0ea5e9','#8b5cf6','#00b575','#ef4444','#ec4899'];

    const datasets = codes.slice(0, 5).map((code, i) => {
        const base = allRates[code] || 1;
        const data = labels.map((_, j) => {
            const variation = (Math.random() * 0.04 - 0.02);
            return parseFloat((base * (1 + variation * (7 - j) / 7)).toFixed(4));
        });
        data[data.length - 1] = base;
        return {
            label: code,
            data,
            borderColor: colors[i],
            backgroundColor: colors[i] + '18',
            fill: i === 0,
            tension: 0.4,
            borderWidth: 2,
            pointRadius: 3,
        };
    });

    chartInstance.data.labels = labels;
    chartInstance.data.datasets = datasets;
    chartInstance.update();
    $('#chartSubtitle').text(`7-day simulated trend vs ${baseCurr}`);
}

function selectCurrencyForChart(code, name) {
    $('#chartSubtitle').text(`${code} (${name}) trend vs ${baseCurr}`);
    const base = allRates[code] || 1;
    const labels = ['7d ago','6d ago','5d ago','4d ago','3d ago','2d ago','Today'];
    const data = labels.map((_, j) => {
        return parseFloat((base * (1 + (Math.random() * 0.04 - 0.02) * (7 - j) / 7)).toFixed(4));
    });
    data[data.length - 1] = base;

    chartInstance.data.labels = labels;
    chartInstance.data.datasets = [{
        label: code,
        data,
        borderColor: '#f59e0b',
        backgroundColor: 'rgba(245,158,11,0.1)',
        fill: true,
        tension: 0.4,
        borderWidth: 2.5,
        pointBackgroundColor: '#f59e0b',
    }];
    chartInstance.update();
}

function updateChartType(type) {
    chartInstance.config.type = type;
    chartInstance.update();
}

function convertCurrency() {
    const amount = parseFloat($('#convAmount').val()) || 0;
    const to = $('#convTo').val();
    const rate = allRates[to];
    if (!rate) { $('#convResult').text('—'); return; }
    const result = (amount * rate).toFixed(2);
    const formatted = new Intl.NumberFormat('en-US').format(result);
    $('#convResult').text(`${formatted} ${to}`);
    $('#convRate').text(`1 ${baseCurr} = ${formatRate(rate, to)} ${to}`);
}

function formatRate(rate, code) {
    if (rate >= 1000) return rate.toFixed(0);
    if (rate >= 10) return rate.toFixed(2);
    if (rate >= 1) return rate.toFixed(4);
    return rate.toFixed(6);
}
</script>
@endsection
