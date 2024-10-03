<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expanse extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'date',
        'title',
        'amount',
        'type',
        'bank',
        'bank_receipt',
        'file',
        'user_id',
    ];

    public function user()
    {
        return $this->BelongsTo(User::class);
    }
}
