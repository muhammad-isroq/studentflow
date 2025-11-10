<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Siswa;
use App\Models\Program;
use App\Models\Guru; // <-- DIUBAH DARI USER

class AttendanceRecap extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'siswa_id',
        'semester_name',
        'total_hadir',
        'total_sesi',
        'percentage',
        'nama_program',
        'nama_ruangan',
        'jadwal_program',
        'guru_id',
        'jam_pelajaran',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function guru(): BelongsTo
    {
        // guru_id sekarang merujuk ke model Guru
        return $this->belongsTo(Guru::class, 'guru_id'); 
    }
}