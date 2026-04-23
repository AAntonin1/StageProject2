<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    protected $fillable = [
        'date',
        'from_address',
        'to_address',
        'departure_time',
        'arrival_time',
        'reason',
        'distance_km',
        'time_btw',
        'type_doc',
        'expense_report_id',
        'user_id',

    ];

    protected $table = 'segment';

    public function expense_report()
    {
        return $this->belong(Expense_report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
