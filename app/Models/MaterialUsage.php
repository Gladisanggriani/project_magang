<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialUsage extends Model
{
    use HasFactory;

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