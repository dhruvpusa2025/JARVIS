<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Investment;

class InvestmentEntry extends Model
{
    protected $fillable = [
        'investment_id',
        'type',
        'amount',
        'price',
        'units',
        'date'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
