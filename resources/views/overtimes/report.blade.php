@extends('layouts.app')

@section('title', 'Laporan Lembur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-bar"></i> Laporan Lembur</h2>
    
    @if(Auth::user()->role === 'bawahan' && $reports->count() > 0)
    <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-download me-2"></i> Download Laporan
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('overtimes.download.pdf') }}">
                    <i class="fas fa-file-pdf me-2"></i> Download PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('overtimes.download.excel') }}">
                    <i class="fas fa-file-excel me-2"></i> Download Excel
                </a>
            </li>
        </ul>
    </div>
    @endif
</div>

@if(Auth::user()->role === 'bawahan')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-light">
            <div class="card-body">
                <h5><i class="fas fa-info-circle me-2"></i>Informasi Laporan</h5>
                <p class="mb-0">
                    Laporan ini hanya menampilkan data lembur yang telah <strong>disetujui oleh pimpinan</strong>. 
                    Data lembur dengan status pending atau rejected tidak ditampilkan.
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
        @if($reports->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Karyawan</th>
                        <th>Departemen</th>
                        <th>Total Lembur</th>
                        <th>Total Jam</th>
                        @if(Auth::user()->role === 'bawahan')
                        <th>Rata-rata per Lembur</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                    @php
                        $average = $report->total_overtimes > 0 ? round($report->total_hours / $report->total_overtimes, 2) : 0;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $report->employee_name }}</td>
                        <td>{{ $report->department }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $report->total_overtimes }} kali</span>
                        </td>
                        <td>
                            <span class="badge bg-success">{{ $report->total_hours }} jam</span>
                        </td>
                        @if(Auth::user()->role === 'bawahan')
                        <td>
                            <span class="badge bg-info">{{ $average }} jam/lembur</span>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(Auth::user()->role === 'bawahan')
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5>Total Lembur Disetujui</h5>
                        @php
                            $totalOvertimes = $reports->sum('total_overtimes');
                        @endphp
                        <h3>{{ $totalOvertimes }} kali</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5>Total Jam Lembur</h5>
                        @php
                            $totalHours = $reports->sum('total_hours');
                        $totalHours = round($totalHours, 2);
                        $average = $totalOvertimes > 0 ? round($totalHours / $totalOvertimes, 2) : 0;
                        @endphp
                        <h3>{{ $totalHours }} jam</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5>Rata-rata per Lembur</h5>
                        <h3>{{ $average }} jam</h3>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @else
        <div class="text-center py-4">
            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
            <h5>Belum ada data laporan</h5>
            <p class="text-muted">
                @if(Auth::user()->role === 'bawahan')
                Tidak ada data lembur yang disetujui untuk ditampilkan. 
                Setelah pengajuan lembur Anda disetujui pimpinan, data akan muncul di sini.
                @else
                Tidak ada data lembur yang disetujui untuk ditampilkan.
                @endif
            </p>
            @if(Auth::user()->role === 'bawahan')
            <a href="{{ route('overtimes.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-2"></i> Ajukan Lembur
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection