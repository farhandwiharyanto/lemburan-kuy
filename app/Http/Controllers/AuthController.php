<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Coba login
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Debug: cek user role
            \Log::info('User logged in:', [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'role' => Auth::user()->role,
                'department' => Auth::user()->department
            ]);
            
            return $this->redirectToDashboard();
        }

        // Jika gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function redirectToDashboard()
    {
        $user = Auth::user();
        
        // Debug redirect
        \Log::info('Redirecting user:', [
            'role' => $user->role,
            'name' => $user->name
        ]);
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'pimpinan') {
            return redirect()->route('pimpinan.dashboard');
        } else {
            return redirect()->route('overtimes.index');
        }
    }
}