<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Siswa;
use Illuminate\Support\Facades\Log;

class Registration extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username', 
        'password', 
        'nama',
        'jenis_kelamin', 
        'agama', 
        'grade',
        'asal_sekolah', 
        'photo', 
        'no_wa_wali',
        'nama_orang_tua', 
        'pekerjaan_orang_tua', 
        'alamat', 
        'tgl_lahir', 
        'tgl_registrasi', 
        'bukti_pembayaran',
        'sumber_info',
        'alasan_kursus',
        'catatan_admin',
        'status',
        'program_id',
        'spp_amount',
        'registration_fee',
        'tgl_masuk',
        'billing_day',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', 
        'tgl_lahir' => 'date',
        'tgl_registrasi' => 'date',
    ];

    protected static function booted()
    {
        static::saved(function ($registration) {
            if ($registration->status === 'announced') {
                \App\Models\Siswa::updateOrCreate(
                    [
                        // Pencarian data unik berdasarkan Nama dan No Wali
                        'nama' => $registration->nama,
                        'no_wali' => $registration->no_wa_wali,
                    ],
                    [
                        // DATA IDENTITAS & BARU
                        'foto' => $registration->photo,
                        'jenis_kelamin' => $registration->jenis_kelamin,
                        'agama' => $registration->agama,
                        'tgl_lahir' => $registration->tgl_lahir,
                        'alamat' => $registration->alamat ?? '-',
                        
                        // DATA PENDIDIKAN
                        'kelas_disekolah' => $registration->grade,
                        'asal_sekolah' => $registration->asal_sekolah,
                        
                        // DATA ORANG TUA
                        'nama_orang_tua' => $registration->nama_orang_tua,
                        'pekerjaan_orang_tua' => $registration->pekerjaan_orang_tua,
                        
                        // DATA PROGRAM & KEUANGAN
                        'program_id' => $registration->program_id,
                        'registration_proof' => $registration->bukti_pembayaran,
                        'spp_amount' => $registration->spp_amount ?? 0,
                        'registration_fee' => $registration->registration_fee ?? 0,
                        
                        // DATA TRACKING / SURVEY
                        'sumber_info' => $registration->sumber_info,
                        'alasan_kursus' => $registration->alasan_kursus,
                        
                        // STATUS & SISTEM
                        'status' => 'active',
                        'tgl_masuk' => $registration->tgl_masuk ?? now(),
                        'tgl_registrasi' => $registration->created_at,
                        'billing_day' => $registration->billing_day ?? 10,
                    ]
                );
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id'); 
    }
}
