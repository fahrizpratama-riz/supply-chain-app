<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | Supply Chain Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            display: flex; align-items: center; justify-content: center;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 40px 40px; pointer-events: none;
        }
        .orb-1 { position:fixed; width:350px; height:350px; background:rgba(100,130,255,0.12); border-radius:50%; filter:blur(80px); top:-80px; right:-80px; }
        .orb-2 { position:fixed; width:250px; height:250px; background:rgba(0,181,117,0.1); border-radius:50%; filter:blur(80px); bottom:-60px; left:-60px; }

        .error-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 28px;
            padding: 64px 56px;
            text-align: center;
            max-width: 480px; width: 90%;
            backdrop-filter: blur(20px);
            animation: fadeUp 0.5s ease both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(30px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .error-icon {
            font-size: 72px; color: #6482ff;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }
        .error-code {
            font-size: 80px; font-weight: 900;
            background: linear-gradient(135deg, #6482ff, #8b5cf6);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1; margin-bottom: 12px;
        }
        .error-title { font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 12px; }
        .error-desc  { font-size: 15px; color: rgba(255,255,255,0.5); line-height: 1.7; margin-bottom: 36px; }

        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, #00b575, #009a62);
            color: #fff; text-decoration: none;
            padding: 13px 28px; border-radius: 12px;
            font-size: 15px; font-weight: 700; transition: all 0.25s;
            box-shadow: 0 4px 16px rgba(0,181,117,0.3);
        }
        .btn-back:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,181,117,0.45); color: #fff; }
    </style>
</head>
<body>
    <div class="orb-1"></div>
    <div class="orb-2"></div>
    <div class="error-card">
        <div class="error-icon"><i class="fa-solid fa-map-location-dot"></i></div>
        <div class="error-code">404</div>
        <div class="error-title">Halaman Tidak Ditemukan</div>
        <div class="error-desc">
            Halaman yang Anda cari tidak tersedia atau telah dipindahkan.<br>
            Kembali ke dashboard untuk melanjutkan pemantauan risiko.
        </div>
        <a href="{{ auth()->check() ? '/' : '/login' }}" class="btn-back">
            <i class="fa-solid fa-house"></i> Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
