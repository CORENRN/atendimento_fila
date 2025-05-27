<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'service',
        'type',
        'stage',
        'status',
        'called_at',
        'finished_at',
        'attendant_id'
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function attendant()
    {
        return $this->belongsTo(Attendant::class);
    }

public function getDurationFormattedAttribute()
{
    if ($this->called_at && $this->finished_at) {
        $inicio = Carbon::parse($this->called_at);
        $fim = Carbon::parse($this->finished_at);

        // Calcular diferença absoluta em segundos
        $diff = abs($fim->diffInSeconds($inicio));

        $h = floor($diff / 3600);
        $m = floor(($diff % 3600) / 60);
        $s = $diff % 60;

        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
    return '00:00:00';
}


}

