@extends('layouts.app')

@section('title', 'Detail Lembur')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-eye"></i> Detail Pengajuan Lembur</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Nama Karyawan</th>
                        <td>{{ $overtime->employee_name }}</td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td>{{ $overtime->department }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Lembur</th>
                        <td>{{ $overtime->date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Jam Lembur</th>
                        <td>{{ $overtime->start_time }} - {{ $overtime->end_time }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Durasi</th>
                        <td>
                            @php
                                $start = \Carbon\Carbon::parse($overtime->start_time);
                                $end = \Carbon\Carbon::parse($overtime->end_time);
                                $duration = $start->diffInHours($end);
                            @endphp
                            {{ $duration }} jam
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $overtime->status == 'approved' ? 'success' : ($overtime->status == 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($overtime->status) }}
                            </span>
                        </td>
                    </tr>
                    @if($overtime->status == 'rejected' && $overtime->rejection_reason)
                    <tr>
                        <th>Alasan Penolakan</th>
                        <td>{{ $overtime->rejection_reason }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <td>{{ $overtime->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <h5>Deskripsi Tugas</h5>
                <div class="border p-3 rounded bg-light">
                    {{ $overtime->task_description }}
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <a href="{{ route('overtimes.index') }}" class="btn btn-secondary me-md-2">Kembali</a>
            @if(Auth::user()->role === 'bawahan' && $overtime->user_id === Auth::id() && $overtime->status === 'pending')
            <a href="{{ route('overtimes.edit', $overtime) }}" class="btn btn-warning me-md-2">Edit</a>
            @endif
        </div>
    </div>
</div>
@endsection