<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'date',
        'fullName',
        'amount',
        'reduction',
        'rest',
        'total',
        'amount_paid',
        'type',
        'bank',
        'bank_receipt',
        'receipt',
        'user_id',
        'student_id',
        'parent_id',
    ];

    public function user()
    {
        return $this->BelongsTo(User::class);
    }

    public function student()
    {
        return $this->BelongsTo(Student::class);
    }

    public function registrant()
    {
        return $this->BelongsTo(Registrant::class);
    }
    
    public function scopeForAY($q, string $ay) {
        [$a,$b] = array_map('intval', explode('/', $ay));
        return $q->where(function($w) use($a,$b){
            $w->whereYear('date', $a)->whereMonth('date', '>=', 8)
              ->orWhere(function($q2) use($b){ $q2->whereYear('date', $b)->whereMonth('date', '<=', 6); });
        });
    }
}
