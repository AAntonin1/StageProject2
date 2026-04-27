<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense_report extends Model
{
    protected $fillable = [
        'month_year',
        'number_plate',
        'km_rate',
        'total_km',
        'total_amount',
        'date',
        'status',
        'user_id',
        'address_work',
        'job',
        'vehicle',
    ];

    protected $table = 'expense_report';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function segment()
    {
        return $this->hasMany(Segment::class);
    }

}
