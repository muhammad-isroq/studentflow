<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginSiswa extends Component
{
    // Properti diubah dari email ke username
    public $username, $password;

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        // Buat kunci unik berdasarkan username dan IP untuk mencegah brute force
        $throttleKey = Str::lower($this->username) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('username', "Terlalu banyak percobaan login. Silakan coba lagi dalam $seconds detik.");
            return;
        }

        // Coba login menggunakan guard pendaftaran dengan kolom username
        if (Auth::guard('registration')->attempt([
            'username' => strtolower($this->username), 
            'password' => $this->password
        ])) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            
            return redirect()->to('/pendaftaran/status');
        }

        // Jika gagal login
        RateLimiter::hit($throttleKey);
        $this->addError('username', 'Username atau password salah.');
    }

    public function render()
    {
        return view('livewire.login-siswa');
    }
}