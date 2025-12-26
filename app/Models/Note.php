<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $primaryKey = 'note_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['note_id', 'user_id', 'title', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}