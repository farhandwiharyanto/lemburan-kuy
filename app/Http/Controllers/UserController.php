<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Cek manual role admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Cek manual role admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $departments = ['IT', 'HRD', 'Finance', 'Marketing', 'Operations', 'Sales'];
        return view('users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Cek manual role admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,pimpinan,bawahan',
            'department' => 'required|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        // Cek manual role admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Cek manual role admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $departments = ['IT', 'HRD', 'Finance', 'Marketing', 'Operations', 'Sales'];
        return view('users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        // Cek manual role admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,pimpinan,bawahan',
            'department' => 'required|string|max:255',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department' => $request->department,
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Cek manual role admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('overtimes.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Cek jika user mencoba menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}