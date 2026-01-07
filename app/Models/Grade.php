<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id', 
        'student_id', 
        'listening', 
        'reading', 
        'writing', 
        'grammar', 
        'speaking', 
        'average'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }

    protected static function booted()
    {
        static::saving(function ($grade) {
            $components = [
                $grade->listening, 
                $grade->reading, 
                $grade->writing, 
                $grade->grammar, 
                $grade->speaking
            ];

            $filled = array_filter($components, fn($val) => !is_null($val) && $val !== '');
            
            if (count($filled) > 0) {
                $grade->average = array_sum($filled) / count($filled);
            } else {
                $grade->average = null;
            }
        });
    }
}