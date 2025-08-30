<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PaymentType extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('PaymentType') // nama log
            ->logOnly(['name']) // field yang dicatat
            ->logOnlyDirty() // hanya catat field yang berubah
            ->dontSubmitEmptyLogs(); // jangan log kalau tidak ada perubahan
    }
}
