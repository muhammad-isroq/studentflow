<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherRecapArchive extends Model
{
    protected $guarded = [];

    // Ubah format JSON database menjadi Array secara otomatis
    protected $casts = [
        'program_details' => 'array',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }
}
