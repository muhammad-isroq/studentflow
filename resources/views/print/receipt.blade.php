<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @media print {
            @page { size: 80mm 200mm; margin: 0; }
            body { width: 70mm; margin: 5mm; }
            .no-print { display: none; }
        }
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; }
        .total { font-weight: bold; font-size: 14px; }
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
        <table>
            <tr><td>No. Kwitansi</td><td>: #{{ $bill->id }}</td></tr>
            <tr><td>Tanggal</td><td>: {{ $bill->paid_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td>dibayarkan oleh</td><td>: {{ $siswa->nama }}</td></tr>
            <tr><td>Diterima oleh</td><td>: {{ strtoupper($staffName) }}</td></tr>
        </table>
        
        <div class="divider"></div>
        <div style="margin: 10px 0;">
            <strong>PEMBAYARAN:</strong><br>
            {{ $paymentType->name }}
        </div>
        
        <table>
            <tr class="total">
                <td>TOTAL</td>
                <td align="right">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <div class="divider"></div>
        <div class="text-center">
            <br>TERIMA KASIH<br>
            Simpan struk ini sebagai bukti pembayaran sah.
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
        const target = document.getElementById('capture-area'); // Target bidikan spesifik
        
        btn.disabled = true;
        btn.innerText = "⏳ Sedang Memotret...";
        btn.style.backgroundColor = "#9ca3af";

        // Konfigurasi html2canvas yang lebih aman untuk browser modern
        html2canvas(target, {
            scale: 2, // Meningkatkan resolusi gambar agar tulisan tajam
            backgroundColor: "#ffffff", // Memaksa latar belakang warna putih
            logging: true, // Mengaktifkan log internal untuk melacak kegagalan
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
                    
                    // UTAMAKAN INI: Perintahkan tab utama (Filament) untuk refresh data
                    if (window.opener && !window.opener.closed) {
                        // Opsi 1: Muat ulang halaman Filament di tab sebelah secara penuh
                        window.opener.location.reload();
                    }

                    // Picu cetak printer
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