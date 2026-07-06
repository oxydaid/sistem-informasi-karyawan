<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Applicant extends Model
{
    protected $casts = [
        'documents' => 'array',
        'metadata' => 'array',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
