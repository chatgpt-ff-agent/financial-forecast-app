<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function payments()
return $this->hasMany(\App\Models\Payment::class);
