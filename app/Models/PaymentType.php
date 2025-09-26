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

    /**
     * Relasi dengan Bills
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Scope untuk payment type SPP/Monthly
     */
    public function scopeSpp($query)
    {
        return $query->where('name', 'like', '%spp%')
                    ->orWhere('name', 'like', '%SPP%')
                    ->orWhere('name', 'like', '%Monthly%');
    }

    /**
     * Scope untuk payment type non-SPP
     */
    public function scopeNonSpp($query)
    {
        return $query->where('name', 'not like', '%spp%')
                    ->where('name', 'not like', '%SPP%')
                    ->where('name', 'not like', '%Monthly%');
    }

    /**
     * Helper method untuk check apakah ini SPP
     */
    public function isSpp(): bool
    {
        return stripos($this->name, 'spp') !== false || 
               stripos($this->name, 'monthly') !== false;
    }
}
