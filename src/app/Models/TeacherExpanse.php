<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherExpanse extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'date',
        'teacher',
        'group',
        'amount',
        'rest',
        'total',
        'percentage',
        'month',
        'year',
        'user_id',
        'teacher_id',
    ];

    public function user()
    {
        return $this->BelongsTo(User::class);
    }

    public function teacher()
    {
        return $this->BelongsTo(Teacher::class);
    }
}
