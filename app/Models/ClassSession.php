<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    protected $guarded = [];

    protected $casts = [
    'session_date' => 'date',
        ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function replacementGuru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'replacement_guru_id');
    }

    public function isAccessExpired(): bool
    {
        // Cek status global dari Cache. Default: true (deadline aktif)
        $isGlobalDeadlineEnabled = cache()->get('global_deadline_status', true);

        if (!$isGlobalDeadlineEnabled) {
            return false;
        }

        if ($this->is_forced_enabled) {
            return false;
        }

        return now()->startOfDay()->diffInDays($this->session_date, false) <= -7;
    }

    public function canTeacherEdit(): bool
    {
        if ($this->is_forced_enabled) {
            return true;
        }

        return !$this->isAccessExpired();
    }
    
}
