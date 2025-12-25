<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Models\User;

class AuthController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 422);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login Successful. Redirecting...'
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // SEND OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Check if email already exists
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email already registered'
            ], 422);
        }

        $otp = rand(100000, 999999);

        // Store OTP and signup data in session
        session([
            'otp' => $otp,
            'otp_name' => $request->name,
            'otp_email' => $request->email,
            'otp_password' => bcrypt($request->password), // hash password here
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // Send OTP via email
        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json([
            'message' => 'OTP sent to your email'
        ]);
    }

    // VERIFY OTP AND REGISTER
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        // Check if OTP exists and is not expired
        if (
            !session()->has('otp') ||
            now()->gt(session('otp_expires_at'))
        ) {
            session()->forget([
                'otp',
                'otp_name',
                'otp_email',
                'otp_password',
                'otp_expires_at'
            ]);

            return response()->json([
                'message' => 'OTP expired'
            ], 422);
        }

        // Check OTP match
        if ($request->otp != session('otp')) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 422);
        }

        // Create the user
        User::create([
            'name' => session('otp_name'),
            'email' => session('otp_email'),
            'password' => session('otp_password'),
            'email_verified_at' => now(),
        ]);

        // Clear session
        session()->forget([
            'otp',
            'otp_name',
            'otp_email',
            'otp_password',
            'otp_expires_at'
        ]);

        return response()->json([
            'message' => 'Sign up Successful. Redirecting...'
        ]);
    }
}
