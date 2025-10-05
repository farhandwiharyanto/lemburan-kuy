<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\OvertimeApproval;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OvertimeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $overtimes = Overtime::with('user')->orderBy('created_at', 'desc')->get();
        } elseif ($user->role === 'pimpinan') {
            $overtimes = Overtime::where('department', $user->department)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $overtimes = Overtime::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('overtimes.index', compact('overtimes'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'bawahan') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Hanya karyawan yang dapat mengajukan lembur.');
        }

        return view('overtimes.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'task_description' => 'required|string',
        ]);

        Overtime::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'department' => $user->department,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'task_description' => $request->task_description,
            'status' => 'pending'
        ]);

        return redirect()->route('overtimes.index')
            ->with('success', 'Pengajuan lembur berhasil dibuat! Menunggu persetujuan pimpinan.');
    }

    public function show(Overtime $overtime)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'bawahan' && $overtime->user_id !== $user->id) {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        if ($user->role === 'pimpinan' && $overtime->department !== $user->department) {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        return view('overtimes.show', compact('overtime'));
    }

    public function edit(Overtime $overtime)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'bawahan' && $overtime->user_id !== $user->id) {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        if ($user->role === 'pimpinan' && $overtime->department !== $user->department) {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        if ($overtime->status !== 'pending') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Hanya data lembur dengan status pending yang dapat di edit.');
        }

        return view('overtimes.edit', compact('overtime'));
    }

    public function update(Request $request, Overtime $overtime)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'bawahan' && $overtime->user_id !== $user->id) {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'task_description' => 'required|string',
        ]);

        $overtime->update($request->all());

        return redirect()->route('overtimes.index')
            ->with('success', 'Data lembur berhasil diperbarui!');
    }

    public function destroy(Overtime $overtime)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'bawahan' && $overtime->user_id !== $user->id) {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        if ($overtime->status !== 'pending') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Hanya data lembur dengan status pending yang dapat di hapus.');
        }

        $overtime->delete();

        return redirect()->route('overtimes.index')
            ->with('success', 'Data lembur berhasil dihapus!');
    }

    public function approve(Overtime $overtime)
    {
        $user = Auth::user();
        
        if ($user->role !== 'pimpinan') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Hanya pimpinan yang dapat menyetujui lembur.');
        }

        if ($overtime->department !== $user->department) {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda hanya dapat menyetujui lembur dari departemen Anda.');
        }

        $overtime->update(['status' => 'approved']);

        OvertimeApproval::create([
            'overtime_id' => $overtime->id,
            'approver_id' => $user->id,
            'status' => 'approved',
            'notes' => 'Lembur disetujui oleh ' . $user->name
        ]);

        return redirect()->route('overtimes.index')
            ->with('success', 'Lembur telah disetujui!');
    }

    public function reject(Request $request, Overtime $overtime)
    {
        $user = Auth::user();
        
        if ($user->role !== 'pimpinan') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Hanya pimpinan yang dapat menolak lembur.');
        }

        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $overtime->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        OvertimeApproval::create([
            'overtime_id' => $overtime->id,
            'approver_id' => $user->id,
            'status' => 'rejected',
            'notes' => $request->rejection_reason
        ]);

        return redirect()->route('overtimes.index')
            ->with('success', 'Lembur telah ditolak!');
    }

    public function report()
{
    $user = Auth::user();
    
    if ($user->role === 'admin') {
        $reports = Overtime::where('status', 'approved')
            ->select('employee_name', 'department', 
                DB::raw('COUNT(*) as total_overtimes'),
                DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600) as total_hours'))
            ->groupBy('employee_name', 'department')
            ->get();
    } elseif ($user->role === 'pimpinan') {
        $reports = Overtime::where('status', 'approved')
            ->where('department', $user->department)
            ->select('employee_name', 'department', 
                DB::raw('COUNT(*) as total_overtimes'),
                DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600) as total_hours'))
            ->groupBy('employee_name', 'department')
            ->get();
    } else {
        // Untuk bawahan, tampilkan hanya data mereka sendiri yang approved
        $reports = Overtime::where('status', 'approved')
            ->where('user_id', $user->id)
            ->select('employee_name', 'department', 
                DB::raw('COUNT(*) as total_overtimes'),
                DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)) / 3600) as total_hours'))
            ->groupBy('employee_name', 'department')
            ->get();
    }

    // Format total_hours menjadi 2 decimal
    foreach ($reports as $report) {
        $report->total_hours = round($report->total_hours, 2);
    }

    return view('overtimes.report', compact('reports'));
}

    public function adminDashboard()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman admin.');
        }

        $stats = [
            'total_overtimes' => Overtime::count(),
            'pending_overtimes' => Overtime::where('status', 'pending')->count(),
            'approved_overtimes' => Overtime::where('status', 'approved')->count(),
            'total_users' => User::count(),
        ];

        $recent_overtimes = Overtime::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_overtimes'));
    }

    public function pimpinanDashboard()
    {
        $user = Auth::user();
        
        if ($user->role !== 'pimpinan') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman pimpinan.');
        }

        $stats = [
            'department_overtimes' => Overtime::where('department', $user->department)->count(),
            'pending_overtimes' => Overtime::where('department', $user->department)
                ->where('status', 'pending')->count(),
            'approved_overtimes' => Overtime::where('department', $user->department)
                ->where('status', 'approved')->count(),
        ];

        $pending_submissions = Overtime::where('department', $user->department)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pimpinan.dashboard', compact('stats', 'pending_submissions'));
    }

    // Tambahkan method ini di OvertimeController
public function downloadReport()
{
    $user = Auth::user();
    
    // Hanya bawahan yang bisa download report
    if ($user->role !== 'bawahan') {
        return redirect()->route('overtimes.index')
            ->with('error', 'Hanya karyawan yang dapat mengunduh laporan.');
    }

    $overtimes = Overtime::where('user_id', $user->id)
        ->where('status', 'approved')
        ->orderBy('date', 'desc')
        ->get();

    // Hitung total hours dengan benar
    $totalHours = 0;
    foreach ($overtimes as $overtime) {
        $start = \Carbon\Carbon::parse($overtime->start_time);
        $end = \Carbon\Carbon::parse($overtime->end_time);
        $totalHours += $end->diffInMinutes($start) / 60; // Dalam jam dengan decimal
    }
    $totalHours = round($totalHours, 2);

    // Generate PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('overtimes.report-pdf', compact('overtimes', 'user', 'totalHours'));
    
    $filename = 'laporan-lembur-' . $user->name . '-' . now()->format('Y-m-d') . '.pdf';
    
    return $pdf->download($filename);
}

public function downloadExcel()
{
    $user = Auth::user();
    
    // Hanya bawahan yang bisa download report
    if ($user->role !== 'bawahan') {
        return redirect()->route('overtimes.index')
            ->with('error', 'Hanya karyawan yang dapat mengunduh laporan.');
    }

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\OvertimeExport($user->id),
        'laporan-lembur-' . $user->name . '-' . now()->format('Y-m-d') . '.xlsx'
    );
}
}