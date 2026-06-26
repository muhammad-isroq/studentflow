<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scoring Sheet - {{ $program->nama_program }}</title>
    <style>
        @media print {
            @page { size: portrait; margin: 10mm; }
            .no-print { display: none; }
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; padding: 10px; }
        .header { text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 8px; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #1e40af; text-transform: uppercase; font-size: 18px; }
        
        .section-title { font-size: 12px; font-weight: bold; color: #1e3a8a; margin-top: 20px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; border-left: 4px solid #3b82f6; padding-left: 8px; }
        .section-title.rapor { color: #b45309; border-left-color: #f59e0b; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #f1f5f9; padding: 10px; border: 1px solid #e2e8f0; font-size: 10px; }
        td { padding: 8px; border: 1px solid #e2e8f0; text-align: center; font-size: 11px; }
        
        .name-col { text-align: left; font-weight: 600; width: 200px; }
        .total-col { background-color: #eff6ff; font-weight: bold; color: #2563eb; }
        .final-col { background-color: #f0fdf4; font-weight: 800; font-size: 12px; color: #16a34a; }
        .rank-col { font-weight: bold; background-color: #f8fafc; font-size: 12px; }
        
        .class-avg-row td { background-color: #f8fafc; font-weight: 900; font-size: 11px; border-top: 2px solid #cbd5e1; }
        .class-avg-title { color: #2563eb; text-align: left !important; }

        /* Rapor Table Specifics */
        .rapor-table th { background-color: #fef3c7; border: 1px solid #fde68a; }
        .rapor-table .total-col { background-color: #fffbef; color: #b45309; }
        .rapor-table .final-col { background-color: #ecfdf5; color: #047857; }
        .rapor-table .class-avg-title { color: #b45309; }

        .badge-success { color: #16a34a; }
        .badge-warning { color: #ca8a04; }
        .badge-danger { color: #dc2626; }
        .footer { margin-top: 15px; font-size: 10px; color: #64748b; font-style: italic; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>REPORT SCORING SHEET THE MASTER</h2>
        <p style="font-weight: bold; margin: 4px 0; font-size: 14px;">{{ strtoupper($program->nama_program) }}</p>
        <p style="font-size: 11px; color: #64748b; margin: 0;">Academic Period: January - June 2026</p>
    </div>

    <div class="section-title">TABLE 1: ORIGINAL STUDENT SCORES (Average of Review & Semester Test)</div>
    <table>
        <thead>
            <tr>
                <th>RANK</th>
                <th class="name-col">STUDENT NAME</th>
                <th>LI</th>
                <th>RE</th>
                <th>WR</th>
                <th>SP</th>
                <th>GR</th>
                <th class="total-col">TOTAL</th>
                <th class="final-col">FINAL AV</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rawData as $index => $row)
                <tr>
                    <td class="rank-col">{{ $index + 1 }}</td>
                    <td class="name-col">{{ $row['nama'] }}</td>
                    <td>{{ number_format($row['raw_l'], 1) }}</td>
                    <td>{{ number_format($row['raw_r'], 1) }}</td>
                    <td>{{ number_format($row['raw_w'], 1) }}</td>
                    <td>{{ number_format($row['raw_s'], 1) }}</td>
                    <td>{{ number_format($row['raw_g'], 1) }}</td>
                    <td class="total-col">{{ number_format($row['raw_total'], 1) }}</td>
                    <td class="final-col">
                        @php
                            $color = $row['raw_final'] >= 80 ? 'badge-success' : ($row['raw_final'] >= 60 ? 'badge-warning' : 'badge-danger');
                        @endphp
                        <span class="{{ $color }}">{{ number_format($row['raw_final'], 1) }}</span>
                    </td>
                </tr>
            @endforeach

            @php
                $countRaw = $rawData->count();
                $avgRawL = $countRaw > 0 ? $rawData->avg('raw_l') : 0;
                $avgRawR = $countRaw > 0 ? $rawData->avg('raw_r') : 0;
                $avgRawW = $countRaw > 0 ? $rawData->avg('raw_w') : 0;
                $avgRawS = $countRaw > 0 ? $rawData->avg('raw_s') : 0;
                $avgRawG = $countRaw > 0 ? $rawData->avg('raw_g') : 0;
                $avgRawTotal = $countRaw > 0 ? $rawData->avg('raw_total') : 0;
                $avgRawFinal = $countRaw > 0 ? $rawData->avg('raw_final') : 0;
            @endphp
            <tr class="class-avg-row">
                <td colspan="2" class="class-avg-title">CLASS AVG (ORIGINAL SCORES)</td>
                <td>{{ number_format($avgRawL, 1) }}</td>
                <td>{{ number_format($avgRawR, 1) }}</td>
                <td>{{ number_format($avgRawW, 1) }}</td>
                <td>{{ number_format($avgRawS, 1) }}</td>
                <td>{{ number_format($avgRawG, 1) }}</td>
                <td class="total-col" style="color: #2563eb;">{{ number_format($avgRawTotal, 1) }}</td>
                <td class="final-col">{{ number_format($avgRawFinal, 1) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title rapor">TABLE 2: REPORT CARD SCORES (Manual Teacher Input)</div>
    <table class="rapor-table">
        <thead>
            <tr>
                <th>RANK</th>
                <th class="name-col">STUDENT NAME</th>
                <th>LI</th>
                <th>RE</th>
                <th>WR</th>
                <th>SP</th>
                <th>GR</th>
                <th class="total-col">TOTAL</th>
                <th class="final-col">FINAL AV</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($raporData as $index => $row)
                <tr>
                    <td class="rank-col">{{ $index + 1 }}</td>
                    <td class="name-col">{{ $row['nama'] }}</td>
                    <td>{{ number_format($row['rapor_l'], 1) }}</td>
                    <td>{{ number_format($row['rapor_r'], 1) }}</td>
                    <td>{{ number_format($row['rapor_w'], 1) }}</td>
                    <td>{{ number_format($row['rapor_s'], 1) }}</td>
                    <td>{{ number_format($row['rapor_g'], 1) }}</td>
                    <td class="total-col">{{ number_format($row['rapor_total'], 1) }}</td>
                    <td class="final-col">
                        @php
                            $color = $row['rapor_final'] >= 80 ? 'badge-success' : ($row['rapor_final'] >= 60 ? 'badge-warning' : 'badge-danger');
                        @endphp
                        <span class="{{ $color }}">{{ number_format($row['rapor_final'], 1) }}</span>
                    </td>
                </tr>
            @endforeach

            @php
                $countRapor = $raporData->count();
                $avgRaporL = $countRapor > 0 ? $raporData->avg('rapor_l') : 0;
                $avgRaporR = $countRapor > 0 ? $raporData->avg('rapor_r') : 0;
                $avgRaporW = $countRapor > 0 ? $raporData->avg('rapor_w') : 0;
                $avgRaporS = $countRapor > 0 ? $raporData->avg('rapor_s') : 0;
                $avgRaporG = $countRapor > 0 ? $raporData->avg('rapor_g') : 0;
                $avgRaporTotal = $countRapor > 0 ? $raporData->avg('rapor_total') : 0;
                $avgRaporFinal = $countRapor > 0 ? $raporData->avg('rapor_final') : 0;
            @endphp
            <tr class="class-avg-row">
                <td colspan="2" class="class-avg-title">CLASS AVG (REPORT CARD SCORES)</td>
                <td>{{ number_format($avgRaporL, 1) }}</td>
                <td>{{ number_format($avgRaporR, 1) }}</td>
                <td>{{ number_format($avgRaporW, 1) }}</td>
                <td>{{ number_format($avgRaporS, 1) }}</td>
                <td>{{ number_format($avgRaporG, 1) }}</td>
                <td class="total-col" style="color: #b45309;">{{ number_format($avgRaporTotal, 1) }}</td>
                <td class="final-col" style="color: #047857;">{{ number_format($avgRaporFinal, 1) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Printed on: {{ now()->format('d M Y H:i') }} | LI: Listening, RE: Reading, WR: Writing, SP: Speaking, GR: Grammar
    </div>

    <div class="no-print" style="margin-top: 25px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Print Report</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

</body>
</html>