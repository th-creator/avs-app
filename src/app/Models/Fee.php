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
    ];

    public function user()
    {
        return $this->BelongsTo(User::class);
    }

    public function student()
    {
        return $this->BelongsTo(Student::class);
    }
}
