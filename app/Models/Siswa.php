<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Siswa extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nama',
        'foto',
        'kelas_disekolah',
        'no_wali',
        'foto_formulir',
        'alamat',
        'tgl_lahir',
        'tgl_masuk',
        'tgl_registrasi',
        'program_id',
        'status',
        'billing_day',
        'spp_amount',
        'registration_fee',
        'registration_proof',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('student')
            ->logOnly([
                'nama',
                'foto',
                'kelas_disekolah',
                'no_wali',
                'foto_formulir',
                'alamat',
                'tgl_lahir',
                'tgl_masuk',
                'tgl_registrasi',
                'program_id',
                'status',
                'billing_day',
                'spp_amount',
                'registration_fee',
                'registration_proof',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getUnpaidBillsAttribute()
    {
        return $this->bills()->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('amount');
    }

    /**
     * Accessor untuk mendapatkan jumlah tagihan yang belum dibayar
     */
    public function getUnpaidBillsCountAttribute()
    {
        return $this->bills()->whereIn('status', ['unpaid', 'partial', 'overdue'])->count();
    }

    /**
     * Scope untuk filter siswa dengan tagihan terlambat
     */
    public function scopeWithOverdueBills($query)
    {
        return $query->whereHas('bills', function ($q) {
            $q->where('due_date', '<', now())
              ->where('status', '!=', 'paid');
        });
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }
}
