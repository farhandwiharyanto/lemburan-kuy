<!DOCTYPE html>
<html lang="id">
<head>
        <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#0d6efd"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Lemburan-Kuy">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lemburan-Kuy - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .status-pending { color: #ffc107; }
        .status-approved { color: #198754; }
        .status-rejected { color: #dc3545; }
        .user-role-badge {
            font-size: 0.7rem;
            margin-left: 5px;
        }
    </style>
</head>
<script>
// Register Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/sw.js')
      .then(function(registration) {
        console.log('ServiceWorker registration successful');
      })
      .catch(function(err) {
        console.log('ServiceWorker registration failed: ', err);
      });
  });
}

// Prompt for PWA installation
let deferredPrompt;
const installButton = document.getElementById('installButton');

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  
  // Show install button if needed
  if (installButton) {
    installButton.style.display = 'block';
    installButton.addEventListener('click', installApp);
  }
});

function installApp() {
  if (deferredPrompt) {
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then((choiceResult) => {
      if (choiceResult.outcome === 'accepted') {
        console.log('User accepted install');
      }
      deferredPrompt = null;
    });
  }
}
</script>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-clock"></i> Lemburan-Kuy
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <!-- Menu untuk Admin -->
                        @if(Auth::user()->role === 'admin')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cogs me-1"></i> Management
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.index') }}">
                                        <i class="fas fa-users me-2"></i> Management User
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i> Admin Dashboard
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        
                        <!-- Menu untuk Pimpinan -->
                        @if(Auth::user()->role === 'pimpinan')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pimpinan.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard Pimpinan
                            </a>
                        </li>
                        @endif
                        
                        <!-- Menu untuk Semua User -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('overtimes.index') }}">
                                <i class="fas fa-list me-1"></i> Data Lembur
                            </a>
                        </li>
                        
                        <!-- Menu Ajukan Lembur hanya untuk Bawahan -->
                        @if(Auth::user()->role === 'bawahan')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('overtimes.create') }}">
                                <i class="fas fa-plus me-1"></i> Ajukan Lembur
                            </a>
                        </li>
                        @endif
                        
                        <!-- Menu Laporan untuk Semua -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('overtimes.report') }}">
                                <i class="fas fa-chart-bar me-1"></i> Laporan
                            </a>
                        </li>
                    @endauth
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                                <span class="badge bg-light text-dark user-role-badge">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <span class="dropdown-item-text">
                                        <small>
                                            <strong>Departemen:</strong> {{ Auth::user()->department }}<br>
                                            <strong>Role:</strong> {{ ucfirst(Auth::user()->role) }}
                                        </small>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>