<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Attendance extends Model
{
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the employee associated with this attendance record.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
