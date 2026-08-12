<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Parkir UKK</title>
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
            padding: 2.5rem 2rem 2rem;
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

        .demo-account-badge {
            cursor: pointer;
            transition: all 0.2s;
        }

        .demo-account-badge:hover {
            transform: scale(1.03);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="mb-3">
            <i class="fa-solid fa-square-parking fa-4x text-white"></i>
        </div>
        <h4 class="fw-bold mb-1">PARKIR SYSTEM</h4>
        <p class="text-white-50 small m-0">Sistem Manajemen & Transaksi Parkir UKK RPL</p>
    </div>

    <div class="login-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold small text-secondary">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" name="username" id="username" class="form-control border-start-0 @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                </div>
                @error('username')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-key text-muted"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" placeholder="Masukkan password" required>
                    <button class="btn btn-light border border-start-0" type="button" id="togglePassword">
                        <i class="fa-solid fa-eye text-muted" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary-custom text-white w-100 mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Ke Sistem
            </button>
        </form>

        <hr class="my-4 text-muted">

        <div class="text-center">
            <p class="small text-muted mb-2 fw-semibold">Klik untuk Isi Akun Demo (Default):</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <span class="badge bg-danger demo-account-badge p-2" onclick="setDemo('admin')">
                    <i class="fa-solid fa-user-shield me-1"></i> Admin
                </span>
                <span class="badge bg-primary demo-account-badge p-2" onclick="setDemo('petugas')">
                    <i class="fa-solid fa-id-card me-1"></i> Petugas
                </span>
                <span class="badge bg-success demo-account-badge p-2" onclick="setDemo('owner')">
                    <i class="fa-solid fa-user-tie me-1"></i> Owner
                </span>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">Password default: <code>password</code></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });

    function setDemo(role) {
        document.getElementById('username').value = role;
        document.getElementById('password').value = 'password';
    }
</script>
</body>
</html>
