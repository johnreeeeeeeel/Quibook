<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $primaryKey = 'reminder_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'reminder_id',
        'user_id',
        'title',
        'description',
        'remind_at',
        'is_completed',
    ];

    protected $casts = [
        'remind_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}