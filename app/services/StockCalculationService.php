<?php

namespace App\Services;

class StockCalculationService
{
    public static function closingStock($siloSemen, $productionCm, $productionPacker): float
    {
        return (float) ($siloSemen ?? 0)
            + (float) ($productionCm ?? 0)
            - (float) ($productionPacker ?? 0);
    }

    public static function stockResistance($closingStock, $rakp, $daysInMonth): float
    {
        $closingStock = (float) ($closingStock ?? 0);
        $rakp = (float) ($rakp ?? 0);
        $daysInMonth = (int) ($daysInMonth ?? 0);

        if ($closingStock <= 0 || $rakp <= 0 || $daysInMonth <= 0) {
            return 0;
        }

        //rumus sementara nnati diubah disini
        return $closingStock / $rakp / $daysInMonth;
    }
}
