<div class="min-h-screen bg-base-200 flex items-center justify-center pt-16 pb-12" data-theme="caramellatte">
    <div class="max-w-md w-full px-4">
        <div class="card bg-base-100 shadow-2xl border border-primary/10" data-theme="light">
            <div class="card-body p-8">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-extrabold text-primary tracking-tight">Login Siswa</h2>
                    <p class="text-sm opacity-60 mt-2">Gunakan akun yang telah Anda daftarkan</p>
                </div>

                <form wire:submit.prevent="login" class="space-y-5">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-bold text-primary">Username Pendaftaran</span>
                        </label>
                        <div class="relative">
                            <input type="text" wire:model="username" 
                                class="input input-bordered focus:input-primary w-full pl-10" 
                                placeholder="Contoh: budi123">
                            {{-- Ikon User (Opsional agar lebih cantik) --}}
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <label class="label">
                            <span class="label-text-alt text-gray-500 italic">Gunakan username yang dibuat saat mendaftar.</span>
                        </label>
                        @error('username') <span class="text-error text-xs mt-1 italic">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-bold text-primary">Password Akun</span>
                        </label>
                        <input type="password" wire:model="password" 
                               class="input input-bordered focus:input-primary w-full" 
                               placeholder="Masukkan password Anda">
                        @error('password') <span class="text-error text-xs mt-1 italic">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary btn-block text-lg shadow-lg uppercase" wire:loading.attr="disabled">
                            <span wire:loading.remove>Masuk ke Dashboard</span>
                        </button>
                    </div>
                </form>

                <div class="divider my-8">ATAU</div>

                <div class="text-center">
                    <p class="text-sm opacity-70">Belum memiliki akun?</p>
                    <a href="/pendaftaran/register" class="btn btn-link btn-sm text-primary font-bold no-underline hover:underline">
                        Daftar Siswa Baru di Sini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>