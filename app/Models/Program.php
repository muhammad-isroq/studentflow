<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Program extends Model
{
    use HasFactory, LogsActivity; 

    protected $fillable = [
        'nama_program',
        'nama_ruangan',
        'jadwal_program',
        'lesson_time',
        'guru_id',
        'jam_pelajaran',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

     public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('program') 
            ->logOnly(['nama_program', 'nama_ruangan', 'jadwal_program', 'guru_id','lesson_time']) 
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs(); 
    }
}
