<div class="min-h-screen pt-24 pb-12" data-theme="caramellatte">
    <div class="max-w-3xl mx-auto px-4">
        <div class="card bg-base-200 shadow-2xl border border-primary/20" data-theme="light">
            <div class="card-body p-6 sm:p-10">
                <h2 class="text-3xl font-extrabold text-center text-primary tracking-tight italic">
                    Formulir Registrasi Siswa Baru 
                </h2>
                <h2 class="text-3xl font-extrabold text-center text-secondary tracking-tight italic">
                    The Master of Dumai
                </h2> <br>
                
                <form wire:submit.prevent="save" class="space-y-6">
                    @csrf

                    {{-- BAGIAN 1: IDENTITAS AKUN & SISWA --}}
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Nama Lengkap</span></label>
                            <input type="text" wire:model="nama" class="input input-bordered focus:input-primary w-full" placeholder="Nama Siswa">
                            @error('nama') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Jenis Kelamin</span></label>
                            <select wire:model="jenis_kelamin" class="select select-bordered focus:select-primary w-full">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Username (Untuk Login)</span></label>
                            <input type="text" wire:model="username" class="input input-bordered focus:input-primary w-full" placeholder="Contoh: budi123">
                            <label class="label">
                                <span class="label-text-alt text-gray-500 italic">Gunakan nama tanpa spasi untuk memudahkan login kembali.</span>
                            </label>
                            @error('username') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Password Akun</span></label>
                            <input type="password" wire:model="password" class="input input-bordered focus:input-primary w-full" placeholder="Minimal 8 karakter">
                            @error('password') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Agama</span></label>
                            <select wire:model="agama" class="select select-bordered focus:select-primary w-full">
                                <option value="">Pilih Agama</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Budha">Budha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                            @error('agama') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Tanggal Lahir</span></label>
                            <input type="date" wire:model="tgl_lahir" class="input input-bordered focus:input-primary w-full">
                            @error('tgl_lahir') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- BAGIAN 2: PENDIDIKAN --}}
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Kelas di Sekolah Saat Ini</span></label>
                            <select wire:model="grade" class="select select-bordered focus:select-primary w-full">
                                <option value="">Pilih Kelas</option>
                                <optgroup label="Tingkat TK/PAUD">
                                    <option value="TK-A">TK - A</option>
                                    <option value="TK-B">TK - B</option>
                                </optgroup>
                                <optgroup label="Tingkat SD/SMP/SMA">
                                    <option value="SD">SD (Sekolah Dasar)</option>
                                    <option value="SMP">SMP (Menengah Pertama)</option>
                                    <option value="SMA">SMA (Menengah Atas)</option>
                                </optgroup>
                                <option value="Umum">Mahasiswa / Umum</option>
                            </select>
                            @error('grade') <span class="text-error text-xs mt-1 italic">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Asal Sekolah / Instansi</span></label>
                            <input type="text" wire:model="asal_sekolah" class="input input-bordered focus:input-primary w-full" placeholder="Nama Sekolah/Kampus">
                            @error('asal_sekolah') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- BAGIAN 3: ORANG TUA / WALI --}}
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Nama Orang Tua / Wali</span></label>
                            <input type="text" wire:model="nama_orang_tua" class="input input-bordered focus:input-primary w-full" placeholder="Nama Lengkap">
                            @error('nama_orang_tua') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Pekerjaan Orang Tua</span></label>
                            <input type="text" wire:model="pekerjaan_orang_tua" class="input input-bordered focus:input-primary w-full" placeholder="Contoh: PNS/Wiraswasta">
                            @error('pekerjaan_orang_tua') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold text-primary">No. WhatsApp Wali (Aktif)</span></label>
                        <input type="text" wire:model="no_wa_wali" class="input input-bordered focus:input-primary w-full" placeholder="08xxxx">
                        @error('no_wa_wali') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold text-primary">Alamat Lengkap</span></label>
                        <textarea wire:model="alamat" class="textarea textarea-bordered focus:textarea-primary w-full min-h-[80px]" placeholder="Alamat tinggal saat ini..."></textarea>
                        @error('alamat') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- BAGIAN 4: SURVEY --}}
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Info Dari Mana?</span></label>
                            <select wire:model="sumber_info" class="select select-bordered focus:select-primary w-full">
                                <option value="">Pilih Sumber</option>
                                <option value="Teman">Teman</option>
                                <option value="Guru">Guru</option>
                                <option value="Staff">Staff</option>
                                <option value="Brosur">Brosur</option>
                                <option value="Spanduk">Spanduk</option>
                                <option value="Sosial Media">Sosial Media</option>
                            </select>
                            @error('sumber_info') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Alasan Kursus Bahasa Inggris?</span></label>
                            <input type="text" wire:model="alasan_kursus" class="input input-bordered focus:input-primary w-full" placeholder="Contoh: Persiapan kerja/hobi">
                            @error('alasan_kursus') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="relative py-4">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-primary/20"></div></div>
                        <div class="relative flex justify-center"><span class="bg-base-100 px-4 text-sm font-bold text-secondary uppercase tracking-widest">Upload Dokumen</span></div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Foto Siswa (Formal)</span></label>
                            <input type="file" wire:model="photo" class="file-input file-input-bordered file-input-primary w-full">
                            @error('photo') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-primary">Bukti Transfer Pendaftaran</span></label>
                            <input type="file" wire:model="bukti_pembayaran" class="file-input file-input-bordered file-input-secondary w-full">
                            @error('bukti_pembayaran') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="btn btn-primary btn-block text-lg shadow-lg hover:shadow-primary/30 transition-all duration-300 uppercase" wire:loading.attr="disabled">
                            
                            Kirim & Daftar Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>