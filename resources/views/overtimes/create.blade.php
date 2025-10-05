@extends('layouts.app')

@section('title', 'Ajukan Lembur')

@section('content')
<div class="card mobile-card">
    <div class="card-header">
        <h4 class="mb-0">
            <i class="fas fa-plus"></i> Ajukan Lembur Baru
            <a href="{{ route('overtimes.index') }}" class="btn btn-sm btn-outline-secondary float-end d-block d-md-none">
                <i class="fas fa-arrow-left"></i>
            </a>
        </h4>
    </div>
    <div class="card-body">
        <div class="alert alert-info mobile-friendly">
            <i class="fas fa-info-circle"></i> 
            Anda mengajukan lembur sebagai: <strong>{{ Auth::user()->name }}</strong> 
            dari departemen <strong>{{ Auth::user()->department }}</strong>
        </div>

        <form action="{{ route('overtimes.store') }}" method="POST">
            @csrf
            
            <div class="row g-3">
                <div class="col-12">
                    <label for="date" class="form-label">Tanggal Lembur *</label>
                    <input type="date" class="form-control form-control-lg" id="date" name="date" 
                           value="{{ old('date') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Jam Mulai *</label>
                    <input type="time" class="form-control form-control-lg" id="start_time" name="start_time" 
                           value="{{ old('start_time') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label for="end_time" class="form-label">Jam Selesai *</label>
                    <input type="time" class="form-control form-control-lg" id="end_time" name="end_time" 
                           value="{{ old('end_time') }}" required>
                </div>
            </div>
            
            <div class="mt-3">
                <label for="task_description" class="form-label">Deskripsi Tugas *</label>
                <textarea class="form-control" id="task_description" name="task_description" 
                          rows="4" required placeholder="Jelaskan tugas yang akan dikerjakan selama lembur...">{{ old('task_description') }}</textarea>
                <div class="form-text mobile-friendly">Pastikan deskripsi tugas jelas dan detail.</div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="{{ route('overtimes.index') }}" class="btn btn-secondary me-md-2 d-none d-md-block">Kembali</a>
                <button type="submit" class="btn btn-primary btn-mobile w-100">
                    <i class="fas fa-paper-plane me-2"></i> Ajukan Lembur
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Set default time untuk mobile
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    
    // Set default date to today
    const dateInput = document.getElementById('date');
    if (!dateInput.value) {
        dateInput.value = now.toISOString().split('T')[0];
    }
    
    // Set default start time to current time + 1 hour
    const startTimeInput = document.getElementById('start_time');
    if (!startTimeInput.value) {
        const startTime = new Date(now.getTime() + 60 * 60 * 1000);
        startTimeInput.value = startTime.toTimeString().slice(0, 5);
    }
    
    // Set default end time to start time + 2 hours
    const endTimeInput = document.getElementById('end_time');
    if (!endTimeInput.value) {
        const endTime = new Date(now.getTime() + 3 * 60 * 60 * 1000);
        endTimeInput.value = endTime.toTimeString().slice(0, 5);
    }
});
</script>
@endsection