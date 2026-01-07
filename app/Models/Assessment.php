<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id', 
        'name', 
        'order'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}