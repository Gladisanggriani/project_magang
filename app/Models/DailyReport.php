<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'cement_mill_status',
        'cement_mill_note',
        'feed',
        'blaine',
        'sieving',
        'production_cm',
        'production_ship',
        'running_hours',
        'clinker_factor',
        'silo_semen',
        'packer1_status',
        'packer1_note',
        'packer2_status',
        'packer2_note',
        'truck_packer_area',
        'truck_emplacement_area',
        'production_packer',
        'created_by',
    ];

    public function materialStocks()
    {
        return $this->hasMany(MaterialStock::class);
    }

    public function materialReceipts()
    {
        return $this->hasMany(MaterialReceipt::class);
    }

    public function materialUsages()
    {
        return $this->hasMany(MaterialUsage::class);
    }

    public function bagStocks()
    {
        return $this->hasMany(BagStock::class);
    }
    public function materialIntransits()
    {
        return $this->hasMany(MaterialIntransit::class);
    }
}
