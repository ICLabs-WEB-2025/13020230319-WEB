<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Sim;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'Admin') {
                return redirect()->route('dashboard');
            }
            return redirect()->route('sim.view', ['sim_id' => Sim::first()->sim_id]);
        }
        return redirect()->back()->withErrors(['email' => 'Kredensial salah']);
    }

    public function logout(Request $request)
    {
        Log::info('Logout attempt', ['user_id' => Auth::id() ?? 'guest']);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out successfully');
        return redirect()->route('admin.login')->with('success', 'Anda telah logout.');
    }
}