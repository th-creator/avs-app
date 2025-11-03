<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
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
        'receipt',
        'group',
        'month',
        'year',
        'bank_receipt',
        'paid',
        'user_id',
        'student_id',
        'group_id',
        'registrant_id',
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

    public function group()
    {
        return $this->BelongsTo(Group::class);
    }

    public function registrant()
    {
        return $this->BelongsTo(Registrant::class);
    }

    public function parent()
    {
        return $this->belongsTo(Payment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Payment::class, 'parent_id');
    }

    public function scopeForAY($q, string $ay) {
        [$a,$b] = array_map('intval', explode('/', $ay));
        return $q->where(function($w) use($a,$b){
            $w->where(function($q1) use($a){ $q1->where('year',$a)->whereBetween('month',[9,12]); })
              ->orWhere(function($q2) use($b){ $q2->where('year',$b)->whereBetween('month',[1,6]); });
        });
    }
}
