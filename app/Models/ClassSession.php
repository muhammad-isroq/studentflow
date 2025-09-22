<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSession extends Model
{
    protected $guarded = [];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }
}
