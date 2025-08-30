<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Guru extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nama_guru',
        'no_hp'
    ];

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('teacher') 
            ->logOnly(['nama_guru', 'no_hp']) 
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs(); 
    }
}
