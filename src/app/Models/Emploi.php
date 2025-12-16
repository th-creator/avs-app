<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Emploi extends Model
{
    protected $fillable = [
        'group_id',
        'group',
        'teacher_name',
        'level',
        'subject',
        'salle',
        'timing',
        'type',
        'from',
        'to'
    ];

    protected $casts = [
        'timing' => 'array',
        'from' => 'date',
        'to' => 'date'
    ];

    public function groupRelation()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Check if this emploi entry is currently active.
     */
    public function isActiveToday()
    {
        $today = Carbon::today();

        if ($this->type === 'default') {
            return true;
        }

        if ($this->type === 'period') {
            return $this->from && $this->to &&
                   $today->between($this->from, $this->to);
        }

        return false;
    }
}
