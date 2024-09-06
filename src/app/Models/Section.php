<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'level',
        'price',
        'user_id',
    ];
    
    public function user()
    {
        return $this->BelongsTo(User::class);
    }
}
