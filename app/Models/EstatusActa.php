<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstatusActa extends Model
{
    use HasFactory;

    protected $table = 'estatus_actas';

    protected $fillable = [
        'nombre',
        'color',
    ];
}
