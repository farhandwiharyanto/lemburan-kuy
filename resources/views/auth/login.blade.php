<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d6efd"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Login - Lemburan-Kuy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
        }
        .btn-mobile {
            min-height: 50px;
            font-size: 1.1rem;
        }
        @media (max-width: 576px) {
            .login-card {
                padding: 20px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-4">
                <h2 class="text-primary">
                    <i class="fas fa-clock"></i> Lemburan-Kuy
                </h2>
                <p class="text-muted">Silakan login ke akun Anda</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required autofocus placeholder="admin@lemburankuy.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="password">
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 btn-mobile">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>
            </form>

            <hr class="my-4">

            <div class="text-center">
                <h6>Akun Demo:</h6>
                <div class="small text-muted">
                    <div><strong>Admin:</strong> admin@lemburankuy.com</div>
                    <div><strong>Pimpinan:</strong> pimpinan@lemburankuy.com</div>
                    <div><strong>Bawahan:</strong> bawahan@lemburankuy.com</div>
                    <div class="mt-1"><strong>Password:</strong> password</div>
                </div>
            </div>

            <!-- Install PWA Button -->
            <div class="text-center mt-3">
                <button id="installButton" class="btn btn-outline-primary btn-sm" style="display: none;">
                    <i class="fas fa-download me-1"></i> Install App
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // PWA Installation
    let deferredPrompt;
    const installButton = document.getElementById('installButton');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        installButton.style.display = 'block';
    });

    installButton.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                installButton.style.display = 'none';
            }
            deferredPrompt = null;
        }
    });
    </script>
</body>
</html>