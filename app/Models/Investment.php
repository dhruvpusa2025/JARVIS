<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'units',
        'buy_price',
        'current_price',
        'invested_amount',
        'current_value',
        'interest_rate',
        'maturity_date',
        'is_sip',
        'sip_amount',
        'sip_date'
    ];
}
