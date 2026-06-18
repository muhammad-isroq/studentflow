<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\AttendanceRecap;

class PrintAttendanceController extends Controller
{
    public function index($program_id, $semester_name)
    {
        $program = Program::with('guru')->findOrFail($program_id);
        
        $reports = AttendanceRecap::with('siswa')
            ->where('program_id', $program_id)
            ->where('semester_name', $semester_name)
            ->get()
            ->sortBy(fn($report) => $report->siswa->nama ?? '');

        return view('print.arsip-absensi', [
            'program' => $program,
            'reports' => $reports,
            'semester_name' => $semester_name
        ]);
    }
}