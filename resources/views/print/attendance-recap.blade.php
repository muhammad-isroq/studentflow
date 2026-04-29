<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report - {{ $program->nama_program }}</title>
    <style>
        @media print {
            @page { size: landscape; margin: 10mm; }
            .no-print { display: none; }
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 11px; 
            color: #334155; 
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }

        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 10px;
        }

        .header h2 { 
            color: #1e40af; 
            margin: 0; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p { margin: 5px 0; color: #64748b; font-weight: 500; }

        .batch-container { margin-bottom: 40px; }

        .batch-info { 
            background: #3b82f6; 
            color: white; 
            padding: 8px 15px; 
            font-weight: bold; 
            border-radius: 8px 8px 0 0;
            display: inline-block;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            border-radius: 0 8px 8px 8px;
            overflow: hidden;
        }

        th { 
            background-color: #f1f5f9; 
            color: #475569; 
            font-weight: 700; 
            text-transform: uppercase;
            font-size: 9px;
            border: 1px solid #e2e8f0;
            padding: 10px 2px;
        }

        td { 
            border: 1px solid #e2e8f0; 
            padding: 8px 4px; 
            text-align: center; 
        }

        tr:nth-child(even) { background-color: #f8fafc; }

        .col-name { width: 180px; text-align: left; padding-left: 12px; font-weight: 600; color: #1e293b; }
        .col-rate { width: 80px; font-weight: 700; color: #0f172a; background-color: #f1f5f9; }
        
        /* Status Colors */
        .status-P { color: #16a34a; font-weight: bold; } /* Present */
        .status-A { color: #dc2626; font-weight: bold; } /* Absent */
        .status-I, .status-S { color: #ca8a04; font-weight: bold; } /* Permit/Sick */
        .status-dot { color: #cbd5e1; }

        .ramadhan-header { background-color: #fff7ed; color: #9a3412; }
        .ramadhan-tag { font-size: 8px; color: #f97316; display: block; margin-top: 2px; }

        .footer-note { margin-top: 15px; font-size: 9px; color: #94a3b8; font-style: italic; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>REKAP ABSENSI SISWA THE MASTER PERIODE JANUARI -  JUNI 2026</h2>
        <p>{{ strtoupper($program->nama_program) }}</p>
        <p style="font-size: 10px;">Periode Rekapitulasi Sesi Aktif</p>
    </div>

    @php
        // Menggunakan 12 sesi per tabel agar tetap lega dengan kolom Rate di akhir
        $chunks = $sessions->chunk(12);
        $batch = 1;
    @endphp

    @foreach ($chunks as $chunk)
        <div class="batch-container">
            <div class="batch-info">Pertemuan {{ ($batch - 1) * 12 + 1 }} - {{ min($batch * 12, $sessions->count()) }}</div>
            <table>
                <thead>
                    <tr>
                        <th class="col-name">Student Name</th>
                        @foreach ($chunk as $session)
                            <th class="{{ $session->is_ramadhan_session ? 'ramadhan-header' : '' }}">
                                {{ $session->session_date->format('d M') }}
                                @if($session->is_ramadhan_session)
                                    <span class="ramadhan-tag">RAMADHAN</span>
                                @endif
                            </th>
                        @endforeach
                        <th class="col-rate">RATE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siswas as $siswa)
                        @php
                            // Ambil skor detail dari array attendanceScores yang sudah dihitung di Page
                            // Note: variabel $attendanceScores harus dikirim dari Route/Controller
                            $scoreDetail = $attendanceScores[$siswa->id] ?? ['score' => 0];
                        @endphp
                        <tr>
                            <td class="col-name">{{ $siswa->nama }}</td>
                            @foreach ($chunk as $session)
                                @php
                                    $status = $attendanceData[$siswa->id][$session->id] ?? '-';
                                    $isSkipped = $session->is_ramadhan_session && !isset($attendanceData[$siswa->id][$session->id]);
                                    
                                    $code = match($status) {
                                        'Hadir' => 'P',
                                        'Absen', 'Alpha' => 'A',
                                        'Izin' => 'I',
                                        'Sakit' => 'S',
                                        default => '•',
                                    };
                                    
                                    $class = $isSkipped ? 'status-dot' : 'status-'.$code;
                                @endphp
                                <td class="{{ $class }}">{{ $isSkipped ? '•' : $code }}</td>
                            @endforeach
                            <td class="col-rate">{{ $scoreDetail['score'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @php $batch++; @endphp
    @endforeach

    <div class="footer-note">
        * Keterangan: P (Present), A (Alpha), I (Izin), S (Sakit), • (Sesi Luar Jadwal/Mutasi)
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 12px 25px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">CETAK LAPORAN</button>
        <button onclick="window.close()" style="padding: 12px 25px; background: #94a3b8; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-left: 10px;">TUTUP</button>
    </div>
</body>
</html>