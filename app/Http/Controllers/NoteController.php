<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Auth::user()->notes()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        // Generate random note_id: NT + 10 random digits
        do {
            $randomDigits = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $noteId = 'NT' . $randomDigits;
        } while (Note::where('note_id', $noteId)->exists());

        $note = $user->notes()->create([
            'note_id' => $noteId,
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return response()->json($note, 201);
    }

    public function update(Request $request, $noteId)
    {
        $note = Auth::user()->notes()->where('note_id', $noteId)->firstOrFail();

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $note->update([
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return response()->json($note);
    }

    public function destroy($noteId)
    {
        $note = Auth::user()->notes()->where('note_id', $noteId)->firstOrFail();
        $note->delete();

        return response()->json(['message' => 'Note deleted successfully']);
    }
}