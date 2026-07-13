<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Employee extends Model
{
    protected $casts = [
        'join_date' => 'date',
        'documents' => 'array',
        'metadata' => 'array',
        'base_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function kpiEvaluations()
    {
        return $this->hasMany(KpiEvaluation::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function cashAdvances()
    {
        return $this->hasMany(CashAdvance::class);
    }
}
