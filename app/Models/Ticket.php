<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'attendant_id',
        'triagem_id',
        'called_at',
        'last_called_at',
        'called_tri_at',
        'finished_at',
        'stage',
        'type',
        'service',
        'cpf',
        'ticket_number',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'last_called_at' => 'datetime',
        'called_tri_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function attendant()
    {
        return $this->belongsTo(User::class);
    }

    public function triagem()
    {
        return $this->belongsTo(User::class, 'triagem_id');
    }

    public function getDurationFormattedAttribute()
    {
        if ($this->called_at && $this->finished_at) {
            $inicio = Carbon::parse($this->called_at);
            $fim = Carbon::parse($this->finished_at);

            $diff = abs($fim->diffInSeconds($inicio));

            $h = floor($diff / 3600);
            $m = floor(($diff % 3600) / 60);
            $s = $diff % 60;

            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        }

        return '00:00:00';
    }
}
