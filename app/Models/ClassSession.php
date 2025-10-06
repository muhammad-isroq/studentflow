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

    
}
