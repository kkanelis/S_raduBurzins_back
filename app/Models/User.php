<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'nickname',
        'phone',
        'date_of_birth',
        'email',
        'password',
        'is_approved',
        'is_admin',
        'terms',
        'rules',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'is_admin' => 'boolean',
            'terms' => 'boolean',
            'rules' => 'boolean',
        ];
    }

    // Update the accessor to use the newer syntax
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // You can also keep the name accessor if needed
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }
}
