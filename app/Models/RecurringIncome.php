<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'type',
        'day_of_month',
        'account_id'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
