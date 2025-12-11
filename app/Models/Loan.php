<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'lender',
        'type',
        'principal_amount',
        'interest_rate',
        'emi_amount',
        'emi_date',
        'outstanding_amount',
        'start_date'
    ];
}
