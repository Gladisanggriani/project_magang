<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialIntransit extends Model
{
    protected $fillable = [
        'daily_report_id',
        'material_name',
        'quantity',
        'unit',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}