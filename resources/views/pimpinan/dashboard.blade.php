@extends('layouts.app')

@section('title', 'Dashboard Pimpinan')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['department_overtimes'] }}</h4>
                        <p>Total Lembur Departemen</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['pending_overtimes'] }}</h4>
                        <p>Menunggu Approval</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-hourglass-half fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['approved_overtimes'] }}</h4>
                        <p>Lembur Disetujui</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Pengajuan Lembur Menunggu Persetujuan</h5>
            </div>
            <div class="card-body">
                @if($pending_submissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pending_submissions as $overtime)
                            <tr>
                                <td>{{ $overtime->employee_name }}</td>
                                <td>{{ $overtime->date->format('d/m/Y') }}</td>
                                <td>{{ $overtime->start_time }} - {{ $overtime->end_time }}</td>
                                <td>{{ Str::limit($overtime->task_description, 50) }}</td>
                                <td>
                                    <form action="{{ route('overtimes.approve', $overtime) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui lemburan?')">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                        data-bs-target="#rejectModal{{ $overtime->id }}">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </td>
                            </tr>

                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal{{ $overtime->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('overtimes.reject', $overtime) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Pengajuan Lembur</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="rejection_reason" class="form-label">Alasan Penolakan</label>
                                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                                        rows="3" required placeholder="Berikan alasan penolakan..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>Tidak ada pengajuan lembur yang menunggu persetujuan</h5>
                    <p class="text-muted">Semua pengajuan lembur dari departemen {{ Auth::user()->department }} sudah diproses.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tachometer-alt"></i> Menu Pimpinan</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('overtimes.index') }}" class="btn btn-outline-primary btn-block text-start">
                        <i class="fas fa-list me-2"></i> Semua Data Lembur
                    </a>
                    <a href="{{ route('overtimes.report') }}" class="btn btn-outline-success btn-block text-start">
                        <i class="fas fa-chart-bar me-2"></i> Laporan Departemen
                    </a>
                    <a href="{{ route('overtimes.create') }}" class="btn btn-outline-info btn-block text-start">
                        <i class="fas fa-plus me-2"></i> Ajukan Lembur
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    <strong>Departemen:</strong> {{ Auth::user()->department }}<br>
                    <strong>Role:</strong> Pimpinan<br>
                    <strong>Fitur:</strong> Dapat menyetujui/menolak lembur dari departemen Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection