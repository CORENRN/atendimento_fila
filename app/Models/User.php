<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const CATEGORIA_SUPER_ADMIN = 'superAdmin';
    public const CATEGORIA_SUPERVISOR = 'supervisor';
    public const CATEGORIA_USER = 'user';

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'categoria',
        'email_verified_at',
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

    // Métodos de permissão que o seu Menu (Sidebar) exige:
    public function isSuperAdmin(): bool { 
        return $this->categoria === self::CATEGORIA_SUPER_ADMIN; 
    }

    public function isSupervisor(): bool { 
        return $this->categoria === self::CATEGORIA_SUPERVISOR; 
    }

    public function isUser(): bool { 
        return $this->categoria === self::CATEGORIA_USER; 
    }
    

    public function hasAdminAccess(): bool
    {
        return $this->isSuperAdmin() || $this->isSupervisor();
    }
    /**
 * Relacionamento com Guiche (muitos-para-muitos).
 */
    public function guiches()
    {
        return $this->belongsToMany(Guiche::class, 'user_guiche', 'user_id', 'guiche_id')->withTimestamps();
    }
}