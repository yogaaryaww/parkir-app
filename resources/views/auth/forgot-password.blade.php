<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Aplikasi Parkir</title>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
        }

        .login-body {
            padding: 2rem;
        }

        .form-control:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 0.25rem rgba(2, 132, 199, 0.15);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="mb-2">
            <i class="fa-solid fa-key-skeleton fa-3x text-white"></i>
        </div>
        <h5 class="fw-bold mb-1">RESET PASSWORD</h5>
        <p class="text-white-50 small m-0">Masukkan email & buat password baru Anda</p>
    </div>

    <div class="login-body">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('password.reset.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold small text-secondary">Alamat Email Terdaftar</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                    <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: admin@parkir.com" required autofocus>
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold small text-secondary">Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                    <button class="btn btn-light border border-start-0" type="button" id="togglePassword">
                        <i class="fa-solid fa-eye text-muted" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Konfirmasi Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 border-end-0" placeholder="Ketik ulang password baru" required>
                    <button class="btn btn-light border border-start-0" type="button" id="toggleConfirmPassword">
                        <i class="fa-solid fa-eye text-muted" id="eyeConfirmIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-custom text-white w-100 mb-3">
                <i class="fa-solid fa-check-circle me-2"></i> Perbarui Password
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="small text-decoration-none text-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman Login
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function setupToggle(buttonId, inputId, iconId) {
        const btn = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (btn && input && icon) {
            btn.addEventListener('click', function () {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }
    }

    setupToggle('togglePassword', 'password', 'eyeIcon');
    setupToggle('toggleConfirmPassword', 'password_confirmation', 'eyeConfirmIcon');
</script>
</body>
</html>
