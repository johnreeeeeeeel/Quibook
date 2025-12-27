<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Notes
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id', 'user_id');
    }

    // Reminders
    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'user_id', 'user_id');
    }

    // To Do
    public function todos()
    {
        return $this->hasMany(Todo::class, 'user_id', 'user_id');
    }

    // Checklists
    public function checklists()
    {
        return $this->hasMany(Checklist::class, 'user_id', 'user_id');
    }
}