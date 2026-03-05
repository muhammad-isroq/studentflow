<div class="min-h-screen pt-10 pb-12" data-theme="caramellatte">
    <div class="max-w-6xl mx-auto">
        {{-- SECTION BROSUR BERDAMPINGAN --}}
        <div class="bg-base-100 py-8 px-4">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-extrabold text-primary mb-2 uppercase tracking-tighter">Informasi & Promo</h2>
                <p class="text-xs opacity-60 italic mt-1">Klik gambar untuk memperbesar informasi</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                {{-- BROSUR 1: INFORMASI --}}
                <div class="flex flex-col">
                    <label for="modal-brosur-info" class="cursor-zoom-in">
                        <div class="card bg-base-200 shadow-xl overflow-hidden border-2 border-primary/20 transition-all hover:border-primary">
                            <figure class="hover:scale-105 transition-transform duration-500">
                                <img src="{{ asset('images/brosur_informasi.jpeg') }}" alt="Brosur Informasi" class="w-full h-auto object-cover aspect-[3/4]" />
                            </figure>
                            <div class="card-body bg-primary text-primary-content p-3 text-center">
                                <p class="text-[10px] font-bold uppercase tracking-widest">Brosur Informasi Program</p>
                            </div>
                        </div>
                    </label>
                </div>

                {{-- BROSUR 2: PROMO --}}
                <div class="flex flex-col">
                    <label for="modal-brosur-promo" class="cursor-zoom-in">
                        <div class="card bg-base-200 shadow-xl overflow-hidden border-2 border-secondary/20 transition-all hover:border-secondary">
                            <figure class="hover:scale-105 transition-transform duration-500">
                                <img src="{{ asset('images/brosur-the-master.jpeg') }}" alt="Brosur Promo" class="w-full h-auto object-cover aspect-[3/4]" />
                            </figure>
                            <div class="card-body bg-secondary text-secondary-content p-3 text-center">
                                <p class="text-[10px] font-bold uppercase tracking-widest">Promo Spesial Bulan Ini!</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-primary mb-2 uppercase tracking-tighter">Informasi Pendaftaran</h2>
        </div>

        {{-- SECTION Rincian Biaya (Baru ditambahkan) --}}
        <div class="px-4 mb-20">
            <div class="grid md:grid-cols-2 gap-8 bg-white p-8 rounded-3xl shadow-xl border border-primary/10" data-theme="light">
                
                {{-- Program Reguler --}}
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-2 h-8 bg-primary rounded-full"></div>
                        <h3 class="text-2xl font-black text-primary uppercase italic">Program Reguler  <small class="text-[10px] lowercase opacity-50 block">(2x seminggu / 1semester, 6-13 Siswa)</small></h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-base-200 rounded-xl">
                            <span class="font-bold">Pre-school</span>
                            <span class="badge badge-primary font-mono font-bold">Rp 530.000 <small class="ml-1">/2 bln</small></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-base-200 rounded-xl">
                            <span class="font-bold">Master Kids</span>
                            <span class="badge badge-primary font-mono font-bold">Rp 500.000 <small class="ml-1">/2 bln</small></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-base-200 rounded-xl">
                            <span class="font-bold">Master Conversation</span>
                            <span class="badge badge-primary font-mono font-bold">Rp 550.000 <small class="ml-1">/2 bln</small></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-base-200 rounded-xl">
                            <span class="font-bold">TOEFL Preparation</span>
                            <span class="badge badge-primary font-mono font-bold">Rp 700.000 <small class="ml-1">/2 bln</small></span>
                        </div>
                    </div>
                </div>

                {{-- Program Private --}}
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-2 h-8 bg-secondary rounded-full"></div>
                        <h3 class="text-2xl font-black text-secondary uppercase italic">Program Private <small class="text-[10px] lowercase opacity-50 block">(1-2 Siswa)</small></h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-4 border-b border-dashed">
                            <span class="font-bold text-lg">Paket 10 Jam</span>
                            <div class="text-right">
                                <span class="text-xl font-black text-secondary font-mono">Rp 165.000</span>
                                <p class="text-[10px] opacity-60">per jam</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-4 border-b border-dashed">
                            <span class="font-bold text-lg">Paket 20 Jam</span>
                            <div class="text-right">
                                <span class="text-xl font-black text-secondary font-mono">Rp 145.000</span>
                                <p class="text-[10px] opacity-60">per jam</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-4 border-b border-dashed">
                            <span class="font-bold text-lg">Paket 36 Jam</span>
                            <div class="text-right">
                                <span class="text-xl font-black text-secondary font-mono">Rp 135.000</span>
                                <p class="text-[10px] opacity-60">per jam</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Biaya Tambahan --}}
                    <div class="mt-6 p-4 bg-yellow-50 rounded-2xl border border-yellow-200">
                        <h4 class="text-xs font-bold text-yellow-800 uppercase mb-2">Biaya tersebut belum termasuk:</h4>
                        <ul class="text-xs text-yellow-700 space-y-1 italic">
                            <li>* Kurikulum (buku paket 1 semester / 1 tahun)</li>
                            <li>* Transportasi mulai Rp. 15.000/pertemuan (apabila private di rumah)</li>
                            <li>* Bagi siswa yang saudara kandung akan mendapatkan diskon 10% dari harga khusus</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALUR PENDAFTARAN --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-primary mb-2 uppercase tracking-tighter">Alur Pendaftaran Siswa Online</h1>
            <div class="h-1 w-48 bg-secondary mx-auto mb-4 rounded-full"></div>
            <p class="text-lg font-medium opacity-70">Empat Tahapan Proses Pendaftaran di The Master of Dumai</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16 px-4">
            {{-- Step 1 --}}
            <div class="card bg-base-100 shadow-xl border-t-8 border-blue-500" data-theme="light">
                <div class="card-body p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-blue-500 text-white w-10 h-10 flex items-center justify-center rounded-lg shadow-lg font-bold">1</span>
                        <h2 class="card-title text-xs leading-tight font-black uppercase">Mendaftar Akun</h2>
                    </div>
                    <p class="text-xs opacity-80 leading-relaxed">Calon siswa mendaftar akun dan mengisi formulir data diri lengkap.</p>
                </div>
            </div>
            {{-- Step 2 --}}
            <div class="card bg-base-100 shadow-xl border-t-8 border-green-500" data-theme="light">
                <div class="card-body p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-green-500 text-white w-10 h-10 flex items-center justify-center rounded-lg shadow-lg font-bold">2</span>
                        <h2 class="card-title text-xs leading-tight font-black uppercase">Verifikasi Data</h2>
                    </div>
                    <p class="text-xs opacity-80 leading-relaxed">Admin memeriksa validasi pendaftaran serta kelengkapan dokumen.</p>
                </div>
            </div>
            {{-- Step 3 --}}
            <div class="card bg-base-100 shadow-xl border-t-8 border-orange-500" data-theme="light">
                <div class="card-body p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-orange-500 text-white w-10 h-10 flex items-center justify-center rounded-lg shadow-lg font-bold">3</span>
                        <h2 class="card-title text-sm leading-tight font-black uppercase">Tes Penentuan Level</h2>
                    </div>
                    <p class="text-xs opacity-80 leading-relaxed">Mengikuti serangkaian tes untuk mengetahui level penempatan.</p>
                </div>
            </div>
            {{-- Step 4 --}}
            <div class="card bg-base-100 shadow-xl border-t-8 border-yellow-500" data-theme="light">
                <div class="card-body p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-yellow-500 text-white w-10 h-10 flex items-center justify-center rounded-lg shadow-lg font-bold">4</span>
                        <h2 class="card-title text-sm leading-tight font-black uppercase">Pengumuman</h2>
                    </div>
                    <p class="text-xs opacity-80 leading-relaxed">Hasil pendaftaran diumumkan secara resmi di dashboard akun dan akan diberitahukan melalui WhatsApp.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-6 mb-5" >
            <div class="text-center">
                <h3 class="text-2xl font-bold text-secondary tracking-tight">Siap untuk bergabung bersama kami?</h3>
                {{-- <p class="opacity-60 text-sm">Pilih program yang sesuai dan klik tombol di bawah.</p> --}}
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-6">
            <div class="text-center flex gap-4">
                <a href="/pendaftaran/register" class="btn btn-primary shadow-2xl border btn-lg">Daftar Sekarang</a>
                <a href="{{ route('login') }}" class="btn btn-outline border-2 btn-primary btn-lg">Login Calon Siswa</a>
            </div>
        </div>

        {{-- MODAL SECTION --}}
        <input type="checkbox" id="modal-brosur-info" class="modal-toggle" />
        <div class="modal" role="dialog">
            <div class="modal-box max-w-5xl p-0 bg-transparent shadow-none relative">
                <label for="modal-brosur-info" class="btn btn-sm btn-circle btn-primary absolute right-2 top-2 z-50">✕</label>
                <img src="{{ asset('images/brosur_informasi.jpeg') }}" class="w-full rounded-lg" />
            </div>
            <label class="modal-backdrop" for="modal-brosur-info">Close</label>
        </div>

        <input type="checkbox" id="modal-brosur-promo" class="modal-toggle" />
        <div class="modal" role="dialog">
            <div class="modal-box max-w-5xl p-0 bg-transparent shadow-none relative">
                <label for="modal-brosur-promo" class="btn btn-sm btn-circle btn-secondary absolute right-2 top-2 z-50">✕</label>
                <img src="{{ asset('images/brosur-the-master.jpeg') }}" class="w-full rounded-lg" />
            </div>
            <label class="modal-backdrop" for="modal-brosur-promo">Close</label>
        </div>
    </div>
</div>