<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Global Supply Chain Risk Intelligence Platform</title>
    <meta name="description" content="Create your account to access the Supply Chain Risk Intelligence Platform.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .orb {
            position: fixed; border-radius: 50%;
            filter: blur(80px); pointer-events: none;
        }
        .orb-1 { width:400px; height:400px; background:rgba(0,181,117,0.12); top:-100px; left:-100px; }
        .orb-2 { width:300px; height:300px; background:rgba(139,92,246,0.1); bottom:-50px; right:-50px; }

        /* Card */
        .register-card {
            background: #fff;
            border-radius: 24px;
            width: 100%;
            max-width: 520px;
            padding: 48px 52px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.3);
            position: relative;
        }

        .register-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #00b575, #6482ff, #f59e0b, #8b5cf6);
            border-radius: 24px 24px 0 0;
        }

        /* Brand */
        .brand-top {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 28px; justify-content: center;
        }
        .brand-top .icon-wrap {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #00b575, #009a62);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: #fff;
            box-shadow: 0 6px 16px rgba(0,181,117,0.4);
        }
        .brand-top .brand-text {
            font-size: 15px; font-weight: 800; color: #0f172a; line-height: 1.2;
        }
        .brand-top .brand-sub { font-size: 10px; color: #94a3b8; }

        /* Header */
        .form-header { text-align: center; margin-bottom: 32px; }
        .form-header h1 { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .form-header p  { color: #94a3b8; font-size: 14px; }

        /* Form fields */
        .field-group { margin-bottom: 18px; }
        .form-label  {
            font-size: 13px; font-weight: 700; color: #374151;
            margin-bottom: 7px; display: block;
        }
        .input-wrap { position: relative; }
        .input-wrap .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #cbd5e1; font-size: 14px; z-index: 2;
        }
        .input-wrap input {
            width: 100%;
            border: 2px solid #e2e8f0;
            border-radius: 11px;
            padding: 12px 14px 12px 42px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .input-wrap input:focus {
            border-color: #00b575;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0,181,117,0.1);
        }
        .input-wrap input::placeholder { color: #cbd5e1; }
        .input-wrap .toggle-pass {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            cursor: pointer; color: #94a3b8; font-size: 14px; z-index: 2;
        }
        .input-wrap .toggle-pass:hover { color: #00b575; }

        /* Row inputs */
        .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        /* Strength indicator */
        .strength-bar-wrap { margin-top: 8px; }
        .strength-bars { display: flex; gap: 4px; margin-bottom: 4px; }
        .strength-bar {
            flex: 1; height: 4px; border-radius: 4px;
            background: #e2e8f0; transition: background 0.3s;
        }
        .strength-label { font-size: 11px; color: #94a3b8; }

        /* Terms */
        .terms-row {
            display: flex; align-items: flex-start; gap: 10px;
            margin-bottom: 24px;
        }
        .terms-row input[type="checkbox"] { width: 16px; height: 16px; margin-top: 2px; flex-shrink:0; cursor:pointer; }
        .terms-row label { font-size: 13px; color: #64748b; line-height: 1.5; cursor:pointer; }
        .terms-row a { color: #00b575; font-weight: 600; text-decoration: none; }

        /* Submit btn */
        .btn-register {
            width: 100%;
            background: linear-gradient(135deg, #00b575, #009a62);
            color: #fff; border: none; border-radius: 11px;
            padding: 13px;
            font-size: 15px; font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 16px rgba(0,181,117,0.35);
        }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,181,117,0.45); }
        .btn-register:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        /* Login link */
        .login-link { text-align: center; font-size: 14px; color: #64748b; }
        .login-link a { color: #00b575; font-weight: 700; text-decoration: none; }
        .login-link a:hover { color: #009a62; }

        /* Errors */
        .alert-error {
            background: #fff1f2; border: 1px solid #fecaca;
            border-radius: 10px; padding: 12px 14px;
            margin-bottom: 18px;
        }
        .alert-error ul { margin: 0; padding-left: 16px; }
        .alert-error li { font-size: 13px; color: #991b1b; font-weight: 500; }

        /* Field-level error */
        .field-error { font-size: 12px; color: #ef4444; font-weight: 500; margin-top: 5px; display: flex; align-items: center; gap-4px; }

        /* Input invalid state */
        .input-invalid input { border-color: #fca5a5; background: #fff1f2; }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="register-card">
        {{-- Brand --}}
        <div class="brand-top">
            <div class="icon-wrap"><i class="fa-solid fa-earth-americas"></i></div>
            <div>
                <div class="brand-text">SupplyChain Intelligence</div>
                <div class="brand-sub">GLOBAL RISK MONITORING PLATFORM</div>
            </div>
        </div>

        <div class="form-header">
            <h1>Buat Akun Baru 🚀</h1>
            <p>Daftar untuk mulai memantau risiko rantai pasok global</p>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" id="registerForm">
            @csrf

            {{-- Name --}}
            <div class="field-group {{ $errors->has('name') ? 'input-invalid' : '' }}">
                <label class="form-label" for="name">Nama Lengkap</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user input-icon"></i>
                    <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap Anda"
                        value="{{ old('name') }}" required autocomplete="name">
                </div>
                @error('name')
                    <div class="field-error"><i class="fa-solid fa-circle-exclamation fa-xs me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="field-group {{ $errors->has('email') ? 'input-invalid' : '' }}">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" placeholder="nama@email.com"
                        value="{{ old('email') }}" required autocomplete="email">
                </div>
                @error('email')
                    <div class="field-error"><i class="fa-solid fa-circle-exclamation fa-xs me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="field-group {{ $errors->has('password') ? 'input-invalid' : '' }}">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" id="password" name="password"
                        placeholder="Minimal 8 karakter" required autocomplete="new-password"
                        oninput="checkStrength(this.value)">
                    <i class="fa-regular fa-eye toggle-pass" id="togglePass1" onclick="togglePass('password', 'togglePass1')"></i>
                </div>
                {{-- Strength Bar --}}
                <div class="strength-bar-wrap" id="strengthWrap" style="display:none;">
                    <div class="strength-bars">
                        <div class="strength-bar" id="sb1"></div>
                        <div class="strength-bar" id="sb2"></div>
                        <div class="strength-bar" id="sb3"></div>
                        <div class="strength-bar" id="sb4"></div>
                    </div>
                    <span class="strength-label" id="strengthLabel"></span>
                </div>
                @error('password')
                    <div class="field-error"><i class="fa-solid fa-circle-exclamation fa-xs me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="field-group {{ $errors->has('password_confirmation') ? 'input-invalid' : '' }}">
                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-shield-halved input-icon"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        placeholder="Ulangi password Anda" required autocomplete="new-password"
                        oninput="checkConfirm()">
                    <i class="fa-regular fa-eye toggle-pass" id="togglePass2" onclick="togglePass('password_confirmation', 'togglePass2')"></i>
                </div>
                <div class="field-error" id="confirmError" style="display:none;">
                    <i class="fa-solid fa-circle-exclamation fa-xs me-1"></i>Password tidak cocok
                </div>
            </div>

            {{-- Terms --}}
            <div class="terms-row">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">
                    Saya menyetujui <a href="#">Syarat & Ketentuan</a> dan <a href="#">Kebijakan Privasi</a> Supply Chain Risk Intelligence Platform
                </label>
            </div>

            <button type="submit" class="btn-register" id="regBtn">
                <i class="fa-solid fa-user-plus"></i>
                Buat Akun Sekarang
            </button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>

    <script>
        function togglePass(fieldId, iconId) {
            const input = document.getElementById(fieldId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash toggle-pass';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye toggle-pass';
            }
        }

        function checkStrength(val) {
            const wrap = document.getElementById('strengthWrap');
            const label = document.getElementById('strengthLabel');
            wrap.style.display = val.length ? 'block' : 'none';

            const bars = [1,2,3,4].map(i => document.getElementById('sb'+i));
            bars.forEach(b => b.style.background = '#e2e8f0');

            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#00b575'];
            const labels = ['Sangat Lemah', 'Lemah', 'Cukup Kuat', 'Kuat'];
            for (let i = 0; i < score; i++) {
                bars[i].style.background = colors[score - 1];
            }
            label.textContent = score ? labels[score - 1] : '';
            label.style.color = score ? colors[score - 1] : '#94a3b8';
        }

        function checkConfirm() {
            const pass    = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const err     = document.getElementById('confirmError');
            if (confirm && pass !== confirm) {
                err.style.display = 'flex';
            } else {
                err.style.display = 'none';
            }
        }

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const pass    = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            if (pass !== confirm) {
                e.preventDefault();
                document.getElementById('confirmError').style.display = 'flex';
                return;
            }
            const btn = document.getElementById('regBtn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Membuat akun...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
