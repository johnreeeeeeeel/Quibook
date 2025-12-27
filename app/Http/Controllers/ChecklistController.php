<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Checklist;
use App\Models\ChecklistItem;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklists = Auth::user()->checklists()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($checklists);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        do {
            $randomDigits = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $checklistId = 'CL' . $randomDigits;
        } while (Checklist::where('checklist_id', $checklistId)->exists());

        $checklist = $user->checklists()->create([
            'checklist_id' => $checklistId,
            'title'        => $request->title,
        ]);

        return response()->json($checklist->load('items'), 201);
    }

    public function update(Request $request, $checklistId)
    {
        $checklist = Auth::user()->checklists()->where('checklist_id', $checklistId)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $checklist->update(['title' => $request->title]);

        return response()->json($checklist->load('items'));
    }

    public function destroy($checklistId)
    {
        $checklist = Auth::user()->checklists()->where('checklist_id', $checklistId)->firstOrFail();
        $checklist->items()->delete(); // delete items first
        $checklist->delete();

        return response()->json(['message' => 'Checklist deleted successfully']);
    }

    // Bonus: Add item
    public function addItem(Request $request, $checklistId)
    {
        $checklist = Auth::user()->checklists()->where('checklist_id', $checklistId)->firstOrFail();

        $request->validate(['text' => 'required|string']);

        do {
            $randomDigits = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $itemId = 'CI' . $randomDigits;
        } while (ChecklistItem::where('item_id', $itemId)->exists());

        $item = $checklist->items()->create([
            'item_id'    => $itemId,
            'text'       => $request->text,
            'is_checked' => false,
        ]);

        return response()->json($item);
    }

    // Bonus: Toggle item
    public function toggleItem(Request $request, $itemId)
    {
        $item = ChecklistItem::whereHas('checklist', function ($q) {
            $q->where('user_id', Auth::user()->user_id);
        })->where('item_id', $itemId)->firstOrFail();

        $item->update(['is_checked' => !$item->is_checked]);

        return response()->json($item);
    }
}