<!DOCTYPE html>
<html>
<head>
    <title>Struk Pembayaran Kolektif - {{ $siswa->nama }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @media print {
    @page { 
        size: 80mm auto; /* Ganti 200mm menjadi auto */
        margin: 0; 
    }
    body { 
        width: 70mm; 
        margin: 0 auto; /* Menggunakan margin minimal agar tidak memicu space kosong */
        padding: 2mm 0; /* Memberikan sedikit ruang di atas dan bawah konten */
    }
    .no-print { 
        display: none; 
    }
}
        body { 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 12px; 
            color: #000;
        }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .total { font-weight: bold; font-size: 14px; }
        .item-row td { padding: 2px 0; }
        
        /* Styling khusus untuk area tanda tangan */
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
            <strong>K W I T A N S I   (KOLEKTIF)</strong>
        </div>
        
        <div class="divider"></div>
        <table>
            <tr><td width="35%">No. Transaksi</td><td>: {{ $bills->first()->id }}</td></tr>
            <tr><td>Waktu</td><td>: {{ $bills->first()->paid_at ? \Carbon\Carbon::parse($bills->first()->paid_at)->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td></tr>
            <tr><td>Nama Siswa</td><td>: {{ strtoupper($siswa->nama) }}</td></tr>
            <tr><td>Program</td><td>: {{ $siswa->program->nama_program ?? '-' }}</td></tr>
        </table>
        
        <div class="divider"></div>
        <div style="margin: 5px 0;">
            <strong>RINCIAN PEMBAYARAN:</strong>
        </div>
        
        <table>
            @foreach($bills as $bill)
            <tr class="item-row">
                <td>
                    {{-- LOGIKA BARU: Cek apakah nama tagihan mengandung kata 'spp' --}}
                    @if(stripos($bill->paymentType->name, 'spp') !== false)
                        Periode Bulan {{ \Carbon\Carbon::parse($bill->due_date)->locale('id')->translatedFormat('F Y') }}
                    @else
                        {{ $bill->paymentType->name }}
                    @endif

                    @if($bill->notes)
                        <br><span style="font-size: 10px; font-style: italic;">* {{ str_ireplace('bayar di muka', 'Cash', $bill->notes) }}</span>
                    @endif
                </td>
                <td align="right">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>
        
        <div class="divider"></div>
        
        <table>
            <tr class="total">
                <td>TOTAL BAYAR</td>
                <td align="right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <table class="signature-table text-center" style="margin-top: 15px;">
            <tr>
                <td style="width: 50%;">Nama Siswa,</td>
                <td style="width: 50%;">Diterima Oleh,</td>
            </tr>
            <tr>
                <td style="height: 40px;"></td>
                <td style="height: 40px;"></td>
            </tr>
            <tr>
                <td style="font-size: 11px;">
                    ( {{ strtoupper($siswa->nama) }} )
                </td>
                <td style="font-size: 11px;">
                    ( {{ strtoupper($staffName) }} )
                </td>
            </tr>
        </table>
        
        <div class="divider" style="margin-top: 15px;"></div>
        <div class="text-center" style="font-size: 10px;">
            <br>TERIMA KASIH<br>
            Simpan struk ini sebagai bukti pembayaran sah.
        </div>
    </div>

    <div class="text-center no-print" style="margin-top: 30px;">
        <button id="btn-save-bulk-proof" style="background-color: #22c55e; color: white; padding: 10px 15px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; width: 100%;">
            📸 Simpan Sebagai Bukti Masal di Sistem
        </button>
        <p id="status-message" style="color: #6b7280; font-size: 11px; margin-top: 5px;">Klik tombol di atas dahulu untuk mengisi bukti pembayaran di semua bulan terkait.</p>
    </div>

    <script>
    document.getElementById('btn-save-bulk-proof').addEventListener('click', function() {
        const btn = this;
        const msg = document.getElementById('status-message');
        const target = document.getElementById('capture-area');
        
        btn.disabled = true;
        btn.innerText = "⏳ Sedang Memotret Struk Kolektif...";
        btn.style.backgroundColor = "#9ca3af";

        const billIds = @json($bills->pluck('id')); 

        html2canvas(target, {
            scale: 2, 
            backgroundColor: "#ffffff",
            width: target.offsetWidth,
            height: target.offsetHeight
        }).then(canvas => {
            let imageData = canvas.toDataURL("image/png");

            btn.innerText = "⏳ Menyimpan Semua Bukti...";

            fetch("{{ route('api.save-multiple-receipt-proof') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ 
                    image: imageData,
                    bill_ids: billIds
                })
            })
            .then(response => {
                if (!response.ok) throw new Error("Koneksi server bermasalah (Status: " + response.status + ")");
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    btn.innerText = "✅ Semua Bukti Berhasil Diperbarui!";
                    btn.style.backgroundColor = "#16a34a";
                    msg.innerHTML = "<span style='color: #16a34a; font-weight: bold;'>Selesai!</span> Sinkronisasi halaman admin...";
                    
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
                btn.innerText = "❌ Gagal Simpan Massal";
                btn.style.backgroundColor = "#dc2626";
                msg.innerText = error.message || "Gagal memproses unggahan bukti massal.";
            });
        });
    });
    </script>
</body>
</html>