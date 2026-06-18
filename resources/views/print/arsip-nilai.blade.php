<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archive Scoring Sheet - {{ $program->nama_program }}</title>
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
        <h2>ARCHIVED REPORT SCORING SHEET</h2>
        <p style="font-weight: bold; margin: 4px 0; font-size: 14px;">{{ strtoupper($program->nama_program) }}</p>
        <p style="font-size: 11px; color: #64748b; margin: 0;">Academic Period: {{ strtoupper($semester_name) }}</p>
    </div>

    <div class="section-title">TABLE 1: ORIGINAL STUDENT SCORES (Average of Review & Semester Test)</div>
    <table>
        <thead>
            <tr>
                <th>RANK</th>
                <th class="name-col">STUDENT NAME</th>
                <th>LS</th>
                <th>RD</th>
                <th>WR</th>
                <th>SP</th>
                <th>GR</th>
                <th class="total-col">TOTAL</th>
                <th class="final-col">FINAL AV</th>
            </tr>
        </thead>
        <tbody>
            @php $sortedRaw = $reports->sortByDesc('final_score')->values(); @endphp
            @foreach ($sortedRaw as $index => $report)
                <tr>
                    <td class="rank-col">{{ $index + 1 }}</td>
                    <td class="name-col">{{ $report->siswa->nama ?? '-' }}</td>
                    <td>{{ number_format($report->avg_listening, 1) }}</td>
                    <td>{{ number_format($report->avg_reading, 1) }}</td>
                    <td>{{ number_format($report->avg_writing, 1) }}</td>
                    <td>{{ number_format($report->avg_speaking, 1) }}</td>
                    <td>{{ number_format($report->avg_grammar, 1) }}</td>
                    <td class="total-col">{{ number_format($report->total_score, 1) }}</td>
                    <td class="final-col">
                        @php $color = $report->final_score >= 80 ? 'badge-success' : ($report->final_score >= 60 ? 'badge-warning' : 'badge-danger'); @endphp
                        <span class="{{ $color }}">{{ number_format($report->final_score, 1) }}</span>
                    </td>
                </tr>
            @endforeach
            <tr class="class-avg-row">
                <td colspan="2" class="class-avg-title">CLASS AVG (ORIGINAL SCORES)</td>
                <td>{{ number_format($reports->avg('avg_listening'), 1) }}</td>
                <td>{{ number_format($reports->avg('avg_reading'), 1) }}</td>
                <td>{{ number_format($reports->avg('avg_writing'), 1) }}</td>
                <td>{{ number_format($reports->avg('avg_speaking'), 1) }}</td>
                <td>{{ number_format($reports->avg('avg_grammar'), 1) }}</td>
                <td class="total-col" style="color: #2563eb;">{{ number_format($reports->avg('total_score'), 1) }}</td>
                <td class="final-col">{{ number_format($reports->avg('final_score'), 1) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title rapor">TABLE 2: REPORT CARD SCORES (Manual Teacher Input)</div>
    <table class="rapor-table">
        <thead>
            <tr>
                <th>RANK</th>
                <th class="name-col">STUDENT NAME</th>
                <th>LS</th>
                <th>RD</th>
                <th>WR</th>
                <th>SP</th>
                <th>GR</th>
                <th class="total-col">TOTAL</th>
                <th class="final-col">FINAL AV</th>
            </tr>
        </thead>
        <tbody>
            @php $sortedRapor = $reports->sortBy('rank')->values(); @endphp
            @foreach ($sortedRapor as $report)
                @php
                    $rapor_total = $report->rapor_listening + $report->rapor_reading + $report->rapor_writing + $report->rapor_speaking + $report->rapor_grammar;
                    $rapor_final = $rapor_total / 5;
                @endphp
                <tr>
                    <td class="rank-col">{{ $report->rank }}</td>
                    <td class="name-col">{{ $report->siswa->nama ?? '-' }}</td>
                    <td>{{ number_format($report->rapor_listening, 1) }}</td>
                    <td>{{ number_format($report->rapor_reading, 1) }}</td>
                    <td>{{ number_format($report->rapor_writing, 1) }}</td>
                    <td>{{ number_format($report->rapor_speaking, 1) }}</td>
                    <td>{{ number_format($report->rapor_grammar, 1) }}</td>
                    <td class="total-col">{{ number_format($rapor_total, 1) }}</td>
                    <td class="final-col">
                        @php $color = $rapor_final >= 80 ? 'badge-success' : ($rapor_final >= 60 ? 'badge-warning' : 'badge-danger'); @endphp
                        <span class="{{ $color }}">{{ number_format($rapor_final, 1) }}</span>
                    </td>
                </tr>
            @endforeach
            @php
                $avgL = $reports->avg('rapor_listening'); $avgR = $reports->avg('rapor_reading');
                $avgW = $reports->avg('rapor_writing'); $avgS = $reports->avg('rapor_speaking'); $avgG = $reports->avg('rapor_grammar');
                $avgTotal = $avgL + $avgR + $avgW + $avgS + $avgG;
            @endphp
            <tr class="class-avg-row">
                <td colspan="2" class="class-avg-title">CLASS AVG (REPORT CARD SCORES)</td>
                <td>{{ number_format($avgL, 1) }}</td>
                <td>{{ number_format($avgR, 1) }}</td>
                <td>{{ number_format($avgW, 1) }}</td>
                <td>{{ number_format($avgS, 1) }}</td>
                <td>{{ number_format($avgG, 1) }}</td>
                <td class="total-col" style="color: #b45309;">{{ number_format($avgTotal, 1) }}</td>
                <td class="final-col" style="color: #047857;">{{ number_format($avgTotal / 5, 1) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Printed on: {{ now()->format('d M Y H:i') }} | LS: Listening, RD: Reading, WR: Writing, SP: Speaking, GR: Grammar
    </div>

    <div class="no-print" style="margin-top: 25px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Print Report</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

</body>
</html>