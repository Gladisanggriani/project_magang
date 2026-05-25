<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rakp extends Model
{
    protected $fillable = [
        'year',
        'month',
        'material_name',
        'value',
    ];
}