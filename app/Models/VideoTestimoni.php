<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTestimoni extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_video',
        'notes1',
        'notes2',
        'nama_ortu',
        'nama_anak',
    ];
}
