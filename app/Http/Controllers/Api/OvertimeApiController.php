<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OvertimeApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
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

        return response()->json([
            'success' => true,
            'data' => $overtimes
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'bawahan') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya karyawan yang dapat mengajukan lembur.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'task_description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $overtime = Overtime::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'department' => $user->department,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'task_description' => $request->task_description,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan lembur berhasil dibuat!',
            'data' => $overtime
        ], 201);
    }

    public function show($id)
    {
        $overtime = Overtime::with('user')->find($id);
        
        if (!$overtime) {
            return response()->json([
                'success' => false,
                'message' => 'Data lembur tidak ditemukan'
            ], 404);
        }

        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'bawahan' && $overtime->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke data ini.'
            ], 403);
        }

        if ($user->role === 'pimpinan' && $overtime->department !== $user->department) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke data ini.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $overtime
        ]);
    }

    public function approve($id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'pimpinan') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pimpinan yang dapat menyetujui lembur.'
            ], 403);
        }

        $overtime = Overtime::find($id);
        
        if (!$overtime) {
            return response()->json([
                'success' => false,
                'message' => 'Data lembur tidak ditemukan'
            ], 404);
        }

        if ($overtime->department !== $user->department) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat menyetujui lembur dari departemen Anda.'
            ], 403);
        }

        $overtime->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Lembur telah disetujui!',
            'data' => $overtime
        ]);
    }
}