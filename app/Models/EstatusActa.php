<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUpperCaseAttributes;

class EstatusActa extends Model
{
    use HasFactory, HasUpperCaseAttributes;

    protected $table = 'estatus_actas';

    protected $fillable = [
        'nombre',
        'color',
    ];
}
