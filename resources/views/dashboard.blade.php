<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Sentiment AI</title>
    <!-- Memanggil Bootstrap 5 untuk Desain Instan -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
        .sentiment-Neutral { background-color: #6c757d; color: white; }
        .sentiment-Positive { background-color: #198754; color: white; }
        .sentiment-Negative { background-color: #dc3545; color: white; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4 fw-bold text-primary">📊 AI Supply Chain News Analyzer</h2>
    
    <div class="card p-4 mb-4">
        <h5 class="card-title">Live News Sentiment (GNews API)</h5>
        <div class="table-responsive">
            <table class="table table-hover mt-3">
                <thead class="table-light">
                    <tr>
                        <th>Source</th>
                        <th>News Title</th>
                        <th>Sentiment</th>
                        <th>Score (Pos/Neg)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="news-table-body">
                    <tr>
                        <td colspan="5" class="text-center text-muted">Sedang memuat data dari AI... ⏳</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Logika JavaScript untuk mengambil JSON dan memasukkannya ke tabel -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Panggil API buatanmu sendiri
        fetch('/api/news-sentiment')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('news-table-body');
                tbody.innerHTML = ''; // Hapus teks loading

                if(data.status === 'success') {
                    data.data.forEach(news => {
                        // Tentukan warna badge berdasarkan sentimen
                        let sentimentBadge = `<span class="badge sentiment-${news.sentiment_analysis.sentiment}">${news.sentiment_analysis.sentiment}</span>`;
                        
                        // Buat baris tabel baru
                        let row = `
                            <tr>
                                <td class="fw-bold">${news.source}</td>
                                <td>${news.title}</td>
                                <td>${sentimentBadge}</td>
                                <td>
                                    <span class="text-success">+${news.sentiment_analysis.positive_score}</span> / 
                                    <span class="text-danger">-${news.sentiment_analysis.negative_score}</span>
                                </td>
                                <td><a href="${news.url}" target="_blank" class="btn btn-sm btn-outline-primary">Read</a></td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data: ${data.message}</td></tr>`;
                }
            })
            .catch(error => {
                const tbody = document.getElementById('news-table-body');
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Terjadi kesalahan pada server.</td></tr>`;
                console.error("Error fetching data: ", error);
            });
    });
</script>

</body>
</html>