@extends('layouts.dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <select id="countrySelector" class="form-select border-0 shadow-sm" style="border-radius: 12px; padding: 12px;">
            <option value="DEU">Germany</option>
            <option value="CHN">China</option>
            <option value="IDN">Indonesia</option>
            <option value="AUS">Australia</option>
        </select>
    </div>
    <div class="col-md-9 d-flex align-items-center">
        <h4 id="countryNameDisplay" class="m-0 fw-bold ms-3">Loading...</h4>
        <span id="riskLevelBadge" class="badge bg-secondary ms-3 px-3 py-2" style="border-radius: 20px;">Checking Risk...</span>
    </div>
</div>

<div class="row">
    <!-- GDP Card -->
    <div class="col-md-3">
        <div class="custom-card stat-card">
            <div>
                <h5 class="card-title">GDP (USD)</h5>
                <h3 class="stat-value" id="gdpVal">-</h3>
            </div>
            <div class="icon-box"><i class="fa-solid fa-sack-dollar"></i></div>
        </div>
    </div>
    <!-- Inflation Card -->
    <div class="col-md-3">
        <div class="custom-card stat-card">
            <div>
                <h5 class="card-title">Inflation Rate</h5>
                <h3 class="stat-value" id="inflationVal">- %</h3>
            </div>
            <div class="icon-box"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
    </div>
    <!-- Risk Score Card -->
    <div class="col-md-3">
        <div class="custom-card stat-card">
            <div>
                <h5 class="card-title">Risk Score</h5>
                <h3 class="stat-value" id="riskScoreVal">- / 100</h3>
            </div>
            <div class="icon-box" style="background-color: rgba(255, 99, 132, 0.1); color: #ff6384;"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
    </div>
    <!-- Currency Card -->
    <div class="col-md-3">
        <div class="custom-card stat-card">
            <div>
                <h5 class="card-title">Local Currency</h5>
                <h3 class="stat-value" id="currencyVal">-</h3>
            </div>
            <div class="icon-box"><i class="fa-solid fa-money-bill-transfer"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart -->
    <div class="col-md-8">
        <div class="custom-card">
            <h5 class="fw-bold mb-4">Currency Impact & Risk Trend</h5>
            <canvas id="mainChart"></canvas>
        </div>
    </div>
    <!-- News Sentiment -->
    <div class="col-md-4">
        <div class="custom-card" style="height: 100%;">
            <h5 class="fw-bold mb-4">News Intelligence</h5>
            <div class="text-center mb-4">
                <h2 id="sentimentText" class="fw-bold">-</h2>
                <p class="text-muted">Overall Sentiment</p>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Positive</span>
                <span class="fw-bold text-success" id="posPct">0%</span>
            </div>
            <div class="progress mb-3" style="height: 8px;">
                <div id="posBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
            </div>
            
            <div class="d-flex justify-content-between mb-2">
                <span>Negative</span>
                <span class="fw-bold text-danger" id="negPct">0%</span>
            </div>
            <div class="progress mb-3" style="height: 8px;">
                <div id="negBar" class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="custom-card">
            <h5 class="fw-bold mb-3">Global Weather Monitoring</h5>
            <div id="weatherMap"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="custom-card">
            <h5 class="fw-bold mb-3">Port Location Dashboard</h5>
            <div id="portMap"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let weatherMap, portMap;
    let weatherMarker;
    let portMarkers = [];
    let mainChart;

    $(document).ready(function() {
        initMaps();
        initChart();
        fetchData($('#countrySelector').val());

        $('#countrySelector').change(function() {
            fetchData($(this).val());
        });
    });

    function initMaps() {
        weatherMap = L.map('weatherMap').setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(weatherMap);

        portMap = L.map('portMap').setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(portMap);
    }

    function initChart() {
        const ctx = document.getElementById('mainChart').getContext('2d');
        mainChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Risk Trend',
                        data: [20, 30, 25, 40, 35, 45],
                        backgroundColor: '#00b575',
                        borderRadius: 5
                    },
                    {
                        label: 'Currency Volatility',
                        data: [15, 20, 18, 25, 22, 30],
                        backgroundColor: '#ffb822',
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    function fetchData(isoCode) {
        // Show loading state
        $('#countryNameDisplay').text('Loading...');
        
        // Fetch Risk Data (which aggregates everything)
        $.get(`/api/risk?iso_code=${isoCode}`, function(res) {
            $('#countryNameDisplay').text(res.country.name);
            $('#gdpVal').text(res.gdp ? '$' + (res.gdp / 1e9).toFixed(2) + 'B' : 'N/A');
            $('#inflationVal').text(res.inflation_rate + ' %');
            $('#riskScoreVal').text(res.risk_score.total);
            $('#currencyVal').text(res.country.currency_code);
            
            // Risk Badge
            let badgeClass = 'bg-success';
            if (res.risk_score.level === 'Medium Risk') badgeClass = 'bg-warning text-dark';
            if (res.risk_score.level === 'High Risk') badgeClass = 'bg-danger';
            $('#riskLevelBadge').removeClass().addClass(`badge ${badgeClass} ms-3 px-3 py-2`).text(res.risk_score.level);

            // Sentiment
            let sentiment = res.news_sentiment;
            $('#sentimentText').text(sentiment.sentiment);
            $('#posPct').text(sentiment.percentages.positive + '%');
            $('#posBar').css('width', sentiment.percentages.positive + '%');
            $('#negPct').text(sentiment.percentages.negative + '%');
            $('#negBar').css('width', sentiment.percentages.negative + '%');
            
            if(sentiment.sentiment === 'Negative') {
                $('#sentimentText').removeClass('text-success text-muted').addClass('text-danger');
            } else if(sentiment.sentiment === 'Positive') {
                $('#sentimentText').removeClass('text-danger text-muted').addClass('text-success');
            } else {
                $('#sentimentText').removeClass('text-success text-danger').addClass('text-muted');
            }

            // Weather Map Update
            if (res.weather) {
                let lat = weatherMap.getCenter().lat;
                let lng = weatherMap.getCenter().lng;
                // Since our API currently doesn't return coordinates in current response cleanly, 
                // we'll center on the first port or just fixed coords for the country based on the country selection
                let clat = 0, clng = 0;
                if(isoCode == 'DEU') { clat=51.16; clng=10.45; }
                if(isoCode == 'CHN') { clat=35.86; clng=104.19; }
                if(isoCode == 'IDN') { clat=-0.789; clng=113.92; }
                if(isoCode == 'AUS') { clat=-25.27; clng=133.77; }
                
                weatherMap.setView([clat, clng], 4);
                if (weatherMarker) weatherMap.removeLayer(weatherMarker);
                weatherMarker = L.marker([clat, clng]).addTo(weatherMap)
                    .bindPopup(`<b>${res.country.name} Weather</b><br>Temp: ${res.weather.temperature_2m}°C<br>Wind: ${res.weather.wind_speed_10m} km/h<br>Precip: ${res.weather.precipitation} mm`).openPopup();
            }

            // Update Chart dynamically
            mainChart.data.datasets[0].data.push(res.risk_score.total);
            mainChart.data.datasets[0].data.shift();
            mainChart.update();
            
            // Fetch Ports
            $.get(`/api/ports?country=${res.country.name}`, function(ports) {
                // Clear old markers
                portMarkers.forEach(m => portMap.removeLayer(m));
                portMarkers = [];
                
                let bounds = L.latLngBounds();
                ports.forEach(port => {
                    let m = L.marker([port.lat, port.lng]).addTo(portMap)
                        .bindPopup(`<b>${port.name}</b><br>${port.country}`);
                    portMarkers.push(m);
                    bounds.extend([port.lat, port.lng]);
                });
                
                if(ports.length > 0) {
                    portMap.fitBounds(bounds, {padding: [50, 50]});
                }
            });
        });
    }
</script>
@endsection
