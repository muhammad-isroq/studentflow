<?php

namespace App\Livewire;

use App\Models\Registration;
use App\Models\Siswa;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PendaftaranSiswa extends Component
{
    use WithFileUploads;

    // Fields sesuai tabel registrations (Email diganti Username)
    public $nama, $username, $password, $grade, $no_wa_wali, $alamat, $tgl_lahir;
    public $photo, $bukti_pembayaran;
    public $jenis_kelamin, $agama, $asal_sekolah, $nama_orang_tua, $pekerjaan_orang_tua, $sumber_info, $alasan_kursus;
    
    // Properti tambahan untuk mode edit/perbaikan
    public $isEditMode = false;
    public $existingPhoto, $existingBukti;

    protected $messages = [
        'username.required' => 'Mohon isi username untuk login kembali.',
        'username.unique' => 'Username ini sudah digunakan, coba yang lain.',
        'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, dan tanda strip.',
        'password.required' => 'Mohon isi password untuk akun pendaftaran.',
        'password.min' => 'Password minimal 8 karakter agar aman.',
        'nama.required' => 'Nama lengkap wajib diisi.',
        'no_wa_wali.numeric' => 'Nomor WhatsApp harus berupa angka.',
        'photo.required' => 'Harap unggah foto formal siswa.',
        'bukti_pembayaran.required' => 'Bukti transfer wajib diunggah.',
    ];

    public function mount()
    {
        if (auth()->guard('registration')->check()) {
            $siswa = auth()->guard('registration')->user();
            
            $this->nama = $siswa->nama;
            $this->username = $siswa->username; // Memuat data username
            $this->grade = $siswa->grade;
            $this->no_wa_wali = $siswa->no_wa_wali;
            $this->alamat = $siswa->alamat;
            $this->tgl_lahir = $siswa->tgl_lahir;
            $this->jenis_kelamin = $siswa->jenis_kelamin;
            $this->agama = $siswa->agama;
            $this->asal_sekolah = $siswa->asal_sekolah;
            $this->nama_orang_tua = $siswa->nama_orang_tua;
            $this->pekerjaan_orang_tua = $siswa->pekerjaan_orang_tua;
            $this->sumber_info = $siswa->sumber_info;
            $this->alasan_kursus = $siswa->alasan_kursus;
            
            $this->existingPhoto = $siswa->photo;
            $this->existingBukti = $siswa->bukti_pembayaran;
            $this->isEditMode = true;
        }
    }

    public function save()
    {
        $rules = [
            'nama' => 'required|string|min:3|max:100',
            'grade' => 'required',
            'no_wa_wali' => 'required|numeric|digits_between:10,15',
            'alamat' => 'required|string|max:500',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'asal_sekolah' => 'required|string|max:150',
            'nama_orang_tua' => 'required|string',
            'pekerjaan_orang_tua' => 'required|string',
            'sumber_info' => 'required',
            'alasan_kursus' => 'required|string',
        ];

        if (!$this->isEditMode) {
            // Validasi Username untuk pendaftaran baru
            $rules['username'] = 'required|string|min:4|alpha_dash|unique:registrations,username';
            $rules['password'] = 'required|string|min:8';
            $rules['photo'] = 'required|image|mimes:jpeg,png,jpg|max:6048';
            $rules['bukti_pembayaran'] = 'required|image|mimes:jpeg,png,jpg|max:6048';
        } else {
            // Username tetap divalidasi tapi abaikan ID diri sendiri agar tidak error saat save
            $rules['username'] = 'required|string|min:4|alpha_dash|unique:registrations,username,' . auth()->guard('registration')->id();
            $rules['photo'] = 'nullable|image|mimes:jpeg,png,jpg|max:6048';
            $rules['bukti_pembayaran'] = 'nullable|image|mimes:jpeg,png,jpg|max:6048';
        }

        $this->validate($rules);

        if ($this->isEditMode) {
            $this->updateData();
        } else {
            $this->registerNewSiswa();
        }
    }

    protected function registerNewSiswa()
    {
        $photoPath = $this->photo->store('photos', 'public');
        $buktiPath = $this->bukti_pembayaran->store('payments', 'public');

        // 1. Simpan ke tabel Registration
        $regSiswa = Registration::create([
            'nama' => strip_tags($this->nama),
            'username' => strtolower($this->username),
            'password' => Hash::make($this->password),
            'jenis_kelamin' => $this->jenis_kelamin,
            'agama' => $this->agama,
            'grade' => $this->grade,
            'asal_sekolah' => $this->asal_sekolah,
            'no_wa_wali' => $this->no_wa_wali,
            'nama_orang_tua' => $this->nama_orang_tua,
            'pekerjaan_orang_tua' => $this->pekerjaan_orang_tua,
            'alamat' => $this->alamat,
            'tgl_lahir' => $this->tgl_lahir,
            'photo' => $photoPath,
            'bukti_pembayaran' => $buktiPath,
            'sumber_info' => $this->sumber_info,
            'alasan_kursus' => $this->alasan_kursus,
            'status' => 'waiting_verification',
        ]);

        // 2. Langsung simpan ke tabel Siswa (Alur Langsung Aktif)
        Siswa::create([
            'nama' => $this->nama,
            'no_wali' => $this->no_wa_wali,
            'foto' => $photoPath,
            'jenis_kelamin' => $this->jenis_kelamin,
            'agama' => $this->agama,
            'kelas_disekolah' => $this->grade,
            'asal_sekolah' => $this->asal_sekolah,
            'nama_orang_tua' => $this->nama_orang_tua,
            'pekerjaan_orang_tua' => $this->pekerjaan_orang_tua,
            'alamat' => $this->alamat,
            'tgl_lahir' => $this->tgl_lahir,
            'registration_proof' => $buktiPath,
            'status' => 'active',
            'tgl_masuk' => now(),
            'tgl_registrasi' => now(),
            'billing_day' => 10,
        ]);

        session()->flash('success', 'Pendaftaran berhasil!');
        auth()->guard('registration')->login($regSiswa);
        return redirect()->to('/pendaftaran/status');
    }

    protected function updateData()
    {
        $userReg = auth()->guard('registration')->user();
        
        $dataUpdate = [
            'nama' => strip_tags($this->nama),
            'username' => strtolower($this->username),
            'jenis_kelamin' => $this->jenis_kelamin,
            'agama' => $this->agama,
            'grade' => $this->grade,
            'asal_sekolah' => $this->asal_sekolah,
            'no_wa_wali' => $this->no_wa_wali,
            'nama_orang_tua' => $this->nama_orang_tua,
            'pekerjaan_orang_tua' => $this->pekerjaan_orang_tua,
            'alamat' => $this->alamat,
            'tgl_lahir' => $this->tgl_lahir,
            'sumber_info' => $this->sumber_info,
            'alasan_kursus' => $this->alasan_kursus,
            'status' => 'waiting_verification',
            'catatan_admin' => null,
        ];

        if ($this->photo) {
            if ($userReg->photo) Storage::disk('public')->delete($userReg->photo);
            $dataUpdate['photo'] = $this->photo->store('photos', 'public');
        }
        if ($this->bukti_pembayaran) {
            if ($userReg->bukti_pembayaran) Storage::disk('public')->delete($userReg->bukti_pembayaran);
            $dataUpdate['bukti_pembayaran'] = $this->bukti_pembayaran->store('payments', 'public');
        }

        // Simpan referensi data lama sebelum update tabel registration untuk pencarian di tabel siswa
        $oldNama = $userReg->getOriginal('nama');
        $oldWali = $userReg->getOriginal('no_wa_wali');

        $userReg->update($dataUpdate);

        // Update Tabel Siswa agar sinkron
        $siswa = Siswa::where('no_wali', $oldWali)->where('nama', $oldNama)->first();

        if ($siswa) {
            $siswa->update([
                'nama' => $dataUpdate['nama'],
                'no_wali' => $dataUpdate['no_wa_wali'],
                'foto' => $dataUpdate['photo'] ?? $siswa->foto,
                'jenis_kelamin' => $dataUpdate['jenis_kelamin'],
                'agama' => $dataUpdate['agama'],
                'kelas_disekolah' => $dataUpdate['grade'],
                'asal_sekolah' => $dataUpdate['asal_sekolah'],
                'nama_orang_tua' => $dataUpdate['nama_orang_tua'],
                'pekerjaan_orang_tua' => $dataUpdate['pekerjaan_orang_tua'],
                'alamat' => $dataUpdate['alamat'],
                'tgl_lahir' => $dataUpdate['tgl_lahir'],
                'registration_proof' => $dataUpdate['bukti_pembayaran'] ?? $siswa->registration_proof,
            ]);
        }

        session()->flash('success', 'Perbaikan data berhasil dikirim.');
        return redirect()->to('/pendaftaran/status');
    }

    public function render()
    {
        return view('livewire.pendaftaran-siswa');
    }
}