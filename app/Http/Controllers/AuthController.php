<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The email address or password is incorrect.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $data['role'] = 'user'; // public registration is always end-user
        $data['status'] = 'active';

        $user = User::create($data);
        $user->assignRole('user');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Your account is ready. Welcome to ARK Jyotish.');
    }

    public function dashboard(): View
    {
        $user = auth()->user();
        $appointments = $user->role === 'admin'
            ? Appointment::query()
            : $user->appointments();

        $nextAppointment = (clone $appointments)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('appointment_date', '>=', today())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        $appointmentCounts = [
            'total' => (clone $appointments)->count(),
            'pending' => (clone $appointments)->where('status', 'pending')->count(),
            'confirmed' => (clone $appointments)->where('status', 'confirmed')->count(),
        ];

        return view('dashboard', compact('nextAppointment', 'appointmentCounts'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out safely.');
    }
}
