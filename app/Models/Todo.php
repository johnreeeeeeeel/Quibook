<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $primaryKey = 'todo_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'todo_id',
        'user_id',
        'task',
        'is_completed',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}