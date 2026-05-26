<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap RKAP Tahun {{ $year }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000000;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000000;
            padding: 7px 8px;
            vertical-align: middle;
        }

        .title-main {
            background: #c5161d;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        .title-sub {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .title-year {
            font-size: 12px;
            text-align: center;
        }

        .section-title {
            background: #f8d7da;
            color: #9f1239;
            font-weight: bold;
            text-align: left;
        }

        .table-header {
            background: #f8d7da;
            color: #9f1239;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background: #f1f5f9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th colspan="5" class="title-main">REKAP RKAP TAHUNAN</th>
        </tr>
        <tr>
            <th colspan="5" class="title-sub">PT Semen Padang Unit Pabrik Dumai</th>
        </tr>
        <tr>
            <th colspan="5" class="title-year">Tahun {{ $year }}</th>
        </tr>

        <tr>
            <td colspan="5"></td>
        </tr>

        <tr>
            <th colspan="5" class="section-title">DETAIL REKAP RKAP</th>
        </tr>

        <tr>
            <th class="table-header">No</th>
            <th class="table-header">Bulan</th>
            <th class="table-header">RKAP Cement Mill</th>
            <th class="table-header">RKAP Packer</th>
            <th class="table-header">Total Bulanan</th>
        </tr>

        @foreach ($monthNames as $monthNumber => $monthName)
            @php
                $cementMillValue = optional($cementMillRakps->get($monthNumber))->value ?? 0;
                $packerValue = optional($packerRakps->get($monthNumber))->value ?? 0;
                $monthlyTotal = $cementMillValue + $packerValue;
            @endphp

            <tr>
                <td class="text-center">{{ $monthNumber }}</td>
                <td>{{ $monthName }}</td>
                <td class="text-right">{{ number_format($cementMillValue, 2, ',', '.') }} Ton</td>
                <td class="text-right">{{ number_format($packerValue, 2, ',', '.') }} Ton</td>
                <td class="text-right">{{ number_format($monthlyTotal, 2, ',', '.') }} Ton</td>
            </tr>
        @endforeach

        <tr class="total-row">
            <td colspan="2" class="text-center">TOTAL PER TAHUN</td>
            <td class="text-right">{{ number_format($totalCementMill ?? 0, 2, ',', '.') }} Ton</td>
            <td class="text-right">{{ number_format($totalPacker ?? 0, 2, ',', '.') }} Ton</td>
            <td class="text-right">{{ number_format($grandTotal ?? 0, 2, ',', '.') }} Ton</td>
        </tr>
    </table>
</body>
</html>