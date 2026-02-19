<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUpperCaseAttributes;

class Departamento extends Model
{
    use HasFactory, HasUpperCaseAttributes;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];
}
