@extends('layouts.app')

@section('title', 'Edit Lembur')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-edit"></i> Edit Pengajuan Lembur</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('overtimes.update', $overtime) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="date" class="form-label">Tanggal Lembur *</label>
                        <input type="date" class="form-control" id="date" name="date" required
                               value="{{ old('date', $overtime->date->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Jam Mulai *</label>
                        <input type="time" class="form-control" id="start_time" name="start_time" required
                               value="{{ old('start_time', $overtime->start_time) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="end_time" class="form-label">Jam Selesai *</label>
                        <input type="time" class="form-control" id="end_time" name="end_time" required
                               value="{{ old('end_time', $overtime->end_time) }}">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="task_description" class="form-label">Deskripsi Tugas *</label>
                <textarea class="form-control" id="task_description" name="task_description" 
                          rows="4" required>{{ old('task_description', $overtime->task_description) }}</textarea>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('overtimes.index') }}" class="btn btn-secondary me-md-2">Batal</a>
                <button type="submit" class="btn btn-primary">Update Lembur</button>
            </div>
        </form>
    </div>
</div>
@endsection