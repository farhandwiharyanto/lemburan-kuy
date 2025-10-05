<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lemburan-Kuy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .btn-custom {
            background-color: #fff;
            color: #667eea;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">
                <i class="fas fa-clock"></i> Lemburan-Kuy
            </h1>
            <p class="lead mb-4">Aplikasi Management Lembur yang Mudah dan Efisien</p>
            <a href="{{ route('overtimes.index') }}" class="btn btn-custom">
                Mulai Menggunakan Aplikasi
            </a>
        </div>
    </div>

    <div class="container my-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-paper-plane fa-3x text-primary mb-3"></i>
                        <h5>Ajukan Lembur</h5>
                        <p>Ajukan permohonan lembur dengan mudah dan cepat</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>Approval System</h5>
                        <p>Proses persetujuan yang efisien dan transparan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                        <h5>Laporan</h5>
                        <p>Monitoring dan laporan data lembur yang lengkap</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>