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
}
