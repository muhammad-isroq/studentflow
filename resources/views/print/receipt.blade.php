<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @media print {
            @page { 
                margin: 0; 
            }
            body { 
                width: 70mm; 
                margin: 0 auto; 
                padding-top: 5mm; 
            }
            .no-print { 
                display: none; 
            }
        }
        
        /* --- PERBAIKAN KETEBALAN FONT UNTUK PRINTER THERMAL --- */
        body { 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 12px; 
            color: #000000 !important; /* Paksa hitam pekat murni */
            font-weight: bold !important; /* Paksa tebal */
        }
        
        /* Paksa semua elemen menjadi tebal */
        * {
            font-weight: bold !important;
        }
        /* ----------------------------------------------------- */

        .text-center { text-align: center; }
        .divider { border-top: 1.5px dashed #000; margin: 5px 0; } /* Dipertebal menjadi 1.5px */
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .total { font-size: 14px; }
        
        .expense-label { background-color: #000; color: #fff; display: inline-block; padding: 2px 5px; font-weight: bold; }
        .signature-table td { border: none !important; padding: 0; }
    </style>
</head>
<body>
    <div id="capture-area" style="background: white; padding: 10px;">
        <div class="text-center">
            <strong style="font-size: 16px;">THE MASTER OF DUMAI</strong><br>
            English Course Center<br>
            Jln. Sultan Hasanudin / Ombak Ujung No. 88 Dumai, Riau <br>
            Tlp. 0812 7770 4026 / 0823 8222 0858
        </div>
        
        <div class="divider"></div>
        
        <div class="text-center" style="margin: 5px 0;">
            @if($bill->transaction_type === 'expense')
                <span class="expense-label">BUKTI PENGELUARAN KAS</span>
            @else
                <strong>K W I T A N S I</strong>
            @endif
        </div>
        
        <div class="divider"></div>
        <table>
            <tr><td width="35%">No. Transaksi</td><td>: #{{ $bill->id }}</td></tr>
            <tr><td>Waktu</td><td>: {{ $bill->paid_at ? \Carbon\Carbon::parse($bill->paid_at)->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td></tr>
            
            @if($bill->transaction_type === 'expense')
                <tr><td>Dibayarkan Kpd</td><td>: {{ $bill->paid_by ?: '-' }}</td></tr>
            @else
                <tr><td>Nama Siswa</td><td>: {{ $bill->paid_by ?: ($siswa ? $siswa->nama : '-') }}</td></tr>
                @if($siswa)
                    <tr><td>Program</td><td>: {{ $siswa->program->nama_program ?? '-' }}</td></tr>
                @endif
            @endif
        </table>
        
        <div class="divider"></div>
        <div style="margin: 10px 0;">
            <strong>KETERANGAN:</strong><br>
            @if(stripos($paymentType->name, 'spp') !== false)
                Periode Bulan {{ \Carbon\Carbon::parse($bill->due_date)->locale('id')->translatedFormat('F Y') }}
            @else
                {{ $paymentType->name }}
            @endif
            
            @if($bill->notes)
                {{-- LOGIKA BARU: Timpa teks 'Bayar di Muka' menjadi 'Cash' secara otomatis saat dicetak --}}
                @php
                    $catatan = str_ireplace('bayar di muka', 'Cash', $bill->notes);
                @endphp
                <br><em>* {{ $catatan }}</em>
            @endif
        </div>
        
        <table>
            <tr class="total">
                <td>TOTAL</td>
                <td align="right">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <table class="signature-table text-center" style="margin-top: 15px;">
            <tr>
                <td style="width: 50%;">
                    @if($bill->transaction_type === 'expense')
                        Penerima,
                    @else
                        Nama Siswa,
                    @endif
                </td>
                <td style="width: 50%;">
                    @if($bill->transaction_type === 'expense')
                        Diserahkan Oleh,
                    @else
                        Diterima Oleh,
                    @endif
                </td>
            </tr>
            <tr>
                <td style="height: 40px;"></td>
                <td style="height: 40px;"></td>
            </tr>
            <tr>
                <td style="font-size: 11px;">
                    ( {{ $bill->transaction_type === 'expense' ? ($bill->paid_by ?: '________________') : ($bill->paid_by ?: ($siswa ? $siswa->nama : '________________')) }} )
                </td>
                <td style="font-size: 11px;">
                    ( {{ strtoupper($staffName) }} )
                </td>
            </tr>
        </table>
        
        <div class="divider" style="margin-top: 15px;"></div>
        <div class="text-center" style="font-size: 10px;">
            <br>
            @if($bill->transaction_type === 'expense')
                Dokumen internal pengeluaran sah.<br>
            @else
                TERIMA KASIH<br>
                Simpan struk ini sebagai bukti pembayaran sah.
            @endif
        </div>
    </div>
    
    <div class="text-center no-print" style="margin-top: 30px;">
        <button id="btn-save-proof" style="background-color: #22c55e; color: white; padding: 10px 15px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; width: 100%;">
            📸 Simpan Sebagai Bukti di Sistem
        </button>
        <p id="status-message" style="color: #6b7280; font-size: 11px; margin-top: 5px;">Klik tombol di atas dahulu sebelum mencetak kertas.</p>
    </div>

    <script>
    document.getElementById('btn-save-proof').addEventListener('click', function() {
        const btn = this;
        const msg = document.getElementById('status-message');
        const target = document.getElementById('capture-area'); 
        
        btn.disabled = true;
        btn.innerText = "⏳ Sedang Memotret...";
        btn.style.backgroundColor = "#9ca3af";

        html2canvas(target, {
            scale: 2, 
            backgroundColor: "#ffffff", 
            logging: true, 
            width: target.offsetWidth,
            height: target.offsetHeight
        }).then(canvas => {
            let imageData = canvas.toDataURL("image/png");

            btn.innerText = "⏳ Mengirim ke Sistem...";

            fetch("{{ route('api.save-receipt-proof', ['bill' => $bill->id]) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ image: imageData })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Koneksi server bermasalah (Status: " + response.status + ")");
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    btn.innerText = "✅ Bukti Berhasil Disimpan!";
                    btn.style.backgroundColor = "#16a34a";
                    msg.innerHTML = "<span style='color: #16a34a; font-weight: bold;'>Data tersimpan!</span> Mengarahkan ke cetak...";
                    
                    if (window.opener && !window.opener.closed) {
                        window.opener.location.reload();
                    }

                    setTimeout(() => {
                        window.print();
                    }, 500);
                } else {
                    throw new Error(data.error || "Gagal memperbarui database.");
                }
            })
            .catch(error => {
                console.error("Detail Error:", error);
                btn.disabled = false;
                btn.innerText = "❌ Gagal Simpan, Coba Lagi";
                btn.style.backgroundColor = "#dc2626";
                msg.innerText = error.message || "Gagal mengirim gambar ke server.";
            });
        });
    });
    </script>
</body>
</html>