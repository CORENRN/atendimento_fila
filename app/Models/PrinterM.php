<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterM extends Model
{
    protected $table = 'printers';
    protected $fillable = ['name', 'ip'];
}
