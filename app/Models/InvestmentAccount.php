<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'broker',
        'account_number',
    ];

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
}
