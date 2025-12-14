<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_account_id',
        'source_account_id',
        'name',
        'type', // STOCK, MF, GOLD, P2P, FD
        'symbol',
        'isin',
        'sector',
        'units',
        'buy_price', // average price
        'current_price',
        'previous_close_price',
        'invested_amount',
        'current_value',
        'interest_rate',
        'maturity_date',
        'is_sip',
        'sip_status',
        'sip_amount',
        'sip_frequency', // MONTHLY, WEEKLY
        'sip_date', // Day of month
        'unrealized_pnl',
        'unrealized_pnl_pct',
        'quantity_discrepant',
        'quantity_long_term',
        'quantity_pledged_margin',
        'quantity_pledged_loan',
    ];

    protected $casts = [
        'maturity_date' => 'date',
        'is_sip' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(InvestmentAccount::class, 'investment_account_id');
    }

    public function sourceAccount()
    {
        return $this->belongsTo(Account::class, 'source_account_id');
    }

    public function entries()
    {
        return $this->hasMany(InvestmentEntry::class)->orderBy('date', 'desc');
    }
}
