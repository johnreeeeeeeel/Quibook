<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\ChecklistController;

Route::get('/', function () {
    return view('pages.index');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/signup', function () {
    return view('auth.signup');
});

Route::get('/reset-password', function () {
    return view('auth.reset-password');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return view('pages.home');
    });

    // Notes
    Route::get('/api/notes', [NoteController::class, 'index']);
    Route::post('/api/notes', [NoteController::class, 'store']);
    Route::put('/api/notes/{noteId}', [NoteController::class, 'update']);
    Route::delete('/api/notes/{noteId}', [NoteController::class, 'destroy']);

    // Reminders
    Route::get('/api/reminders', [ReminderController::class, 'index']);
    Route::post('/api/reminders', [ReminderController::class, 'store']);
    Route::put('/api/reminders/{reminderId}', [ReminderController::class, 'update']);
    Route::delete('/api/reminders/{reminderId}', [ReminderController::class, 'destroy']);

    // To Do
    Route::get('/api/todos', [TodoController::class, 'index']);
    Route::post('/api/todos', [TodoController::class, 'store']);
    Route::put('/api/todos/{todoId}', [TodoController::class, 'update']);
    Route::delete('/api/todos/{todoId}', [TodoController::class, 'destroy']);

    // Checklists
    Route::get('/api/checklists', [ChecklistController::class, 'index']);
    Route::post('/api/checklists', [ChecklistController::class, 'store']);
    Route::put('/api/checklists/{checklistId}', [ChecklistController::class, 'update']);
    Route::delete('/api/checklists/{checklistId}', [ChecklistController::class, 'destroy']);
    Route::post('/api/checklists/{checklistId}/items', [ChecklistController::class, 'addItem']);
    Route::patch('/api/checklist-items/{itemId}', [ChecklistController::class, 'toggleItem']);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLink']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);