<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['item_id', 'checklist_id', 'text', 'is_checked'];

    protected $casts = [
        'is_checked' => 'boolean',
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class, 'checklist_id', 'checklist_id');
    }
}