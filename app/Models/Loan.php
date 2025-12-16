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
        'loan_type', // BANK, PERSONAL
        'principal_amount',
        'interest_rate',
        'interest_payment_frequency',
        'interest_payment_date',
        'emi_amount',
        'emi_date',
        'outstanding_amount',
        'start_date'
    ];
}
