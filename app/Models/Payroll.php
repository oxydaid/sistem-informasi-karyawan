<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Payroll extends Model
{
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
