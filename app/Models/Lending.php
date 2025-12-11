<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lending extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower',
        'amount',
        'interest_rate',
        'frequency',
        'outstanding_amount',
        'start_date',
        'return_date'
    ];
}
