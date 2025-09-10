<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'date',
        'phone',
        'parent_phone',
        'field',
        'level',
        'user_id',
        'school',
    ];

    
    public function user()
    {
        return $this->BelongsTo(User::class);
    }
    
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }
    public function scopeForAY($q, string $ay) {
        [$a,$b] = array_map('intval', explode('/', $ay));
        return $q->where(function($w) use($a,$b){
            $w->whereYear('date', $a)->whereMonth('date', '>=', 9)
              ->orWhere(function($q2) use($b){ $q2->whereYear('date', $b)->whereMonth('date', '<=', 6); });
        });
    }
}
