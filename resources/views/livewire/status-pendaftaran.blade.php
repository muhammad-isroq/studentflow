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
            <div class="card-body flex-col md:flex-row items-center gap-4 sm:gap-6 text-center md:text-left">
                <div class="avatar">
                    <div class="w-20 sm:w-24 rounded-xl ring ring-primary ring-offset-base-100 ring-offset-2">
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto Profil" />
                    </div>
                </div>
                <div class="flex-grow">
                    <h2 class="text-xl sm:text-2xl font-bold italic text-primary uppercase">Halo, {{ $user->nama }}!</h2>
                    <p class="opacity-70 text-xs sm:text-sm">Selamat datang di dashboard calon siswa The Master of Dumai.</p>
                    <div class="flex justify-center md:justify-start">
                        <div class="badge badge-secondary mt-2 uppercase font-bold p-3">ID: REG-{{ $user->id }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEPS SECTION --}}
        <div class="card bg-base-100 shadow-xl overflow-hidden" data-theme="light">
            <div class="card-body p-4 sm:p-8">
                <h3 class="card-title text-base sm:text-lg mb-6 border-b pb-2">Status Alur Pendaftaran:</h3>
                
                <div class="w-full overflow-x-hidden">
                    <ul class="steps steps-vertical lg:steps-horizontal w-full min-h-[350px] lg:min-h-0 py-2">
                        {{-- Step 1 --}}
                        <li class="step step-primary text-xs sm:text-sm mb-4 lg:mb-0" data-content="✓">
                            <div class="flex flex-col text-left lg:text-center ml-4 lg:ml-0">
                                <span class="font-bold">Registrasi</span>
                                <span class="text-[10px] text-success font-bold italic">Success</span>
                            </div>
                        </li>
                        
                        {{-- Step 2 --}}
                        @php $isStep2 = in_array($status, ['selection', 'announced']); @endphp
                        <li class="step {{ $isStep2 ? 'step-primary' : '' }} text-xs sm:text-sm mb-4 lg:mb-0">
                            <div class="flex flex-col text-left lg:text-center ml-4 lg:ml-0">
                                <span class="font-bold">Verifikasi</span>
                                <span class="text-[10px] opacity-60">Admin Checking</span>
                            </div>
                        </li>
                        
                        {{-- Step 3 --}}
                        <li class="step {{ $isStep2 ? 'step-primary' : '' }} text-xs sm:text-sm mb-4 lg:mb-0">
                            <div class="flex flex-col text-left lg:text-center ml-4 lg:ml-0">
                                <span class="font-bold">Penentuan Level</span>
                                <span class="text-[10px] opacity-60">Tes Tertulis/Lisan</span>
                            </div>
                        </li>
                        
                        {{-- Step 4 --}}
                        <li class="step {{ $status == 'announced' ? 'step-primary' : '' }} text-xs sm:text-sm">
                            <div class="flex flex-col text-left lg:text-center ml-4 lg:ml-0">
                                <span class="font-bold">Pengumuman</span>
                                <span class="text-[10px] opacity-60">Hasil Akhir</span>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- STATUS MESSAGE BOX (Responsif) --}}
                <div class="alert mt-6 sm:mt-10 flex-col sm:flex-row items-start sm:items-center {{ $status == 'selection' ? 'bg-blue-50 border-blue-200' : 'bg-base-200 border-none' }}">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="text-left">
                            @if($status == 'waiting_verification')
                                <h3 class="font-bold text-sm sm:text-base">Menunggu Verifikasi</h3>
                                <p class="text-[11px] sm:text-xs">Data & bukti transfer sedang diperiksa. Mohon cek berkala dalam 1x24 jam.</p>
                            
                            @elseif($status == 'selection')
                                <h3 class="font-bold text-blue-700 text-sm sm:text-base">Pembayaran Valid</h3>
                                <p class="text-[11px] sm:text-xs text-blue-600">Saat ini Anda dalam antrean <strong>Proses Penentuan Level</strong>. Kami akan menghubungi via WhatsApp.</p>

                            @elseif($status == 'announced')
                                {{-- Desain box pengumuman untuk mobile --}}
                                <div class="p-1">
                                    <h3 class="font-black text-lg text-green-800 uppercase italic leading-tight">Levelmu telah ditentukan!</h3>
                                    <div class="mt-4 space-y-3">
                                        <div class="bg-white p-3 rounded-lg border border-green-200">
                                            <p class="text-[10px] uppercase text-gray-400 font-bold">Program:</p>
                                            <p class="text-sm font-bold text-primary">{{ $user->program->nama_program ?? 'Program Reguler' }}</p>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <div class="bg-white p-3 rounded-lg border border-green-200">
                                                <p class="text-[10px] uppercase text-gray-400 font-bold">Jadwal:</p>
                                                <p class="text-xs font-bold">{{ $user->program->jadwal ?? 'Cek WA' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
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