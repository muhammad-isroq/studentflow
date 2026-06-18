<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\SemesterReport;

class PrintArsipController extends Controller
{
    public function index($program_id, $semester_name)
    {
        $program = Program::with('guru')->findOrFail($program_id);
        
        $reports = SemesterReport::with('siswa')
            ->where('program_id', $program_id)
            ->where('semester_name', $semester_name)
            ->get();

        return view('print.arsip-nilai', [
            'program' => $program,
            'reports' => $reports,
            'semester_name' => $semester_name
        ]);
    }
}