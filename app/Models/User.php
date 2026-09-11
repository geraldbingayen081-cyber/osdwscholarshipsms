<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdminOrStaff();
    }

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute(): string
    {
        $middle = $this->middle_name ? " {$this->middle_name} " : ' ';
        return "{$this->first_name}{$middle}{$this->last_name}";
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return null;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isAdminOrStaff(): bool
    {
        return in_array($this->role, ['admin', 'staff']);
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }
}
