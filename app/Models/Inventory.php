<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\InventoryObserver;

#[ObservedBy([InventoryObserver::class])]
class Inventory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_barang',
        'kode_aset',
        'kategori',
        'jumlah',
        'lokasi',
        'status',
        'user_id',
        'tanggal_beli',  
        'harga',           
        'bukti_pembelian',
        'keterangan',
        'gambar',
    ];

    /**
     * Get the user that is responsible for the inventory item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}