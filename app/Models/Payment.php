<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'category_id',
        'name',
        'amount',
        'type', // income or expense
        'recurring', // boolean
        'frequency', // e.g., monthly, weekly, yearly
        'start_date',
        'end_date',
        'active'
    ];

    protected $casts = [
        'recurring' => 'boolean',
        'active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'float',
    ];

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }
}
