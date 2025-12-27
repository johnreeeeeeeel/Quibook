<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reminder;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Auth::user()->reminders()
            ->orderBy('remind_at', 'asc')
            ->get();

        return response()->json($reminders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'description' => 'nullable|string',
            'remind_at' => 'required|date',
        ]);

        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        do {
            $randomDigits = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $reminderId = 'RM' . $randomDigits;
        } while (Reminder::where('reminder_id', $reminderId)->exists());

        $reminder = $user->reminders()->create([
            'reminder_id'  => $reminderId,
            'title'        => $request->title,
            'description'  => $request->description,
            'remind_at'    => $request->remind_at,
            'is_completed' => false,
        ]);

        return response()->json($reminder, 201);
    }

    public function update(Request $request, $reminderId)
    {
        $reminder = Auth::user()->reminders()->where('reminder_id', $reminderId)->firstOrFail();

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'remind_at'   => 'required|date',
            'is_completed' => 'boolean',
        ]);

        $reminder->update($request->only(['title', 'description', 'remind_at', 'is_completed']));

        return response()->json($reminder);
    }

    public function destroy($reminderId)
    {
        $reminder = Auth::user()->reminders()->where('reminder_id', $reminderId)->firstOrFail();
        $reminder->delete();

        return response()->json(['message' => 'Reminder deleted successfully']);
    }
}