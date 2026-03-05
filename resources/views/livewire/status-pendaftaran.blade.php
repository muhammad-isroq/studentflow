<div class="min-h-screen bg-base-200 pt-24 pb-12" data-theme="caramellatte">
    
    <div class="max-w-4xl mx-auto px-4">
        {{-- Guard Check --}}
        @if(!auth()->guard('registration')->check())
            <script>window.location = "/pendaftaran";</script>
        @endif

        @php
            $user = auth()->guard('registration')->user();
            $status = $user->status;
        @endphp

        {{-- ALERT SECTION: REJECTED --}}
        @if($status === 'rejected')
        <div class="alert alert-error shadow-lg mb-6 border-l-8 border-red-700 rounded-lg">
            <div class="flex flex-col md:flex-row items-start gap-4 w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-8 w-8 text-white" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-grow">
                    <h3 class="font-bold text-lg uppercase text-white">Pendaftaran Perlu Perbaikan</h3>
                    <div class="text-sm mt-2 bg-black/10 p-4 rounded-md italic border border-white/20 text-white">
                        "{{ $user->catatan_admin ?? 'Mohon maaf, data atau bukti pembayaran Anda belum sesuai syarat.' }}"
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('pendaftaran.register') }}" class="btn btn-sm bg-white text-error border-none hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Perbaiki Data Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- USER PROFILE CARD --}}
        <div class="card bg-base-100 shadow-xl mb-8" data-theme="light">
            <div class="card-body flex-row items-center gap-6">
                <div class="avatar">
                    <div class="w-24 rounded-xl ring ring-primary ring-offset-base-100 ring-offset-2">
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto Profil" />
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold italic text-primary uppercase text-left">Halo, {{ $user->nama }}!</h2>
                    <p class="opacity-70 text-sm text-left">Selamat datang di dashboard calon siswa The Master of Dumai.</p>
                    <div class="flex justify-start">
                        <div class="badge badge-secondary mt-2 uppercase font-bold p-3">ID: REG-{{ $user->id }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEPS SECTION --}}
        <div class="card bg-base-100 shadow-xl overflow-hidden" data-theme="light">
            <div class="card-body">
                <h3 class="card-title mb-8 border-b pb-2">Status Alur Pendaftaran:</h3>
                
                <ul class="steps steps-vertical lg:steps-horizontal w-full py-4">
                    {{-- Step 1 selalu selesai --}}
                    <li class="step step-primary">Registrasi <br><span class="text-xs opacity-50 italic text-success font-bold">Success</span></li>
                    
                    {{-- Step 2: Verifikasi (Selesai jika status bukan waiting_verification atau rejected) --}}
                    <li class="step {{ in_array($status, ['selection', 'announced']) ? 'step-primary' : '' }}">
                        Verifikasi <br><span class="text-xs opacity-50">Admin Checking</span>
                    </li>
                    
                    {{-- Step 3: Seleksi (Aktif jika status selection atau announced) --}}
                    <li class="step {{ in_array($status, ['selection', 'announced']) ? 'step-primary' : '' }}">
                        Penentuan Level <br><span class="text-xs opacity-50">Tes Tertulis/Lisan</span>
                    </li>
                    
                    {{-- Step 4: Selesai jika announced --}}
                    <li class="step {{ $status == 'announced' ? 'step-primary' : '' }}">
                        Pengumuman <br><span class="text-xs opacity-50">Hasil Akhir</span>
                    </li>
                </ul>

                {{-- STATUS MESSAGE BOX --}}
                <div class="alert mt-10 {{ $status == 'selection' ? 'bg-blue-50 border-blue-200' : 'bg-base-200 border-none' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="text-left">
                        @if($status == 'waiting_verification')
                            <h3 class="font-bold">Menunggu Verifikasi</h3>
                            <div class="text-xs">Data dan bukti transfer Anda sedang diperiksa oleh admin. Mohon cek berkala dalam 1x24 jam.</div>
                        
                        @elseif($status == 'selection')
                            <h3 class="font-bold text-blue-700 text-left">Pembayaran Valid & Masuk Tahap Seleksi</h3>
                            <div class="text-xs text-blue-600">Terima kasih! Pembayaran Anda telah dikonfirmasi. Saat ini Anda berada dalam antrean <strong>Proses Seleksi</strong>. Admin akan segera menghubungi Anda via WhatsApp untuk jadwal tes.</div>

                        @elseif($status == 'announced')
                            <div class="mt-6 p-6 md:mx-14 bg-green-50 border-2 border-green-500 rounded-2xl shadow-inner animate-pulse">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-green-500 rounded-full text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="font-black text-xl text-green-800 uppercase italic">Levelmu telah ditentukan!</h3>
                            </div>

                            <div class="space-y-4 text-left">
                                <div class="bg-white p-4 rounded-xl border border-green-200">
                                    <p class="text-xs uppercase text-gray-500 font-bold mb-1">Program / Kelas Penempatan:</p>
                                    <p class="text-lg font-bold text-primary italic">
                                        {{ $user->program->nama_program ?? 'Program Reguler' }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white p-4 rounded-xl border border-green-200">
                                        <p class="text-xs uppercase text-gray-500 font-bold mb-1">Jadwal Mulai:</p>
                                        <p class="font-bold text-gray-800">
                                            {{ $user->program->jadwal ?? 'Akan diinfokan via WA' }}
                                        </p>
                                    </div>
                                    <div class="bg-white p-4 rounded-xl border border-green-200">
                                        <p class="text-xs uppercase text-gray-500 font-bold mb-1">Lokasi:</p>
                                        <p class="font-bold text-gray-800">The Master of Dumai</p>
                                    </div>
                                </div>
                                
                                <div class="p-4 bg-yellow-100 rounded-xl border border-yellow-300">
                                    <p class="text-xs font-medium text-yellow-800">
                                        <strong>Catatan:</strong> Kami akan segera menghubungi anda dalam 1x24 jam untuk langkah selanjutnya.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @elseif($status == 'rejected')
                            <h3 class="font-bold text-error">Data Tidak Valid</h3>
                            <div class="text-xs text-error">Mohon perbaiki data Anda sesuai dengan instruksi admin di atas agar kami dapat memproses pendaftaran Anda.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- LOGOUT BUTTON --}}
        <div class="mt-8 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-primary border btn-lg shadow-2xl" data-theme="light">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar / Logout
                </button>
            </form>
        </div>

    </div>
</div>