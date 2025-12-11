<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'balance',
        'account_number',
        'credit_limit'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
