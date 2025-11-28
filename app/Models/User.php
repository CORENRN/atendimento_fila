<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Constantes de categoria
    public const CATEGORIA_SUPER_ADMIN = 'superAdmin';
    public const CATEGORIA_ADMIN = 'admin';
    public const CATEGORIA_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'categoria',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Relacionamento com guichês.
     */
    public function guiches()
    {
        return $this->belongsToMany(Guiche::class, 'user_guiche', 'user_id', 'guiche_id')->withTimestamps();
    }

    /**
     * Retorna as categorias válidas.
     */
    public static function categorias(): array
    {
        return [
            self::CATEGORIA_SUPER_ADMIN,
            self::CATEGORIA_ADMIN,
            self::CATEGORIA_USER,
        ];
    }

    /**
     * Verifica se é Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->categoria === self::CATEGORIA_SUPER_ADMIN;
    }

    /**
     * Verifica se é Admin.
     */
    public function isAdmin(): bool
    {
        return $this->categoria === self::CATEGORIA_ADMIN;
    }

    /**
     * Verifica se é User.
     */
    public function isUser(): bool
    {
        return $this->categoria === self::CATEGORIA_USER;
    }
}
