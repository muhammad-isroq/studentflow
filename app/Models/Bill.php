<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Bill extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('bill') 
            ->logOnly(['siswa_id', 'payment_type_id', 'amount', 'due_date', 'status', 'paid_at', 'proof_of_payment']) 
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs(); 
    }
}
