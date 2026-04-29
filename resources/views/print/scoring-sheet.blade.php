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
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #1e40af; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f5f9; padding: 12px; border: 1px solid #e2e8f0; font-size: 10px; }
        td { padding: 10px; border: 1px solid #e2e8f0; text-align: center; font-size: 11px; }
        
        .name-col { text-align: left; font-weight: 600; width: 200px; }
        .total-col { background-color: #eff6ff; font-weight: bold; color: #2563eb; }
        .final-col { background-color: #f0fdf4; font-weight: 800; font-size: 13px; color: #16a34a; }
        .rank-col { font-weight: bold; background-color: #f8fafc; }

        .badge-success { color: #16a34a; }
        .badge-warning { color: #ca8a04; }
        .badge-danger { color: #dc2626; }
        
        .footer { margin-top: 20px; font-size: 10px; color: #64748b; font-style: italic; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>REPORT SCORING SHEET THE MASTER PERIODE JANUARI -  JUNI 2026</h2>
        <p>{{ strtoupper($program->nama_program) }}</p>
        <p style="font-size: 11px; color: #64748b;">Rumus: ([Avg. Review + Semester] / 2)</p>
    </div>

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
            @foreach ($data as $index => $row)
                <tr>
                    <td class="rank-col">{{ $index + 1 }}</td>
                    <td class="name-col">{{ $row['nama'] }}</td>
                    <td>{{ number_format($row['l'], 1) }}</td>
                    <td>{{ number_format($row['r'], 1) }}</td>
                    <td>{{ number_format($row['w'], 1) }}</td>
                    <td>{{ number_format($row['s'], 1) }}</td>
                    <td>{{ number_format($row['g'], 1) }}</td>
                    <td class="total-col">{{ number_format($row['total'], 1) }}</td>
                    <td class="final-col">
                        @php
                            $color = $row['final'] >= 80 ? 'badge-success' : ($row['final'] >= 60 ? 'badge-warning' : 'badge-danger');
                        @endphp
                        <span class="{{ $color }}">{{ number_format($row['final'], 1) }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Printed on: {{ now()->format('d M Y H:i') }} | LS: Listening, RD: Reading, WR: Writing, SP: Speaking, GR: Grammar
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Report</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

</body>
</html>