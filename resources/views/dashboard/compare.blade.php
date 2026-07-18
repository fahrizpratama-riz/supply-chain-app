@extends('layouts.dashboard')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .page-header::after {
        content:''; position:absolute; width:300px; height:300px;
        background:rgba(255,255,255,0.06); border-radius:50%;
        right:-80px; top:-100px; pointer-events:none;
    }
    .page-header h2 { font-size:26px; font-weight:800; margin:0 0 6px; }
    .page-header p  { margin:0; opacity:0.8; font-size:14px; }

    /* Country selector bars */
    .compare-selector {
        background:#fff; border-radius:16px; padding:20px 24px;
        box-shadow:0 4px 20px rgba(0,0,0,0.05);
        margin-bottom:24px;
    }
    .country-pick-card {
        border-radius:14px; padding:16px;
        border:2px solid #e8ecef;
        transition:all 0.2s;
    }
    .country-pick-card.side-a { border-color:#8b5cf6; background:#faf5ff; }
    .country-pick-card.side-b { border-color:#0ea5e9; background:#f0f9ff; }
    .country-pick-card select {
        border:none; background:transparent;
        font-size:16px; font-weight:700; outline:none; width:100%;
    }
    .vs-badge {
        width:52px; height:52px;
        background:linear-gradient(135deg, #8b5cf6, #0ea5e9);
        border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        color:#fff; font-weight:800; font-size:16px;
        flex-shrink:0;
        box-shadow:0 4px 16px rgba(139,92,246,0.3);
    }

    /* Compare result cards */
    .compare-row {
        background:#fff; border-radius:14px;
        border:1.5px solid #f0f0f0;
        padding:0; overflow:hidden;
        margin-bottom:10px;
        transition:box-shadow 0.2s;
    }
    .compare-row:hover {
        box-shadow:0 4px 16px rgba(0,0,0,0.07);
    }
    .compare-row-header {
        background:#f8f9fa;
        padding:10px 16px;
        font-weight:700; font-size:13px; color:#555;
        display:flex; align-items:center; gap:8px;
        border-bottom:1px solid #f0f0f0;
    }
    .compare-row-body {
        display:grid;
        grid-template-columns:1fr auto 1fr;
        gap:0;
    }
    .compare-cell {
        padding:14px 16px;
        text-align:center;
    }
    .compare-cell.a { border-right:1px dashed #f0f0f0; }
    .compare-cell.b { border-left:1px dashed #f0f0f0; }
    .compare-cell .c-val {
        font-size:22px; font-weight:800; color:#1a1f2e; margin-bottom:2px;
    }
    .compare-cell .c-sub { font-size:11px; color:#aaa; }
    .compare-winner {
        background:#f0fdf4;
        border-radius:8px;
        padding:2px 10px;
        font-size:11px;
        font-weight:700;
        color:#00b575;
        margin-top:4px;
        display:inline-block;
    }
    .compare-winner.worse {
        background:#fff1f2;
        color:#ef4444;
    }
    .compare-mid {
        display:flex; align-items:center; justify-content:center;
        padding:14px 8px;
    }
    .compare-mid span {
        font-size:11px; color:#bbb; font-weight:600;
        writing-mode:horizontal-tb;
    }

    /* Bar comparison visual */
    .bar-compare-wrap {
        padding:12px 16px 14px;
        display:grid; grid-template-columns:1fr 60px 1fr; gap:8px; align-items:center;
    }
    .bar-a { background:#e9d5ff; border-radius:8px; height:10px; display:flex; justify-content:flex-end; overflow:hidden; }
    .bar-a .fill { background:#8b5cf6; border-radius:8px; height:100%; transition:width 1s ease; }
    .bar-b { background:#bae6fd; border-radius:8px; height:10px; overflow:hidden; }
    .bar-b .fill { background:#0ea5e9; border-radius:8px; height:100%; transition:width 1s ease; }
    .bar-label { text-align:center; font-size:11px; color:#888; font-weight:700; }

    /* Risk compare gauge */
    .risk-compare-box {
        background:#fff; border-radius:14px;
        border:1.5px solid #f0f0f0; padding:20px;
        margin-bottom:12px;
    }

    /* Chart */
    #compareChart { max-height:260px; }

    /* Flag */
    .country-flag {
        width:32px; height:22px; border-radius:4px;
        object-fit:cover; box-shadow:0 1px 4px rgba(0,0,0,0.12);
    }

    /* Summary verdict */
    .verdict-card {
        background:linear-gradient(135deg, #8b5cf6, #0ea5e9);
        border-radius:16px; padding:20px; color:#fff; margin-bottom:16px;
    }
    .verdict-card h5 { font-weight:800; margin-bottom:8px; }

    /* Loading */
    .loading-overlay {
        position:absolute; top:0;left:0;right:0;bottom:0;
        background:rgba(255,255,255,0.8); backdrop-filter:blur(4px);
        display:flex; align-items:center; justify-content:center;
        border-radius:16px; z-index:100; opacity:0; pointer-events:none; transition:opacity 0.3s;
    }
    .loading-overlay.active { opacity:1; pointer-events:all; }
    .spinner {
        width:40px; height:40px; border:4px solid rgba(139,92,246,0.2);
        border-top-color:#8b5cf6; border-radius:50%; animation:spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }
    .placeholder-compare {
        text-align:center; padding:60px 20px; color:#ccc;
    }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fa-solid fa-code-compare" style="font-size:36px; opacity:0.85;"></i>
        <div>
            <h2>Country Comparison Engine</h2>
            <p>Compare GDP, Inflation, Risk Score, Weather, and Currency side-by-side for any two countries</p>
        </div>
    </div>
    <div class="d-flex gap-4 mt-2">
        <div><span class="fw-bold fs-5" id="hdrCountryA">—</span><br><small style="opacity:0.75;">Country A</small></div>
        <div><span class="fw-bold fs-5">vs</span></div>
        <div><span class="fw-bold fs-5" id="hdrCountryB">—</span><br><small style="opacity:0.75;">Country B</small></div>
    </div>
</div>

{{-- SELECTOR --}}
<div class="compare-selector">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        {{-- Country A --}}
        <div class="country-pick-card side-a flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:12px;height:12px;border-radius:50%;background:#8b5cf6;"></div>
                <span style="font-size:12px;font-weight:700;color:#8b5cf6;">COUNTRY A</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <img id="flagA" src="" class="country-flag" style="display:none;">
                <select id="countryA" style="color:#1a1f2e;">
                    <option value="">🔄 Loading...</option>
                </select>
            </div>
        </div>

        <div class="vs-badge">VS</div>

        {{-- Country B --}}
        <div class="country-pick-card side-b flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:12px;height:12px;border-radius:50%;background:#0ea5e9;"></div>
                <span style="font-size:12px;font-weight:700;color:#0ea5e9;">COUNTRY B</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <img id="flagB" src="" class="country-flag" style="display:none;">
                <select id="countryB" style="color:#1a1f2e;">
                    <option value="">🔄 Loading...</option>
                </select>
            </div>
        </div>

        <button class="btn fw-bold" id="compareBtn" onclick="runComparison()"
            style="background:linear-gradient(135deg,#8b5cf6,#0ea5e9); color:#fff; border:none; border-radius:12px; padding:14px 28px; font-size:15px; white-space:nowrap;">
            <i class="fa-solid fa-code-compare me-2"></i>Compare Now
        </button>
    </div>
</div>

{{-- RESULTS --}}
<div id="comparePlaceholder" class="placeholder-compare">
    <i class="fa-solid fa-code-compare" style="font-size:64px; margin-bottom:16px; display:block;"></i>
    <p class="fw-semibold" style="color:#999; font-size:16px;">Select two countries and click Compare</p>
    <small class="text-muted">GDP • Inflation • Risk • Weather • Currency will be displayed</small>
</div>

<div id="compareResults" style="display:none; position:relative;">
    <div class="loading-overlay" id="compareLoader">
        <div class="spinner"></div>
    </div>

    {{-- Verdict Banner --}}
    <div class="verdict-card" id="verdictCard">
        <h5><i class="fa-solid fa-trophy me-2"></i><span id="verdictText">—</span></h5>
        <p class="mb-0" style="opacity:0.85; font-size:14px;" id="verdictDetail">—</p>
    </div>

    <div class="row g-3">
        <div class="col-md-7">
            {{-- Country Name Header --}}
            <div class="compare-row">
                <div class="compare-row-body" style="border-radius:14px; overflow:hidden;">
                    <div class="compare-cell a" style="background:#faf5ff;">
                        <img id="resultFlagA" src="" class="country-flag mb-2" style="height:30px;width:46px;">
                        <div class="c-val" id="resNameA" style="font-size:16px;">—</div>
                        <div class="c-sub" id="resIsoA">—</div>
                    </div>
                    <div class="compare-mid" style="background:#f8f9fa;">
                        <span>COMPARE</span>
                    </div>
                    <div class="compare-cell b" style="background:#f0f9ff;">
                        <img id="resultFlagB" src="" class="country-flag mb-2" style="height:30px;width:46px;">
                        <div class="c-val" id="resNameB" style="font-size:16px;">—</div>
                        <div class="c-sub" id="resIsoB">—</div>
                    </div>
                </div>
            </div>

            {{-- GDP --}}
            <div class="compare-row">
                <div class="compare-row-header">
                    <i class="fa-solid fa-sack-dollar text-success"></i> GDP (Current USD)
                </div>
                <div class="compare-row-body">
                    <div class="compare-cell a">
                        <div class="c-val text-success" id="resGdpA">—</div>
                        <div class="c-sub">GDP</div>
                        <div id="gdpWinnerA"></div>
                    </div>
                    <div class="compare-mid"><span>GDP</span></div>
                    <div class="compare-cell b">
                        <div class="c-val text-success" id="resGdpB">—</div>
                        <div class="c-sub">GDP</div>
                        <div id="gdpWinnerB"></div>
                    </div>
                </div>
                <div class="bar-compare-wrap">
                    <div class="bar-a"><div class="fill" id="barGdpA" style="width:50%;"></div></div>
                    <div class="bar-label">GDP</div>
                    <div class="bar-b"><div class="fill" id="barGdpB" style="width:50%;"></div></div>
                </div>
            </div>

            {{-- Inflation --}}
            <div class="compare-row">
                <div class="compare-row-header">
                    <i class="fa-solid fa-arrow-trend-up text-warning"></i> Inflation Rate
                </div>
                <div class="compare-row-body">
                    <div class="compare-cell a">
                        <div class="c-val text-warning" id="resInflA">—</div>
                        <div class="c-sub">Inflation %</div>
                        <div id="inflWinnerA"></div>
                    </div>
                    <div class="compare-mid"><span>INFL</span></div>
                    <div class="compare-cell b">
                        <div class="c-val text-warning" id="resInflB">—</div>
                        <div class="c-sub">Inflation %</div>
                        <div id="inflWinnerB"></div>
                    </div>
                </div>
                <div class="bar-compare-wrap">
                    <div class="bar-a"><div class="fill" id="barInflA" style="width:50%;"></div></div>
                    <div class="bar-label">INFL</div>
                    <div class="bar-b"><div class="fill" id="barInflB" style="width:50%;"></div></div>
                </div>
            </div>

            {{-- Risk Score --}}
            <div class="compare-row">
                <div class="compare-row-header">
                    <i class="fa-solid fa-triangle-exclamation text-danger"></i> Overall Risk Score
                </div>
                <div class="compare-row-body">
                    <div class="compare-cell a">
                        <div class="c-val" id="resRiskA" style="color:#ef4444;">—</div>
                        <div class="c-sub">/ 100</div>
                        <div id="riskWinnerA"></div>
                    </div>
                    <div class="compare-mid"><span>RISK</span></div>
                    <div class="compare-cell b">
                        <div class="c-val" id="resRiskB" style="color:#ef4444;">—</div>
                        <div class="c-sub">/ 100</div>
                        <div id="riskWinnerB"></div>
                    </div>
                </div>
                <div class="bar-compare-wrap">
                    <div class="bar-a" style="background:#fecaca;"><div class="fill" id="barRiskA" style="width:50%;background:#ef4444;"></div></div>
                    <div class="bar-label">RISK</div>
                    <div class="bar-b" style="background:#fecaca;"><div class="fill" id="barRiskB" style="width:50%;background:#ef4444;"></div></div>
                </div>
            </div>

            {{-- Weather --}}
            <div class="compare-row">
                <div class="compare-row-header">
                    <i class="fa-solid fa-cloud-sun text-primary"></i> Weather Conditions
                </div>
                <div class="compare-row-body">
                    <div class="compare-cell a">
                        <div class="c-val text-primary" id="resTempA">—</div>
                        <div class="c-sub" id="resWindA">Wind: —</div>
                        <div id="wxWinnerA"></div>
                    </div>
                    <div class="compare-mid"><span>WTHR</span></div>
                    <div class="compare-cell b">
                        <div class="c-val text-primary" id="resTempB">—</div>
                        <div class="c-sub" id="resWindB">Wind: —</div>
                        <div id="wxWinnerB"></div>
                    </div>
                </div>
            </div>

            {{-- Currency --}}
            <div class="compare-row">
                <div class="compare-row-header">
                    <i class="fa-solid fa-money-bill-transfer" style="color:#f59e0b;"></i> Currency
                </div>
                <div class="compare-row-body">
                    <div class="compare-cell a">
                        <div class="c-val" style="color:#f59e0b;" id="resCurrA">—</div>
                        <div class="c-sub" id="resCurrCodeA">—</div>
                    </div>
                    <div class="compare-mid"><span>CURR</span></div>
                    <div class="compare-cell b">
                        <div class="c-val" style="color:#f59e0b;" id="resCurrB">—</div>
                        <div class="c-sub" id="resCurrCodeB">—</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            {{-- Radar/Bar chart comparison --}}
            <div class="custom-card mb-3">
                <h6 class="fw-bold mb-3">
                    <i class="fa-solid fa-chart-radar me-2" style="color:#8b5cf6;"></i>
                    Risk Factor Radar
                </h6>
                <canvas id="compareChart"></canvas>
            </div>

            {{-- Summary Scores --}}
            <div class="custom-card">
                <h6 class="fw-bold mb-3">
                    <i class="fa-solid fa-ranking-star me-2" style="color:#8b5cf6;"></i>
                    Trade Suitability Score
                </h6>
                <div id="tradeSuitability">
                    <div class="text-center text-muted py-3">Run comparison to see scores</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let dataA = null, dataB = null;
let compareChart = null;

$(document).ready(function() {
    // Load countries
    $.get('/api/countries', function(data) {
        data.sort((a,b) => a.name.localeCompare(b.name));
        let optsA = '', optsB = '';
        data.forEach(c => {
            optsA += `<option value="${c.iso_code}" data-name="${c.name}">${c.name}</option>`;
            optsB += `<option value="${c.iso_code}" data-name="${c.name}">${c.name}</option>`;
        });
        $('#countryA').html(optsA);
        $('#countryB').html(optsB);

        // Set defaults: Germany vs Indonesia
        const defA = data.find(c => c.iso_code === 'DEU') || data[0];
        const defB = data.find(c => c.iso_code === 'IDN') || data[1];
        if (defA) { $('#countryA').val(defA.iso_code); updateFlag('A', defA.iso_code); }
        if (defB) { $('#countryB').val(defB.iso_code); updateFlag('B', defB.iso_code); }
    });

    $('#countryA').on('change', function() { updateFlag('A', $(this).val()); });
    $('#countryB').on('change', function() { updateFlag('B', $(this).val()); });

    initCompareChart();
});

function updateFlag(side, iso) {
    const flagUrl = `https://flagcdn.com/w40/${iso.slice(0,2).toLowerCase()}.png`;
    $(`#flag${side}`).attr('src', flagUrl).show();
    $(`#hdrCountry${side}`).text($(`#country${side} option:selected`).data('name') || iso);
}

function initCompareChart() {
    const ctx = document.getElementById('compareChart').getContext('2d');
    compareChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Weather Risk', 'Inflation Risk', 'Currency Risk', 'News Risk'],
            datasets: [
                {
                    label: 'Country A',
                    data: [0,0,0,0],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.15)',
                    borderWidth: 2,
                },
                {
                    label: 'Country B',
                    data: [0,0,0,0],
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14,165,233,0.15)',
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 25,
                    ticks: { display: false },
                    grid: { color: 'rgba(0,0,0,0.07)' }
                }
            },
            plugins: {
                legend: { position: 'top' }
            },
            animation: { duration: 800 }
        }
    });
}

async function runComparison() {
    const isoA = $('#countryA').val();
    const isoB = $('#countryB').val();
    if (!isoA || !isoB) { alert('Please select both countries.'); return; }
    if (isoA === isoB) { alert('Please select two different countries.'); return; }

    $('#comparePlaceholder').hide();
    $('#compareResults').show();
    $('#compareLoader').addClass('active');

    const nameA = $('#countryA option:selected').text();
    const nameB = $('#countryB option:selected').text();
    $('#hdrCountryA').text(nameA);
    $('#hdrCountryB').text(nameB);

    try {
        const [resA, resB] = await Promise.all([
            $.get(`/api/risk?iso_code=${isoA}`),
            $.get(`/api/risk?iso_code=${isoB}`)
        ]);
        dataA = resA;
        dataB = resB;
        renderComparison(isoA, nameA, isoB, nameB);
    } catch(e) {
        alert('Failed to load comparison data. Please try again.');
    } finally {
        $('#compareLoader').removeClass('active');
    }
}

function renderComparison(isoA, nameA, isoB, nameB) {
    // Flags
    const flagUrlA = `https://flagcdn.com/w40/${isoA.slice(0,2).toLowerCase()}.png`;
    const flagUrlB = `https://flagcdn.com/w40/${isoB.slice(0,2).toLowerCase()}.png`;
    $('#resultFlagA').attr('src', flagUrlA);
    $('#resultFlagB').attr('src', flagUrlB);
    $('#resNameA').text(nameA);
    $('#resNameB').text(nameB);
    $('#resIsoA').text(isoA);
    $('#resIsoB').text(isoB);

    // GDP
    const gdpA = dataA.gdp || 0;
    const gdpB = dataB.gdp || 0;
    $('#resGdpA').text(gdpA ? '$' + formatGDP(gdpA) : 'N/A');
    $('#resGdpB').text(gdpB ? '$' + formatGDP(gdpB) : 'N/A');
    setWinner('gdpWinner', gdpA, gdpB, true);
    const gdpMax = Math.max(gdpA, gdpB) || 1;
    setTimeout(() => {
        $('#barGdpA .fill').css('width', (gdpA/gdpMax*100)+'%');
        $('#barGdpB .fill').css('width', (gdpB/gdpMax*100)+'%');
    }, 200);

    // Inflation
    const inflA = dataA.inflation_rate || 0;
    const inflB = dataB.inflation_rate || 0;
    $('#resInflA').text(inflA + '%');
    $('#resInflB').text(inflB + '%');
    setWinner('inflWinner', inflA, inflB, false); // lower is better
    const inflMax = Math.max(inflA, inflB) || 1;
    setTimeout(() => {
        $('#barInflA .fill').css('width', (inflA/inflMax*100)+'%');
        $('#barInflB .fill').css('width', (inflB/inflMax*100)+'%');
    }, 200);

    // Risk
    const rsA = dataA.risk_score || {};
    const rsB = dataB.risk_score || {};
    const riskA = rsA.total || 0;
    const riskB = rsB.total || 0;
    $('#resRiskA').text(`${riskA}/100`);
    $('#resRiskB').text(`${riskB}/100`);
    setWinner('riskWinner', riskA, riskB, false); // lower risk = better
    setTimeout(() => {
        $('#barRiskA .fill').css('width', riskA+'%');
        $('#barRiskB .fill').css('width', riskB+'%');
    }, 200);

    // Weather
    const wA = dataA.weather || {};
    const wB = dataB.weather || {};
    const tempA = wA.temperature_2m ?? '—';
    const tempB = wB.temperature_2m ?? '—';
    const windA = wA.wind_speed_10m ?? '—';
    const windB = wB.wind_speed_10m ?? '—';
    $('#resTempA').text(tempA !== '—' ? tempA + '°C' : '—');
    $('#resTempB').text(tempB !== '—' ? tempB + '°C' : '—');
    $('#resWindA').text('Wind: ' + (windA !== '—' ? windA + ' km/h' : '—'));
    $('#resWindB').text('Wind: ' + (windB !== '—' ? windB + ' km/h' : '—'));

    // Currency
    const cA = dataA.country || {};
    const cB = dataB.country || {};
    $('#resCurrCodeA').text(cA.currency_code || '—');
    $('#resCurrCodeB').text(cB.currency_code || '—');
    $('#resCurrA').text(cA.currency_code || '—');
    $('#resCurrB').text(cB.currency_code || '—');

    // Radar chart
    compareChart.data.datasets[0].label = nameA;
    compareChart.data.datasets[0].data = [rsA.weather||0, rsA.inflation||0, rsA.currency||0, rsA.news||0];
    compareChart.data.datasets[1].label = nameB;
    compareChart.data.datasets[1].data = [rsB.weather||0, rsB.inflation||0, rsB.currency||0, rsB.news||0];
    compareChart.update();

    // Trade Suitability
    const suitA = Math.max(0, 100 - riskA);
    const suitB = Math.max(0, 100 - riskB);
    const suitLevel = (s) => s >= 70 ? ['Highly Suitable', 'text-success'] : s >= 50 ? ['Suitable', 'text-warning'] : ['Risky', 'text-danger'];
    const [lA, cA2] = suitLevel(suitA);
    const [lB, cB2] = suitLevel(suitB);
    $('#tradeSuitability').html(`
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span class="fw-bold" style="color:#8b5cf6;">${nameA}</span>
                <span class="fw-bold ${cA2}">${lA} (${suitA}%)</span>
            </div>
            <div class="progress" style="height:10px; border-radius:8px; background:#e9d5ff;">
                <div class="progress-bar" style="width:${suitA}%;background:#8b5cf6;border-radius:8px;transition:width 1s;"></div>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-between mb-1">
                <span class="fw-bold" style="color:#0ea5e9;">${nameB}</span>
                <span class="fw-bold ${cB2}">${lB} (${suitB}%)</span>
            </div>
            <div class="progress" style="height:10px; border-radius:8px; background:#bae6fd;">
                <div class="progress-bar" style="width:${suitB}%;background:#0ea5e9;border-radius:8px;transition:width 1s;"></div>
            </div>
        </div>
    `);

    // Verdict
    const better = riskA <= riskB ? nameA : nameB;
    const betterRisk = Math.min(riskA, riskB);
    const betterGDP = gdpA >= gdpB ? nameA : nameB;
    $('#verdictText').html(`🏆 ${better} is the <strong>lower risk</strong> choice`);
    $('#verdictDetail').text(`Risk: ${riskA}/100 vs ${riskB}/100 | ${betterGDP} has higher GDP`);
}

function setWinner(prefix, valA, valB, higherIsBetter) {
    const winnerA = higherIsBetter ? valA >= valB : valA <= valB;
    if (valA && valB) {
        $(`#${prefix}A`).html(`<span class="compare-winner ${winnerA ? '' : 'worse'}">${winnerA ? '✅ Better' : '⚠️ Worse'}</span>`);
        $(`#${prefix}B`).html(`<span class="compare-winner ${!winnerA ? '' : 'worse'}">${!winnerA ? '✅ Better' : '⚠️ Worse'}</span>`);
    }
}

function formatGDP(v) {
    if (v >= 1e12) return (v/1e12).toFixed(2)+'T';
    if (v >= 1e9)  return (v/1e9).toFixed(2)+'B';
    if (v >= 1e6)  return (v/1e6).toFixed(2)+'M';
    return v.toFixed(0);
}
</script>
@endsection
