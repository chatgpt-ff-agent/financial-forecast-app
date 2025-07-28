<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }
}
