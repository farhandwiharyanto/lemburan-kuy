@extends('layouts.app')

@section('title', 'Data Lembur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-list"></i> Data Pengajuan Lembur</h2>
    @if(Auth::user()->role === 'bawahan')
    <a href="{{ route('overtimes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Ajukan Lembur Baru
    </a>
    @endif
</div>

<div class="card">
    <div class="card-body">
        @if($overtimes->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Karyawan</th>
                        <th>Departemen</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overtimes as $overtime)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $overtime->employee_name }}</td>
                        <td>{{ $overtime->department }}</td>
                        <td>{{ $overtime->date->format('d/m/Y') }}</td>
                        <td>{{ $overtime->start_time }} - {{ $overtime->end_time }}</td>
                        <td>
                            @php
                                $start = \Carbon\Carbon::parse($overtime->start_time);
                                $end = \Carbon\Carbon::parse($overtime->end_time);
                                $duration = $start->diffInHours($end);
                            @endphp
                            {{ $duration }} jam
                        </td>
                        <td>
                            <span class="badge bg-{{ $overtime->status == 'approved' ? 'success' : ($overtime->status == 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($overtime->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('overtimes.show', $overtime) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if(Auth::user()->role === 'bawahan' && $overtime->user_id === Auth::id() && $overtime->status === 'pending')
                            <a href="{{ route('overtimes.edit', $overtime) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('overtimes.destroy', $overtime) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data lembur?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif

                            @if(Auth::user()->role === 'pimpinan' && $overtime->department === Auth::user()->department && $overtime->status === 'pending')
                            <form action="{{ route('overtimes.approve', $overtime) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui lemburan?')">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                data-bs-target="#rejectModal{{ $overtime->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </td>
                    </tr>

                    @if(Auth::user()->role === 'pimpinan' && $overtime->department === Auth::user()->department && $overtime->status === 'pending')
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
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5>Belum ada data lembur</h5>
            @if(Auth::user()->role === 'bawahan')
            <a href="{{ route('overtimes.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus"></i> Ajukan Lembur Pertama
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection