<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUpperCaseAttributes;

class CategoriaBien extends Model
{
    use HasFactory, HasUpperCaseAttributes;

    protected $table = 'categoria_bienes';
    
    protected $fillable = [
        'nombre',
        'descripcion',
    ];
}
