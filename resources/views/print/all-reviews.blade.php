<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Reviews Table - {{ $program->nama_program }}</title>
    <style>
        @media print {
            @page { size: landscape; margin: 5mm; }
            .no-print { display: none; }
        }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 0; padding: 10px; 
        -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;}
        .header { text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 20px; }
        th, td { border: 1px solid #333; text-align: center; padding: 2px 1px; }
        
        /* Header Warna sesuai gambar */
        .bg-blue { background-color: #93c5fd; }
        .bg-gray { background-color: #e5e7eb; }
        .bg-yellow { background-color: #fde047; font-weight: bold; }
        
        .col-no { width: 25px; }
        .col-review { width: 100px; text-align: left; padding-left: 5px; }
        .col-skill { width: 25px; font-size: 8px; font-weight: bold; }
        
        .student-header { font-size: 10px; height: 25px; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">STUDENTS REVIEW & SEMESTER TEST SCORE</div>
    <div style="text-align: center; margin-bottom: 10px;">PROGRAM: {{ strtoupper($program->nama_program) }}</div>

    @php
        // Kita bagi siswa menjadi kelompok (misal 7 siswa per tabel agar muat A4 Landscape)
        $studentChunks = $students->chunk(7);
        $skills = ['listening' => 'LS', 'reading' => 'RD', 'writing' => 'WR', 'grammar' => 'GR', 'speaking' => 'SP'];
    @endphp

    @foreach ($studentChunks as $chunk)
        <table>
            <thead>
                <!-- Baris Nama Siswa -->
                <tr>
                    <th rowspan="2" class="col-no bg-gray">NO</th>
                    <th rowspan="2" class="col-review bg-blue">STUDENTS NAME</th>
                    @foreach ($chunk as $siswa)
                        <th colspan="5" class="bg-blue student-header">{{ strtoupper($siswa->nama) }}</th>
                    @endforeach
                </tr>
                <!-- Baris Singkatan Skill -->
                <tr>
                    @foreach ($chunk as $siswa)
                        @foreach ($skills as $s)
                            <th class="col-skill bg-gray">{{ $s }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($assessments as $index => $assessment)
                    <tr>
                        <td class="bg-gray">{{ $index + 1 }}</td>
                        <td class="col-review">{{ strtoupper($assessment->name) }}</td>
                        @foreach ($chunk as $siswa)
                            @php 
                                $grade = $grades->where('assessment_id', $assessment->id)
                                               ->where('student_id', $siswa->id)
                                               ->first();
                            @endphp
                            <td>{{ $grade->listening ?? '' }}</td>
                            <td>{{ $grade->reading ?? '' }}</td>
                            <td>{{ $grade->writing ?? '' }}</td>
                            <td>{{ $grade->grammar ?? '' }}</td>
                            <td>{{ $grade->speaking ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-yellow">
                    <td colspan="2">AVERAGE</td>
                    @foreach ($chunk as $siswa)
                        @php
                            // Hitung rata-rata per skill untuk siswa ini di semua unit yang tampil
                            $siswaGrades = $grades->whereIn('assessment_id', $assessments->pluck('id'))
                                                  ->where('student_id', $siswa->id);
                        @endphp
                        <td>{{ $siswaGrades->avg('listening') ? round($siswaGrades->avg('listening')) : '' }}</td>
                        <td>{{ $siswaGrades->avg('reading') ? round($siswaGrades->avg('reading')) : '' }}</td>
                        <td>{{ $siswaGrades->avg('writing') ? round($siswaGrades->avg('writing')) : '' }}</td>
                        <td>{{ $siswaGrades->avg('grammar') ? round($siswaGrades->avg('grammar')) : '' }}</td>
                        <td>{{ $siswaGrades->avg('speaking') ? round($siswaGrades->avg('speaking')) : '' }}</td>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    @endforeach

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #3b82f6; color: white; border: none; border-radius: 5px;">PRINT SEKARANG</button>
    </div>
</body>
</html>