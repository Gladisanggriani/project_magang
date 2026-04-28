<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BagStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'bag_type',
        'quantity',
        'unit',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}