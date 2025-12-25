<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    // Send reset link
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $token = Str::random(6);

        // Save token in DB
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );

        Mail::to($request->email)->send(new ResetPasswordMail($token));

        return response()->json([
            'message' => 'Reset token sent to your email.'
        ]);
    }

    // Verify token & update password
    public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'token' => 'required',
        'password' => 'required|min:6|confirmed'
    ]);

    $record = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$record) {
        return response()->json(['message' => 'Invalid token or email'], 422);
    }

    if (\Carbon\Carbon::parse($record->created_at)->addMinutes(60)->lt(now())) {
        return response()->json(['message' => 'Token expired'], 422);
    }

    if ($record->token !== $request->token) {
        return response()->json(['message' => 'Invalid token'], 422);
    }

    // ✅ Hash the new password
    User::where('email', $request->email)
        ->update(['password' => Hash::make($request->password)]);

    // Delete token
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return response()->json(['message' => 'Password updated successfully']);
}
}
