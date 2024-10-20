<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registrant extends Model
{
    use HasFactory;
    
    protected $fillable = [
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
}
