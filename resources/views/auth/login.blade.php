<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Global Supply Chain Risk Intelligence Platform</title>
    <meta name="description" content="Login to access the Global Supply Chain Risk Intelligence Platform dashboard.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f172a;
            overflow: hidden;
        }

        /* ===== LEFT PANEL ===== */
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        /* Animated background orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 350px; height: 350px; background: rgba(0,181,117,0.15); top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 250px; height: 250px; background: rgba(99,130,255,0.12); bottom: 100px; right: 50px; animation-delay: 3s; }
        .orb-3 { width: 180px; height: 180px; background: rgba(245,158,11,0.1); top: 50%; left: 40%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        /* Grid pattern overlay */
        .left-panel::before {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .brand-logo {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 60px; z-index: 1;
        }
        .brand-logo .icon-wrap {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #00b575, #009a62);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #fff;
            box-shadow: 0 8px 24px rgba(0,181,117,0.4);
        }
        .brand-logo .brand-name {
            font-size: 18px; font-weight: 800; color: #fff; line-height: 1.2;
        }
        .brand-logo .brand-sub { font-size: 11px; color: rgba(255,255,255,0.5); }

        .hero-title {
            font-size: 44px; font-weight: 900; color: #fff;
            line-height: 1.15; margin-bottom: 20px; z-index: 1;
        }
        .hero-title span { color: #00b575; }

        .hero-desc {
            font-size: 16px; color: rgba(255,255,255,0.55); line-height: 1.8;
            max-width: 420px; z-index: 1; margin-bottom: 48px;
        }

        .feature-pills {
            display: flex; flex-direction: column; gap: 14px; z-index: 1;
        }
        .feature-pill {
            display: flex; align-items: center; gap: 14px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px; padding: 14px 18px;
            backdrop-filter: blur(10px);
            animation: slideIn 0.5s ease both;
        }
        .feature-pill:nth-child(1) { animation-delay: 0.1s; }
        .feature-pill:nth-child(2) { animation-delay: 0.2s; }
        .feature-pill:nth-child(3) { animation-delay: 0.3s; }
        .feature-pill:nth-child(4) { animation-delay: 0.4s; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .fp-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .fp-text strong  { font-size: 13px; font-weight: 700; color: #fff; display: block; }
        .fp-text span    { font-size: 12px; color: rgba(255,255,255,0.45); }

        /* ===== RIGHT PANEL ===== */
        .right-panel {
            width: 480px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 52px;
            position: relative;
        }

        .right-panel::before {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 4px; height: 100%;
            background: linear-gradient(180deg, #00b575, #6482ff, #f59e0b);
        }

        .form-header { margin-bottom: 36px; }
        .form-header h1 {
            font-size: 30px; font-weight: 800; color: #0f172a;
            margin-bottom: 8px; line-height: 1.2;
        }
        .form-header p { color: #94a3b8; font-size: 15px; }

        .form-label {
            font-size: 13px; font-weight: 700; color: #374151;
            margin-bottom: 8px; display: block;
        }

        .form-input-wrap { position: relative; margin-bottom: 20px; }
        .form-input-wrap .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #cbd5e1; font-size: 15px; z-index: 2;
        }
        .form-input-wrap input {
            width: 100%;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 13px 14px 13px 44px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .form-input-wrap input:focus {
            border-color: #00b575;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0,181,117,0.1);
        }
        .form-input-wrap input::placeholder { color: #cbd5e1; }
        .form-input-wrap .toggle-pass {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            cursor: pointer; color: #94a3b8; font-size: 15px; z-index: 2;
            transition: color 0.2s;
        }
        .form-input-wrap .toggle-pass:hover { color: #00b575; }

        /* Options row */
        .form-options {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px;
        }
        .form-check-label { font-size: 13px; color: #64748b; cursor: pointer; }
        .form-check-input:checked { background-color: #00b575; border-color: #00b575; }
        .forgot-link { font-size: 13px; color: #00b575; font-weight: 600; text-decoration: none; }
        .forgot-link:hover { color: #009a62; }

        /* Submit btn */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #00b575, #009a62);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(0,181,117,0.35);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,181,117,0.45);
        }
        .btn-login:active { transform: translateY(0); }

        /* Divider */
        .divider {
            display: flex; align-items: center; gap: 12px;
            color: #cbd5e1; font-size: 13px; margin-bottom: 24px;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #e2e8f0;
        }

        /* Register link */
        .register-link {
            text-align: center; font-size: 14px; color: #64748b;
        }
        .register-link a {
            color: #00b575; font-weight: 700; text-decoration: none;
        }
        .register-link a:hover { color: #009a62; }

        /* Error alerts */
        .alert-error {
            background: #fff1f2; border: 1px solid #fecaca;
            border-radius: 12px; padding: 14px 16px;
            display: flex; align-items: flex-start; gap: 10px;
            margin-bottom: 20px;
            animation: shake 0.4s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60%  { transform: translateX(-6px); }
            40%, 80%  { transform: translateX(6px); }
        }
        .alert-error i    { color: #ef4444; margin-top: 1px; }
        .alert-error span { font-size: 13px; color: #991b1b; font-weight: 500; line-height: 1.5; }

        .alert-success {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 12px; padding: 14px 16px;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px;
        }
        .alert-success i    { color: #00b575; }
        .alert-success span { font-size: 13px; color: #166534; font-weight: 500; }

        /* Demo credentials */
        .demo-card {
            background: #fffbeb; border: 1px solid #fde68a;
            border-radius: 12px; padding: 14px 16px;
            margin-bottom: 24px;
        }
        .demo-card p { font-size: 12px; color: #92400e; margin: 0; line-height: 1.6; }
        .demo-card strong { color: #78350f; }

        /* Responsive */
        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 40px 28px; }
        }
    </style>
</head>
<body>

    {{-- LEFT PANEL --}}
    <div class="left-panel">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="brand-logo">
            <div class="icon-wrap"><i class="fa-solid fa-earth-americas"></i></div>
            <div>
                <div class="brand-name">SupplyChain<br>Intelligence</div>
            </div>
        </div>

        <h1 class="hero-title">
            Monitor Global<br>
            <span>Supply Chain Risk</span><br>
            in Real-Time
        </h1>

        <p class="hero-desc">
            Platform monitoring risiko rantai pasok global berbasis multi-API dan analitik data — cuaca, ekonomi, pelabuhan, dan berita geopolitik dalam satu dashboard.
        </p>

        <div class="feature-pills">
            <div class="feature-pill">
                <div class="fp-icon" style="background:rgba(0,181,117,0.15); color:#00b575;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="fp-text">
                    <strong>Risk Scoring Engine</strong>
                    <span>Weather + Inflation + Currency + News Sentiment</span>
                </div>
            </div>
            <div class="feature-pill">
                <div class="fp-icon" style="background:rgba(100,130,255,0.15); color:#6482ff;"><i class="fa-solid fa-earth-americas"></i></div>
                <div class="fp-text">
                    <strong>Interactive World Map</strong>
                    <span>Leaflet.js dengan data 190+ negara</span>
                </div>
            </div>
            <div class="feature-pill">
                <div class="fp-icon" style="background:rgba(245,158,11,0.15); color:#f59e0b;"><i class="fa-solid fa-chart-line"></i></div>
                <div class="fp-text">
                    <strong>Data Visualization</strong>
                    <span>GDP Trend, Inflation, Currency — Chart.js</span>
                </div>
            </div>
            <div class="feature-pill">
                <div class="fp-icon" style="background:rgba(139,92,246,0.15); color:#8b5cf6;"><i class="fa-solid fa-brain"></i></div>
                <div class="fp-text">
                    <strong>AI Sentiment Analysis</strong>
                    <span>Lexicon-based NLP untuk berita logistik</span>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="right-panel">
        <div class="form-header">
            <h1>Selamat Datang 👋</h1>
            <p>Masuk ke dashboard Supply Chain Risk Intelligence Platform</p>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation fa-sm"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert-success">
                <i class="fa-solid fa-circle-check fa-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Demo Credentials --}}
        <div class="demo-card">
            <p>
                <strong>🎯 Demo Login:</strong><br>
                Email: <strong>admin@supplychain.com</strong> &nbsp;|&nbsp; Password: <strong>password123</strong>
            </p>
        </div>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            {{-- Email --}}
            <label class="form-label" for="email">Email Address</label>
            <div class="form-input-wrap">
                <i class="fa-solid fa-envelope input-icon"></i>
                <input type="email" id="email" name="email" placeholder="admin@supplychain.com"
                    value="{{ old('email') }}" required autocomplete="email">
            </div>

            {{-- Password --}}
            <label class="form-label" for="password">Password</label>
            <div class="form-input-wrap">
                <i class="fa-solid fa-lock input-icon"></i>
                <input type="password" id="password" name="password" placeholder="Masukkan password..." required autocomplete="current-password">
                <i class="fa-regular fa-eye toggle-pass" id="togglePass" onclick="togglePassword()"></i>
            </div>

            {{-- Options --}}
            <div class="form-options">
                <div class="d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" style="width:16px;height:16px;cursor:pointer;">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <a href="#" class="forgot-link">Lupa password?</a>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <i class="fa-solid fa-right-to-bracket"></i>
                Masuk ke Dashboard
            </button>
        </form>

        <div class="divider">atau</div>

        <div class="register-link">
            Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
        </div>

        <div style="text-align:center; margin-top:32px;">
            <small style="color:#cbd5e1; font-size:11px;">
                © 2025 Supply Chain Risk Intelligence Platform
            </small>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('togglePass');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash toggle-pass';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye toggle-pass';
            }
        }

        // Animate btn on submit
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Logging in...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
