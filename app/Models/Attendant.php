<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'username', 'password', 'active'];

    protected $hidden = ['password'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
