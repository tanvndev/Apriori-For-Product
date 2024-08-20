<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AprioriRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'antecedents',
        'consequents',
        'support',
        'confidence',
        'lift'
    ];

    protected $casts = [
        'antecedents' => 'array',
        'consequents' => 'array',
        'support' => 'float',
        'confidence' => 'float',
        'lift' => 'float'
    ];
}
