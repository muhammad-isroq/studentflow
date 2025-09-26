<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    // Izinkan semua field diisi secara massal
    protected $guarded = [];

    protected $casts = [
           'session_date' => 'date:Y-m-d',
       ];

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    // public function siswa(): BelongsTo
    // {
    //     return $this->belongsTo(Siswa::class);
    // }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function getSessionDateFormattedAttribute(): string
       {
           return $this->session_date ? $this->session_date->format('l, d M Y') : 'Tanggal tidak tersedia';
       }
}