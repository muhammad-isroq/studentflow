<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\Storage;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasRoles, Impersonate;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo', 
        'position', 
        'instagram_url', 
        'linkedin_url',
        'password_changed_at',
        'tanggal_lahir',
        'last_activity',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
            'last_activity'     => 'datetime',
        ];
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->photo) {
            // 3. Gunakan Storage::url() untuk cara yang lebih andal
            return Storage::url($this->photo);
        }

        return null;
    }

    public function canImpersonate()
    {
        return $this->role === 'admin' || $this->role === 'staff';
    }

    public function canBeImpersonated()
    {
        return $this->role === 'guru';
    }
}
