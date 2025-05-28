<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guiche extends Model
{
    use HasFactory;

    protected $table = 'guiche';

    protected $fillable = ['name'];

    /**
     * Relacionamento com User (muitos-para-muitos).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_guiche', 'guiche_id', 'user_id')->withTimestamps();
    }
}
