@extends('layouts.dashboard')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #1a1f2e 0%, #2d3561 100%);
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
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
        right: -80px; top: -100px;
    }
    .page-header h2 { font-size: 26px; font-weight: 800; margin: 0 0 6px; }
    .page-header p  { margin: 0; opacity: 0.75; font-size: 14px; }

    /* Filter bar */
    .filter-bar {
        background: #fff;
        border-radius: 16px;
        padding: 14px 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 24px;
    }
    .topic-btn {
        padding: 7px 16px;
        border: 1.5px solid #e8ecef;
        border-radius: 20px;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        color: #888;
        cursor: pointer;
        transition: all 0.2s;
    }
    .topic-btn.active, .topic-btn:hover {
        background: #1a1f2e;
        border-color: #1a1f2e;
        color: #fff;
    }
    .topic-btn.active-logistics  { background:#667eea; border-color:#667eea; color:#fff; }
    .topic-btn.active-trade      { background:#00b575; border-color:#00b575; color:#fff; }
    .topic-btn.active-shipping   { background:#0ea5e9; border-color:#0ea5e9; color:#fff; }
    .topic-btn.active-economy    { background:#f59e0b; border-color:#f59e0b; color:#fff; }
    .topic-btn.active-geopolitics{ background:#ef4444; border-color:#ef4444; color:#fff; }

    /* Sentiment overview */
    .sentiment-overview {
        background: #fff;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.05);
        margin-bottom: 24px;
    }

    /* News card */
    .news-card {
        background: #fff;
        border-radius: 14px;
        border: 1.5px solid #f0f0f0;
        padding: 18px 20px;
        margin-bottom: 16px;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        position: relative;
        overflow: hidden;
    }
    .news-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        border-color: #d0e8ff;
    }
    .news-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        border-radius: 4px 0 0 4px;
    }
    .news-card.positive::before { background: #00b575; }
    .news-card.negative::before { background: #ef4444; }
    .news-card.neutral::before  { background: #f59e0b; }

    .news-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .source-badge {
        font-size: 11px;
        background: #f4f7f6;
        color: #555;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 600;
    }
    .topic-tag {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 700;
    }
    .topic-logistics  { background:#ede9fe; color:#6d28d9; }
    .topic-trade      { background:#d1fae5; color:#065f46; }
    .topic-shipping   { background:#dbeafe; color:#1d4ed8; }
    .topic-economy    { background:#fef3c7; color:#92400e; }
    .topic-geopolitics{ background:#fee2e2; color:#991b1b; }

    .sentiment-tag {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 700;
        margin-left: auto;
    }
    .sent-positive { background:#d1fae5; color:#065f46; }
    .sent-negative { background:#fee2e2; color:#991b1b; }
    .sent-neutral  { background:#fef3c7; color:#92400e; }

    .news-title {
        font-size: 15px;
        font-weight: 700;
        color: #1a1f2e;
        line-height: 1.4;
        margin-bottom: 8px;
    }
    .news-title a { color: inherit; text-decoration: none; }
    .news-title a:hover { color: #00b575; }
    .news-desc {
        font-size: 13px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-time {
        font-size: 11px;
        color: #bbb;
    }

    /* Score breakdown */
    .score-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
        font-size: 12px;
        color: #888;
    }
    .score-chip {
        background: #f4f7f6;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 700;
        color: #555;
        font-size: 11px;
    }

    /* Loading */
    .news-skeleton {
        background: #fff;
        border-radius: 14px;
        border: 1.5px solid #f0f0f0;
        padding: 18px 20px;
        margin-bottom: 16px;
    }
    .skel { background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:6px; }
    @keyframes shimmer { to { background-position:-200% 0; } }

    /* Pie chart */
    #sentimentPie { max-height: 200px; }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fa-solid fa-newspaper" style="font-size:36px; opacity:0.85;"></i>
        <div>
            <h2>News Intelligence</h2>
            <p>Real-time supply chain, trade, and logistics news with AI-powered sentiment analysis</p>
        </div>
    </div>
    <div class="d-flex gap-4 mt-2">
        <div><span class="fw-bold fs-5" id="totalNews">—</span><br><small style="opacity:0.75;">Articles Found</small></div>
        <div><span class="fw-bold fs-5 text-success" id="posCount">—</span><br><small style="opacity:0.75;">Positive</small></div>
        <div><span class="fw-bold fs-5 text-danger" id="negCount">—</span><br><small style="opacity:0.75;">Negative</small></div>
        <div><span class="fw-bold fs-5 text-warning" id="neuCount">—</span><br><small style="opacity:0.75;">Neutral</small></div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="filter-bar">
    <i class="fa-solid fa-filter" style="color:#888;"></i>
    <span class="text-muted fw-semibold" style="font-size:13px;">Topic:</span>
    <button class="topic-btn active active-logistics" onclick="loadNews('supply chain logistics', 'logistics', this)">
        📦 Logistics
    </button>
    <button class="topic-btn" onclick="loadNews('global trade geopolitics', 'trade', this)">
        🤝 Trade
    </button>
    <button class="topic-btn" onclick="loadNews('shipping freight port', 'shipping', this)">
        🚢 Shipping
    </button>
    <button class="topic-btn" onclick="loadNews('economy inflation currency', 'economy', this)">
        💰 Economy
    </button>
    <button class="topic-btn" onclick="loadNews('geopolitics sanctions trade war', 'geopolitics', this)">
        🌐 Geopolitics
    </button>
    <div class="ms-auto">
        <select id="countryFilter" class="form-select form-select-sm" style="min-width:160px; border-radius:10px;">
            <option value="">All Countries</option>
        </select>
    </div>
</div>

<div class="row g-3">
    {{-- NEWS FEED --}}
    <div class="col-md-8">
        <div id="newsContainer">
            {{-- Skeletons --}}
            @for($i=0;$i<6;$i++)
            <div class="news-skeleton">
                <div class="d-flex gap-2 mb-3">
                    <div class="skel" style="height:22px;width:80px;"></div>
                    <div class="skel" style="height:22px;width:60px;"></div>
                </div>
                <div class="skel mb-2" style="height:18px;width:85%;"></div>
                <div class="skel mb-1" style="height:13px;"></div>
                <div class="skel" style="height:13px;width:70%;"></div>
            </div>
            @endfor
        </div>
        <div id="noNews" class="text-center py-5" style="display:none;">
            <i class="fa-solid fa-newspaper" style="font-size:48px;color:#e0e0e0;"></i>
            <p class="text-muted mt-3">No articles found. Try a different topic.</p>
        </div>
    </div>

    {{-- SENTIMENT SIDEBAR --}}
    <div class="col-md-4">
        {{-- Overall Sentiment Chart --}}
        <div class="custom-card mb-3">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-brain me-2 text-primary"></i>Overall Sentiment</h6>
            <canvas id="sentimentPie"></canvas>
            <div class="mt-3" id="sentimentSummary">
                <div class="d-flex justify-content-between mb-1"><small>Positive</small><small class="fw-bold text-success" id="posPct">0%</small></div>
                <div class="progress mb-2" style="height:6px;"><div id="posBar" class="progress-bar bg-success" style="width:0%;transition:width 0.8s;"></div></div>
                <div class="d-flex justify-content-between mb-1"><small>Neutral</small><small class="fw-bold text-warning" id="neuPct">0%</small></div>
                <div class="progress mb-2" style="height:6px;"><div id="neuBar" class="progress-bar bg-warning" style="width:0%;transition:width 0.8s;"></div></div>
                <div class="d-flex justify-content-between mb-1"><small>Negative</small><small class="fw-bold text-danger" id="negPct">0%</small></div>
                <div class="progress" style="height:6px;"><div id="negBar" class="progress-bar bg-danger" style="width:0%;transition:width 0.8s;"></div></div>
            </div>
        </div>

        {{-- How It Works --}}
        <div class="custom-card mb-3">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>How Sentiment Works</h6>
            <p class="text-muted" style="font-size:13px; line-height:1.7;">
                Our system uses <strong>Lexicon-Based Sentiment Analysis</strong> — a dictionary of positive and negative supply-chain keywords matched against each article.
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-success-subtle text-success px-2">📈 growth</span>
                <span class="badge bg-success-subtle text-success px-2">✅ stable</span>
                <span class="badge bg-danger-subtle text-danger px-2">⚠️ war</span>
                <span class="badge bg-danger-subtle text-danger px-2">📉 crisis</span>
                <span class="badge bg-danger-subtle text-danger px-2">🚨 delay</span>
            </div>
        </div>

        {{-- Risk Alert --}}
        <div class="custom-card" id="riskAlert" style="display:none;">
            <h6 class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>Risk Alert</h6>
            <p id="riskAlertText" class="text-muted mb-0" style="font-size:13px;"></p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let pieChart = null;
let currentTopic = 'logistics';

$(document).ready(function() {
    // Populate country filter
    $.get('/api/countries', function(data) {
        data.sort((a,b) => a.name.localeCompare(b.name)).forEach(c => {
            $('#countryFilter').append(`<option value="${c.name}">${c.name}</option>`);
        });
    });
    $('#countryFilter').on('change', function() {
        const country = $(this).val();
        const queries = {
            logistics:   'supply chain logistics',
            trade:       'global trade geopolitics',
            shipping:    'shipping freight port',
            economy:     'economy inflation currency',
            geopolitics: 'geopolitics sanctions trade war'
        };
        let baseQ = queries[currentTopic] || 'supply chain logistics';
        let q = country ? country + ' ' + baseQ : baseQ;
        fetchNews(q, currentTopic);
    });

    // Init pie chart
    initPieChart();

    // Load default news
    loadNews('supply chain logistics', 'logistics', document.querySelector('.topic-btn.active'));

    // Auto-refresh every 5 minutes to stay current
    setInterval(() => {
        const activeBtn = document.querySelector('.topic-btn.active');
        if (activeBtn) activeBtn.click();
    }, 300000);
});

function initPieChart() {
    const ctx = document.getElementById('sentimentPie').getContext('2d');
    pieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Positive', 'Neutral', 'Negative'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: ['#00b575', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed}%`
                    }
                }
            },
            animation: { duration: 800 }
        }
    });
}

function loadNews(query, topic, btnEl) {
    currentTopic = topic;
    // Update active button
    document.querySelectorAll('.topic-btn').forEach(b => {
        b.className = 'topic-btn';
    });
    if (btnEl) {
        btnEl.classList.add('active', `active-${topic}`);
    }

    // Combine with country filter
    const country = $('#countryFilter').val();
    const finalQ = country ? `${country} ${query}` : query;
    fetchNews(finalQ, topic);
}

function fetchNews(query, topic) {
    // Show skeleton
    let skeletons = '';
    for(let i=0;i<6;i++) {
        skeletons += `<div class="news-skeleton">
            <div class="d-flex gap-2 mb-3">
                <div class="skel" style="height:22px;width:80px;border-radius:6px;"></div>
                <div class="skel" style="height:22px;width:60px;border-radius:6px;"></div>
            </div>
            <div class="skel mb-2" style="height:18px;width:85%;border-radius:6px;"></div>
            <div class="skel mb-1" style="height:13px;border-radius:6px;"></div>
            <div class="skel" style="height:13px;width:70%;border-radius:6px;"></div>
        </div>`;
    }
    $('#newsContainer').html(skeletons);
    $('#noNews').hide();

    $.get('/api/news', { q: query }, function(res) {
        renderNews(res, topic || currentTopic);
    }).fail(function() {
        $('#newsContainer').html('<div class="alert alert-warning">Failed to load news. Check API key configuration.</div>');
    });
}

function renderNews(res, topic) {
    const articles = res.articles || [];
    const overall  = res.overall_sentiment || {};
    const sp       = overall.percentages || { positive: 0, negative: 0 };
    const neutral  = Math.max(0, 100 - sp.positive - sp.negative);

    // Update header counts
    const pos = articles.filter(a => a.sentiment?.sentiment === 'Positive').length;
    const neg = articles.filter(a => a.sentiment?.sentiment === 'Negative').length;
    const neu = articles.length - pos - neg;
    $('#totalNews').text(articles.length);
    $('#posCount').text(pos);
    $('#negCount').text(neg);
    $('#neuCount').text(neu);

    // Update sentiment bars
    $('#posPct').text(sp.positive+'%');
    $('#negPct').text(sp.negative+'%');
    $('#neuPct').text(neutral+'%');
    setTimeout(() => {
        $('#posBar').css('width', sp.positive+'%');
        $('#negBar').css('width', sp.negative+'%');
        $('#neuBar').css('width', neutral+'%');
    }, 100);

    // Update pie chart
    if (pieChart) {
        pieChart.data.datasets[0].data = [sp.positive, neutral, sp.negative];
        pieChart.update();
    }

    // Risk alert
    if (sp.negative > 50) {
        $('#riskAlertText').text(`⚠️ ${sp.negative}% of current news sentiment is negative — elevated supply chain disruption risk detected.`);
        $('#riskAlert').show();
    } else {
        $('#riskAlert').hide();
    }

    if (articles.length === 0) {
        $('#newsContainer').empty();
        $('#noNews').show();
        return;
    }

    const topicLabels = {
        logistics:   ['📦 Logistics',   'topic-logistics'],
        trade:       ['🤝 Trade',        'topic-trade'],
        shipping:    ['🚢 Shipping',     'topic-shipping'],
        economy:     ['💰 Economy',      'topic-economy'],
        geopolitics: ['🌐 Geopolitics',  'topic-geopolitics'],
    };
    const [topicLabel, topicClass] = topicLabels[topic] || ['📰 News', 'topic-logistics'];

    // Show data source badge
    const sourceBadgeHtml = `<div class="alert alert-success alert-sm py-2 px-3 mb-3" style="font-size:12px;border-radius:10px;">
          <i class="fa-solid fa-satellite-dish me-1"></i>
          <strong>Google News (Real-time)</strong> — Automatically tracked for supply chain risks
       </div>`;

    let html = sourceBadgeHtml;
    articles.forEach(a => {
        const s = a.sentiment || {};
        const sent = s.sentiment || 'Neutral';
        const sp2  = s.percentages || {};
        const sentClass = sent === 'Positive' ? 'sent-positive' : sent === 'Negative' ? 'sent-negative' : 'sent-neutral';
        const cardClass  = sent === 'Positive' ? 'positive' : sent === 'Negative' ? 'negative' : 'neutral';
        const timeAgo = a.publishedAt ? formatTimeAgo(a.publishedAt) : '';
        const desc = a.description ? `<div class="news-desc">${a.description}</div>` : '';
        const img  = a.image ? `<img src="${a.image}" alt="" style="width:80px;height:55px;object-fit:cover;border-radius:8px;flex-shrink:0;" onerror="this.style.display='none'">` : '';

        html += `
        <div class="news-card ${cardClass}">
            <div class="news-meta">
                <span class="source-badge"><i class="fa-solid fa-rss me-1"></i>${a.source || 'Unknown'}</span>
                <span class="topic-tag ${topicClass}">${topicLabel}</span>
                <span class="sentiment-tag ${sentClass}">${sent === 'Positive' ? '😊' : sent === 'Negative' ? '😟' : '😐'} ${sent}</span>
            </div>
            <div class="d-flex gap-3 align-items-start">
                ${img}
                <div style="flex:1;min-width:0;">
                    <div class="news-title">
                        <a href="${a.url || '#'}" target="_blank" rel="noopener">${a.title || 'No title'}</a>
                    </div>
                    ${desc}
                </div>
            </div>
            <div class="score-row">
                <span class="score-chip">👍 Pos: ${sp2.positive||0}%</span>
                <span class="score-chip">👎 Neg: ${sp2.negative||0}%</span>
                <span class="news-time ms-auto"><i class="fa-regular fa-clock me-1"></i>${timeAgo}</span>
            </div>
        </div>`;
    });

    $('#newsContainer').html(html);
}

function formatTimeAgo(dateStr) {
    const d = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - d) / 60000);
    if (diff < 60)  return diff + 'm ago';
    if (diff < 1440) return Math.floor(diff/60) + 'h ago';
    return Math.floor(diff/1440) + 'd ago';
}
</script>
@endsection