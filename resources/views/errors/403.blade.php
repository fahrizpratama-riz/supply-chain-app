<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | Supply Chain Platform</title>
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
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 40px 40px; pointer-events: none;
        }
        .orb {
            position: fixed; border-radius: 50%; filter: blur(80px); pointer-events: none;
        }
        .orb-1 { width: 350px; height: 350px; background: rgba(239,68,68,0.12); top: -80px; left: -80px; }
        .orb-2 { width: 250px; height: 250px; background: rgba(139,92,246,0.1); bottom: -60px; right: -60px; }

        .error-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 28px;
            padding: 64px 56px;
            text-align: center;
            max-width: 480px;
            width: 90%;
            backdrop-filter: blur(20px);
            animation: fadeUp 0.5s ease both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(30px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .error-icon {
            width: 100px; height: 100px;
            background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(239,68,68,0.05));
            border: 2px solid rgba(239,68,68,0.3);
            border-radius: 28px;
            display: flex; align-items: center; justify-content: center;
            font-size: 44px; color: #ef4444;
            margin: 0 auto 28px;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.3); }
            50%       { box-shadow: 0 0 0 16px rgba(239,68,68,0); }
        }

        .error-code {
            font-size: 80px; font-weight: 900;
            background: linear-gradient(135deg, #ef4444, #f97316);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1; margin-bottom: 12px;
        }
        .error-title {
            font-size: 24px; font-weight: 800; color: #fff;
            margin-bottom: 12px;
        }
        .error-desc {
            font-size: 15px; color: rgba(255,255,255,0.5);
            line-height: 1.7; margin-bottom: 36px;
        }

        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, #00b575, #009a62);
            color: #fff; text-decoration: none;
            padding: 13px 28px; border-radius: 12px;
            font-size: 15px; font-weight: 700;
            transition: all 0.25s;
            box-shadow: 0 4px 16px rgba(0,181,117,0.3);
            margin-right: 10px;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,181,117,0.45);
            color: #fff;
        }
        .btn-login-link {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.7); text-decoration: none;
            padding: 13px 28px; border-radius: 12px;
            font-size: 15px; font-weight: 600;
            transition: all 0.2s;
        }
        .btn-login-link:hover { background: rgba(255,255,255,0.15); color: #fff; }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="error-card">
        <div class="error-icon">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="error-code">403</div>
        <div class="error-title">Akses Ditolak</div>
        <div class="error-desc">
            {{ $exception->getMessage() ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}<br>
            Halaman ini membutuhkan hak akses <strong style="color:#ef4444;">Admin</strong>.
        </div>
        <div>
            <a href="/" class="btn-back"><i class="fa-solid fa-house"></i> Kembali ke Dashboard</a>
            @guest
            <a href="/login" class="btn-login-link"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
            @endguest
        </div>
    </div>
</body>
</html>
