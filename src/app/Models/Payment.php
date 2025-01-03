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
}
