<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registrant extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ay_no',
        'date',
        'center',
        'status',
        'enter_date',
        'user_id',
        'student_id',
        'group_id',
    ];

    public function user()
    {
        return $this->BelongsTo(User::class);
    }

    public function student()
    {
        return $this->BelongsTo(Student::class);
    }

    public function group()
    {
        return $this->BelongsTo(Group::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeForAY($q, string $ay) {
        [$a,$b] = array_map('intval', explode('/', $ay));
        return $q->where(function($w) use($a,$b){
            $w->whereYear('enter_date', $a)->whereMonth('enter_date', '>=', 9)
              ->orWhere(function($q2) use($b){ $q2->whereYear('enter_date', $b)->whereMonth('enter_date', '<=', 6); });
        });
    }
}
