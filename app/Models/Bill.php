<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\BillObserver;

#[ObservedBy([BillObserver::class])]
class Bill extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
        'paid_amount' => 'integer',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

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

    /**
     * Accessor untuk menghitung sisa pembayaran
     */
    public function getRemainingAmountAttribute(): int
    {
        return $this->amount - ($this->paid_amount ?? 0);
    }

    /**
     * Accessor untuk mengecek apakah tagihan sudah terlambat
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }

    /**
     * Accessor untuk mendapatkan persentase pembayaran
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->amount <= 0) return 0;
        
        return round(($this->paid_amount / $this->amount) * 100, 2);
    }

    /**
     * Accessor untuk mendapatkan nama bulan (khusus SPP)
     */
    public function getMonthNameAttribute(): ?string
    {
        if (!$this->paymentType || !$this->paymentType->isSpp()) {
            return null;
        }
        
        return $this->due_date ? $this->due_date->format('F') : null;
    }

    /**
     * Accessor untuk mendapatkan tahun (khusus SPP)
     */
    public function getYearAttribute(): ?int
    {
        if (!$this->paymentType || !$this->paymentType->isSpp()) {
            return null;
        }
        
        return $this->due_date ? $this->due_date->year : null;
    }

    /**
     * Scope untuk tagihan yang belum dibayar
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope untuk tagihan yang terlambat
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'paid');
    }

    /**
     * Scope untuk tagihan yang sudah dibayar
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope untuk tagihan berdasarkan payment type
     */
    public function scopeByPaymentType($query, $paymentTypeId)
    {
        return $query->where('payment_type_id', $paymentTypeId);
    }

    /**
     * Scope untuk tagihan SPP
     */
    public function scopeSpp($query)
    {
        return $query->whereHas('paymentType', function ($q) {
            $q->spp();
        });
    }

    /**
     * Scope untuk tagihan non-SPP
     */
    public function scopeNonSpp($query)
    {
        return $query->whereHas('paymentType', function ($q) {
            $q->nonSpp();
        });
    }

    /**
     * Scope untuk tagihan berdasarkan bulan dan tahun (khusus SPP)
     */
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereYear('due_date', $year)
                    ->whereMonth('due_date', $month);
    }

    /**
     * Scope untuk tagihan tahun ini
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereYear('due_date', now()->year);
    }

    /**
     * Static method untuk membuat tagihan SPP bulanan
     */
    public static function createMonthlySpp($siswa, $month, $year)
    {

        if ($siswa->status !== 'active') { 
            return null; // Batalkan pembuatan tagihan jika tidak aktif
        }

        // Cari atau buat payment type SPP
        $sppPaymentType = PaymentType::firstOrCreate([
            'name' => 'Monthly SPP'
        ]);

        // Hitung tanggal jatuh tempo
        $billingDay = $siswa->billing_day ?? 10;
        $dueDate = Carbon::create($year, $month, $billingDay);

        return self::create([
            'siswa_id' => $siswa->id,
            'payment_type_id' => $sppPaymentType->id,
            'amount' => $siswa->spp_amount ?? 500000,
            'due_date' => $dueDate,
            'status' => 'unpaid',
        ]);
    }

    /**
     * Method untuk menandai sebagai lunas
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_amount' => $this->amount, // Set paid_amount sama dengan amount
        ]);

        return $this;
    }

    /**
     * Method untuk check apakah ini tagihan SPP
     */
    public function isSpp(): bool
    {
        return $this->paymentType && $this->paymentType->isSpp();
    }

    /**
     * Boot method untuk auto-update status
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bill) {
            // Auto update status berdasarkan pembayaran
            if (isset($bill->paid_amount) && $bill->paid_amount >= $bill->amount) {
                $bill->status = 'paid';
                if (!$bill->paid_at) {
                    $bill->paid_at = now();
                }
            } elseif (isset($bill->paid_amount) && $bill->paid_amount > 0) {
                $bill->status = 'partial';
            } elseif ($bill->due_date && $bill->due_date < now() && $bill->status === 'unpaid') {
                $bill->status = 'overdue';
            }
        });
    }
}