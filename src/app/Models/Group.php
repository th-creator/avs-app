<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule',
        'n_place',
        'availability',
        'timing',
        'salle',
        'teacher_id',
        'section_id',
        'user_id',
    ];
    
    public function user()
    {
        return $this->BelongsTo(User::class);
    }
    
    public function teacher()
    {
        return $this->BelongsTo(Teacher::class);
    }
    
    public function section()
    {
        return $this->BelongsTo(Section::class);
    }
}
