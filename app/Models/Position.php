<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Position extends Model
{
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
